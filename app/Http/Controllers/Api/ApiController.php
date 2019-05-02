<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Log;
use App\MockupLogger;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Ramsey\Uuid\Uuid;
use DB;
use App\Sku;
use App\Product;
use App\Item;
use App\ItemInput;
use App\Order;
use App\Webhook;
use App\Progresswebhook;
use App\Eta;
use App\WeMockupFiles;

use App\Events\OrderCreated;


class ApiController extends Controller
{
    public function test(Request $request) {
      return 'Hello ' . $request->Application->name;
    }

    public function getProduct($productid, Request $request) {
      $product = Product::find($productid);
      if ($product) {
        $product->skus;
        $product->inputoptions;
        return $product;

      } else {
        return response('Product not found',404);
      }
    }

    //Get item details (for progress etc)
    //
    //
    //
    //
    public function getItem($itemid, Request $request) {
      $item = Item::find($itemid);
      if ($item) {
        //include the file outputs for downloads
        if ($item->status == 'COMPLETE') {
          $Files = new WeMockupFiles;
          $files = $Files->getOutputLinks($item);
          $item->outputfiles = $files;
        }


        $eta = new Eta($item);
        //append the returned object
        $item->eta_information = $eta;


        return $item;
      } else {
        return response('Item not found.', 404);
      }
    }


    //Get items details - plural multiple items at once
    //
    //
    //
    public function getItems(Request $request) {
      $item = Item::whereIn('id',$request->items)->get();
      if ($item) {
        return $item;
      } else {
        return response('Item not found.', 404);
      }
    }



    //Accept an incoming item for rendering from api
    //
    //
    //
    //
    public function newOrder(Request $request) {



          //create order with origin from the authenticated application
          $uuid4 = Uuid::uuid4();
          $orderattributes = array(
            'origin' => $request->Application->name,
            'shopify_order_id' => $request->Application->name . $uuid4->toString(),
            'email' => $request->Application->email,
            'firstname' => $request->Application->name,
            'lastname' => $request->Application->name,
            'amount' => 0.00,
            'orderuid' => $uuid4->toString()
          );

          $order = new Order;
          $order->origin = $orderattributes['origin'];
          $order->shopify_order_id = $orderattributes['shopify_order_id'];
          $order->email = $orderattributes['email'];
          $order->amount = $orderattributes['amount'];
          $order->orderuid = $orderattributes['orderuid'];
          $order->firstname = $orderattributes['firstname'];
          $order->lastname = $orderattributes['lastname'];

          $order->save();


          $uuid4 = Uuid::uuid4();
          $item = new Item;
          $item->itemuid = $uuid4->toString();
          $item->skucode = $request->skuid;
          $item->price = 0.00;
          //assign sku id to Item
          $item->sku_id = $request->skuid;
          $item->status = 'PENDINGSETUP';
          $order->items()->save($item);

          //dispatch new order Event
          event(new OrderCreated($order));
          MockupLogger::Order('debug',$order,'API_ORDER_NEW');

          //add item input values
          if ($this->processItemInputs($item, $request->inputoptions)) {

              //send to processing queue
              $job = (new \App\Jobs\ProcessItem($item))->onQueue(env('QUEUE_ITEMPROCESSING'));
              dispatch($job);

              //mark the item as QUEUED and set timestamp for queued
              $item->markAsQueued();

              //check if any webhooks have been specified and save them
              if (isset($request->webhookurl) && $request->webhookurl != '') {
                $webhook = new Webhook;
                $webhook->webhookurl = $request->webhookurl;
                $item->webhooks()->save($webhook);
              }

              if (isset($request->progresswebhookurl) && $request->progresswebhookurl != '') {
                $progresswebhook = new Progresswebhook;
                $progresswebhook->webhookurl = $request->progresswebhookurl;
                $item->progresswebhooks()->save($progresswebhook);
              }

              //return the item as result. (because this is what the api consumer needs)
              return $item;

          } else {
            Log::debug('Could not process item inputs');
            return response('Could not process item inputs.', 501);
          }
    }


    //Process item inputs for the new item
    //
    //
    //
    //
    private function processItemInputs($item, $inputoptions) {
      if ($this->isItemValidForSubmit($item)) {

        if ($this->inputOptionsValid($inputoptions)) {

          MockupLogger::Item('debug',$item,'ITEM_SUBMITTED',['product_inputoptions'=>$inputoptions]);


          //Save ItemInputs and add Item to queue for processing.
          foreach ($inputoptions as $inputoption) {
            $iteminput = new ItemInput([
              'item_id'=>$item->id,
              'input_type'=>$inputoption['input_type'],
              'variable_name'=>$inputoption['variable_name'],
              'value'=>$inputoption['value'],
              'filename'=>$inputoption['filename'],
              'filekey'=>$inputoption['value'],
              'filestackurl'=>$inputoption['value']
            ]);
            $iteminput->save();
          }



          return true;


        } else {
          //item inputs not valid
          Log::debug('item inputs not valid');
          return false;
        }


      } else {
        //item not in valid state to process
        Log::debug('not in correct status');
        return false;
      }
    }

    /**
     * Is the item valid for submitting?
     *
     * Has to be PENDINGSETUP or FAILED (not already submitted)
     *
     *
     *
     * @param  Item    $item [description]
     * @return boolean       [description]
     */
    private function isItemValidForSubmit(Item $item) {
      if ($item->status == 'PENDINGSETUP' or $item->status == 'FAILED' or $item->status == 'CANCELLED') {
        return true;
      } else {
        return false;
      }
    }


    /**
     * Checks all input options provided to make sure all have a valid value.
     *
     *
     *
     *
     *
     * @param  [type] $inputoptions [description]
     * @return [type]               [description]
     */
    private function inputOptionsValid($inputoptions) {
      $error = false;
      foreach ($inputoptions as $inputoption) {
        if (isset($inputoption['variable_name']) && isset($inputoption['value'])) {
            //
            //handle the file upload items and setting to null if not.
            if (!isset($inputoption['filename'])) { $inputoption['filename'] = '';}
            if (!isset($inputoption['filestackurl'])) { $inputoption['filestackurl'] = '';}
            if (!isset($inputoption['filekey'])) { $inputoption['filekey'] = '';}

        } else {
          //Not all input values have been filled out.
          $error = true;
        }

      }

      if ($error) {
        return false;
      } else {
        return true;
      }
    }




    //Get item input options for a sku
    //
    //
    //
    public function getSku($skuid, Request $request) {
      $sku = Sku::find($skuid);
      if ($sku) {
        $sku->product->inputoptions;
        //json_decode input option data
        foreach ($sku->product->inputoptions as $option) {
          if ($option->data != '') {
            $option->data = json_decode($option->data);
          }
        }
        return $sku;
      } else {
        return response('SKU not found.', 404);
      }
    }





    //Search products with query and return results with skus
    //
    //
    //
    public function searchProducts($query, Request $request) {
      $products = Product::where('name','like','%' . $query . '%')
                  ->orderBy('name')
                  ->with('skus')
                  ->limit(5)
                  ->get();

      return $products;
    }


}
