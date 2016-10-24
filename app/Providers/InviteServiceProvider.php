<?php

namespace App\Providers;

use App\Services\Invite\InviteServiceImpl;
use Illuminate\Support\ServiceProvider;

class InviteServiceProvider extends ServiceProvider
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
        $app->bind('App\Services\Invite\InviteServiceInterface', function ($app) {
            $auth = $app->make('auth');
            $inviteService = new InviteServiceImpl($auth);

            return $inviteService;
        });

    }
}
