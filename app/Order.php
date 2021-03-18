<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $timestamps = false;

    protected $table = 'order';

    protected $fillable = [
        'detail_id','order_id','user','amount','status','ps','product_name','product_price'
    ];
}
