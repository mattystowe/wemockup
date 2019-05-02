<?php
/**
 *
 *	Take an item that has been submitted and setup/queue any itemjobs
 *
 *
 *
 *
 *
 *
 *
 */
namespace App\Jobs;

use Log;
use App\MockupLogger;
use App\Jobs\Job;
use App\Item;
use App\ItemJob;
use App\Itempostproc;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Host;
use Artisan;

class ProcessItem extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $item;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
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

      if ($this->item->status != 'CANCELLED') {

        //Get the job type and jobname
        //
        $jobname = $this->item->sku->product->type->jobname;

        switch($jobname) {
          //
          //
          //
          //
          case 'BlenderSingleFrame':
            $this->processBlenderSingleFrame();
          break;

          //
          //
          //
          //
          //
          case 'BlenderMultipleFrame':
            $this->processBlenderMultipleFrame();
          break;


          //
          //
          //
          //
          //
          case 'RenderStSingleFrame':
            $this->processRenderStreetSingleFrame();
          break;

          //
          //
          //
          //
          //
          case 'RenderStMultipleFrame':
            $this->processRenderStreetMultipleFrame();
          break;

        }

        //Setup the prost processing stages ready for when all itemjobs finished.
        //
        $this->addPostProcessingStages();



        MockupLogger::Item('debug',$this->item,'ITEM_PROCESSED');

      } else {
        MockupLogger::Item('debug',$this->item,'ITEM_NOT_PROCESSED - Status is CANCELLED');
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
        MockupLogger::Item('error',$this->item,'ITEM Failed');
        //Mark its parent item as failed because of this single job
        //
        $this->item->markAsFailed();
        $host = new Host;
        $host->setInstanceScaleProtection(false);
    }





    /**
     * Persist a list of post processing stages required for processing after all jobs have finished on the item
     *
     *
     *
     *
     *
     */
    public function addPostProcessingStages() {
      $priority = 1;
      foreach($this->item->sku->postprocs as $postproc) {
        $itempost = new Itempostproc;
        $itempost->item_id = $this->item->id;
        $itempost->postproc_id = $postproc->id;
        $itempost->status = 'PENDING';
        $itempost->priority = $priority;
        $itempost->save();
        $priority++;
      }

    }


    /**
     * Process the setting up of jobs for single frame of type: BlenderSingleFrame
     *
     *
     *
     *
     * @return [type] [description]
     */
    public function processBlenderSingleFrame() {

        //Create new itemjob
        $itemjob = new ItemJob([
          'item_id'=>$this->item->id,
          'status'=>'QUEUED',
          'frame'=>$this->item->sku->product->frame_start,
          'date_queued'=>Carbon::now(),
        ]);
        $itemjob->save();

        //queue for worker nodes
        $job = (new \App\Jobs\BlenderFrame($itemjob))->onQueue(env('QUEUE_ITEMJOBS'));
        dispatch($job);
    }







    public function processBlenderMultipleFrame() {

      $frame_start = $this->item->sku->product->frame_start;
      $frame_end = $this->item->sku->product->frame_end;

      $frame = $frame_start;
      while ($frame <= $frame_end) {
        //Create new itemjob
        $itemjob = new ItemJob([
          'item_id'=>$this->item->id,
          'status'=>'QUEUED',
          'frame'=>$frame,
          'date_queued'=>Carbon::now(),
        ]);
        $itemjob->save();

        //queue for worker nodes
        $job = (new \App\Jobs\BlenderFrame($itemjob))->onQueue(env('QUEUE_ITEMJOBS'));
        dispatch($job);

        $frame ++;
      }


    }


    public function processRenderStreetSingleFrame() {
      //Create new itemjob
      $itemjob = new ItemJob([
        'item_id'=>$this->item->id,
        'status'=>'QUEUED',
        'frame'=>$this->item->sku->product->frame_start,
        'date_queued'=>Carbon::now(),
      ]);
      $itemjob->save();

      //queue for worker nodes
      $job = (new \App\Jobs\RenderStreetJob($itemjob,$this->item->sku->product->frame_start,$this->item->sku->product->frame_end))->onQueue(env('QUEUE_ITEMJOBS'));
      dispatch($job);
    }



    public function processRenderStreetMultipleFrame() {
      //Create new itemjob
      $itemjob = new ItemJob([
        'item_id'=>$this->item->id,
        'status'=>'QUEUED',
        'frame'=>$this->item->sku->product->frame_start,
        'date_queued'=>Carbon::now(),
      ]);
      $itemjob->save();

      //queue for worker nodes
      $job = (new \App\Jobs\RenderStreetJob($itemjob,$this->item->sku->product->frame_start,$this->item->sku->product->frame_end))->onQueue(env('QUEUE_ITEMJOBS'));
      dispatch($job);
    }


}
