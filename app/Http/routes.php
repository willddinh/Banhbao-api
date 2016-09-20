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
    $api->get('users', 'App\Http\Controllers\UserController@getUser');
    $api->post('signup', 'App\Http\Controllers\Auth\AuthController@signup');
    $api->post('login', 'App\Http\Controllers\Auth\AuthController@login');
    $api->post('token/refresh', 'App\Http\Controllers\Auth\AuthController@refreshToken');
    //for web ui
    
    $api->get('ui/menu/{group}','App\Http\Controllers\WebUIController@menu');
    
    // need authentication
    $api->group(['middleware' => 'api.auth'], function ($api) {

        //dump
        $api->get('dump', [
            'as' => 'dump.index',
            'uses' => 'App\Http\Controllers\DumpController@index',
        ]);
        
        
    });
});
