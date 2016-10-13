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
        $merchantTransaction->trans_status = $requestPayment['status'];
        $merchantTransaction->status = MerchantTransaction::STATUS_PENDING;
        $merchantTransaction->trans_ref = $requestPayment['trans_ref'];
        $merchantTransaction->save();
        $pay_url = $requestPayment['pay_url'];

        return $this->respond(compact('pay_url'));
    }

    public function fakeConfirmPay(Request $request){
        $user = $this->auth->user();
        $confirmPayment = json_decode('{"amount":10000,"trans_status":"close","response_time": "2014-12-31T00:52:12Z","response_message":"Giao dịch thành công","response_code":"00","order_info":"test dich vu","order_id":"001","trans_ref":"44df289349c74a7d9690ad27ed217094", "request_time":"2014-12-31T00:50:11Z","order_type":"ND"}', true);
        // Ex: 
        $response_code = $confirmPayment['response_code'];

        //update user_balance
        $userBalance = UserBalance::query()->where('user_id', $user->id)->first();
        if(!$userBalance )
            throw new SystemException("Can not found user balance");
        $userBalance->main_balance = $userBalance->main_balance + $confirmPayment['amount'];
        $userBalance->save();
        unset($confirmPayment['trans_ref']);
        unset($confirmPayment['request_time']);
        unset($confirmPayment['order_type']);


        $balance = UserBalance::query()->where('user_id', $user->id)->select('main_balance', 'secondary_balance', 'status')->first();
        if(!$balance)
            throw new SystemException("User has no balance");
        $result = $balance->getAttributes();

        return $this->respond(['message'=>'success',
            'paymen_info'=>$confirmPayment,
            'balance_info'=>$result]);
    }

    public function confirmPay(Request $request){
        $user = $this->auth->user();
//        $tran = app('translator');
//        $currentLocale= $tran->getlocale();
        $response_code = $request->get('response_code');
        $response_message = $request->get('response_message');
        $trans_ref = $request->get('trans_ref');
        $merchantTransaction = MerchantTransaction::query()->where('trans_ref', $trans_ref)->first();

        if($merchantTransaction->status != MerchantTransaction::STATUS_PENDING)
            throw new SystemException("Transaction status not valid");

        if(!$merchantTransaction)
            throw new SystemException("Can not found merchant transaction");
        $userTransaction = UserTransaction::query()->find($merchantTransaction->order_id);

        if($userTransaction->status != UserTransaction::STATUS_PENDING)
            throw new SystemException("Transaction status not valid");
        if(!$userTransaction)
            throw new SystemException("Can not found user transaction");

        if('00' != $response_code){
            $merchantTransaction->status = MerchantTransaction::STATUS_FAIL;
            $merchantTransaction->response_code = $response_code;
            $merchantTransaction->response_message = $response_message;
            $merchantTransaction->save();

            $userTransaction->status = UserTransaction::STATUS_FAIL;
            $userTransaction->save();
            throw new BusinessException("Error:".$response_message);
        }
            
        
        $amount = $request->get('amount');
        $card_name = $request->get('card_name');
        $card_type = $request->get('card_type');
        $order_id = $request->get('order_id');
        $order_type = $request->get('order_type');
        $request_time = $request->get('request_time');
        
        $response_time = $request->get('response_time');
        $trans_ref = $request->get('trans_ref');
        $trans_status = $request->get('trans_status');

        
//        $merchantTransaction->status = MerchantTransaction::STATUS_PENDING;
        $merchantTransaction->transaction_amount = $amount;
        $merchantTransaction->card_name = $card_name;
        $merchantTransaction->card_type = $card_type;
        $merchantTransaction->order_type = $order_type;
        $merchantTransaction->request_time = $request_time;
        $merchantTransaction->response_code = $response_code;
        $merchantTransaction->response_message = $response_message;
        $merchantTransaction->response_time = $response_time;
        $merchantTransaction->trans_status = $trans_status;
        $merchantTransaction->save();
        
        $confirmPayment = OnePayGate::confirmPayment($trans_ref,$response_code);

        // Ex: {"amount":10000,"trans_status":"close","response_time": "2014-12-31T00:52:12Z","response_message":"Giao dịch thành công","response_code":"00","order_info":"test dich vu","order_id":"001","trans_ref":"44df289349c74a7d9690ad27ed217094", "request_time":"2014-12-31T00:50:11Z","order_type":"ND"}
        $response_code = $confirmPayment['response_code'];
        if('00' != $response_code){
            $merchantTransaction->status = MerchantTransaction::STATUS_FAIL_CONFIRM;
            $merchantTransaction->response_code = $response_code;
            $merchantTransaction->response_message = $response_message;
            $merchantTransaction->save();

            $userTransaction->status = UserTransaction::STATUS_FAIL_CONFIRM;
            $userTransaction->save();
            throw new BusinessException("Error:".$response_message);
        }

        $merchantTransaction->status = MerchantTransaction::STATUS_SUCCESS;
        $merchantTransaction->save();

        
        $userTransaction->status = UserTransaction::STATUS_VALID;
        $userTransaction->save();

        //update user_balance
        $userBalance = UserBalance::query()->where('user_id', $userTransaction->user_id)->first();
        if(!$userBalance )
            throw new SystemException("Can not found user balance");
        $userBalance->main_balance = $userBalance->main_balance + $userTransaction->total;
        $userBalance->save();
        unset($confirmPayment['trans_ref']);
        unset($confirmPayment['request_time']);
        unset($confirmPayment['order_type']);


        $balance = UserBalance::query()->where('user_id', $user->id)->select('main_balance', 'secondary_balance', 'status')->first();
        if(!$balance)
            throw new SystemException("User has no balance");
        $result = $balance->getAttributes();

        return $this->respond(['message'=>'success',
            'paymen_info'=>$confirmPayment,
            'balance_info'=>$result]);
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
