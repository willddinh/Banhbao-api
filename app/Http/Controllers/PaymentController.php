<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Translation\Translator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class PaymentController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    
    public function __construct(AuthManager $auth)
    {

        $this->auth = $auth;
    }


    public function pay(Request $request){
        $user = $this->auth->user();
        $tran = app('translator');
        $currentLocale= $tran->getlocale();

        $packageCode = $request->get('packageCode');
        $amount = $request->get('amount');
        
        
        return $this->respond(compact('user', 'currentLocale'));
    }

    public function payList(){
        $tran = app('translator');
        $currentLocale= $tran->getlocale();

        $config = Config::get('payment');
        $payPackages  = $config['pay_list']['pay_packages'];
        return $this->respond(compact('payPackages'));
    }
}
