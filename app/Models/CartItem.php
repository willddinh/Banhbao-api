<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    public $table = 'cart_items';


    public function cart()
    {
        return $this->belongsTo('App\Models\Cart');
    }

    public function entity()
    {
        return $this->belongsTo('App\Models\Entity', 'product_id', 'id');
    }

}
