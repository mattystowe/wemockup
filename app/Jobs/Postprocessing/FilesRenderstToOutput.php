<?php
//Copy renderstreet output to S3 output
//
//
//
//
namespace App\Jobs\Postprocessing;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use App\MockupLogger;
use App\Host;
use App\Itempostproc;
use App\WeMockupFiles;
use App\PostProcessingHelper;
use App\RenderStreet;


class FilesRenderstToOutput extends Job implements ShouldQueue
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
      $host = new Host;
      $host->setInstanceScaleProtection(true);

      //1.Process this stage
      MockupLogger::Item('debug', $this->itempostproc->item, 'POSTPROCESS::FilesRenderstToOutput::Start');

      //2.Update status of stage.
      $this->itempostproc->markAsProcessing();

      //get files from render.st to local working directory
      $rs = new RenderStreet;
      if ($rs->downloadWorkingFiles($this->itempostproc->item)) {

        //upload working directory to S3
        $files = new WeMockupFiles;
        $files->copyWorkingLocalToOutputS3($this->itempostproc->item);

        //3.Update to complete
        MockupLogger::Item('debug', $this->itempostproc->item, 'POSTPROCESS::FilesRenderstToOutput::Complete');
        $this->itempostproc->markAsComplete();
        //Call the next stage
        PostProcessingHelper::next($this->itempostproc->item);
      } else {
        //
        //ErrorException downloading files from renderst.
        //
        //
        $host->setInstanceScaleProtection(false);
        //
        //TODO throw exception
      }





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
        MockupLogger::Item('error', $this->itempostproc->item, 'POSTPROCESS::FilesRenderstToOutput::Failed');
        //Mark as Failed
        //
        $this->itempostproc->markAsFailed();
        $host = new Host;
        $host->setInstanceScaleProtection(false);
    }
}
