<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class RenderStreetToken extends Model
{

    //Token expiry days - (renderstreet = 14 but play it safe)
    //
    //
    //
    private $token_valid_days = 3;



    //Returns a valid token if one exists within the valid time period
    //otherwise return false
    //
    //
    public function getValid() {
      $date = Carbon::now()->subDays($this->token_valid_days);
      $result = DB::table('render_street_tokens')
        ->where('created_at','>',$date)
        ->orderBy('created_at','desc')
        ->get();
      if (count($result)>0) {
        return $result[0]->token;
      } else {
        return false;
      }
    }




}
