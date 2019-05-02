<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Log;
use App\Http\Requests;
use App\WeMockupFiles;
use App\Blender;
use App\Item;
use App\ItemJob;
use Storage;
use App\MockupLogger;
use App\Host;

use File;
use App\RenderStreet;
use App\RenderStreetToken;
use App\CyberDuck;
use App\Eta;
use App\Webhook;

class TestController extends Controller
{


  public $MockupLogger;

  public function __construct()
  {
      $this->MockupLogger = new MockupLogger;
  }




    public function test() {


    }


}
