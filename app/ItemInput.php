<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Item;

class ItemInput extends Model
{

  protected $fillable = [
    'item_id',
    'input_type',
    'variable_name',
    'value',
    'filename',
    'filekey',
    'filestackurl'
  ];

  public function item() {
    return $this->belongsTo('App\Item');
  }
}
