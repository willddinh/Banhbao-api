<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Cviebrock\EloquentSluggable\SluggableInterface;
use App\Interfaces\ModelInterface as ModelInterface;

/**
 * Class TreeCategory.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class TreeCategory extends BaseModel implements ModelInterface, SluggableInterface
{
    use SluggableTrait;

    public $table = 'tree_categories';
    public $timestamps = false;
    protected $fillable = ['code', 'name','group_code', 'description'];
    protected $appends = ['url'];

    protected $sluggable = array(
        'build_from' => 'title',
        'save_to' => 'slug',
    );

    /*public function articles()
    {
        return $this->hasMany('App\Models\Article');
    }*/

    /*public function products()
    {
        return $this->hasMany('App\Models\Product');
    }*/

    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = $value;
    }

    public function getUrlAttribute()
    {
        return 'category/'.$this->attributes['slug'];
    }
}
