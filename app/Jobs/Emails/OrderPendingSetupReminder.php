<?php

namespace App\Jobs\Emails;

use App\Jobs\Job;
use App\Order;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderPendingSetupReminder extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;


    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        $order = $this->order;
        $mailer->send('emails.orderpendingsetup', ['order' => $order], function ($message) use ($order) {
          $message->subject('Order reminder - you have items still yet to setup.');
          $message->to($order->email, $order->firstname . ' ' . $order->lastname);
          $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
          $message->sender(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
          $message->replyTo(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }
}
