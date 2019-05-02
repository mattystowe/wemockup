<?php

namespace App\Http\Controllers;

use Log;
use App\MockupLogger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Order;
use App\Item;
use App\ItemInput;
use App\ItemJob;
use App\Itempostproc;
use App\WeMockupFiles;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Eta;
use DB;
use App\Events\OrderCreated;

class OrdersController extends Controller
{


  public function __construct(){}



    public function index() {
      //$order = Order::where('orderuid', '=', $orderuid)->firstOrFail();
      return view('orders.index');

    }





    /**
     * Search orders - return alongside items
     *
     *
     *
     *
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function search($query = null) {
      if ($query == null) {
        $orders = Order::paginate(10);
      } else {
        $orders = Order::where('id','=',$query)->orWhere('email','=',$query)->paginate(10);

      }
      foreach($orders as $order) {
        $items = $order->items;
      }
      return $orders;
    }




    /**
     * Return full order with items, skus and products
     *
     * @param  [type] $orderuid [description]
     * @return [type]           [description]
     */
    public function loadOrder($orderuid) {
      $order = Order::where('orderuid','=',$orderuid)->with('items')->get();

      if (!$order->isEmpty()) { // should only be 1
        //order loaded with items.

        //Lazy load the Sku and Sku's product
        foreach ($order[0]->items as $item) {
          $sku = $item->sku;
          $product = $sku->product;
        }

        return $order[0];
      } else {
        return 'notfound';
      }
    }



    /**
     * Load item with its sku, product, order, inputoptions,
     *
     *
     * @param  [type] $itemuid [description]
     * @return [type]          [description]
     */
    public function loadItem($itemuid) {
      $item = Item::where('itemuid','=',$itemuid)->first();
      if ($item) {
        //append all associated data items by lazy loading.
        $order = $item->order;
        $sku = $item->sku;
        $product = $item->sku->product;
        $inputoptions = $item->sku->product->inputoptions;
        if (!$inputoptions->isEmpty()) {
          foreach ($inputoptions as $inputoption) {
            $inputoption->data = json_decode($inputoption->data);
          }
        }
        $postprocs = $item->sku->postprocs;

        $iteminputs = $item->iteminputs;


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
        return 'notfound';
      }

    }



    /**
     * Load item with its sku, product, order, inputoptions,
     *
     * WITH STATS
     *
     *
     * @param  [type] $itemuid [description]
     * @return [type]          [description]
     */
    public function loadItemWithStats($itemuid) {
      $item = $this->loadItem($itemuid);

      //Removed the stats bit - can add in here later if still required.
      //
      /*$itemjobs = $item->itemjobs->groupBy('status');
      $stats = array();
      foreach ($itemjobs as $status => $itemjob) {
        $stats[$status] = count($itemjob);
      }
      $item->itemjobstats = $stats;

      //do not return all of the itemjobs in the json
      unset($item->itemjobs);*/


      return $item;

    }



    /**
     * return item jobs paginated for itemuid
     *
     *
     *
     *
     *
     * @param  [type] $itemuid [description]
     * @return [type]          [description]
     */
    public function loadItemJobs($itemuid) {
      $item = Item::where('itemuid','=',$itemuid)->first();
      if ($item) {
        $itemjobs = $item->itemjobs()->paginate(20);
        return $itemjobs;
      } else {
        return 'not found';
      }
    }


    /**
     * return item jobs paginated for itemuid
     *
     *
     *
     *
     *
     * @param  [type] $itemuid [description]
     * @return [type]          [description]
     */
    public function loadItemPostprocs($itemuid) {
      $item = Item::where('itemuid','=',$itemuid)->first();
      if ($item) {
        $itempostprocs = $item->itempostprocs;
        foreach ($itempostprocs as $post) {
          $postproc = $post->postproc;
        }
        return $itempostprocs;
      } else {
        return 'not found';
      }
    }



    /**
     * return the processing logs for a ItemJob
     *
     *
     *
     * @param  [type] $itemid [description]
     * @return [type]         [description]
     */
    public function loadItemJobLog($itemjobid) {
      $itemjob = ItemJob::find($itemjobid);
      if ($itemjob) {
        return $itemjob->processinglog;
      } else {
        return 'not found';
      }
    }

    public function loadItemPostProcLog($itempostprocid) {
      $itempostproc = Itempostproc::find($itempostprocid);
      if ($itempostproc) {
        return $itempostproc->processinglog;
      } else {
        return 'not found';
      }
    }





