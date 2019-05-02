<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Order;
use App\Item;
use DB;
use Carbon\Carbon;
use Artisan;

class ScheduledTasksController extends Controller
{

    //Main scheduler cron point
    //
    //
    //
    public function scheduleRun() {
      $result = Artisan::call('schedule:run');
      return "200 Ok";
    }

    /**
     * Generate emails reminders to all orders that contain items that have not yet been setup
     * within the last 2 days.
     *
     *
     *
     *
     * @return [type] [description]
     */
    public function sendPendingItemReminderEmails() {
      $result = DB::table('orders')
                ->leftjoin('items','items.order_id','=','orders.id')
                ->select('orders.id')
                ->where('items.status','=','PENDINGSETUP')
                ->where('items.created_at','>',Carbon::now()->subDays(2))
                ->where('items.created_at','<',Carbon::now()->subDays(1))
                ->get();
      if (is_array($result) && count($result)>0) {
        foreach ($result as $order) {
          //
          //Dispatch reminder email
          $Order = Order::find($order->id);
          $job = (new \App\Jobs\Emails\OrderPendingSetupReminder($Order))->onQueue(env('QUEUE_EMAILS'));
          dispatch($job);
        }
      }
      return $result;
    }


}
