<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Carbon\Carbon;
use App\Host;
use DB;
class HostsController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }


  /**
   * Return a list of healthy host workers - (successful heartbeat in the last 10 minutes)
   *
   *
   *
   *
   * @return [type] [description]
   */
  public function getHealthyHosts() {
    return Host::where('created_at','>',Carbon::now()
    ->subMinutes(10))
    ->groupBy('hostname')
    ->get();
  }





}
