<?php

namespace App\Http\Controllers;

use Log;
use App\MockupLogger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;

use App\Order;
use App\Item;
use Ramsey\Uuid\Uuid;
use App\Sku;
use App\Events\OrderCreated;

class ShopifyController extends Controller
{

    /**
     * api key that shopify uses to post into the application.
     *
     *
     *
     * @var string
     */
    public $apikey = "apikeygoeshere";







    /**
     * Process new orders posted by shopify webhooks for new orders.
     *
     *
     *
     *
     *
     *
     * @param  Request $request [description]
     * @param  [type]  $apikey  [description]
     * @return [type]           [description]
     */
    public function neworder(Request $request, $apikey) {
      if ($apikey == $this->apikey) {

        //get request
        $orderdetails = $request->all();
        $order_items = $orderdetails['line_items'];
        $order_customer = $orderdetails['customer'];


        //create order
        $uuid4 = Uuid::uuid4();
        $orderattributes = array(
          'origin' => "shopify",
          'shopify_order_id' => $orderdetails['id'],
          'email' => $orderdetails['email'],
          'amount' => $orderdetails['total_line_items_price'],
          'orderuid' => $uuid4->toString(),
          'firstname' => $order_customer['first_name'],
          'lastname' => $order_customer['last_name']
        );

        //$order = Order::createNew($orderattributes);
        $order = new Order;
        $order->origin = $orderattributes['origin'];
        $order->shopify_order_id = $orderattributes['shopify_order_id'];
        $order->email = $orderattributes['email'];
        $order->amount = $orderattributes['amount'];
        $order->orderuid = $orderattributes['orderuid'];
        $order->firstname = $orderattributes['firstname'];
        $order->lastname = $orderattributes['lastname'];

        $order->save();


        //dump($order_items);
        //process line items for the new order
        foreach ($order_items as $lineitem) {

            //line item may be multiple quantities - so a new item must be
            //created in the order for each
            $quantity = $lineitem['quantity'];
            for ($i=0; $i < $quantity; $i++) {

              $uuid4 = Uuid::uuid4();
              $item = new Item;
              $item->itemuid = $uuid4->toString();
              $item->skucode = $lineitem['sku'];
              $item->price = $lineitem['price'];
              //assign sku id to Item
              $sku = Sku::find($item->skucode);
              $item->sku_id = $sku->id;
              $item->status = 'PENDINGSETUP';
              $order->items()->save($item);
            }

        }

        //dispatch new order Event
        event(new OrderCreated($order));


        MockupLogger::Order('debug',$order,'ORDER_NEW');


        return $order;


      } else {
        //not authorised
        return new Response('Invalid Key', 403);
      }

    }
}
