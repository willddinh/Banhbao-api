<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBalance extends Model
{
    public $table = 'user_balance';
    protected $fillable = ['main_balance', 'secondary_balance', 'description', 'status'];
    protected $appends = ['url'];

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';
    

    protected $dates = ['deleted_at'];


    public function user(){
        return $this->belongsTo('App\Models\User');
    }


}
