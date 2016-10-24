<?php

namespace App\Services\Invite;


use App\Exceptions\ConnectPayGateException;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Log;

class InviteServiceImpl implements InviteServiceInterface
{


    protected $auth;

    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }
    public function invite($email)
    {
        $user = $this->auth->user();
        Log::info($user->username.' invite '.$email);
        // TODO: Implement invite() method.
    }

    public function markClick($email)
    {
        // TODO: Implement markClick() method.
    }

    public function finish($email)
    {
        // TODO: Implement finish() method.
    }
}
