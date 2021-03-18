<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;

    protected $table = 'product';

    protected $fillable = [
        'shop_id','product_name','product_price'
    ];
}
