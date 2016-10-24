<?php

namespace App\Services\Payment;


use App\Models\Cart;

interface PayCalculatorInterface 
{
    public function calculateCart(Cart $cart);
}
