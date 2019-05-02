<?php
/**
 * When progress updates are triggered for ItemJobs,
 * we need to calculate the overall progress of the Item.
 *
 *
 *
 *
 *
 *
 *
 */
namespace App\Listeners;

use Log;
use App\Events\ItemJobProgressUpdated;
use App\ItemJob;
use App\Item;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateItemProgress
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
        $itemjob = $event->itemjob;
        $itemjob->item->calculateAndSaveProgress();

    }
}
