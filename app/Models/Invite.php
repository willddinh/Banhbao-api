<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    public $table = 'invites';
    protected $fillable = array('host_id', 'email', 'invite_url', 'status', 'guest_id');
    
    const STATUS_INIT = 'INIT';
    const STATUS_CLICKED = 'CLICKED';
    const STATUS_REGISTERED = 'REGISTERED';
    
    public $timestamps = true;
    
    
}
