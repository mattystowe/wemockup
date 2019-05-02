<?php

namespace App\Console\Commands;

use Log;
use App\MockupLogger;
use Illuminate\Console\Command;
use App\RenderStreet;
use App\ItemJob;
use App\Item;
use DB;
use Artisan;




class GetRenderStResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renderstreet:results';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll renderstreet for render results.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //1.Get a list of all render street jobs currently processing
        $renderstreet_jobs = $this->getRenderStreetJobsProcessing();
        $this->line(print_r($renderstreet_jobs,true));

        if (is_array($renderstreet_jobs) && count($renderstreet_jobs)>0) {
          //2.Reach out to render.st service to get update on these jobs
          $rs = new RenderStreet;
          $renderstreet_results = $rs->getResults($renderstreet_jobs);
          $this->line(print_r($renderstreet_results,true));


          //3.Go through and update progress/status/kick off post processing jobs.
          if ($renderstreet_results) {

            $rs->processResults($renderstreet_results);

          } else {
            Log::error('console:: renderstreet:results error');
          }
        }

    }




    //Returns an array of all renderstreet job ids that are still processing on the system.
    //
    public function getRenderStreetJobsProcessing() {
      $results = DB::table('item_jobs')
              ->join('items', 'item_jobs.item_id', '=', 'items.id')
              ->join('skus','items.sku_id','=','skus.id')
              ->join('products','skus.product_id','=','products.id')
              ->join('types','products.type_id','=','types.id')
              ->select(
                'item_jobs.external_id'
              )
              ->where('item_jobs.status','=','PROCESSING')
              ->whereIn('types.jobname',['RenderStSingleFrame','RenderStMultipleFrame'])
              ->orderBy('item_jobs.id','asc')
              ->get();
      $external_ids = array();
      foreach ($results as $result) {
        if (isset($result->external_id)) {
          $external_ids[] = $result->external_id;
        }
      }
      return $external_ids;
    }


}
