<?php

namespace App\Models;

use App\Interfaces\ModelInterface;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model implements ModelInterface, SluggableInterface
{

    use SluggableTrait;
    public $table = 'sub_categories';
    public $timestamps = false;
    protected $fillable = ['title', 'group'];
    protected $appends = ['url'];

    protected $sluggable = array(
        'build_from' => 'title',
        'save_to' => 'slug',
    );

    public function entities()
    {
        return $this->belongsToMany('App\Models\Entity');
    }

    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = $value;
    }

    public function getUrlAttribute()
    {
        return 'category/'.$this->attributes['slug'];
    }

}
