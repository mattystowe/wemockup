<?php

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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use App\RenderStreetToken;
use App\Events\ItemJobProgressUpdated;
use App\CyberDuck;


class RenderStreet
{

  public $api_endpoint = 'https://api.render.st/v1/';
  public $ftp_endpoint = 'ftp.render.st';
  public $ftp_port = '51225';

  public $itemjob;

  public $token;

  private $connect_timeout = 30;
  private $request_timeout = 30;

  private $blenderVersion = '2780';



  public function __construct()
  {

  }



  //Return the path of the remote job output for an Item
  //
  //
  //
  public function getRemoteOutputDir(Item $item) {
    //1.get the itemjob id and external id from the item.
    $itemjobs = $item->itemjobs;
    $itemjob = $itemjobs[0];

    //2.construct the path.
    $path = '/output/' . $itemjob->external_id;
    return $path;
  }


  //Download the remote output files to local working directory
  //
  //
  //
  //
  public function downloadWorkingFiles(Item $item) {
    $files = new WeMockupFiles;
    $source = $this->getRemoteOutputDir($item) . "/";
    $dest = storage_path('app/' . $files->outputWorkingDirectory($item)) . '/';
    Log::debug('source: ' . $source);
    Log::debug('dest: ' . $dest);
    $duck = new CyberDuck;
    $duck->setHost($this->ftp_endpoint, $this->ftp_port);
    $duck->setLogin(env('RENDERSTREET_ACCOUNT_USER'),env('RENDERSTREET_ACCOUNT_PASS'));
    if ($duck->processDownload($source,$dest)) {
      MockupLogger::Item('debug',$item,'RenderStreet::Bundle Downloaded Successfully ');
      return true;
    } else {
      throw new Exception('RenderStreet bundle download failed');
      return false;
    }


  }



  public function setItemJob(ItemJob $itemjob) {
    $this->itemjob = $itemjob;
  }


  //Get an array of results for a list of render.st jobs
  //
  //
  //
  //
  //
  public function getResults($jobs) {
    //1.Authenticate with render.st
    if ($this->authenticate()) {
      return $this->getJobResults($jobs);
    } else {
      Log::error('console: renderstreet:results :: Error authenticating with render.st');
      return false;
    }
  }






  //Get a list of results from renderstreet api statuses
  //
  //
  //input array of renderstreet job ids
  //
  //
  //
  public function getJobResults($jobids) {
    $httpClient = $this->getHttpClient();
    $url = $this->api_endpoint . 'get-jobs.php';
    $params = [
      'token'=>$this->token,
      'ids'=>implode(',',$jobids)
    ];

    try {
      $response = $httpClient->request('POST', $url,[
                                                    'form_params' => $params
                                                    ]
      );


      if ($response->getStatusCode() == '200') {

        //Job created - save the render street jobid into the itemjob
        $body = json_decode($response->getBody());
        if ($body->status == "success") {

          return $body->data;

        } else {
          Log::error('renderstreet::getjobresults render.st error: ' . $body->message);
          return false;
        }

      } else {
        Log::error('renderstreet::getjobresults render.st error: ' . $body->message);
        return false;
      }



    } catch (RequestException $e) {
        if ($e->hasResponse()) {
          Log::error('renderstreet::getjobresults render.st error: ' . $body->message);
          return false;
        } else {
          Log::error('renderstreet::getjobresults render.st error: ');
          return false;
        }
    }
  }



  //Process the itemjobs from the results
  //
  //
  //
  public function processResults($jobresults) {
    foreach ($jobresults as $job) {
      //1.load the itemjob
      $itemjobresult = ItemJob::where('external_id','=',$job->Id)->get();
      $itemjob = ItemJob::find($itemjobresult[0]->id);
      if ($itemjob) {
        if ($itemjob->status != 'COMPLETE' && $itemjob->status != 'FAILED')
            //2.handle status
            switch ($job->Status) {
              case 'Finished':
                //
                //ItemJob Finished
                $itemjob->markAsComplete();
                break;
                case 'Error':
                  //
                  //ItemJob error
                  $this->markItemJobAsFailed($itemjob,$job->message);
                  break;

                default:
                  //update progress
                  $this->updateItemJobProgress($itemjob,$job->PercentDone);
                  break;

            }

      }
    }
  }


  public function updateItemJobProgress(ItemJob $itemjob, $progress) {
    MockupLogger::ItemJob('debug',$itemjob,'Progress update: ' . $progress);
    event(new ItemJobProgressUpdated($itemjob, $progress));
  }

  public function markItemJobAsFailed(ItemJob $itemjob, $message) {
    MockupLogger::ItemJob('debug',$itemjob,'Renderstreet::Failed: ' . $message);
    $itemjob->markAsFailed();
  }





