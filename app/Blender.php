<?php
/**
 * Blender interface.
 *
 *
 *
 * Responsible for -
 *
 * 	1.Rendering - execute render script.
 * 	2.Monitoring the output file - updating progress of itemjob (trigger events)
 * 	3.setting status of itemjob to error and reporting errors on the job
 * 	4.pushing up to output folder.
 * 	5.returning boolean result for all of the above.
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
use App\product;
use Aws\S3\S3Client;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use App\Events\ItemJobProgressUpdated;

class Blender
{

  /////////////////////////////////////////
  ///The name of the main blend file in the product project folder.
  ///
  ///
  public $project_file_name = 'index.blend';


  //////////////////////////////////////////
  ///The name of the log output file to monitor for output.
  ///
  ///
  public $project_log_file = 'blender.log';


  ////////////////////////////////////////////
  ///The script used in all projects to configure the blender file
  ///
  ///
  public $renderscript_file = 'render.py';
  public $setupscript_file = 'setup.py';


  //////////////////////////////////////////
  ///The path to the blender bin
  ///
  ///
  public $PathToBlender; //'/Applications/blender277a/blender.app/Contents/MacOS/blender';




  public $files; // holds wemockup filesystem resource.

  /////////////////////////////////////////
  ///The progress of the job.
  ///
  ///
  ///
  public $jobProgress = 0;



  public $itemjob;

  public $error = false;
  public $errors = array();


  public function __construct(ItemJob $itemjob)
  {
      $this->itemjob = $itemjob;
      $this->files = new WeMockupFiles;
      $this->PathToBlender = config('paths.pathtoblender');
  }




  /**
   * Main starting point for Blender.
   *
   * returns bool.
   *
   * @param  ItemJob $itemjob [description]
   * @return [type]           [description]
   */
  public function process() {

    //Log::debug('Starting Blender');
    MockupLogger::ItemJob('debug',$this->itemjob,'Starting Blender');


    $this->render();

    //return result boolean//////////////
    if ($this->error) {
      return false;
    } else {
      return true;
    }

  }



  //Setup the blender file (not render/process)
  //
  //executes the setup script for the package
  //
  //
  //
  public function setup() {
    $this->clearLogFile();
    $process = new Process($this->constructSetupCommand());
    $process->start();
    while ($process->isRunning()) {
      sleep(3);
    }

    //Check the script has exited successfully
    if ($process->isSuccessful()) {
        //Log::debug('SCRIPT SUCCESS');
        MockupLogger::ItemJob('debug',$this->itemjob,'Script Success');
    } else {
        //Log::debug('SCRIPT ERROR');
        MockupLogger::ItemJob('debug',$this->itemjob,'Script Error');
        $this->error = true;
        $this->errors[] = 'Error with execution of setup script';
    }

    //return result boolean//////////////
    if ($this->error) {
      return false;
    } else {
      return true;
    }
  }


  //////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////








  public function render() {
    $this->clearLogFile();
    $this->execute();
    $this->saveLogToJob();
    $this->saveOutput();
  }








  /**
   * Clear out the contents of the log file prior to it being used again.
   *
   *
   *
   *
   * @return [type] [description]
   */
  public function clearLogFile() {
    $logfilepath = $this->getBlenderLogPath();
    Storage::disk('local')->put($logfilepath,'');
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
  public function saveLogToJob() {
    //$logdata = file_get_contents($this->getFullyQualifiedBlenderLogPath());
    //$this->itemjob->processinglog = $logdata;
    //$this->itemjob->save();
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
    //$files = new WeMockupFiles;
    if ($this->itemjob->item->sku->product->type->jobname == 'BlenderSingleFrame') {
      //single frame so push directly to output directory
      $this->files->pushToS3($this->outputFileName(), $this->workingOutputFilePathFullyQualified(), $this->outputFilePath());
    } else {
      //Multiple frame - so push up to working directory
      $this->files->pushToS3($this->outputFileName(), $this->workingOutputFilePathFullyQualified(), $this->workingOutputFilePath());
    }
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

    MockupLogger::ItemJob('debug',$this->itemjob,'Execute Blender Task');
    MockupLogger::ItemJob('debug',$this->itemjob,'File Path: ' . $this->getBlendFilePath());


    //$process = new Process('/Applications/blender277a/blender.app/Contents/MacOS/blender --factory-startup -noaudio --debug  -b /Users/matthewstowe/Desktop/test.blend --python-exit-code 1 --python /Users/matthewstowe/Desktop/test.py > /Users/matthewstowe/Desktop/testoutput.txt'); // test proc
    $process = new Process($this->constructCommand());
    $process->start();
    while ($process->isRunning()) {
      sleep(3);
      $this->checkProgress();
    }

    //Check the script has exited successfully
    if ($process->isSuccessful()) {
        //Log::debug('SCRIPT SUCCESS');
        MockupLogger::ItemJob('debug',$this->itemjob,'Script Success');
    } else {
        //Log::debug('SCRIPT ERROR');
        MockupLogger::ItemJob('debug',$this->itemjob,'Script Error');
        $this->error = true;
        $this->errors[] = 'Error with execution of render script';
    }




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
    $cmd = $this->PathToBlender . ' --factory-startup -noaudio --debug  ';
    $cmd .= '-b ' . $this->getBlendFilePath();
    $cmd .= ' --python-exit-code 1';
    $cmd .= ' --python ' . $this->getBlendPythonPath();
    $cmd .= ' > ' . $this->getFullyQualifiedBlenderLogPath();
    MockupLogger::ItemJob('debug',$this->itemjob,'constructCommand: ' . $cmd);

    return $cmd;

  }




  //Construct the setup construct
  //
  //
  //
  //
  public function constructSetupCommand() {
    $cmd = $this->PathToBlender . ' --factory-startup -noaudio --debug  ';
    $cmd .= '-b ' . $this->getBlendFilePath();
    $cmd .= ' --python-exit-code 1';
    $cmd .= ' --python ' . $this->getBlendPythonSetupPath();
    $cmd .= ' > ' . $this->getFullyQualifiedBlenderLogPath();
    MockupLogger::ItemJob('debug',$this->itemjob,'constructSetupCommand: ' . $cmd);

    return $cmd;
  }





  /**
   * Check Progress
   *
   *
   *
   *
   * @return [type] [description]
   */
  public function checkProgress() {
    $logdata = file_get_contents($this->getFullyQualifiedBlenderLogPath());
    $prog = $this->getProgress($logdata);
    if ($prog > $this->jobProgress) {
      //update progress here
      $this->jobProgress = $prog;
      //Log::debug('Progress update: ' . $this->jobProgress);
      MockupLogger::ItemJob('debug',$this->itemjob,'Progress update: ' . $this->jobProgress);
      event(new ItemJobProgressUpdated($this->itemjob, $this->jobProgress));
    }

  }









  /**
	 * Get progress for Cycles engine
	 *
	 * return int [0-100]
	 *
	 *
	 */
	public function getProgress($logdata) {

    $progress = 0;

    preg_match_all("/[0-9]+\/[0-9]+/", $logdata, $output_array); //Array[0] -> of all xxxx/xxxx matches
    if (is_array($output_array) && count($output_array)>0 && count($output_array[0])>0) {
  		$CyclesRatio = $output_array[0][count($output_array[0])-1];  // xxxx/xxxx
  		$total = substr(strstr($CyclesRatio,"/"),1); // xxxx/total
  		$done = strstr($CyclesRatio,"/",true); // done/xxxx
  		$progress = round(($done / $total)*100);
    }
    if ($progress <= 100) {
		    return $progress;
    } else {
      return 0;
    }


	}







  /////////////////////////////////////////////////////////////////////////////////
  ///
  ///
  ///
  ///PATHS
  ///
  ///
  ///


  /**
   * Return the fully qualified path to the project blend file.
   *
   *
   *
   * @return [type] [description]
   */
  public function getBlendFilePath() {
    //$files = new WeMockupFiles;
    $filepath = storage_path('app/' . $this->files->localProductDirectory($this->itemjob->item->sku->product) . '/' . $this->project_file_name);
    return $filepath;
  }

  public function getBlendPythonPath() {
    //$files = new WeMockupFiles;
    $filepath = storage_path('app/' . $this->files->localProductDirectory($this->itemjob->item->sku->product) . '/' . $this->renderscript_file);
    return $filepath;
  }


  public function getBlendPythonSetupPath() {
    //$files = new WeMockupFiles;
    $filepath = storage_path('app/' . $this->files->localProductDirectory($this->itemjob->item->sku->product) . '/' . $this->setupscript_file);
    return $filepath;
  }



  /**
   * Return the fully qualified path for the project outpu log file.
   *
   *
   * @return [type] [description]
   */
  public function getFullyQualifiedBlenderLogPath() {
    $filepath = storage_path('app/' . $this->getBlenderLogPath());
    return $filepath;
  }

  /**
   * Return the local path for the project outpu log file.
   *
   *
   * @return [type] [description]
   */
  public function getBlenderLogPath() {
    //$files = new WeMockupFiles;
    $filepath = $this->files->localProductDirectory($this->itemjob->item->sku->product) . '/' . $this->project_log_file;
    return $filepath;
  }




  public function outputFileName() {
      //$files = new WeMockupFiles;
      $outputnumber = sprintf( '%04d', $this->itemjob->frame ); // padded with zeros.
      return $this->files->outputFilePrefix . $outputnumber . '.' . strtolower($this->itemjob->item->sku->frameconfig->outputformat);
  }


  //fully qualified output filepath
  public function workingOutputFilePathFullyQualified() {
      $filepath = storage_path('app/' . $this->workingOutputFilePath());
      return $filepath;
  }

  //output filepath
  public function workingOutputFilePath() {
      //$files = new WeMockupFiles;
      $filepath = $this->files->outputWorkingDirectory($this->itemjob->item) . '/' . $this->outputFileName();
      return $filepath;
  }


  public function outputFilePathFullyQualified() {
      $filepath = storage_path('app/' . $this->outputFilePath());
      return $filepath;
  }

  public function outputFilePath() {
    //$files = new WeMockupFiles;
    $filepath = $this->files->outputDirectory($this->itemjob->item) . '/' . $this->outputFileName();
    return $filepath;
  }









}
