<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class User extends Model
{
    public $table = 'users';

    public function balance()
    {
        return $this->hasOne('App\Models\UserBalance');
    }
}
