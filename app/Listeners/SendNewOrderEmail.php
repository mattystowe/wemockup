<?php

namespace App\Listeners;

use App\Events\OrderCreated;
//use App\Jobs\Emails\NewOrder;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;

class SendNewOrderEmail
{

    protected $mailer;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
      $this->mailer = $mailer;
    }

    /**
     * Handle the event. Queue the email on the "emails" queue
     *
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {

        $order = $event->order;
        switch ($order->origin) {
          case 'doohpress':
            //
            //do not send out emails for doohpress origin
            //
            break;
            //
            //
            //Add nore origins here for other systems

          default:
            $job = (new \App\Jobs\Emails\NewOrder($order))->onQueue(env('QUEUE_EMAILS'));
            dispatch($job);
            break;
        }



    }
}
