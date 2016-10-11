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
        
        
    });
});
