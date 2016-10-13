<?php

namespace App\Http\Controllers;

use App;
use App\Exceptions\BusinessException;
use App\Exceptions\SystemException;
use App\Models\Book;
use App\Models\Entity;
use App\Models\MerchantTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserBalance;
use App\Models\UserTransaction;
use App\Services\Payment\OnePayGate;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Translation\Translator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class CartController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    
    public function __construct(AuthManager $auth)
    {

        $this->auth = $auth;
    }


    public function addOrderItem(Request $request){

        $productId = $request->get('productId');
        $entity = Entity::query()->find($productId);

        if(!$entity)
            throw new BusinessException("productId not valid");

//        $product = $this->resolveProduct($entity);

        $user = $this->auth->user();
        $appSession = $request->header("app-session");
        if($user){
            $order = Order::query()->with('items')->where('user_id', $user->id)
                ->where('status', Order::STATUS_INIT)
                ->first();
            if($order){
                $items = $order->items;
                foreach ($items as $item ){
                    if($item->product_id == $productId)
                        return  $this->respond(['orderId'=>$order->id]);
                }

            }else{
                $order = new Order();
                $order->user_id = $user->id;
                $order->status = Order::STATUS_INIT;
                $order->save();
            }

           
        }else{
            $order = Order::query()->with('items')->where('app_session',$appSession)
                ->where('status', Order::STATUS_INIT)
                ->first();

            if($order){
                $items = $order->items;
                foreach ($items as $item ){
                    if($item->product_id == $productId)
                       return $this->respond(['orderId'=>$order->id]);
                }

            }else{
                $order = new Order();
                $order->app_session = $appSession;
                $order->status = Order::STATUS_INIT;
                $order->save();
            }

        }
        $this->addItemToOrder($order, $productId, $entity);
        return $this->respond(['orderId'=>$order->id]);
    }

    private function addItemToOrder($order, $productId, $entity){
        $orderItem = new OrderItem();
        $orderItem->order_id = $order->id;
        $orderItem->product_id = $productId;
        $orderItem->product_name = $entity->title;
        $orderItem->price = $entity->getRentPrice();
        $orderItem->quantity = 1;
        $orderItem->save();
        return $orderItem;
    }

    private function resolveProduct($entity){
        $type = $entity->type;
        if(Entity::TYPE_BOOK == $type){
            return Book::query()->find($entity->id);
        }
    }


}
