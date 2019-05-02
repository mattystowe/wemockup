<?php
/**
 * Trello interface
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
namespace App;


use Log;
use App\MockupLogger;
use App\Item;
use DB;
use Carbon\Carbon;
use Trello\Client;

class Trello
{

  public $client;


  public function __construct()
  {
    $this->client = new Client();
    $this->client->authenticate(config('app.trello_key'), config('app.trello_token'), Client::AUTH_URL_CLIENT_ID);

  }


  public function createCard(Item $item) {
    $name = '[' . $item->order->email . '] ' . $item->sku->name . ' ' . $item->sku->product->name;

    $orderlink = config('app.url') . '/administrator#/order/' . $item->order->orderuid;
    $itemlink = config('app.url') . '/administrator#/item/' . $item->itemuid;
    $description = 'Order #' . $item->order->id . ', [' . $orderlink . '](' . $orderlink . ')';
    $description .= '
    Item #' . $item->id . ', [' . $itemlink . '](' . $itemlink . ')';

    $params = array(
      'idList'=>config('app.trello_list_pendingsetup'),
      'pos'=>'top',
      'name'=>$name,
      'desc'=>$description
    );
    $result = $this->client->cards()->create($params);
    return $result;
  }



  public function moveCard(Item $item,$listid) {
      $params = array(
        'idList'=>$listid
      );
      if ($item->trellocard_id != '') {
        $result = $this->client->cards()->update($item->trellocard_id,$params);
      }
      return $result;
  }





}
