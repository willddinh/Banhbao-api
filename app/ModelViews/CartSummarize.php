<?php

namespace App\ModelViews;


class CartSummarize
{
    public $totalServicePrice;
    public $shippingFee;
    public $total;
    public $deposit;
    public $totalItem;

    /**
     * CartSummarize constructor.
     * @param $totalServicePrice
     * @param $shippingFee
     * @param $total
     * @param $deposit
     */
    public function __construct($totalServicePrice, $shippingFee, $total, $deposit, $totalItem)
    {
        $this->totalServicePrice = $totalServicePrice;
        $this->shippingFee = $shippingFee;
        $this->total = $total;
        $this->deposit = $deposit;
        $this->totalItem = $totalItem;
    }


}
