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

$app->get('/', function () use ($app) {
    return $app->version();
});
//$globalPath = 'api/';

//$app->post($globalPath.'auth/login', 'App\Http\Controllers\Auth\AuthController@postLogin');
//$app->post($globalPath.'user', 'App\Http\Controllers\UserController@getUser');

$app->group(['prefix' => 'api'], function () use ($app) {
    $app->get('users', 'App\Http\Controllers\UserController@getUser');
    $app->post('signup', 'App\Http\Controllers\Auth\AuthController@postSignup');
});