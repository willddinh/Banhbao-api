<?php

namespace App\Http\Controllers;

use Illuminate\Auth\AuthManager;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class DumpController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    public function __construct(AuthManager $auth)
    {

        $this->auth = $auth;
    }


    public function index(Request $request){
        $user = $this->auth->user();
        return $this->respond(compact('user'));
    }
}
