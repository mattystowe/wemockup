<?php
/**
 * Job to handle video output formatting.
 *
 *
 * MPEG, MOV, etc.......
 *
 *
 *
 *
 *
 *
 *
 *
 */
namespace App\Jobs\Postprocessing;

use Exception;
use Log;
use App\MockupLogger;
use App\Jobs\Job;
use App\Itempostproc;
use App\WeMockupFiles;
use App\PostProcessingHelper;
use App\FFMpeg;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Host;
use Artisan;
use App\RenderStreet;

class VideoOutput extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $itempostproc;
    public $format;

    public $error = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($format, Itempostproc $itempostproc)
    {
        $this->itempostproc = $itempostproc;
        $this->format = $format;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      Artisan::call('cache:clear');
      $host = new Host;
      $host->setInstanceScaleProtection(true);

      //1.Process this stage
      MockupLogger::Item('debug', $this->itempostproc->item, 'POSTPROCESS::VideoOutput::Start : ' . $this->format);

      //2.Update status of stage.
      $this->itempostproc->markAsProcessing();

      //3.get all files needed into working directory
      switch($this->itempostproc->item->sku->product->type->jobname) {
        case 'RenderStMultipleFrame':
          $rs = new RenderStreet;
          $rs->downloadWorkingFiles($this->itempostproc->item);
          break;
        case 'RenderStSingleFrame':
          $rs = new RenderStreet;
          $rs->downloadWorkingFiles($this->itempostproc->item);
          break;

        default:
            //all other outputs
            $Files = new WeMockupFiles;
            $Files->downloadWorkingFilesFromS3($this->itempostproc->item);
            break;
      }





      //4.Perform encoding
      switch($this->format) {
          case 'mov':
              $ffmpeg = new FFMpeg($this->itempostproc,'mov');
              if (!$ffmpeg->process()) { $this->error = true; }
              break;

          default:
              break;
      }



      if ($this->error != true) {
        //3.Update to complete
        $this->itempostproc->markAsComplete();
        //Call the next stage
        $this->itempostproc->fresh(['item']);
        if ($this->itempostproc->item->status != 'FAILED' && $this->itempostproc->item->status != 'CANCELLED') {
          PostProcessingHelper::next($this->itempostproc->item);
        }

      } else {
        //
        //
        //Script error - mark error encoding video
        MockupLogger::Item('error', $this->itempostproc->item, 'POSTPROCESS::VideoOutput::error encoding video.');
        throw new Exception('Video encoding failed');
        $host->setInstanceScaleProtection(false);
      }

      $host->setInstanceScaleProtection(false);

    }



    /**
     * Handle failed jobs (if --tries=3 on the queue listener)
     *
     *
     *
     *
     * @return [type] [description]
     */
    public function failed()
    {
        MockupLogger::Item('error', $this->itempostproc->item, 'POSTPROCESS::VideoOutput::Failed');
        //Mark as Failed
        //
        $this->itempostproc->markAsFailed();
        $host = new Host;
        $host->setInstanceScaleProtection(false);
    }

}
