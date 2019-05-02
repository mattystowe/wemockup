<?php

namespace App\Jobs\Emails;

use Log;
use App\Jobs\Job;
use App\Item;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ItemFailed extends Job implements ShouldQueue
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
     public function handle(Mailer $mailer)
     {
         $item = $this->item;
         $mailer->send('emails.itemfailed', ['item' => $item], function ($message) use ($item) {
           $message->subject('One of the jobs on your order item has failed.');
           $message->to($item->order->email, $item->order->firstname . ' ' . $item->order->lastname);
           $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
           $message->sender(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
           $message->replyTo(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
         });
     }
}
