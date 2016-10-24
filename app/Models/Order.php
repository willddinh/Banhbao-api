<?php

namespace App\Models;

use App\Models\Traits\CartCalculatorTrait;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $table = 'orders';
    const STATUS_INIT = 'INIT';
    const STATUS_EXPIRED = 'EXPIRED';
    const STATUS_PAYED = 'PAYED';
    const STATUS_FINISHED = 'FINISHED';
    use CartCalculatorTrait;

    public function items()
    {
        return $this->hasMany('App\Models\OrderItem');
    }

    public function delete()
    {
        $this->items()->delete();
        return parent::delete();
    }

    public function getShippingFee(){
        return $this->shipping_fee;
    }
   


}
