<?php

namespace App;

use Log;
use App\MockupLogger;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Order;
use App\ItemJob;
use App\Events\ItemCompleted;
use App\Events\ItemFailed;
use App\Events\ItemCancelled;
use App\Events\ItemQueued;
use App\Events\ItemProcessing;
use App\Events\ItemProgressUpdated;
use App\WeMockupFiles;
use Storage;


class Item extends Model
{
    public function order() {
      return $this->belongsTo('App\Order');
    }

    public function sku() {
      return $this->belongsTo('App\Sku');
    }

    public function iteminputs() {
      return $this->hasMany('App\ItemInput');
    }

    public function itemjobs() {
      return $this->hasMany('App\ItemJob');
    }

    public function itempostprocs() {
      return $this->hasMany('App\Itempostproc')->orderBy('priority');
    }

    public function webhooks() {
      return $this->hasMany('App\Webhook');
    }

    public function progresswebhooks() {
      return $this->hasMany('App\Progresswebhook');
    }



    //Send webhooks with item details
    //
    //
    //
    //
    //
    public function sendWebhooks() {
      $webhooks = $this->webhooks;
      foreach ($webhooks as $webhook) {
        $webhook->send();
      }
    }

    //Send progress webhooks with item details
    //
    //
    //
    //
    //
    public function sendProgressWebhooks($progress) {
      $progresswebhooks = $this->progresswebhooks;
      foreach ($progresswebhooks as $webhook) {
        $webhook->send($progress);
      }
    }



    //Clean up item input files for this item
    //
    //return bool.
    //
    public function cleanupItemInputs() {
        $files = new WeMockupFiles;
        $iteminput_dir = $files->localInputFilesDirectory($this);
        if (is_dir(storage_path('app/' . $iteminput_dir ))) {
          $result = Storage::disk('local')->deleteDirectory($iteminput_dir);
          return $result;
        } else {
          //iteminput directory does not exist to delete
          return false;
        }
    }



    //cleanup bundle files from local system (If any have been created)
    //
    //
    //return bool.
    //
    //
    //
    public function cleanupBundleFiles() {
      $files = new WeMockupFiles;
      switch($this->sku->product->type->jobname) {
        case 'RenderStMultipleFrame':
          return $this->deleteBundleFiles();
          break;
        case 'RenderStSingleFrame':
          return $this->deleteBundleFiles();
          break;

        default:
        return false;
      }

    }

    //perform the actual deletion of bundle files.
    //
    //
    //
    private function deleteBundleFiles() {
      $itemjobs = $this->itemjobs;
      $itemjob = $itemjobs[0];
      $files = new WeMockupFiles;
      $bundle_dir = $files->bundleDirectory($itemjob);
      if (is_dir(storage_path('app/' . $bundle_dir ))) {
        $result = Storage::disk('local')->deleteDirectory($bundle_dir);
        return $result;
      } else {
        //iteminput directory does not exist to delete
        return false;
      }
    }


    //Cleanup working files for this item
    //
    //
    //
    public function cleanupWorkingFiles() {
      $files = new WeMockupFiles;
      $working_dir = $files->outputWorkingDirectory($this);
      if (is_dir(storage_path('app/' . $working_dir ))) {
        $result = Storage::disk('local')->deleteDirectory($working_dir);
        return $result;
      } else {
        //iteminput directory does not exist to delete
        return false;
      }
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
      event(new ItemQueued($this));
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
      if ($this->status != 'PROCESSING') {
        $this->status = 'PROCESSING';
        $this->date_processing = Carbon::now();
        $this->save();
        event(new ItemProcessing($this));
      }
    }


    /**
     * Mark the item as FINISHING
     *
     *
     *
     *
     * @return [type] [description]
     */
    public function markAsFinishing() {
      if ($this->status != 'FINISHING') {
        $this->status = 'FINISHING';
        $this->date_finishing = Carbon::now();
        $this->save();
      }
    }



    /**
     * Mark the item as complete
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

      event(new ItemCompleted($this));


    }



    /**
     * Mark the item as failed if not already
     *
     *
     *
     *
     *
     * @return [type] [description]
     */
    public function markAsFailed() {
      if ($this->status != 'FAILED') {
        $this->status = 'FAILED';
        $this->date_failed = Carbon::now();
        $this->save();

        //throw new event to handle cancelling of all itemjobs/postprocesses still pending.
        event(new ItemFailed($this));
      }
    }



    public function markAsCancelled($reason = null) {
      $this->status = 'CANCELLED';
      $this->date_cancelled = Carbon::now();
      $this->cancelled_reason = $reason;
      $this->save();

      //throw new event to handle cancelling of all itemjobs/postprocesses still pending. - With reason.
      event(new ItemCancelled($this));
    }




    /**
     * Return the progress of the Item using calculating for all of its sub items.
     *
     *
     *
     *
     *
     * @return [int] [progress 1 - 100]
     */
    public function calculateAndSaveProgress() {
      $totalItemJobs = $this->itemjobs()->count();
      $completeItemJobs = $this->itemjobs()->where('status','=','COMPLETE')->count();
      $processingItemJobs = $this->itemjobs()->where('status','=','PROCESSING')->count();
      if ($processingItemJobs>0) {
        $processingItemJobsAvgProgress = $this->itemjobs()->where('status','=','PROCESSING')->avg('progress');
      } else {
        $processingItemJobsAvgProgress = 0;
      }
      $progress = round(  (($completeItemJobs + ($processingItemJobsAvgProgress/100)) / $totalItemJobs) *100  );
      if ($progress > $this->progress) {
        $this->progress = $progress;
        $this->save();
        //throw event for item progress updated.
        event(new ItemProgressUpdated($this, $progress));
      }
      return $progress;
    }



    /**
     * Reset the item for processing again (eg if previously failed)
     *
     * This method removes all previous items and postprocs
     *
     *
     *
     *
     *
     */
    public function resetForProcessing() {
      $this->itemjobs()->delete();
      $this->itempostprocs()->delete();

    }

}
