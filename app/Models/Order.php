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

    public function delete()
    {
        $this->items()->delete();
        return parent::delete();
    }

    //@todo: many policies apply here :/>
    public function getTotal(){
        $result = 0;
        if($this->items){
            foreach ($this->items as $item) {
                $result = $result + $item->price;
            }
        }
        return $result;
    }

    public function getDeposit(){
        $result = 0;
        if($this->items){
            foreach ($this->items as $item) {
                $aDeposit =$item->price - $item->rent_price;
                $aDeposit = $aDeposit > 0 ? $aDeposit : 0;
                $result = $result + $aDeposit;
            }
        }

        $result = $result - $this->shipping_fee;
        
        return $result > 0 ? $result : 0;
    }

    public function getRentFee(){
        $result = $this->shipping_fee;
        if($this->items){
            foreach ($this->items as $item) {
                $aFee =$item->rent_price;
                $result = $result + $aFee ;
            }
        }
        return $result;
    }


}
