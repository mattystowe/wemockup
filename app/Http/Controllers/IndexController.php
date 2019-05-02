<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class IndexController extends Controller
{

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {

  }

    /**
     * Index view
     *
     *
     *
     * @return [type] [description]
     */
    public function index() {
      return view('index.index');
    }
}
