<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Cviebrock\EloquentSluggable\SluggableInterface;
use App\Interfaces\ModelInterface as ModelInterface;

class BookRate extends BaseModel
{

    public $table = 'book_rates';


    protected $fillable = ['comment', 'star'];

    public function book()
    {
        return $this->belongsTo("App\Models\Book");
    }

}
