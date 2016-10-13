<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSession extends Model
{
    public $table = 'app_session';
    protected $fillable = array('session', 'user_id');
    
}
