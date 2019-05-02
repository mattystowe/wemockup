<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Type;


class TypesController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  public function getall() {
    return Type::all();
  }

  public function add(Request $request) {
    if ($request->input('name') && $request->input('jobname')) {
      $type = new Type;
      $type->name = $request->input('name');
      $type->jobname = $request->input('jobname');

      if ($type->save()) {
        return $type;
      }


    }
    //else return error response
    return (new Response("error", 403));

  }

  public function edit(Request $request) {
    if ($request->input('id')) {

      $type = Type::find($request->input('id'));
      $type->name = $request->input('name');
      $type->jobname = $request->input('jobname');
      if ($type->save()) {
        return $type;
      }
    }

    //else return error response
    return (new Response("error", 403));
  }



  public function destroy($typeid) {
    $type = Type::findOrFail($typeid);
    if ($type->delete()) {

      return (new Response("ok", 200));
    } else {

      //else return error response
      return (new Response("error", 403));
    }

  }


}
