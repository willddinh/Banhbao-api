<?php

namespace App\Services\Payment;


use App\Exceptions\ConnectPayGateException;
use App\Models\Cart;
use App\ModelViews\CartSummarize;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Log;

class PayCalculatorServiceImpl implements PayCalculatorInterface
{


    protected $auth;
    

    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    public function calculateCart(Cart $cart)
    {
        $shippingFee = 15000;

        $deposit = $this->getDeposit($cart, $shippingFee);
        $rentFee = $this->getRentFee($cart, $shippingFee);
        $total = $this->getTotal($cart, $shippingFee);
        
        return new CartSummarize($rentFee,$shippingFee, $total,  $deposit);
    }

    public function getTotal($cart, $shippingFee){
        $result = 0;
        if($cart->items){
            foreach ($cart->items as $item) {
                $result = $result + $item->price;
            }
        }
        return $result;
    }

    public function getDeposit($cart, $shippingFee){
        $result = 0;
        if($cart->items){
            foreach ($cart->items as $item) {
                $aDeposit =$item->price - $item->rent_price;
                $aDeposit = $aDeposit > 0 ? $aDeposit : 0;
                $result = $result + $aDeposit;
            }
        }

        $result = $result - $shippingFee;

        return $result > 0 ? $result : 0;
    }

    public function getRentFee($cart, $shippingFee){
        $result = $shippingFee;
        if($cart->items){
            foreach ($cart->items as $item) {
                $aFee =$item->rent_price;
                $result = $result + $aFee ;
            }
        }
        return $result;
    }
}
