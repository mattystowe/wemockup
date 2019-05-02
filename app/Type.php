<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;


class Type extends Model
{


    /**
     * Type has many products.
     *
     * @return [type] [description]
     */
    public function products() {
      return $this->hasMany('App\Product');
    }

}
