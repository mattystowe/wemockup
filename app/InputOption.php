<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;


class InputOption extends Model
{
  protected $fillable = [
    'product_id',
    'name',
    'description',
    'image',
    'input_type',
    'data',
    'variable_name',
    'priority'
  ];


    /**
     * Input option belong to a product
     *
     *
     *
     * @return [type] [description]
     */
    public function product() {
      return $this->belongsTo('App\Product');
    }


}
