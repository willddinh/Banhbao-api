<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    public $table = 'user_addresses';
    protected $fillable = ['user_id', 'type', 'address', 'province', 'district', 'is_main', 'full_name', 'phone'];
    const TYPE_HOME = 'home';
    const TYPE_OFFICE = 'office';

    public function user(){
        return $this->belongsTo('App\Models\User');
    }


}
