<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Sku;
use App\Postproc;


class SkusController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }


    /**
     * Associate a post processing stage with a sku with priority
     *
     *
     * @param Request $request [description]
     * @param [type]  $skuid   [description]
     */
    public function addPostProc(Request $request, $skuid) {
      $sku = Sku::find($skuid);
      if ($sku) {
        $postproc = Postproc::find($request->input('postprocid'));
        if ($postproc) {
          $result = $sku->postprocs()->attach($postproc->id, ['priority'=>$request->input('priority')]);
          if (!$result) {
            $postproc->priority = $request->input('priority');
            return $postproc;
          } else {
            return (new Response("Could not associate postproc with sku", 403));
          }
        } else {
          return (new Response("Could not find postproc", 403));
        }
      } else {
        return (new Response("Could not find sku", 403));
      }
    }


    /**
     * Remove association between processing stage and sku
     *
     *
     *
     * @param  Request $request [description]
     * @param  [type]  $skuid   [description]
     * @return [type]           [description]
     */
    public function removePostProc(Request $request, $skuid) {
      $sku = Sku::find($skuid);
      if ($sku) {
        $postproc = Postproc::find($request->input('postprocid'));
        if ($postproc) {
          $result = $sku->postprocs()->detach($postproc->id);
          if ($result) {
            return 'true';
          } else {
            return (new Response("Could not associate postproc with sku", 403));
          }
        } else {
          return (new Response("Could not find postproc", 403));
        }
      } else {
        return (new Response("Could not find sku", 403));
      }
    }



    public function orderPostProcs(Request $request, $skuid) {
      $orderValues = $request->input('orderValues');
      $sku = Sku::find($skuid);
      if ($sku) {

        foreach ($orderValues as $item) {
          $sku->postprocs()->updateExistingPivot($item['procid'], ['priority'=>$item['priority']]);
        }
        return 'Saved Successfully';
      } else {
        return (new Response("Could not find sku", 403));
      }

    }




}
