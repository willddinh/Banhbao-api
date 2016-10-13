<?php

namespace App\Services\App;


use App\Models\AppSession;
use Carbon\Carbon;
use Hash;

class AppService
{

    static function  genAppSession()
    {
        $session = Hash::make(Carbon::now()->toTimeString().self::generateRandomString());
        $appSession = new AppSession();
        $appSession->session = $session;
        $appSession->save();
        return $session;
    }

    static function  relateSessionToUser()
    {

    }

    static function  generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
