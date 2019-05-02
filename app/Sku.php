<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
use App\Frameconfig;
use App\Postproc;


class Sku extends Model
{

  protected $fillable = [
    'skuuid',
    'name',
    'description',
    'product_id',
    'frameconfig_id'
  ];
    /**
     * This SKU belongs to a single product.
     *
     *
     *
     * @return [type] [description]
     */
    public function product() {
      return $this->belongsTo('App\Product');
    }


    /**
     * This SKU has a frame configuration
     *
     *
     *
     * @return [type] [description]
     */
    public function frameconfig() {
      return $this->belongsTo('\App\Frameconfig');
    }


    /**
     * This SKU may have many post processing stages in order of its joining priority
     *
     *
     *
     * @return [type] [description]
     */
    public function postprocs() {
      return $this->belongsToMany('App\Postproc')->withPivot('priority')->orderBy('pivot_priority');
    }
}
