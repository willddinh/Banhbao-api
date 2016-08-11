<?php
/**
 * Created by PhpStorm.
 * User: DuongLT
 * Date: 8/11/2016
 * Time: 1:42 PM
 */

namespace App\Http\Controllers;

use App\User;

class UserController extends Controller
{

    use ApiControllerTrait;
    public function getUser(){
        $users = User::all();
        return $this->respond($users);
    }
    
}