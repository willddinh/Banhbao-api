<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Auth\AuthManager;
use Illuminate\Translation\Translator;
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

        $tran = app('translator');
        $currentLocale= $tran->getlocale();
        
        return $this->respond(compact('user', 'currentLocale'));
    }
}
