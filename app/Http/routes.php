<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
/*$languages = LaravelLocalization::getSupportedLocales();
foreach ($languages as $language => $values) {
    $supportedLocales[] = $language;
}*/

$app->get('/', function () use ($app) {
    return $app->version();
});


$locale = Request::header("location");
if(!$locale)
    $locale = 'vi';

$tran = app('translator');
$tran->setlocale($locale);

$appSession = Request::header("app_session");


/*
if (in_array($locale, $supportedLocales)) {
    LaravelLocalization::setLocale($locale);
    App::setLocale($locale);
}*/
//$globalPath = 'api/';

//$app->post($globalPath.'auth/login', 'App\Http\Controllers\Auth\AuthController@postLogin');
//$app->post($globalPath.'user', 'App\Http\Controllers\UserController@getUser');

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->post('app/session', 'App\Http\Controllers\AppController@session');
    
    $api->post('search/book/autocomplete/{text}', 'App\Http\Controllers\SearchController@booksAutoComplete');
    $api->get('users', 'App\Http\Controllers\UserController@getUser');
    $api->post('signup', 'App\Http\Controllers\Auth\AuthController@signup');
    $api->post('facebook', 'App\Http\Controllers\Auth\AuthController@facebook');
    $api->post('login', 'App\Http\Controllers\Auth\AuthController@login');
    $api->post('token/refresh', 'App\Http\Controllers\Auth\AuthController@refreshToken');
    //for web ui
    
    $api->get('ui/menu/{group}','App\Http\Controllers\WebUIController@menu');
    $api->get('ui/slider','App\Http\Controllers\WebUIController@slider');

    $api->get('book/getBySubCat/{subCatId?}','App\Http\Controllers\BookController@getBySubCat');
    $api->get('book/getCategories','App\Http\Controllers\BookController@getCategories');
    $api->get('book/getPublishers','App\Http\Controllers\BookController@getPublisher');
    $api->get('book/getSubCats','App\Http\Controllers\BookController@getBookSubCats');
    $api->get('book/list','App\Http\Controllers\BookController@listBooks');
    $api->get('book/detail/{id}','App\Http\Controllers\BookController@detail');
    $api->get('payment/pay-list','App\Http\Controllers\PaymentController@payList');


    $api->post('cart/addCartItem','App\Http\Controllers\CartController@addCartItem');
    $api->post('cart/deleteCartItem','App\Http\Controllers\CartController@deleteCartItem');
    $api->post('cart/updateCartItem','App\Http\Controllers\CartController@updateCartItem');
    $api->post('cart/deleteCart','App\Http\Controllers\CartController@deleteCart');
    $api->get('cart/cartInfo','App\Http\Controllers\CartController@cartInfo');
    
    // need authentication
    $api->group(['middleware' => 'api.auth'], function ($api) {

        //dump
        $api->get('dump', [
            'as' => 'dump.index',
            'uses' => 'App\Http\Controllers\DumpController@index',
        ]);
        //payment

        $api->post('payment/pay', [
            'as' => 'payment.pay',
            'uses' => 'App\Http\Controllers\PaymentController@pay',
        ]);

        $api->post('payment/fakeConfirmPay', [
            'as' => 'payment.confirmPay',
            'uses' => 'App\Http\Controllers\PaymentController@fakeConfirmPay',
        ]);
        $api->post('payment/confirmPay', [
            'as' => 'payment.confirmPay',
            'uses' => 'App\Http\Controllers\PaymentController@confirmPay',
        ]);



        //order
        $api->post('order/shipping','App\Http\Controllers\OrderController@shipping');
        $api->post('order/createOrder','App\Http\Controllers\OrderController@createOrder');
        $api->post('order/payment','App\Http\Controllers\OrderController@payment');

        //user shipping address
        $api->post('address/new','App\Http\Controllers\OrderController@addShippingAddress');
        $api->post('address/update','App\Http\Controllers\OrderController@updateShippingAddress');
        $api->post('address/all','App\Http\Controllers\OrderController@getShippingAddreses');

        //user profile
        $api->post('balance/info', [
            'as' => 'balance.info',
            'uses' => 'App\Http\Controllers\BalanceController@info',
        ]);

        $api->post('profile/me', [
            'as' => 'balance.info',
            'uses' => 'App\Http\Controllers\ProfileController@me',
        ]);


    });
});
