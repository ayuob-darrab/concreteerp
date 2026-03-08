<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoSetting extends Model
{
    use HasFactory;

    protected $table = 'seo_settings';

    protected $fillable = [
        'site_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_site',
        'canonical_domain',
        'robots',
        'locale',
        'locale_alternate',
        'extra_meta',
        'structured_data',
    ];

    /**
     * الحصول على إعدادات SEO الحالية (سطر واحد للنظام بالكامل)
     */
    public static function current()
    {
        return Cache::remember('seo_settings', 3600, function () {
            return self::first();
        });
    }

    /**
     * مسح كاش SEO بعد التحديث
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('seo_settings');
        });
        static::deleted(function () {
            Cache::forget('seo_settings');
        });
    }
}
