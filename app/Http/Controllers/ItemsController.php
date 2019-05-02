<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Item;


class ItemsController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }



  /**
   * Get items that are currently processing/finishing
   *
   *
   * @return [type] [description]
   */
  public function getProcessing() {
      return Item::where('status','=','PROCESSING')->orWhere('status','=','FINISHING')->orWhere('status','=','QUEUED')->get();
  }


}
