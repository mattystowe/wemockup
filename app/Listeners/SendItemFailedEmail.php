<?php

namespace App\Listeners;

use App\Events\ItemFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendItemFailedEmail
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
    public function handle(ItemFailed $event)
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
          $job = (new \App\Jobs\Emails\ItemFailed($item))->onQueue(env('QUEUE_EMAILS'));
          dispatch($job);
          break;
      }

    }
}