  //Add a job to render.st queue for processing
  //
  //
  //
  //
  //
  public function addJob($start, $end) {
      //
      //TODO add other options like ondemand, callbacks etc

      //1.Authenticate with render.st
      if ($this->authenticate()) {
        //
        //
        $files = new WeMockupFiles;
        $params = [
          'token'=>$this->token,
          'dir'=>'/' . $files->bundleDir,
          'file'=>$this->getFile($files->bundleDirectory($this->itemjob)),
          'engine'=>'CYCLES',
          'version'=>$this->blenderVersion,
          'output'=>$this->itemjob->item->sku->frameconfig->outputformat,
          'jobname'=>'Item' . $this->itemjob->item->id,
          'start'=>$start,
          'end'=>$end,
          //'percentage'=>100,
          //'ondemand'=>1,
          'callback-url'=>$this->callBackUrl()
        ];
        MockupLogger::ItemJob('debug',$this->itemjob,'JobParams: ' . json_encode($params));
        if ($this->createNewJob($params)) {
          return true;
        } else {
          MockupLogger::ItemJob('error',$this->itemjob,'Could not create new render.st job');
          return false;
        }
      } else {
        MockupLogger::ItemJob('error',$this->itemjob,'Error authenticating with render.st');
        return false;
      }

  }


  //Returns the url address at which render.st should request upon a job result
  //
  //
  //
  public function callBackUrl() {

    $url = config('app.url') . '/renderstreet/result/' . $this->itemjob->id;
    if (config('app.env') != 'local') {
      return $url;
    } else {
      return '';
    }
  }



  //Get the file path to send to renderstreet api.
  //
  //
  public function getFile($bundleDirectory) {
    $dirParts = explode('/',$bundleDirectory);
    unset($dirParts[0]);
    $filePath = '/' . implode('/',$dirParts) . '/index.blend';
    return $filePath;
  }










  //Create a new job and start it on render.st
  //
  //
  //
  //
  //
  //
  public function createNewJob($params) {
    $httpClient = $this->getHttpClient();
    $url = $this->api_endpoint . 'add-job.php';
    try {
      $response = $httpClient->request('POST', $url,[
                                                    'form_params' => $params
                                                    ]
      );


      if ($response->getStatusCode() == '200') {

        //Job created - save the render street jobid into the itemjob
        $body = json_decode($response->getBody());
        if ($body->status == "success") {

          $this->itemjob->external_id = $body->data->JobId;
          if ($this->itemjob->save()) {
            return true;
          } else {
            MockupLogger::ItemJob('error',$this->itemjob,'render.st error: Could not persist the external_id into itemjob table.');
            return false;
          }

        } else {
          MockupLogger::ItemJob('error',$this->itemjob,'render.st error: ' . $body->message);
          return false;
        }

      } else {
        MockupLogger::ItemJob('error',$this->itemjob,'render.st error: ' . $body->message);
        return false;
      }



    } catch (RequestException $e) {
        if ($e->hasResponse()) {
          MockupLogger::ItemJob('error',$this->itemjob,'render.st error: ' . $body->message);
          return false;
        } else {
          MockupLogger::ItemJob('error',$this->itemjob,$e);
          return false;
        }
    }

  }








  public function authenticate() {

    //1.check that there is not already a valid token
    $token = new RenderStreetToken;
    $valid_token = $token->getValid();
    if ($valid_token) {
      $this->token = $valid_token;
      return true;
    } else {

      //2.otherwise authenticate for a new access token
      $httpClient = $this->getHttpClient();
      $url = $this->api_endpoint . 'login.php';

      try {
        $response = $httpClient->request('POST', $url,[
                                                      'form_params' => [
                                                                      'email' => env('RENDERSTREET_ACCOUNT_USER'),
                                                                      'pass' => env('RENDERSTREET_ACCOUNT_PASS')
                                                                        ]
                                                      ]
        );


        if ($response->getStatusCode() == '200') {

          //persist the token
          $body = json_decode($response->getBody());
          if ($body->status == "success") {
            $token->token = $body->data->token;
            $token->save();
            $this->token = $token->token;
            return true;
          } else {
            MockupLogger::ItemJob('error',$this->itemjob,'render.st error: Authentication' . $e->getResponse());
            return false;
          }

        } else {
          MockupLogger::ItemJob('error',$this->itemjob,'render.st error: Authentication' . $e->getResponse());
          return false;
        }



      } catch (RequestException $e) {
          if ($e->hasResponse()) {
            MockupLogger::ItemJob('error',$this->itemjob,'render.st error: Authentication' . $e->getResponse());
            return false;
          } else {
            return false;
          }
      }


    }


  }




    /**
     * Returns the default guzzle client with the default settings configured
     *
     *
     *
     * @return [type] [description]
     */
    private function getHttpClient() {
        $client = new \GuzzleHttp\Client([
          'timeout'=>$this->request_timeout,
          'connect_timeout'=>$this->connect_timeout
        ]);
        return $client;
    }

}
