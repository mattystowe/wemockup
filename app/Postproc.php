<?php
/**
 * Post Processing Stages - Definitions available for joining to SKUs
 *
 * eg output stitch MOV, MPEG,
 *
 *
 *
 *
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sku;


class Postproc extends Model
{

  protected $fillable = [
    'name',
    'jobname',
    'data'
  ];


    /**
     * This post processing stage is being used by many SKUs potentially.
     *
     *
     * @return [type] [description]
     */
    public function skus() {
      return $this->belongsToMany('App\Sku');
    }
}
