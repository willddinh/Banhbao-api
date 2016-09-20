<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Slider.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class Slider extends BaseModel
{
    public $table = 'sliders';

    public function images()
    {
        return $this->morphMany('App\Models\Photo', 'relationship', 'type');
    }
}
