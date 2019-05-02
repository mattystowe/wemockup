<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Product;
use App\Sku;
use App\InputOption;
use Ramsey\Uuid\Uuid;




class ProductsController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }



  public function add(Request $request) {

      $product = new Product($request->all());
      $uuid4 = Uuid::uuid4();
      $product->productuid = $uuid4->toString();
      if ($product->save()) {
        return $product;
      }



    //else return error response
    return (new Response("error", 403));

  }




  public function edit(Request $request, $productid) {
    $product = Product::findOrFail($productid);
    $product->name = $request->input('name');
    $product->description = $request->input('description');
    $product->type_id = $request->input('type_id');
    $product->category_id = $request->input('category_id');
    $product->frame_start = $request->input('frame_start');
    $product->frame_end = $request->input('frame_end');
    $product->image = $request->input('image');
    $product->fullimage = $request->input('fullimage');
    $product->location = $request->input('location');
    if ($product->save()) {
      return 'Successfully saved';
    } else {
      return (new Response("error saving product", 403));
    }
  }


  public function destroy($productid) {
    $product = Product::findOrFail($productid);

    if ($product->delete()) {
      return 'Successfully deleted';
    } else {
      return (new Response("error saving product", 403));
    }
  }


  /**
   * Create a new SKU item for a product.
   *
   *
   * @param Request $request [description]
   */
  public function addSku(Request $request) {
    $product = Product::find($request->input('product_id'));
    if ($product) {
      //create the new sku and return it.
      $sku = new Sku($request->all());
      $uuid4 = Uuid::uuid4();
      $sku->skuuid = $uuid4->toString();

      if ($product->skus()->save($sku)) {
        return $sku;
      } else {
        return (new Response("error", 403));
      }
    } else {
      return (new Response("error", 403));
    }
  }


  public function editSku(Request $request) {
    $sku = Sku::find($request->input('id'));
    if ($sku) {
      $sku->name = $request->input('name');
      $sku->description = $request->input('description');
      $sku->frameconfig_id = $request->input('frameconfig_id');
      if ($sku->save()) {
        return $sku;
      } else {
        return (new Response("error", 403));
      }
    } else {
      return (new Response("error", 403));
    }
  }



  public function deleteSku($skuid) {
    $sku = Sku::findOrFail($skuid);
    if ($sku->delete()) {
      return 'deleted successfully';
    } else {
      return (new Response("error deleting sku", 403));
    }
  }




  public function loadSku($skuid) {
    $product = Sku::where('id', '=', $skuid)
                  ->with('product')
                  ->with('postprocs')
                  ->with('frameconfig')
                  ->firstOrFail();

    return $product;
  }




  public function getall() {
    return Product::with('category')->with('type')->get();
  }


  public function load($productid) {
    $product = Product::where('id', '=', $productid)
                  ->with('skus')
                  ->with('inputoptions')
                  ->with('category')
                  ->with('type')
                  ->firstOrFail();

    return $product;
  }



  /**
   * Add an input item to a product
   *
   *
   * @param Request $request [description]
   */
  public function addInputOption(Request $request, $productid) {
    $product = Product::findOrFail($productid);

    $inputoption = new InputOption($request->all());

    $product->inputoptions()->save($inputoption);

    return $inputoption;

  }


  /**
   * Save updates to existing input option
   *
   *
   *
   * @param  Request $request       [description]
   * @param  [type]  $inputoptionid [description]
   * @return [type]                 [description]
   */
  public function editInputOption(Request $request, $inputoptionid) {
    $inputoption = InputOption::findOrFail($inputoptionid);

    $inputoption->name = $request->input('name');
    $inputoption->description = $request->input('description');
    $inputoption->image = $request->input('image');
    $inputoption->input_type = $request->input('input_type');
    $inputoption->data = $request->input('data');
    $inputoption->variable_name = $request->input('variable_name');

    if ($inputoption->save()) {
      return $inputoption;
    } else {
      return (new Response("error", 403));
    }
  }



  public function deleteInputOption(Request $request, $inputoptionid) {
    $inputoption = InputOption::findOrFail($inputoptionid);


    if ($inputoption->delete()) {
      return 'Successfully deleted';
    } else {
      return (new Response("error", 403));
    }
  }


  public function orderInputOptions(Request $request, $productid) {
    $orderValues = $request->input('orderValues');
    $product = Product::find($productid);
    if ($product) {

      foreach ($orderValues as $item) {
        $inputoption = InputOption::findOrFail($item['inputoptionid']);
        $inputoption->priority = $item['priority'];
        $inputoption->save();
        //$product->inputoptions()->updateExistingPivot($item['inputoptionid'], ['priority'=>$item['priority']]);
      }
      return 'Saved Successfully';
    } else {
      return (new Response("Could not find product", 403));
    }

  }




}
