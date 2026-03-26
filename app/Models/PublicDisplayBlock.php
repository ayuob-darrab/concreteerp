<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PublicDisplayBlock extends Model
{
    protected $table = 'public_display_blocks';

    protected $fillable = [
        'page_key',
        'block_type',
        'title',
        'body',
        'list_items',
        'icon_fa',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'list_items' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeForPage($query, string $pageKey)
    {
        return $query->where('page_key', $pageKey)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * عنوان للعرض في لوحة التحكم: الحقل title أو مقتطف من النص أو نوع الكتلة.
     *
     * @param  array<string, string>  $typeOptions
     */
    public function adminCardTitle(array $typeOptions): string
    {
        if (filled($this->title)) {
            return $this->title;
        }

        $typeLabel = $typeOptions[$this->block_type] ?? $this->block_type;

        if (filled($this->body)) {
            $oneLine = preg_replace('/\s+/u', ' ', trim($this->body));

            return Str::limit($oneLine, 70, '…');
        }

        if ($this->list_items && count($this->list_items)) {
            return $typeLabel . ' — قائمة نقاط';
        }

        return $typeLabel . ' — #' . $this->sort_order;
    }
}
