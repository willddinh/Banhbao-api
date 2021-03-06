<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

 $app->withFacades();

class_alias('Illuminate\Support\Facades\Hash', 'Hash');
class_alias('Illuminate\Support\Facades\Config', 'Config');
class_alias('Illuminate\Support\Facades\Request', 'Request');
//class_alias('Illuminate\Support\Facades\Event', 'Event');


//class_alias('Tymon\JWTAuth\Facades\JWTAuth', 'JWTAuth');
//class_alias('Tymon\JWTAuth\Facades\JWTFactory', 'JWTFactory');

 $app->withEloquent();
 $app->configure('jwt');
 $app->configure('common');
 $app->configure('fully');
$app->configure('elasticsearch');
$app->configure('payment');
//$app->configure('mail');



//$app->alias('cache', 'Illuminate\Cache\CacheManager');
//$app->alias('auth', 'Illuminate\Auth\AuthManager');
/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//    App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);


$app->middleware([
    Vluzrmos\LumenCors\CorsMiddleware::class,
]);

//'Vluzrmos\LumenCors\CorsMiddleware'

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'appSession' =>App\Http\Middleware\CheckAppSessionMiddleware::class,
]);


/* $app->routeMiddleware([
     'jwt.auth'    => Tymon\JWTAuth\Middleware\GetUserFromToken::class,
     'jwt.refresh' => Tymon\JWTAuth\Middleware\RefreshToken::class,
 ]);*/

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);

 $app->register(App\Providers\EventServiceProvider::class);
//$app->register('Tymon\JWTAuth\Providers\JWTAuthServiceProvider');
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);

$app->register(App\Providers\RepositoryServiceProvider::class);
$app->register(Cviebrock\LaravelElasticsearch\LumenServiceProvider::class);

$app->register(App\Providers\InviteServiceProvider::class);
$app->register(App\Providers\PayCalculatorServiceProvider::class);





$app->register('Vluzrmos\Tinker\TinkerServiceProvider');
$app->register('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');

// dingo/api
$app->register(Dingo\Api\Provider\LumenServiceProvider::class);

app('Dingo\Api\Auth\Auth')->extend('jwt', function ($app) {
    return new Dingo\Api\Auth\Provider\JWT($app['Tymon\JWTAuth\JWTAuth']);
});

//Injecting auth
$app->singleton(Illuminate\Auth\AuthManager::class, function ($app) {
    return $app->make('auth');
});

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../app/Http/routes.php';
});


$app->configureMonologUsing(function(Monolog\Logger $monolog) {

    $handler = (new \Monolog\Handler\StreamHandler(storage_path('/logs/xyz.log')))
        ->setFormatter(new \Monolog\Formatter\LineFormatter(null, null, true, true));

    return $monolog->pushHandler($handler);
});


\DB::listen(function($sql) {
    Log::info($sql->sql);
    Log::info(implode("|",$sql->bindings));
    Log::info($sql->time);
});
return $app;
