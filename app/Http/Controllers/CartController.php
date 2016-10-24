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
use App\Models\UserBalance;
use App\Models\UserTransaction;
use App\Services\Payment\OnePayGate;
use App\Services\Payment\PayCalculatorInterface;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Translation\Translator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class CartController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    protected $payCalculator;
    
    public function __construct(AuthManager $auth, PayCalculatorInterface $payCalculator)
    {

        $this->auth = $auth;
        $this->payCalculator = $payCalculator;
    }

    public function cartInfo(Request $request){
        $user = $this->auth->user();
        $appSession = $request->header("app-session");
        if($user){
            $cart = Cart::query()->with('items.entity.book')->where('user_id', $user->id)
                ->where('status', Cart::STATUS_INIT)
                ->first();
            if(!$cart)
                throw new BusinessException("Not found");
            
           
        }else{
            $cart = Cart::query()->with('items.entity.book')->where('app_session', $appSession)
                ->where('status', Cart::STATUS_INIT)
                ->first();
            if(!$cart)
                throw new BusinessException("Not found");

        }
        $calculateCart = $this->payCalculator->calculateCart($cart);
        return  $this->respond(compact('cart', 'calculateCart'));
    }


    public function deleteCart(Request $request){
        $cartId = $request->get('cartId');

        $user = $this->auth->user();
        $appSession = $request->header("app-session");

        if($user){
            $cart = Cart::query()->find($cartId);
            if($cart->user_id != $user->id)
                throw new BusinessException("invalid action");
            if($cart){
                $cart->delete();
            }else
                throw new BusinessException("user has no cart");


        }else{
            $cart = Cart::query()->find($cartId);
            if($cart->app_session != $appSession)
                throw new BusinessException("invalid action");

            if($cart){
                $cart->delete();
            }else
                throw new BusinessException("user has no cart");

        }

        return $this->respond(['message'=>'ok']);

    }


    public function deleteCartItem(Request $request){
        $cartId = $request->get('cartId');
        $productId = $request->get('productId');
        $entity = Entity::query()->find($productId);

        if(!$entity)
            throw new BusinessException("productId not valid");
        $user = $this->auth->user();
        $appSession = $request->header("app-session");

        if($user){
            $cart = Cart::query()->with('items')->find($cartId);
            if($cart->user_id != $user->id)
                throw new BusinessException("invalid action");
            if($cart){
                $items = $cart->items;
                foreach ($items as $item ){
                    if($item->product_id == $productId){
                        $cartItem =  CartItem::query()->find($productId);
                        $cartItem->delete();
                        return  $this->respond(['cartId'=>$cart->id]);
                    }

                }

            }else
                throw new BusinessException("user has no cart");


        }else{
            $cart = Cart::query()->with('items')->find($cartId);
            if($cart->app_session != $appSession)
                throw new BusinessException("invalid action");

            if($cart){
                $items = $cart->items;
                foreach ($items as $item ){
                    if($item->product_id == $productId){
                        $cartItem =  CartItem::query()->find($item->id);
                        $cartItem->delete();
                        return  $this->respond(['cartId'=>$cart->id]);
                    }

                }

            }else
                throw new BusinessException("user has no cart");

        }

        return $this->respond(['cartId'=>$cart->id]);

    }


    public function updateCartItem(Request $request){
        $cartId = $request->get('cartId');
        $productId = $request->get('productId');
        $quantity = $request->get('quantity');
        if($quantity < 1)
            throw new BusinessException("quantity must be positive");
        $entity = Entity::query()->find($productId);

        if(!$entity)
            throw new BusinessException("productId not valid");
        $user = $this->auth->user();
        $appSession = $request->header("app-session");

        if($user){
            $cart = Cart::query()->with('items')->find($cartId);
            if($cart->user_id != $user->id)
                throw new BusinessException("invalid action");
            if($cart){
                $items = $cart->items;
                foreach ($items as $item ){
                    if($item->product_id == $productId){
                        $cartItem =  CartItem::query()->find($productId);
                        if($cartItem){
                            $cartItem->quantity = $quantity;
                            $cartItem->save();
                            return  $this->respond(['cartId'=>$cart->id]);
                        }else
                            throw new BusinessException("product not in cart");
                    }

                }

            }else
                throw new BusinessException("user has no cart");


        }else{
            $cart = Cart::query()->with('items')->find($cartId);
            if($cart->app_session != $appSession)
                throw new BusinessException("invalid action");

            if($cart){
                $items = $cart->items;
                foreach ($items as $item ){
                    if($item->product_id == $productId){
                        $cartItem =  CartItem::query()->find($productId);
                        if($cartItem){
                            $cartItem->quantity = $quantity;
                            $cartItem->save();
                            return  $this->respond(['cartId'=>$cart->id]);
                        }else
                            throw new BusinessException("product not in cart");

                    }

                }

            }else
                throw new BusinessException("user has no cart");

        }

        return $this->respond(['cartId'=>$cart->id]);

    }


    public function addCartItem(Request $request){

        $productId = $request->get('productId');
        $entity = Entity::query()->find($productId);

        if(!$entity)
            throw new BusinessException("productId not valid");

//        $product = $this->resolveProduct($entity);

        $user = $this->auth->user();
        $appSession = $request->header("app-session");
        if($user){
            $cart = Cart::query()->with('items')->where('user_id', $user->id)
                ->where('status', Cart::STATUS_INIT)
                ->first();
            if($cart){
                $items = $cart->items;
                foreach ($items as $item ){
                    if($item->product_id == $productId)
                        return  $this->respond(['cartId'=>$cart->id]);
                }

            }else{
                $cart = new Cart();
                $cart->user_id = $user->id;
                $cart->app_session = $appSession;
                $cart->status = Cart::STATUS_INIT;
                $cart->save();
            }

           
        }else{
            $cart = Cart::query()->with('items')->where('app_session',$appSession)
                ->where('status', Cart::STATUS_INIT)
                ->first();

            if($cart){
                $items = $cart->items;
                foreach ($items as $item ){
                    if($item->product_id == $productId)
                       return $this->respond(['cartId'=>$cart->id]);
                }

            }else{
                $cart = new Cart();
                $cart->app_session = $appSession;
//                $cart->user_id = $user->id;
                $cart->status = Cart::STATUS_INIT;
                $cart->save();
            }

        }
        $this->addItemToCart($cart, $productId, $entity);
        return $this->respond(['cartId'=>$cart->id]);
    }

    private function addItemToCart($cart, $productId, $entity){
        $cartItem = new CartItem();
        $cartItem->cart_id = $cart->id;
        $cartItem->product_id = $productId;
        $cartItem->product_name = $entity->title;
//        $cartItem->price = $entity->getRentPrice();
        $cartItem->quantity = 1;
        $cartItem->save();
        return $cartItem;
    }

    private function resolveProduct($entity){
        $type = $entity->type;
        if(Entity::TYPE_BOOK == $type){
            return Book::query()->find($entity->id);
        }
    }


}
