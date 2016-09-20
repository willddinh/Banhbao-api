<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Cviebrock\EloquentSluggable\SluggableInterface;
use App\Interfaces\ModelInterface as ModelInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publisher extends BaseModel implements ModelInterface , SluggableInterface
{
    use SluggableTrait;
    use SoftDeletes;
    public $table = 'publishers';
    protected $fillable = array('name');
    protected $appends = ['url'];
    protected $dates = ['deleted_at'];
    protected $sluggable = array(
        'build_from' => 'name',
        'save_to' => 'slug',
    );

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
        return '/publisher/'.$this->attributes['slug'];
    }
}
