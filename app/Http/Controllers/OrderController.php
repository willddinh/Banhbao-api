<?php

namespace App\Http\Controllers;

use App;
use App\Exceptions\BusinessException;
use App\Exceptions\SystemException;
use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Entity;
use App\Models\MerchantTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use App\Models\UserBalance;
use App\Models\UserTransaction;
use App\Services\Payment\OnePayGate;
use Carbon\Carbon;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Translation\Translator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;

    public function __construct(AuthManager $auth)
    {

        $this->auth = $auth;
    }

    public function shipping(Request $request){
        $user = $this->auth->user();
        $appSession = $request->header("app-session");
        $cart = Cart::query()->with('items.entity')->where('user_id', $user->id)
            ->where('app-session', $appSession)
            ->where('status', Cart::STATUS_INIT)
            ->first();
        if(!$cart)
            throw new BusinessException("Not found");
        $shippingAddressId = $request->get('shippingAddressId');
        $cart->shipping_address_id = $shippingAddressId;


        return  $this->respond(['message'=>'ok']);
    }

    public function createOrder(Request $request){
        $user = $this->auth->user();
        $appSession = $request->header("app-session");
        $cart = Cart::query()->with('items.entity')->where('user_id', $user->id)
            ->where('app-session', $appSession)
            ->where('status', Cart::STATUS_INIT)
            ->first();
        if(!$cart)
            throw new BusinessException("Not found cart");
        if(!$cart->items){
            $cart->delete();
            throw new BusinessException("Not found cart item");
        }


        //clear my unused order
        Order::query()->where('user_id', $user->id)
            ->where('status', Order::STATUS_INIT)
            ->delete();
        //create order from cart and address
        $order = new Order();
        $order->app_session = $appSession;
        $order->user_id = $user->id;
        $order->status = Order::STATUS_INIT;
        if($cart->shipping_address_id)
            $order->shipping_id = $cart->shipping_address_id;
        else{
            $userAddress = UserAddress::query()->where('user_id', $user->id)
                ->where('is_main', 1)->first();
            if($userAddress)
                $order->shipping_id = $userAddress->id;
            else
                throw new BusinessException("Not found shipping address");
        }
        $order->cart_id = $cart->id;
        //@todo calculate shipping fee
        $order->shipping_fee = 15000;

        $order->save();
        //transform cart item to order item
        foreach ($cart->items as $cartItem){
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $cartItem->product_id;
            $orderItem->product_name = $cartItem->product_name;
            $orderItem->rent_price = $cartItem->entity->getRentPrice();
            $orderItem->price  = $cartItem->entity->price;
            $orderItem->quantity = $cartItem->quantity;
            $orderItem->save();
        }

        $order = Order::query()->with('items')->find($order->id);

        return  $this->respond(compact('order'));
    }

    public function payment(Request $request){
        $user = $this->auth->user();
        $appSession = $request->header("app-session");
        $orderId = $request->get("orderId");
        $order = Order::query()->with('items')->find($orderId);
        if(!$order)
            throw new BusinessException("order not exist");
        if($order->user_id != $user->id)
            throw new BusinessException("order not valid");
        //check user balance
        $userBalance = UserBalance::query()->where('user_id', $user->id)->first();
        if(!$userBalance)
            throw new BusinessException("user balance not exist");
        $total = $order->getTotal();
        if($total < $userBalance->main_balance)
            throw new BusinessException("your balance is not enought to pay");
        $deposit = $order->getDeposit();

        $rentFee = $order->getRentFee();
        
        //make payment
        
        // phi dich vu
        $userTransaction = new UserTransaction();

        $userTransaction->order_id = $order->id;
        $userTransaction->user_id = $user->id;
        $userTransaction->debt_account = "331.1";
        $userTransaction->creditor_account = "152.1";
        $userTransaction->total = $rentFee;
        $userTransaction->status = UserTransaction::STATUS_VALID;
        $userTransaction->save();

        // dat coc
        $userTransaction = new UserTransaction();

        $userTransaction->order_id = $order->id;
        $userTransaction->user_id = $user->id;
        $userTransaction->debt_account = "131.1";
        $userTransaction->creditor_account = "152.1";
        $userTransaction->total = $deposit;
        $userTransaction->status = UserTransaction::STATUS_VALID;
        $userTransaction->save();
        // update status
        $order->status = Order::STATUS_PAYED;
        $order->pay_at = Carbon::now()->toDateTimeString();
        $order->save();

        //update user balance
        $userBalance->deposit = $userBalance->deposit +  $deposit;
        $userBalance->service_balance = $userBalance->service_balance +  $rentFee;
        $userBalance->main_balance = $userBalance->main_balance - $deposit - $rentFee;
        $userBalance->save();

        return  $this->respond(['message'=>'ok']);
    }

//@todo validate user address
    public function addShippingAddress(Request $request){
        $user = $this->auth->user();
//        $appSession = $request->header("app-session");
        $fullName = $request->get("full_name");
        $province = $request->get("province");
        $district = $request->get("district");
        $address = $request->get("address");
        $type = $request->get("type");
        $phone = $request->get("phone");
        $isMain =  $request->get("is_main");

        $userAddress = new UserAddress();

        $userAddress->user_id =$user->id;
        $userAddress->full_name =$fullName;
        $userAddress->province =$province;
        $userAddress->district =$district;
        $userAddress->address =$address;
        $userAddress->type =$type;
        $userAddress->phone =$phone;
        $userAddress->is_main =$isMain;
        $userAddress->save();

        return  $this->respond(compact('userAddress'));
    }

    public function updateShippingAddress(Request $request){
        $user = $this->auth->user();
        
        $addressId = $request->get("addressId");
        $userAddress = UserAddress::query()->find($addressId);
        if($userAddress->user_id != $user->id)
            throw new BusinessException("Invalid address");
        
        $fullName = $request->get("full_name");
        $province = $request->get("province");
        $district = $request->get("district");
        $address = $request->get("address");
        $type = $request->get("type");
        $phone = $request->get("phone");
        $isMain =  $request->get("is_main");

        $userAddress->full_name =$fullName;
        $userAddress->province =$province;
        $userAddress->district =$district;
        $userAddress->address =$address;
        $userAddress->type =$type;
        $userAddress->phone =$phone;
        $userAddress->is_main =$isMain;
        $userAddress->save();

        return  $this->respond(compact('userAddress'));
    }


    public function getShippingAddreses(){
        $user = $this->auth->user();
        $shippingAddresses = UserAddress::query()->where('user_id', $user->id);

        return  $this->respond(compact('shippingAddresses'));
    }

}
