<?php

namespace App\Listeners;

use App\Events\Event;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Trello;

class UpdateTrelloCard
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
    public function handle(Event $event)
    {
        $item = $event->item;
        if (config('app.trello') == 'true') {
          $trelloClient = new Trello;

          if ($item->trellocard_id != '') {

            switch ($item->status) {
              case 'QUEUED':
                $trelloClient->moveCard($item,config('app.trello_list_queued'));
                break;
                case 'PROCESSING':
                  $trelloClient->moveCard($item,config('app.trello_list_processing'));
                  break;
                  case 'COMPLETE':
                    $trelloClient->moveCard($item,config('app.trello_list_completed'));
                    break;
                    case 'CANCELLED':
                      $trelloClient->moveCard($item,config('app.trello_list_cancelled'));
                      break;
                      case 'FAILED':
                        $trelloClient->moveCard($item,config('app.trello_list_failed'));
                        break;

            }


          }


        }
    }







}
