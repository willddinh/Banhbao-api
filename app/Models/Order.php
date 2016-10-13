<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $table = 'orders';
    const STATUS_INIT = 'INIT';
    const STATUS_EXPIRED = 'EXPIRED';
    const STATUS_PAYED = 'PAYED';
    const STATUS_FINISHED = 'FINISHED';

    public function items()
    {
        return $this->hasMany('App\Models\OrderItem');
    }
}
