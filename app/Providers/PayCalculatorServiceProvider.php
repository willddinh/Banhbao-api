<?php

namespace App\Providers;

use App\Services\Invite\InviteServiceImpl;
use App\Services\Payment\PayCalculatorServiceImpl;
use Illuminate\Support\ServiceProvider;

class PayCalculatorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $app = $this->app;
        $app->bind('App\Services\Payment\PayCalculatorInterface', function ($app) {
            $auth = $app->make('auth');
            $service = new PayCalculatorServiceImpl($auth);

            return $service;
        });

    }
}
