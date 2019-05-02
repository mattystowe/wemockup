<?php

namespace App\Listeners;

use App\Events\ItemCancelled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendItemCancelledEmail
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
     * @param  ItemCancelled  $event
     * @return void
     */
    public function handle(ItemCancelled $event)
    {
      $item = $event->item;
      switch ($item->order->origin) {
        case 'doohpress':
          //
          //do not send email for doohpress orders
          //
          break;
          //
          //add other origins here
          //

        default:
          $job = (new \App\Jobs\Emails\ItemCancelled($item))->onQueue(env('QUEUE_EMAILS'));
          dispatch($job);
          break;
      }

    }
}
