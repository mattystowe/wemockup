<?php

namespace App\Listeners;

use App\Events\ItemCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendItemCompleteEmail
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
     * @param  ItemCompleted  $event
     * @return void
     */
    public function handle(ItemCompleted $event)
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
          $job = (new \App\Jobs\Emails\ItemComplete($item))->onQueue(env('QUEUE_EMAILS'));
          dispatch($job);
          break;
      }

    }
}
