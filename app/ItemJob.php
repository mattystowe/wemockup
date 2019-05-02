<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;
use Carbon\Carbon;
use App\MockupLogger;
use App\Host;
use App\Events\ItemJobCompleted;

class ItemJob extends Model
{




  protected $fillable = [
    'item_id',
    'status',
    'frame',
    'data',
    'date_queued',
    'date_processing',
    'date_complete',
    'date_error',
    'external_id'
  ];





    public function item() {
      return $this->belongsTo('App\Item');
    }



    /**
     * Mark item job status as PROCESSING alongside its date
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

      MockupLogger::ItemJob('debug',$this,'ITEMJOB_PROCESSING');
      //mark the parent item for this job as processing also if not already processing.
      $this->item->markAsProcessing();


    }


    /**
     * Mark item job status as PROCESSING alongside its date
     *
     *
     *
     * @return [type] [description]
     */
    public function markAsComplete() {
      $this->status = 'COMPLETE';
      $this->progress = 100;
      $this->date_complete = Carbon::now();
      $this->save();
      MockupLogger::ItemJob('debug',$this,'ITEMJOB_COMPLETE');
      event(new ItemJobCompleted($this));
    }



    /**
     * Mark the itemjob as failed
     *
     *
     *
     * @return [type] [description]
     */
    public function markAsFailed() {
      $this->status = 'FAILED';
      $this->date_failed = Carbon::now();
      $this->save();
      MockupLogger::ItemJob('debug',$this,'ITEMJOB_FAILED');

      //Mark the item parent as failed also.
      $this->item->markAsFailed();

    }



    /**
     * Mark the itemjob as aborted
     *
     *
     *
     * @return [type] [description]
     */
    public function markAsAborted() {
      $this->status = 'ABORTED';
      $this->date_aborted = Carbon::now();
      $this->save();
      MockupLogger::ItemJob('debug',$this,'ITEMJOB_ABORTED');

    }






}