    public function cancelItem(Request $request, $itemuid) {
      $item = Item::where('itemuid','=',$itemuid)->first();
      if ($item) {
        $item->markAsCancelled($request->input('reason'));
        return $item;
      } else {
        return 'not found';
      }
    }





    /**
     * Submit item for processing
     *
     *
     *
     *
     * @param  [type] $itemuid [description]
     * @return [type]          [description]
     */
    public function submitItem(Request $request, $itemuid) {
      $error = false;

      $item = Item::where('itemuid','=',$itemuid)->first();
      if ($item) {
        $order = $item->order;

        if ($this->isItemValidForSubmit($item)) {

          //Check input options are filled out.
          //
          $inputoptions = json_decode($request->input('inputoptions'));


          if ($this->inputOptionsValid($inputoptions)) {

            MockupLogger::Item('debug',$item,'ITEM_SUBMITTED',['product_inputoptions'=>$inputoptions]);


            //Save ItemInputs and add Item to queue for processing.
            foreach ($inputoptions as $inputoption) {
              $iteminput = new ItemInput([
                'item_id'=>$item->id,
                'input_type'=>$inputoption->input_type,
                'variable_name'=>$inputoption->variable_name,
                'value'=>$inputoption->value,
                'filename'=>$inputoption->filename,
                'filekey'=>$inputoption->filekey,
                'filestackurl'=>$inputoption->filestackurl
              ]);
              $iteminput->save();
            }

            //send to processing queue
            $job = (new \App\Jobs\ProcessItem($item))->onQueue(env('QUEUE_ITEMPROCESSING'));
            dispatch($job);

            //mark the item as QUEUED and set timestamp for queued
            $item->markAsQueued();


          } else {
            $error = true;
          }


        } else {
          $error = true;
        }


      } else {
        //could not find item by uid
        $error = true;
      }



      ///////////////////
      if ($error) {
        return 'error';
      } else {
        return 'ok';
      }
    }





    /**
     * Re-Submit item for processing - if attempting again from failed job
     *
     *
     *
     *
     * @param  [type] $itemuid [description]
     * @return [type]          [description]
     */
    public function resubmitItem($itemuid) {
      $error = false;

      $item = Item::where('itemuid','=',$itemuid)->first();


      if ($item) {
        $order = $item->order;

        if ($this->isItemValidForSubmit($item)) {



            MockupLogger::Item('debug',$item,'ITEM_RE_SUBMITTED');

            //remove the itemjobs and postprocs from the item before processing again.
            $item->resetForProcessing();


            //send to processing queue
            $job = (new \App\Jobs\ProcessItem($item))->onQueue(env('QUEUE_ITEMPROCESSING'));
            dispatch($job);

            //mark the item as QUEUED and set timestamp for queued
            $item->markAsQueued();




        } else {
          $error = true;
        }


      } else {
        //could not find item by uid
        $error = true;
      }



      ///////////////////
      if ($error) {
        return 'error';
      } else {
        return 'ok';
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
        if (isset($inputoption->variable_name) && isset($inputoption->value)) {
            //
            //handle the file upload items and setting to null if not.
            if (!isset($inputoption->filename)) { $inputoption->filename = '';}
            if (!isset($inputoption->filestackurl)) { $inputoption->filestackurl = '';}
            if (!isset($inputoption->filekey)) { $inputoption->filekey = '';}

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





    /**
     * Generate a test order for incoming sku
     *
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function createTestOrder(Request $request, $skuid) {
      $email = $request->input('email');
      $firstname = $request->input('firstname');
      $lastname = $request->input('lastname');
      //create order
      $uuid4 = Uuid::uuid4();
      $orderattributes = array(
        'origin' => 'admintest',
        'shopify_order_id' => 'TEST-' . $uuid4->toString(),
        'email' => $email,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'amount' => 0.00,
        'orderuid' => $uuid4->toString()
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


      $uuid4 = Uuid::uuid4();
      $item = new Item;
      $item->itemuid = $uuid4->toString();
      $item->skucode = $skuid;
      $item->price = 0.00;
      //assign sku id to Item
      $item->sku_id = $skuid;
      $item->status = 'PENDINGSETUP';
      $order->items()->save($item);

      //dispatch new order Event
      event(new OrderCreated($order));


      MockupLogger::Order('debug',$order,'ORDER_NEW');

      return $order;

    }








}
