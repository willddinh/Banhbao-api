<?php
/**
 * Created by PhpStorm.
 * User: DuongLT
 * Date: 9/15/2016
 * Time: 9:58 AM
 */
namespace App\Models\Traits;

trait CartCalculatorTrait{

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

        $result = $result - $this->getShippingFee();

        return $result > 0 ? $result : 0;
    }

    public function getRentFee(){
        $result = $this->getShippingFee();
        if($this->items){
            foreach ($this->items as $item) {
                $aFee =$item->rent_price;
                $result = $result + $aFee ;
            }
        }
        return $result;
    }
}