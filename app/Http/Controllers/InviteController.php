<?php

namespace App\Http\Controllers;

use App;
use App\Services\Invite\InviteServiceInterface;
use Illuminate\Auth\AuthManager;
use Illuminate\Translation\Translator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class InviteController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    protected $inviteService;
    
    public function __construct(AuthManager $auth, InviteServiceInterface $inviteService)
    {
        $this->auth = $auth;
        $this->inviteService = $inviteService;
    }


    public function invite(Request $request){
        $email = $request->get('email');
        $this->inviteService->invite($email);
        
        return $this->respond(['message'=>'ok']);
    }
}
