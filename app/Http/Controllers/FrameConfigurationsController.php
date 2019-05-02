<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Frameconfig;





class FrameConfigurationsController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function getall() {
    return Frameconfig::all();
  }





  public function add(Request $request) {

      $frameconfig = new Frameconfig($request->all());
      if ($frameconfig->save()) {
        return $frameconfig;
      }



    //else return error response
    return (new Response("error", 403));

  }



  public function edit(Request $request) {
    if ($request->input('id')) {

      $frameconfig = Frameconfig::find($request->input('id'));
      $frameconfig->name = $request->input('name');
      $frameconfig->dimx = $request->input('dimx');
      $frameconfig->dimy = $request->input('dimy');
      $frameconfig->outputformat = $request->input('outputformat');
      $frameconfig->watermark = $request->input('watermark');
      if ($frameconfig->save()) {
        return $frameconfig;
      }
    }

    //else return error response
    return (new Response("error", 403));
  }



  public function destroy($frameconfigid) {
    $type = Frameconfig::findOrFail($frameconfigid);
    if ($type->delete()) {

      return (new Response("ok", 200));
    } else {

      //else return error response
      return (new Response("error", 403));
    }

  }


}
