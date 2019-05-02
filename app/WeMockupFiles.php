<?php
/**
 * Class interface to abstract the management of files in WeMockup.
 *
 *
 * productfiles/
 * 			productid/
 * 					inputfiles/{itemid}/
 *
 *
 * output/
 *     orders/{orderuid}/{item_uid}/working/{files}
 *     orders/{orderuid}/{item_uid}/output/{files}   (ultimate final output for item downloads) - served up as entire directory for downloading.
 *
 *
 *
 */
namespace App;

use Log;
use App\MockupLogger;
use App\Item;
use App\ItemJob;
use App\product;
use Storage;
use Aws\S3\S3Client;
use File;

class WeMockupFiles
{


    public $bucket = 'wemockupstorage';





    ////////////////////////////////////////
    //
    //Base directory for product files - both locally within storage and also S3
    //
    //
    public $productFilesDir = 'productfiles';


    ////////////////////////////////////////
    //
    //Base directory for output files - both locally within storage and also S3
    //
    //
    public $outputFilesDir = 'output';

    ////////////////////////////////////////
    //
    //Prefix for all output files (eg output_0001.png)
    //
    //
    public $outputFilePrefix = "output_";


    public $bundleDir = 'bundles';




    public function localProductDirectory(Product $product) {
      return $this->productFilesDir . '/' . $product->location;
    }

    public function localInputFilesDirectory(Item $item) {
      return $this->localProductDirectory($item->sku->product) . '/inputfiles/' . $item->id;
    }

    public function localConfigFilePath(Product $product) {
      return $this->localProductDirectory($product) . '/config.json';
    }

    public function getS3KeyPrefix(Product $product) {
      return $this->productFilesDir . '/' . $product->location;
    }

    public function getLocalInputItemPath(ItemInput $iteminput) {
      return $this->localInputFilesDirectory($iteminput->item) .'/' . $iteminput->filename;
    }





    public function outputDirectory(Item $item) {
      return $this->outputFilesDir . '/orders/' . $item->order->orderuid . '/' . $item->itemuid . '/output';
    }

    public function outputWorkingDirectory(Item $item) {
      return $this->outputFilesDir . '/orders/' . $item->order->orderuid . '/' . $item->itemuid . '/working';
    }




    public function bundleDirectory(ItemJob $itemjob) {
      return $this->bundleDir . '/' . env('APP_ENV') . '/bundle' . $itemjob->item->id;
    }






    //Generate a file bundle for the current itemjob
    //(eg this can be passed to external render farms if necessary)
    //
    //
    //
    //
    //
    public function generateBundle(ItemJob $itemjob) {
      MockupLogger::ItemJob('debug',$itemjob,'Generating bundle: ' . $this->bundleDirectory($itemjob));
      $sourceDir = storage_path('app/' . $this->localProductDirectory($itemjob->item->sku->product));
      $destinationDir = storage_path('app/' . $this->bundleDirectory($itemjob));
      //Log::debug($sourceDir);
      //Log::debug($destinationDir);
      $success = File::copyDirectory($sourceDir, $destinationDir);
      if ($success) {
        return true;
      } else {
        return false;
      }
    }


    /**
     * Push up to S3
     *
     *
     *
     *
     * @param  [type] $filename       [description]
     * @param  [type] $source         [description]
     * @param  [type] $destinationkey [description]
     * @return [type]                 [description]
     */
    public function pushToS3($filename, $source, $destinationkey) {
      //Log::debug('PushToOutput: Filename: ' . $filename);
      //Log::debug('PushToOutput: Source: ' . $source);
      //Log::debug('PushToOutput: Destinationkey: ' . $destinationkey);
      $config = config('filesystems.disks.s3');
      $s3Client = S3Client::factory(array(
                                          'version'=>'2006-03-01',
                                          'credentials' => array(
                                              'key'    => $config['key'],
                                              'secret' => $config['secret'],
                                          ),
                                          'region'=>'eu-west-1'
                                      ));
      $result = $s3Client->putObject(array(
                                    //'ACL'        => 'public-read',
                                    'ACL'        => 'private',
                                    'Bucket'     => $this->bucket,
                                    'Key'        => $destinationkey,
                                    'SourceFile' => $source,

      ));
    }




