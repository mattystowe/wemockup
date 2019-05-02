<?php
/**
 *
 * Handle the Progress Updates event for ItemJobs during their processing
 *
 * Saves the progress to the itemjob for output in the ui.
 *
 *
 *
 *
 *
 *
 *
 *
 */
namespace App\Listeners;

use App\Events\ItemJobProgressUpdated;
use App\ItemJob;
use App\Item;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateItemJobProgress
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
     * @param  ItemJobProgressUpdated  $event
     * @return void
     */
    public function handle(ItemJobProgressUpdated $event)
    {
        //1.Persist the progress to the item.
        $itemjob = $event->itemjob;
        $itemjob->progress = $event->progress;
        $itemjob->save();


    }
}
