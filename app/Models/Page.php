<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;
use App\Services\CacheService;

class Page extends Model
{
    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $page_translation = $this->hasMany(PageTranslation::class)->where('lang', $lang)->first();
        return $page_translation != null ? $page_translation->$field : $this->$field;
    }

    protected $fillable = [
        'type',
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'keywords',
        'meta_image'
    ];

    // Boot the model to add event listeners
    public static function boot()
    {
        parent::boot();

        // Clear cache when a page is created, updated or deleted
        static::created(function ($model) {
            CacheService::clearPageCache();
            CacheService::clearPolicyCache();
        });

        static::updated(function ($model) {
            CacheService::clearPageCache();
            CacheService::clearPolicyCache();
        });

        static::deleted(function ($model) {
            CacheService::clearPageCache();
            CacheService::clearPolicyCache();
        });
    }

    public function page_translations()
    {
        return $this->hasMany(PageTranslation::class);
    }
}