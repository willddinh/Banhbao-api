<?php

namespace App\Http\Controllers;

use App;
use App\Models\MerchantTransaction;
use App\Models\UserTransaction;
use App\Services\Payment\OnePayGate;
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
        $validateInput = $this->validateInput($packageCode, $amount);
        if($validateInput['status'] != 'success')
            return $this->errorWithStatus($validateInput, [], 500);


        // create user_transaction and some
        $package = $this->resolvePackageCode($packageCode);
        $userTransaction = new UserTransaction();
        $userTransaction->user_id = $user->id;
        $userTransaction->debt_account = $package['code'];
        $userTransaction->creditor_account = '112.1';
        $userTransaction->quantity = 1;
        $userTransaction->price = $amount;
        $userTransaction->total = $amount;
        $userTransaction->product_name = $package['product_name'];
        $userTransaction->product_id = $package['code'];
        $userTransaction->status = UserTransaction::STATUS_PENDING;
        $userTransaction->save();

        $requestPayment = OnePayGate::requestPayment($amount,
            $userTransaction->product_name, $userTransaction->id);
//        {"pay_url":"http://api.1pay.vn/bank-charging/sml/nd/order?token=LuNIFOeClp9d8SI7XWNG7O%2BvM8GsLAO%2BAHWJVsaF0%3D", "status":"init", "trans_ref":"16aa72d82f1940144b533e788a6bcb6"}
        $merchantTransaction = new MerchantTransaction();
        $merchantTransaction->provider = '1pay';
        $merchantTransaction->user_id = $user->id;
        $merchantTransaction->init_amount = $amount;
        $merchantTransaction->order_id = $userTransaction->id;
        $merchantTransaction->order_info = $package['product_name'];
        $merchantTransaction->status = $requestPayment['status'];
        $merchantTransaction->trans_ref = $requestPayment['trans_ref'];
        $merchantTransaction->save();
        $pay_url = $requestPayment['pay_url'];

        return $this->respond(compact('pay_url'));
    }

    public function payList(){
        $tran = app('translator');
        $currentLocale= $tran->getlocale();

        $config = Config::get('payment');
        $payPackages  = $config['pay_list']['pay_packages'];
        return $this->respond(compact('payPackages'));
    }

    private function validateInput($packageCode, $amount)
    {
        $package = $this->resolvePackageCode($packageCode);
        if(!$package)
            return ['status'=>'error', 'message'=>'pakage not found'];
        if($package['amount'] != $amount)
            return ['status'=>'error', 'message'=>'amount invalid'];
        return ['status' => 'success'];

    }

    private function resolvePackageCode($packageCode)
    {
        $config = Config::get('payment');
        $payPackages = $config['pay_list']['pay_packages'];
        foreach ($payPackages as $aPackage){
            if($aPackage['code'] == $packageCode)
                return $aPackage;
        }
    }
}
