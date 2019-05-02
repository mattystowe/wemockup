<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\OrderCreated;

class Order extends Model
{

    protected $fillable = [
      'origin',
      'shopify_order_id',
      'email',
      'orderuid',
      'amount',
      'firstname',
      'lastname'
    ];
    /**
     * Order contains many Items
     *
     *
     *
     * @return [type] [description]
     */
    public function items() {
      return $this->hasMany('App\Item');
    }



    /**
     * explicitely create a new order
     *
     *
     * @return [type] [description]
     */
    public static function createNew($attributes) {
      $order = static::create($attributes);
      event(new OrderCreated($order));
      return $order;
    }
}
