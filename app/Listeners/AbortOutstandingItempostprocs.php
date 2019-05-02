<?php
/**
 * In response to an Item being marked as FAILED this handler processed the following -
 *
 * 1.Marks all Itempostprocs that are NOT IN(COMPLETE,FAILED,PROCESSING) as ABORTED
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
use App\Events\Event;
use App\Events\ItemFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class AbortOutstandingItempostprocs
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
     * @param  ItemFailed  $event
     * @return void
     */
    public function handle(Event $event)
    {
      $event->item->itempostprocs()->whereNotIn('status',['COMPLETE','FAILED'])->update(['status'=>'ABORTED','date_aborted'=>Carbon::now()]);
    }
}
