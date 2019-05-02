<?php
//
//Accepts webhooks from render.st on job result
//
//
//
//
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\RenderStreet;
use Log;
use App\MockupLogger;
use App\ItemJob;

class RenderStreetController extends Controller
{

  public function __construct()
  {

  }


  public function result($itemjobid) {
      $itemjob = ItemJob::find($itemjobid);
      if ($itemjob) {
          MockupLogger::ItemJob('debug',$itemjob,'RenderStreet::Webhook Received');
          $rs = new RenderStreet;
          $renderstreet_results = $rs->getResults(array($itemjob->external_id));
          //3.Go through and update progress/status/kick off post processing jobs.
          if ($renderstreet_results) {

            $rs->processResults($renderstreet_results);
            return "ok";
          } else {
            MockupLogger::ItemJob('error',$itemjob,'RenderStreet::Webhook Could not process results.');
            return "Error could not process results";
          }
      } else {
        //
        //
        //
        return "No job found.";
      }
  }


}
