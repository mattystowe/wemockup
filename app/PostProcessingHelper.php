<?php
/**
 * Class to help with managing post processing stages for items
 *
 *
 *
 */
namespace App;

use Log;
use App\MockupLogger;
use App\Item;
use App\Itempostproc;
use App\Jobs\Postprocessing\VideoOutput;
use App\Jobs\Postprocessing\FilesCopyWorkingToOutput;
use App\Jobs\Postprocessing\FilesRenderstToOutput;


class PostProcessingHelper
{


  public static function startPostProcessing(Item $item) {
    //1.Get first itempostprocs
    $itempostproc = $item->itempostprocs->first();
    MockupLogger::Item('debug',$item,'PostProcessingHelper::startPostProcessing');
    self::queueJob($itempostproc);
  }


  /**
   * Queues the next stage for an item (or mark item as complete if no more)
   *
   *
   *
   * @param  Item   $item [description]
   * @return [type]       [description]
   */
  public static function next(Item $item) {


        //1.Get the next itempostproc in the list that is PENDING status otherwise update item to COMPLETE
          $itempostprocs = $item->itempostprocs()->where('status','=','PENDING')->get();
          if ($itempostprocs->count()>0) {
            $itempostproc = $itempostprocs->first();
            self::queueJob($itempostproc);
          } else {
            $item->markAsComplete();
          }

  }



  /**
   * Queue the correct job for the itempostproc stage
   *
   *
   *
   *
   *
   * @param  Itempostproc $itempostproc [description]
   * @return [type]                     [description]
   */
  private static function queueJob(Itempostproc $itempostproc) {
    $job = null;
    switch ($itempostproc->postproc->jobname) {
      case 'ExportMOV':
        MockupLogger::Item('debug',$itempostproc->item,'QUEUE::Itempostproc::ExportMOV');
        $job = (new \App\Jobs\Postprocessing\VideoOutput('mov', $itempostproc))->onQueue(env('QUEUE_POSTPROCESSES'));
        break;
      case 'ExportMOVSlideshow':
        MockupLogger::Item('debug',$itempostproc->item,'QUEUE::Itempostproc::ExportMOV');
        $job = (new \App\Jobs\Postprocessing\VideoOutput('mov', $itempostproc))->onQueue(env('QUEUE_POSTPROCESSES'));
        break;
      case 'FilesCopyWorkingToOutput':
        MockupLogger::Item('debug',$itempostproc->item,'QUEUE::Itempostproc::FilesCopyWorkingToOutput');
        $job = (new \App\Jobs\Postprocessing\FilesCopyWorkingToOutput($itempostproc))->onQueue(env('QUEUE_POSTPROCESSES'));
        break;
      case 'FilesRenderstToOutput':
        MockupLogger::Item('debug',$itempostproc->item,'QUEUE::Itempostproc::FilesRenderstToOutput');
        $job = (new \App\Jobs\Postprocessing\FilesRenderstToOutput($itempostproc))->onQueue(env('QUEUE_POSTPROCESSES'));
        break;
      //
      //
      //OTHER STAGE TYPES GO HERE
      //
      //
      //
      //
      default:
        break;
    }

    //Send the job to the processing queue
    if ($job != null) {
      dispatch($job);
      $itempostproc->markAsQueued();
    }
  }





}
