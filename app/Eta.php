<?php
/**
 * ETA for order Items
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
use DB;
use Carbon\Carbon;

use App\Events\ItemJobProgressUpdated;

class Eta
{



  private $item;

  public $message = null;
  public $reasons = array();



  public function __construct(Item $Item)
  {
      $this->item = $Item;
      $this->getEta();

  }




  public function getEta() {
      if ($this->item->status == 'PENDINGSETUP' || $this->item->status == 'QUEUED') {
        $this->getStaticEta();
      }

      if ($this->item->status == 'PROCESSING') {
        $this->getLiveEta();
      }

  }





  /**
   * Calculate the static eta (using historical data from last time this sku was processed successfully)
   *
   *
   * @return [type] [description]
   */
  public function getStaticEta() {
    $response = DB::table('items')
        ->select([DB::raw('timestampdiff(SECOND, items.date_queued, items.date_complete) as last_completed_time'),'id'])
        ->where('sku_id','=',$this->item->sku->id)
        ->where('status','=','COMPLETE')
        ->orderBy('id','desc')
        ->limit(1)
        ->get();

    if (is_array($response) && count($response)>0) {
      $complete_time = $response[0]->last_completed_time;
      $complete_time_human = Carbon::now()->subSeconds($complete_time)->diffForHumans(null, true);
      $this->message = "Estimated processing time: " . $complete_time_human;
      $this->reason = $this->getReason($complete_time);
    } else {
      //We do not have any historical data on this sku.  This is the first time this has ever been ordered.
      $this->message = "You are the first person ever to order this item! So we have no idea how long this will take to process.";
      $this->reason = "Without historical data, we cannot estimate how long this item will take to process.";
    }


  }



  /**
   * Return a reason depending on the length of time input as seconds.
   *
   *
   * @param  [type] $seconds [description]
   * @return [type]          [description]
   */
  public function getReason($seconds) {
    $reason = null;
    if ($seconds > 0) {
      $mins = $seconds / 60;

      if ($mins >= 5) {
        $reason = "Its not that long really, and the wait will be worth it!  Just don't complain too much to the monkey while he's working.  Monkeys work faster for nice people.  He's a clever monkey who knows a thing about material physics and computer graphics. We think he's done well.";
      }
      if ($mins >= 10) {
        $reason = "Simulating light is very hard!  Especially for 100 mockup monkeys chained to desks.  We're going to feed them more bananas to speed them up!.";
      }
      if ($mins >= 20) {
        $reason = "Ok, I know this sounds like a long time. But believe me, trying to source 1000 monkeys, chain them to a desk and force them to work for bananas is hard!  They also need to learn the theory behind how to simulate light, and that required a LOT of bananas!";
      }
      if ($mins >= 30) {
        $reason = "Ok, this is a long time to wait.  We get it.  But what you are waiting for is our HR department.  They are now currently scurrying around in a frenzy trying to recruit more monkeys to churn out your work more quickly.";
      }


    }
    return $reason;
  }



/**
 * Get an estimated live ETA for processing jobs.
 *
 *
 *
 * @return [type] [description]
 */
public function getLiveEta() {
  //time currently in processing (secs)
  //$now = Carbon::now()->toDateTimeString();
  $now = Carbon::now();
  $processing_started = new Carbon($this->item->date_processing);
  $time_processing = $now->diffInSeconds($processing_started);

  /*$response = DB::table('items')
      ->select(DB::raw('timestampdiff(SECOND, items.date_processing, \'' . $now . '\') as time_processing'))
      ->where('id',$this->item->id)
      ->get();*/

  //$time_processing = $response[0]->time_processing;

  if ($time_processing > 0 && $this->item->progress > 0) {
    $total_time_estimate = ($time_processing / $this->item->progress) * 100;
    $total_time_left = $total_time_estimate - $time_processing;

    //$etadebug = "time_processing = " . $time_processing . ", Item_Progress = " . $this->item->progress . ", total_time_left = " . $total_time_left;
    //MockupLogger::Item('debug',$this->item,'ETA: ' . $etadebug);

    if ($total_time_left <60) {
      $this->message = "Estimated processing time: " . round($total_time_left) . " secs.";
    } else {
      if ($total_time_left < 60*60) {
        $this->message = "Estimated processing time: " . round($total_time_left/60) . " mins.";
      } else {
        $this->message = "Estimated processing time: " . round($total_time_left/60/60) . " hours.";
      }
      
    }
    $this->reason = $this->getReason($total_time_left);
  } else {
    $this->message = "Estimated processing time: We're thinking about it now..";
    $this->reason = "We're currently working out the eta based on actual processing power available right now for your item.";
  }
}



}
