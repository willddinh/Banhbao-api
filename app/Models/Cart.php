<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public $table = 'carts';
    const STATUS_INIT = 'INIT';
    const STATUS_EXPIRED = 'EXPIRED';
    const STATUS_FINISHED = 'FINISHED';

    public function items()
    {
        return $this->hasMany('App\Models\CartItem');
    }

}
