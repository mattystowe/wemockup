<?php

namespace App\Jobs;

use Exception;
use Log;
use App\MockupLogger;
use App\Jobs\Job;
use App\ItemJob;
use Carbon\Carbon;
use App\WeMockupFiles;
use App\Blender;
use App\PostProcessingHelper;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Host;
use Artisan;

class BlenderFrame extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $itemjob;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ItemJob $itemjob)
    {
        $this->itemjob = $itemjob;
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

                  //4.Execute Blender - Wait and Monitor progress
                  $blender = new Blender($this->itemjob);
                  if ($blender->process()) {


                    //5.Report response - mark itemjob  as COMPLETE
                    $this->itemjob->markAsComplete();
                    //
                    //6.Check if all itemjobs for this item are COMPLETE
                    //
                    /*$outstandingItems = ItemJob::where('status','!=','COMPLETE')->where('item_id','=',$this->itemjob->item_id)->count();
                    if ($outstandingItems==0) {
                      MockupLogger::Item('debug',$this->itemjob->item,'ITEMS_ALL_COMPLETE');

                      if ($this->itemjob->item->itempostprocs->count()>0) {
                        MockupLogger::Item('debug',$this->itemjob->item,'STARTING_POSTPROCS');

                        //Get latest fresh item to make sure status is still fine to proceed to postprocs
                        //
                        $this->itemjob->fresh(['item']);
                        if ($this->itemjob->item->status != 'FAILED' && $this->itemjob->item->status != 'CANCELLED') {

                            //Mark item as FINISHING
                            $this->itemjob->item->markAsFinishing();

                            //Queue the first post proc job
                            PostProcessingHelper::startPostProcessing($this->itemjob->item);

                        }



                      } else {
                        //if no post processing steps mark the item as complete -
                        $this->itemjob->item->markAsComplete();
                      }
                    }*/


                  } else {
                    //
                    //
                    //Error handle execution problem
                    //
                    MockupLogger::ItemJob('debug',$this->itemjob,'ITEMJOB Blender Processing Failed.');
                    $host->setInstanceScaleProtection(false);
                    throw new Exception('Blender processing failed');

                  }


          } else {
            //Item is not in the right status for this item to be processed
            MockupLogger::ItemJob('debug',$this->itemjob,'ITEMJOB::Skipping: Item is ' . $this->itemjob->item->status);


          }


          $host->setInstanceScaleProtection(false);

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
