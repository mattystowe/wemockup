<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;


class Category extends Model
{

  /**
   * Category has many products in it.
   *
   *
   * @return [type] [description]
   */
  public function products() {
    return $this->hasMany('App\Product');
  }


}
