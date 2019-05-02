<?php
/**
 * Job to copy all working files to the output directory
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
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Host;
use Artisan;

class FilesCopyWorkingToOutput extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;


    public $itempostproc;


    /**
     * Create a new job instance.
     *
     * @return void
     */
     public function __construct(Itempostproc $itempostproc)
     {
         $this->itempostproc = $itempostproc;
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
      MockupLogger::Item('debug', $this->itempostproc->item, 'POSTPROCESS::FilesCopyWorkingToOutput::Start');

      //2.Update status of stage.
      $this->itempostproc->markAsProcessing();

      $Files = new WeMockupFiles;
      $Files->copyWorkingS3ToOutputS3($this->itempostproc->item);

      //3.Update to complete
      MockupLogger::Item('debug', $this->itempostproc->item, 'POSTPROCESS::FilesCopyWorkingToOutput::Complete');
      $this->itempostproc->markAsComplete();
      //Call the next stage
      PostProcessingHelper::next($this->itempostproc->item);

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
        MockupLogger::Item('error', $this->itempostproc->item, 'POSTPROCESS::FilesCopyWorkingToOutput::Failed');
        //Mark as Failed
        //
        $this->itempostproc->markAsFailed();
        $host = new Host;
        $host->setInstanceScaleProtection(false);
    }


}
