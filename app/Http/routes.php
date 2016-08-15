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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->get('users', 'App\Http\Controllers\UserController@getUser');
    $api->post('signup', 'App\Http\Controllers\Auth\AuthController@postSignup');
    $api->post('register', 'App\Http\Controllers\Auth\AuthController@register');
    $api->post('login', 'App\Http\Controllers\Auth\AuthController@postLogin');
});

/*$app->group(['prefix' => 'api'], function () use ($app) {
    $app->get('users', 'App\Http\Controllers\UserController@getUser');
    $app->post('signup', 'App\Http\Controllers\Auth\AuthController@postSignup');
    $app->post('register', 'App\Http\Controllers\Auth\AuthController@register');
    $app->post('login', 'App\Http\Controllers\Auth\AuthController@postLogin');
});*/


/*$app->group(['prefix' => 'projects', 'middleware' => 'jwt.auth'], function($app) {
    $app->post('/', 'App\Http\Controllers\ProjectsController@store');
    $app->put('/{projectId}', 'App\Http\Controllers\ProjectsController@update');
    $app->delete('/{projectId}', 'App\Http\Controllers\ProjectsController@destroy');
});

$app->group(['prefix' => 'projects'], function ($app)
{
    $app->get('/', 'App\Http\Controllers\ProjectsController@index');
    $app->get('/{projectId}', 'App\Http\Controllers\ProjectsController@show');
});*/