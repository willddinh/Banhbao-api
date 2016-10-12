<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantTransaction extends Model
{
    public $table = 'merchant_transactions';
    protected $fillable = ['provider', 'init_amount', 'order_id',
        'order_info', 'status', 'transaction_amount', 'card_name', 'card_type', 'order_type',
    'request_time', 'response_code', 'response_message', 'response_time'];

    const STATUS_INIT = 'INIT';
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_PENDING= 'PENDING';
    const STATUS_FAIL= 'FAIL';
    const STATUS_FAIL_CONFIRM= 'FAIL_CONFIRM';
    public $timestamps = false;




}
