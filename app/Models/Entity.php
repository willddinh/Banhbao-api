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

    


    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'entity_tags');
    }

    public function subCategories()
    {
        return $this->belongsToMany('App\Models\SubCategory', 'entity_categories');
    }

    


}
