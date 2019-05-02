<?php
/**
 * FFMpeg interface.
 *
 *
 *
 * Responsible for -
 *
 * 	1.Encoding
 * 	2.pushing up to output folder.
 * 	3.returning boolean result for all of the above.
 *
 *
 *
 *
 *
 *
 */
namespace App;

use Exception;
use Log;
use App\MockupLogger;
use Storage;
use App\WeMockupFiles;
use App\Item;
use App\ItemJob;
use App\Itempostproc;
use App\product;
use Aws\S3\S3Client;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class FFMpeg
{




  //////////////////////////////////////////
  ///The path to the blender bin
  ///
  ///
  public $PathToFFMpeg; // '/Applications/ffmpeg/ffmpeg';

  public $outputPrefix = 'output';

  public $outputFormat;



  public $files; // holds wemockup filesystem resource.

  public $itempostproc;


  public $error = false;
  public $errors = array();




  public function __construct(Itempostproc $itempostproc, $outputFormat)
  {
      $this->itempostproc = $itempostproc;
      $this->outputFormat = $outputFormat;
      $this->files = new WeMockupFiles;
      $this->PathToFFMpeg = config('paths.pathtoffmpeg');
  }




  /**
   * Main starting point for FFMpeg
   *
   * returns bool.
   *
   * @param  ItemJob $itemjob [description]
   * @return [type]           [description]
   */
  public function process() {

    //Log::debug('Starting Blender');
    MockupLogger::Item('debug',$this->itempostproc->item,'FFMpeg Starting');

    $this->encode();

    //return result boolean//////////////
    if ($this->error) {
      return false;
    } else {
      return true;
    }

  }


  //////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////








  public function encode() {
    $this->execute();
    //$this->saveLogToItemPostProc();
    $this->saveOutput();
  }




  /**
   * Save the output log to the item job record
   *
   *
   *
   *
   *
   * @return [type] [description]
   */
  public function saveLogToItemPostProc() {
    //$logdata = file_get_contents($this->getFullyQualifiedLogPath());
    //$this->itempostproc->processinglog = $logdata;
    //$this->itempostproc->save();
  }



  /**
   * Push output up to S3
   *
   *
   *
   *
   *
   *
   * @return [type] [description]
   */
  public function saveOutput() {
    MockupLogger::Item('debug', $this->itempostproc->item, 'FFMpeg Copy file to S3 output folder: ' . $this->s3OutputKey());
    $this->files->pushToS3($this->OutputFileName(), $this->outputFilePathFullyQualified(), $this->s3OutputKey());
  }




  /**
   * Execute the task in blender
   *
   *
   *
   *
   * @return [type] [description]
   */
  public function execute() {

    $process = new Process($this->constructCommand());
    $process->start();
    while ($process->isRunning()) {
      sleep(1);
      //check progress?
    }


    //Check the script has exited successfully
    if ($process->isSuccessful()) {
        MockupLogger::Item('debug',$this->itempostproc->item,'FFMpeg Success');
    } else {
        MockupLogger::Item('debug',$this->itempostproc->item,'FFMPeg Error');
        $this->error = true;
        $this->errors[] = 'Error encoding video';
    }




  }






  public function OutputFileName() {
    return $this->outputPrefix . '.' . $this->outputFormat;
  }






  /**
   * Construct the command to execute.
   *
   *
   *
   *
   *
   * @return [type] [description]
   */
  public function constructCommand() {


    //Get data for config
    $postproc_config = json_decode($this->itempostproc->postproc->data);



    //set the output string according to the file string formats
    switch($this->itempostproc->item->sku->product->type->jobname) {
      case 'RenderStMultipleFrame':
        $output_file_string = '/item' . $this->itempostproc->item->id . '_%05d.'; // 5 padding zeros
        break;
      case 'RenderStSingleFrame':
          $output_file_string = '/item' . $this->itempostproc->item->id . '_%05d.';
          break;

      default:
          //all other outputs
          $output_file_string = '/output_%04d.';
          break;
    }

    switch($postproc_config->type) {

      /// Basic video output
      /// pathtoffmpeg/ffmpeg -framerate 30  -i output_%04d.png -s 1080x764 -c:v libx264 -crf 23 -pix_fmt yuv420p output.mov
      case "normal":
          $cmd = $this->PathToFFMpeg . ' -framerate ' . $postproc_config->outputframerate;
          $cmd .= ' -i ' . $this->getWorkingDirectory() . $output_file_string . strtolower($this->itempostproc->item->sku->frameconfig->outputformat);
          $cmd .= ' -loglevel verbose';
          $cmd .= ' -s ' . $this->itempostproc->item->sku->frameconfig->dimx . 'x' . $this->itempostproc->item->sku->frameconfig->dimy;
          $cmd .= ' -c:v libx264 -crf 23 -pix_fmt yuv420p -y ' . $this->outputFilePathFullyQualified();
          $cmd .= ' 2> ' . $this->getFullyQualifiedLogPath();
          break;


      /// Slideshow - 1 slide per minute output as 30fps video with fade between images.
      /// pathtoffmpeg/ffmpeg -framerate 1  -i output_%04d.png -vf "framerate=fps=30:interp_start=0:interp_end=255" -s 1080x764 -c:v libx264 -crf 23 -pix_fmt yuv420p output.mov
      case "slideshow":
          $cmd = $this->PathToFFMpeg . ' -framerate ' . $postproc_config->inputframerate;
          $cmd .= ' -i ' . $this->getWorkingDirectory() . $output_file_string . strtolower($this->itempostproc->item->sku->frameconfig->outputformat);
          $cmd .= ' -vf "framerate=fps=' . $postproc_config->outputframerate . ':interp_start=0:interp_end=255"';
          $cmd .= ' -loglevel verbose';
          $cmd .= ' -s ' . $this->itempostproc->item->sku->frameconfig->dimx . 'x' . $this->itempostproc->item->sku->frameconfig->dimy;
          $cmd .= ' -c:v libx264 -crf 23 -pix_fmt yuv420p -y ' . $this->outputFilePathFullyQualified();
          $cmd .= ' 2> ' . $this->getFullyQualifiedLogPath();
          break;
    }





    MockupLogger::Item('debug',$this->itempostproc->item,'FFMpeg Running Command: ' . $cmd);

    return $cmd;

  }








  /////////////////////////////////////////////////////////////////////////////////
  ///
  ///
  ///
  ///PATHS
  ///
  ///
  ///

  public function getWorkingDirectory() {
    return storage_path('app/' . $this->files->outputWorkingDirectory($this->itempostproc->item));
  }


  public function outputFilePathFullyQualified() {
      $filepath = storage_path('app/' . $this->outputFilePath());
      return $filepath;
  }

  public function outputFilePath() {
    //$files = new WeMockupFiles;
    $filepath = $this->files->outputWorkingDirectory($this->itempostproc->item) . '/' . $this->OutputFileName();
    return $filepath;
  }

  public function s3OutputKey() {
    $filepath = $this->files->outputDirectory($this->itempostproc->item) . '/' . $this->OutputFileName();
    return $filepath;
  }





  /**
   * Return the fully qualified path for the project outpu log file.
   *
   *
   * @return [type] [description]
   */
  public function getFullyQualifiedLogPath() {
    $filepath = $this->getWorkingDirectory() . '/encoding_log.log';
    return $filepath;
  }


}
