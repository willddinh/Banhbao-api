<?php

namespace App\Http\Controllers;

use App;
use App\Exceptions\BusinessException;
use App\Exceptions\SystemException;
use App\Models\MerchantTransaction;
use App\Models\UserBalance;
use App\Models\UserTransaction;
use App\Services\Payment\OnePayGate;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Translation\Translator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class BalanceController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }


    public function info(){
        $user = $this->auth->user();
        $tran = app('translator');
//        $currentLocale= $tran->getlocale();
        $balance = UserBalance::query()->where('user_id', $user->id)->select('main_balance', 'secondary_balance', 'status')->first();
        if(!$balance)
            throw new SystemException("User has no balance");
        $result = $balance->getAttributes();

        return $this->respond(compact('result'));
    }


}
