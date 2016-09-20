<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\SluggableTrait;
use Cviebrock\EloquentSluggable\SluggableInterface;
use App\Interfaces\ModelInterface as ModelInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends BaseModel implements ModelInterface , SluggableInterface
{
    use SluggableTrait;
    use SoftDeletes;
    public $table = 'authors';
    protected $fillable = array('name');
    protected $appends = ['url'];

    protected $sluggable = array(
        'build_from' => 'name',
        'save_to' => 'slug',
    );

    

    protected $dates = ['deleted_at'];

    public function books()
    {
        return $this->hasMany('App\Models\Book');
    }

    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = $value;
    }

    public function getUrlAttribute()
    {
        return '/author/'.$this->attributes['slug'];
    }
}