    //Push contents of a directory to destination directory on S3
    //
    //
    //
    //
    //
    //
    public function pushDirectoryToS3($sourceDir, $destDir) {
      $config = config('filesystems.disks.s3');
      $s3Client = S3Client::factory(array(
                                          'version'=>'2006-03-01',
                                          'credentials' => array(
                                              'key'    => $config['key'],
                                              'secret' => $config['secret'],
                                          ),
                                          'region'=>'eu-west-1'
                                      ));
      $dir = $sourceDir;
      $bucket = $this->bucket;
      $keyPrefix = $destDir;
      $options = array(
                                    'params'      => array('ACL' => 'private')
      );
      //Log::debug('dir: ' . $dir);
      //Log::debug('keyPrefix: ' . $keyPrefix);
      $result = $s3Client->uploadDirectory($dir, $bucket, $keyPrefix, $options);
    }







    /**
     * Download project files to local disk ready for work
     *
     *
     *
     * @param  ItemJob $itemjob [description]
     * @return [type]           [description]
     */
    public function getProductFiles(Product $product) {
      if ($this->localProductDirectoryExist($product)) {
        //Log::debug('WeMockupFiles_LOCAL_PRODUCT_ALREADY_AVAILABLE id:' . $product->id);
        return true;
      } else {
        //Log::debug('WeMockupFiles_S3_DOWNLOAD_PRODUCT id:' . $product->id);
        $result = $this->downloadProductFilesFromS3($product);
        return $result;
      }
    }



    /**
     * Go through any input options and download any files for usage.
     *
     *
     *
     *
     * @param  ItemJob $itemjob [description]
     * @return [type]           [description]
     */
    public function getItemInputFiles(Item $item) {

      //create the directory for inputfiles specific to item
      if (!is_dir(storage_path('app/' . $this->localInputFilesDirectory($item)))) {
        //create
        //Log::debug('create inputitems dir.');
        MockupLogger::Item('debug',$item,'ITEM Create InputItems Directory');

        Storage::disk('local')->makeDirectory($this->localInputFilesDirectory($item));
      }


      $config = config('filesystems.disks.s3');
      $s3Client = S3Client::factory(array(
                                          'version'=>'2006-03-01',
                                          'credentials' => array(
                                              'key'    => $config['key'],
                                              'secret' => $config['secret'],
                                          ),
                                          'region'=>'eu-west-1'
                                      ));

      $iteminputs = $item->iteminputs;
      foreach($iteminputs as $iteminput) {


        if ($iteminput->input_type == 'imageupload' || $iteminput->input_type == 'videoupload') { // only download if file

            if (!Storage::disk('local')->exists($this->getLocalInputItemPath($iteminput))) {

              //cater for different origin (API generated input)
              switch($item->order->origin) {
                case 'doohpress':
                    //
                    //doohpress api orders - stream downloads from url rather than S3
                    //
                    MockupLogger::Item('debug',$item,'WeMockupFiles_URL_STREAM_DOWNLOAD_ITEM: ' . $iteminput->value);
                    $dest = $this->getLocalInputItemPath($iteminput);
                    //$dest = storage_path('app/' . $this->getLocalInputItemPath($iteminput));
                    $url = $iteminput->value;
                    $stream = fopen($url, 'r');
                    Storage::disk('local')->put($dest, $stream);
                    fclose($stream);
                    break;

                    //
                    //
                    //more origins to go here in future (eg wemockupforbusinessapi)
                    //
                    //

                default:
                    //All normal orders
                    //
                    //
                    //Log::debug('WeMockupFiles_S3_DOWNLOAD_ITEM: ' . $iteminput->filekey);
                    MockupLogger::Item('debug',$item,'WeMockupFiles_S3_DOWNLOAD_ITEM: ' . $iteminput->filekey);
                    $result = $s3Client->getObject(array(
                      'Bucket' => $this->bucket,
                      'Key'    => $iteminput->filekey,
                      'SaveAs' => storage_path('app/' . $this->getLocalInputItemPath($iteminput))
                    ));
                    break;
              }





            } else {
              MockupLogger::Item('debug',$item,'WeMockupFiles_ItemAlreadyExists: ' . $this->getLocalInputItemPath($iteminput));
              //Log::debug('WeMockupFiles_ItemAlreadyExists: ' . $this->getLocalInputItemPath($iteminput));
            }
        }

      }


    }



    /**
     * Do the files already exist locally for this product?
     *
     *
     *
     * @param  [type] $productid [description]
     * @return [type]            [description]
     */
    public function localProductDirectoryExist(Product $product) {
      if (is_dir(storage_path('app/' . $this->localProductDirectory($product)))) {
        return true;
      } else {
        return false;
      }
    }



