<?php

namespace App\Models;

use App\Events\Observer\UserAuditObserver;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends BaseModel
{
    use SoftDeletes;
    public $table = 'entity';
    protected $fillable = array('title', 'price', 'currency_unit');

    protected $dates = ['deleted_at'];

    const TYPE_BOOK = 'BOOK';
    const TYPE_FASHION = 'FASHION';
    


    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'entity_tags');
    }

    public function subCategories()
    {
        return $this->belongsToMany('App\Models\SubCategory', 'entity_categories');
    }

    public function book()
    {
        return $this->hasOne('App\Models\Book', 'entity_id', 'id');
    }

    public function getRentPrice(){
        if($this->type == Entity::TYPE_BOOK)
            return $this->book->rent_price;
    }


}
