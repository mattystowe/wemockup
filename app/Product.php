<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Type;
use App\Category;
use App\InputOption;
use App\Sku;


class Product extends Model
{
  protected $fillable = [
    'productuuid',
    'name',
    'description',
    'type_id',
    'category_id',
    'frame_start',
    'frame_end',
    'image',
    'fullimage',
    'location'
  ];


    /**
     * Product belongs to a single category
     *
     *
     * @return [type] [description]
     */
    public function category() {
      return $this->belongsTo('App\Category');
    }


    /**
     * Product belongs to a type.
     *
     *
     * @return [type] [description]
     */
    public function type() {
      return $this->belongsTo('App\Type');
    }


    /**
     * Product has many input options - default ordered by priority.
     *
     *
     * @return [type] [description]
     */
    public function inputoptions() {
      return $this->hasMany('App\InputOption')->orderBy('priority');
    }

    /**
     * This product has many SKUs
     *
     *
     *
     * @return [type] [description]
     */
    public function skus() {
      return $this->hasMany('App\Sku');
    }


}
