<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Trello;

class CreateItemTrelloCards
{

    public $order;
    public $trelloClient;

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
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        $this->order = $event->order;
        if (config('app.trello') == 'true') {
          $this->trelloClient = new Trello;
          $this->generateItemCards();
        }

    }



    public function generateItemCards() {
      foreach($this->order->items as $item) {
        $card = $this->trelloClient->createCard($item);
        if ($card) {
          $item->trellocard_id = $card['id'];
          $item->save();
        }
      }
    }



}
