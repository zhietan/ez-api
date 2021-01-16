<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class Order extends Model
{
    //
    use SoftDeletes;
    use Uuid;
   protected $keyType = 'string';
   public $incrementing = false;
   protected $guarded = [];
   protected $primaryKey = 'order_id';

}
