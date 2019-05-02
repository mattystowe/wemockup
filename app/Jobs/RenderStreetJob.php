<?php
//
//1.Generate a package and upload to ftp of renderstreet farm.
//2.Launch a job on farm for deferred processing.
//
//
//
//
//
namespace App\Jobs;

use Exception;
use Log;
use App\MockupLogger;
use App\Jobs\Job;
use App\ItemJob;
use Carbon\Carbon;
use App\WeMockupFiles;
use App\Blender;
use File;
use App\RenderStreet;
use App\RenderStreetToken;
use Storage;
use App\PostProcessingHelper;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Host;
use Artisan;
use App\CyberDuck;

class RenderStreetJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $itemjob;
    public $start; // start frame
    public $end; // end frame

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ItemJob $itemjob, $start, $end)
    {
        $this->itemjob = $itemjob;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      Artisan::call('cache:clear');
      $host = new Host;
      $host->setInstanceScaleProtection(true);

        //Make sure the model is reloaded latest and Item is still in correct statuses (ie not cancelled or failed)
        //
        //
        $this->itemjob->fresh(['item']);
        if ($this->itemjob->item->status != 'FAILED' && $this->itemjob->item->status != 'CANCELLED') {


                  $this->itemjob->markAsProcessing();

                  //1.download the project files from blob if not already available locally.
                  $files = new WeMockUpFiles;
                  $files->getProductFiles($this->itemjob->item->sku->product);

                  //2.download any assets (for inputoption types: imageupload and videoupload)
                  $files->getItemInputFiles($this->itemjob->item);

                  //3.write the config file  for inputitems (variable_name=>value) sets.  Accessible by blender script.
                  $files->writeProductConfig($this->itemjob);

                  //4.blender setup/Persist
                  $this->setupAndSave();

                  //5.package up into bundle
                  $this->createBundle();

                  //6.upload to renderstreet ftp
                  if ($this->uploadBundleToRenderStreet()) {
                    //Start the renderstreet job on the farm
                    $this->startJob();
                  } else {
                    MockupLogger::ItemJob('error',$this->itemjob,'RENDERSTREETJOB::Upload to ftp failed');
                    $host->setInstanceScaleProtection(false);
                    throw new Exception('RenderStreet processing failed');
                  }



          } else {
            //Item is not in the right status for this item to be processed
            MockupLogger::ItemJob('debug',$this->itemjob,'ITEMJOB::Skipping: Item id ' . $this->itemjob->item->status);


          }


          $host->setInstanceScaleProtection(false);
    }





    public function createBundle() {
      $files = new WeMockUpFiles;
      $files->generateBundle($this->itemjob);
    }




    public function uploadBundleToRenderStreet() {

      $rs = new RenderStreet;
      $files = new WeMockupFiles;
      $source = storage_path('app/' . $files->bundleDirectory($this->itemjob)) . "/";
      $dest = "/" . $files->bundleDirectory($this->itemjob) . "/";

      //check that directory exists - otherwise create it before uploading
      $directoryExists = Storage::disk('renderstreet')->exists($files->bundleDirectory($this->itemjob));
      if (!$directoryExists) {
        //create directory
        MockupLogger::ItemJob('debug',$this->itemjob,'RenderStreet::Bundle Creating New Directory ' . $files->bundleDirectory($this->itemjob));
        Storage::disk('renderstreet')->makeDirectory($files->bundleDirectory($this->itemjob));
      }


      $duck = new CyberDuck;
      $duck->setHost($rs->ftp_endpoint, $rs->ftp_port);
      $duck->setLogin(env('RENDERSTREET_ACCOUNT_USER'),env('RENDERSTREET_ACCOUNT_PASS'));
      if ($duck->processUpload($source,$dest)) {
        MockupLogger::ItemJob('debug',$this->itemjob,'RenderStreet::Bundle Uploaded Successfully ');
        return true;
      } else {
        $host = new Host;
        $host->setInstanceScaleProtection(false);
        throw new Exception('RenderStreet upload failed');
        return false;
      }

    }






    public function setupAndSave() {
      $blender = new Blender($this->itemjob);
      if ($blender->setup()) {
        return true;
      } else {
        MockupLogger::ItemJob('error',$this->itemjob,'RENDERSTREETJOB::Blender Setup Failed');
        $host = new Host;
        $host->setInstanceScaleProtection(false);
        throw new Exception('RenderStreet Setup failed');
        return false;
      }
    }




    public function startJob() {
      $rs = new RenderStreet;
      $rs->setItemJob($this->itemjob);
      if ($rs->addJob($this->start,$this->end)) {
        MockupLogger::ItemJob('debug',$this->itemjob,'RENDERSTREETJOB::Job Submitted to farm successfully.');
        return true;
      } else {
        MockupLogger::ItemJob('error',$this->itemjob,'RENDERSTREETJOB::Job Submission to farm failed.');
        $host = new Host;
        $host->setInstanceScaleProtection(false);
        throw new Exception('RenderStreet StartJob failed');
        return false;
      }
    }




    /**
     * Handle failed jobs (if --tries=3 on the queue listener)
     *
     *
     *
     *
     * @return [type] [description]
     */
    public function failed()
    {
        MockupLogger::ItemJob('error',$this->itemjob,'ITEMJOB Failed:: Marking Item as FAILED');
        //Mark the itemjob as failed
        $this->itemjob->markAsFailed();
        $host = new Host;
        $host->setInstanceScaleProtection(false);
    }
}
