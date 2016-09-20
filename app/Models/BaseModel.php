<?php

namespace App\Models;

use App\Events\Observer\UserAuditObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel.
 *
 * @author Sefa Karagöz <karagozsefa@gmail.com>
 */
class BaseModel extends Model
{

    public function hasAttribute($attr)
    {
        return array_key_exists($attr, $this->attributes);
    }

    protected static function boot()
    {
        parent::boot();
        self::observe(new UserAuditObserver());
    }
    
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {

            $query->where('title', 'LIKE', "%$search%")
                    ->where('lang', getLang())
                    ->orWhere('content', 'LIKE', "%$search%");
        });
    }
}
