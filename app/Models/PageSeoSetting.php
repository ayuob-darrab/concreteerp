<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageSeoSetting extends Model
{
    use HasFactory;

    protected $table = 'page_seo_settings';

    protected $fillable = [
        'page_key',
        'page_title',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'canonical_url',
        'schema_markup',
        'sitemap_priority',
        'sitemap_changefreq',
    ];

    protected $casts = [
        'sitemap_priority' => 'decimal:1',
    ];

    public static function getByPageKey(string $pageKey): ?self
    {
        return Cache::remember("page_seo_{$pageKey}", 3600, function () use ($pageKey) {
            return self::where('page_key', $pageKey)->first();
        });
    }

    public static function getAllPages()
    {
        return Cache::remember('page_seo_all', 3600, function () {
            return self::all()->keyBy('page_key');
        });
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            Cache::forget("page_seo_{$model->page_key}");
            Cache::forget('page_seo_all');
        });
        static::deleted(function ($model) {
            Cache::forget("page_seo_{$model->page_key}");
            Cache::forget('page_seo_all');
        });
    }

    public function getSchemaMarkupDecodedAttribute(): ?array
    {
        if (empty($this->schema_markup)) {
            return null;
        }
        return json_decode($this->schema_markup, true);
    }
}
