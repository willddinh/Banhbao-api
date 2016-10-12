<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTransaction extends Model
{
    const STATUS_PENDING = 'PENDING';
    const STATUS_VALID = 'VALID';
    const STATUS_FAIL = 'FAIL';
    const STATUS_FAIL_CONFIRM = 'FAIL_CONFIRM';
    public $table = 'user_transactions';
    public $timestamps = false;
    protected $fillable = ['user_id', 'order_id', 'debt_account',
        'creditor_account', 'quantity', 'price', 'total', 'transaction_group_id', 'detail',
    'product_id', 'product_name'];

}
