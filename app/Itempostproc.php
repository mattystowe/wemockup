<?php

namespace App;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Host;


class Itempostproc extends Model
{

  




  public function postproc() {
    return $this->belongsTo('App\Postproc');
  }

  public function item() {
    return $this->belongsTo('App\Item');
  }



  /**
   * Mark the item as queued
   *
   *
   *
   * @return [type] [description]
   */
  public function markAsQueued() {
    $this->status = 'QUEUED';
    $this->date_queued = Carbon::now();
    $this->save();
  }




  /**
   * Mark the item as processing
   *
   *
   *
   *
   * @return [type] [description]
   */
  public function markAsProcessing() {
    $host = new Host;
    $this->status = 'PROCESSING';
    $this->date_processing = Carbon::now();
    $this->instance_type = $host->getInstanceType();
    $this->hostname = $host->getHostname();
    $this->save();
  }



  /**
   * Mark the itempostproc as complete
   *
   *
   *
   * @return [type] [description]
   */
  public function markAsComplete() {
    $this->status = 'COMPLETE';
    $this->date_complete = Carbon::now();
    $this->save();
  }



  /**
   * Mark the itempostproc as failed
   *
   *
   *
   * @return [type] [description]
   */
  public function markAsFailed() {
    $this->status = 'FAILED';
    $this->date_failed = Carbon::now();
    $this->save();

    //mark its parent as failed also
    $this->item->markAsFailed();
  }


  /**
   * Mark the itempostproc as aborted
   *
   *
   *
   * @return [type] [description]
   */
  public function markAsAborted() {
    $this->status = 'ABORTED';
    $this->date_aborted = Carbon::now();
    $this->save();
  }




}
