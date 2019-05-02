<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Postproc;





class PostprocessingController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function getall() {
    return Postproc::all();
  }





  public function add(Request $request) {

      $postproc = new Postproc($request->all());
      if ($postproc->save()) {
        return $postproc;
      }



    //else return error response
    return (new Response("error", 403));

  }



  public function edit(Request $request) {
    if ($request->input('id')) {

      $postproc = Postproc::find($request->input('id'));
      $postproc->name = $request->input('name');
      $postproc->jobname = $request->input('jobname');
      $postproc->data = $request->input('data');
      if ($postproc->save()) {
        return $postproc;
      }
    }

    //else return error response
    return (new Response("error", 403));
  }



  public function destroy($stepid) {
    $postproc = Postproc::findOrFail($stepid);
    if ($postproc->delete()) {

      return (new Response("ok", 200));
    } else {

      //else return error response
      return (new Response("error", 403));
    }

  }


}
