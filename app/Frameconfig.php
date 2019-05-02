<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sku;


class Frameconfig extends Model
{

  protected $fillable = [
    'name',
    'dimx',
    'dimy',
    'outputformat',
    'watermark'
  ];

  protected $casts = [
     'watermark' => 'boolean'
  ];


  public function skus() {
    return $this->hasMany('App\Sku');
  }
}