    /**
     * Download the project files from S3 to local
     *
     *
     *
     * @param  [type] $productid [description]
     * @return [type]            [description]
     */
    public function downloadProductFilesFromS3($product) {
        $config = config('filesystems.disks.s3');
        $s3Client = S3Client::factory(array(
                                            'version'=>'2006-03-01',
                                            'credentials' => array(
                                                'key'    => $config['key'],
                                                'secret' => $config['secret'],
                                            ),
                                            'region'=>'eu-west-1'
                                        ));

        //download to local

        if ($s3Client->downloadBucket(
                storage_path('app/' . $this->localProductDirectory($product)),
                $this->bucket,
                $this->getS3KeyPrefix($product)))
                {
                return true;
          } else {
                return false;
        }
    }






    /**
     * Download the item working files from S3 into local working directory ready for working.
     *
     *
     *
     *
     * @param  Item   $item [description]
     * @return [type]       [description]
     */
    public function downloadWorkingFilesFromS3(Item $item) {
      $config = config('filesystems.disks.s3');
      //Log::debug($this->outputWorkingDirectory($item));
      $s3Client = S3Client::factory(array(
                                          'version'=>'2006-03-01',
                                          'credentials' => array(
                                              'key'    => $config['key'],
                                              'secret' => $config['secret'],
                                          ),
                                          'region'=>'eu-west-1'
                                      ));

      //download to local

      if ($s3Client->downloadBucket(
              storage_path('app/' . $this->outputWorkingDirectory($item)),
              $this->bucket,
              $this->outputWorkingDirectory($item)))
              {
              return true;
        } else {
              return false;
      }
    }




    /**
     * Copy all remote working files into the S3 output folder.
     *
     * All done remotely
     *
     *
     *
     * @param  Item   $item [description]
     * @return [type]       [description]
     */
    public function copyWorkingS3ToOutputS3(Item $item) {
      //get a list of files,
      $files = Storage::disk('s3')->files($this->outputWorkingDirectory($item));
      foreach ($files as $file) {
        $filedestination = str_replace('working', 'output', $file);
        Storage::disk('s3')->copy($file, $filedestination);
      }

    }


    //Copy all local working files to S3 output folder
    //
    //
    //
    //
    public function copyWorkingLocalToOutputS3(Item $item) {
      $targetDir = $this->outputDirectory($item) . '/';
      $sourceDir = storage_path('app/' . $this->outputWorkingDirectory($item));
      $this->pushDirectoryToS3($sourceDir, $targetDir);
    }





    /**
     * Get a list of output links for an item
     *
     *
     *
     *
     *
     * @param  Item   $item [description]
     * @return [type]       [description]
     */
    public function getOutputLinks(Item $item) {
      $config = config('filesystems.disks.s3');
      $s3Client = S3Client::factory(array(
                                          'version'=>'2006-03-01',
                                          'credentials' => array(
                                              'key'    => $config['key'],
                                              'secret' => $config['secret'],
                                          ),
                                          'region'=>'eu-west-1'
                                      ));

      $outputFiles = array();
      $files = Storage::disk('s3')->files($this->outputDirectory($item));
      foreach ($files as $file) {

        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key'    => $file
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+60 minutes');
        $presignedUrl = (string) $request->getUri();


        $output = new \stdClass;
        $output->link = $presignedUrl;
        $output->filename = basename($file);
        $output->filetype = pathinfo($file, PATHINFO_EXTENSION);
        $outputFiles[] = $output;
      }
      return $outputFiles;
    }





    /**
     * Write the config file for the product.  (json file containing the input item variables etc)
     *
     *
     *
     *
     *
     * @param  Item   $item [description]
     * @return [type]       [description]
     */
    public function writeProductConfig(ItemJob $itemjob) {
      $config_file_path = $this->localProductDirectory($itemjob->item->sku->product) . '/config.json';
      //Log::debug('Writing config file: ' . $config_file_path);
      MockupLogger::ItemJob('debug',$itemjob,'ITEMJOB Write config file: ' . $config_file_path);



      $config = array();

      //Add input options//////////////////////////////////////
      foreach($itemjob->item->iteminputs as $iteminput) {
        $configitem = array();
        if ($iteminput->input_type == 'imageupload' || $iteminput->input_type=='videoupload') { // set the filepath in the config to relative to the file
          $filepath = 'inputfiles/' . $itemjob->item->id . '/' . $iteminput->filename;
          $config['iteminputs'][$iteminput->variable_name] = $filepath;
        } else {
          $config['iteminputs'][$iteminput->variable_name] = $iteminput->value;
        }
      }

      //add frame configuration/////////////////////////////////
      $config['frameconfig'] = $itemjob->item->sku->frameconfig;

      //output
      $config['outputdir'] = storage_path('app/' .$this->outputWorkingDirectory($itemjob->item));

      //Add itemjob
      $config['itemjob'] = $itemjob;

      $json = json_encode($config);
      Storage::disk('local')->put($config_file_path, $json);

    }



}
