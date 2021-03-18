<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    public $timestamps = false;

    protected $table = 'shop';

    protected $fillable = [
        'shop_name','add_user'
    ];
}
