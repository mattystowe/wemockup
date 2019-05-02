<?php
//Process itemjob completion.
//Checks for further itemjobs on the item.  And kicks off post processes if any.
//Otherwise marks the item as complete
//
//
//
//
//
namespace App\Listeners;

use App\Events\ItemJobCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\MockupLogger;
use App\PostProcessingHelper;
use App\ItemJob;

class ProcessCompletedItemJob
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ItemJobCompleted  $event
     * @return void
     */
    public function handle(ItemJobCompleted $event)
    {
      //
      //Check if all itemjobs for this item are COMPLETE
      //
      $outstandingItems = ItemJob::where('status','!=','COMPLETE')->where('item_id','=',$event->itemjob->item_id)->count();
      if ($outstandingItems==0) {
        MockupLogger::Item('debug',$event->itemjob->item,'ITEMS_ALL_COMPLETE');

        if ($event->itemjob->item->itempostprocs->count()>0) {
          MockupLogger::Item('debug',$event->itemjob->item,'STARTING_POSTPROCS');

          //Get latest fresh item to make sure status is still fine to proceed to postprocs
          //
          $event->itemjob->fresh(['item']);
          if ($event->itemjob->item->status != 'FAILED' && $event->itemjob->item->status != 'CANCELLED') {

              //Mark item as FINISHING
              $event->itemjob->item->markAsFinishing();

              //Queue the first post proc job
              PostProcessingHelper::startPostProcessing($event->itemjob->item);

          }



        } else {
          //if no post processing steps mark the item as complete -
          $event->itemjob->item->markAsComplete();
        }
      }
    }



}
