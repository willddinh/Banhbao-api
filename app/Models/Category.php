<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Cviebrock\EloquentSluggable\SluggableInterface;
use App\Interfaces\ModelInterface as ModelInterface;

/**
 * Class Category.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class Category extends Model implements ModelInterface, SluggableInterface
{
    use SluggableTrait;

    public $table = 'categories';
    public $timestamps = false;
    protected $fillable = ['title', 'group'];
    protected $appends = ['url'];

    protected $sluggable = array(
        'build_from' => 'title',
        'save_to' => 'slug',
    );

    public function articles()
    {
        return $this->hasMany('App\Models\Article');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

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
        return $this->group.'/'.$this->attributes['slug'];
    }
}
