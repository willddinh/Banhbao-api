<?php

namespace App\Http\Controllers;

use App;
use App\Exceptions\BusinessException;
use App\Exceptions\SystemException;
use App\Models\MerchantTransaction;
use App\Models\Order;
use App\Models\UserAddress;
use App\Models\UserBalance;
use App\Models\UserTransaction;
use App\Services\Payment\OnePayGate;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Translation\Translator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ProfileController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }


    public function me(){
        $user = $this->auth->user();
        $myBalance = UserBalance::query()->where('user_id', $user->id)->select('main_balance', 'secondary_balance', 'deposit', 'status')->first();
        if(!$myBalance)
            throw new SystemException("User has no balance");
        $balance = $myBalance->getAttributes();
        $addresses = UserAddress::query()->where('user_id', $user->id);

        return $this->respond(compact('balance', 'user', 'addresses'));
    }

    public function myOrders(){
        
        $user = $this->auth->user();
        $orders = Order::query()->with('items')->where('user_id', $user->id)
            ->where('status', Order::STATUS_PAYED);
        return $this->respond(compact('orders'));
        
    }


}
