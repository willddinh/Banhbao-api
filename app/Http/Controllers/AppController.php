<?php

namespace App\Http\Controllers;

use App;
use App\Services\App\AppService;
use Illuminate\Auth\AuthManager;
use Illuminate\Translation\Translator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class AppController extends BaseController
{
    use ApiControllerTrait;

    protected $auth;
    
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }


    public function session(){
        $session = AppService::genAppSession();
        return $this->respond(compact('session'));
    }
}
