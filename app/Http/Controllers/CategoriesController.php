<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;

use App\Category;

class CategoriesController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }



    //get all categories
    //
    //
    public function getall() {
      return Category::all();
    }




    public function add(Request $request) {
      if ($request->input('name')) {
        $category = new Category;
        $category->name = $request->input('name');

        if ($category->save()) {
          return $category;
        }


      }
      //else return error response
      return (new Response("error", 403));

    }


    public function edit(Request $request) {
      if ($request->input('id')) {

        $category = Category::find($request->input('id'));
        $category->name = $request->input('name');
        if ($category->save()) {
          return $category;
        }
      }

      //else return error response
      return (new Response("error", 403));
    }



    public function destroy($categoryid) {
      $category = Category::findOrFail($categoryid);
      if ($category->delete()) {

        return (new Response("ok", 200));
      } else {

        //else return error response
        return (new Response("error", 403));
      }

    }
}
