<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    public $timestamps = false;

    protected $table = 'detail';

    protected $fillable = [
        'shop_id','end_time','password','date','status','openUser'
    ];
}
