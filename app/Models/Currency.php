<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'symbol',
        'exchange_rate',
        'is_default',
        'is_active',
        'decimal_places',
        'rate_updated_at',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'rate_updated_at' => 'datetime',
    ];

    // ===== Scopes =====

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ===== Static Methods =====

    /**
     * Get the default currency
     */
    public static function getDefault(): ?Currency
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Get currency by code
     */
    public static function getByCode(string $code): ?Currency
    {
        return static::where('code', $code)->first();
    }

    /**
     * Convert amount from one currency to another
     */
    public static function convert(float $amount, string $fromCode, string $toCode): float
    {
        if ($fromCode === $toCode) {
            return $amount;
        }

        $fromCurrency = static::getByCode($fromCode);
        $toCurrency = static::getByCode($toCode);

        if (!$fromCurrency || !$toCurrency) {
            return $amount;
        }

        // Convert to default currency first, then to target
        $amountInDefault = $amount * $fromCurrency->exchange_rate;
        $amountInTarget = $amountInDefault / $toCurrency->exchange_rate;

        return round($amountInTarget, $toCurrency->decimal_places);
    }

    /**
     * Convert to default currency
     */
    public static function convertToDefault(float $amount, string $fromCode): float
    {
        $default = static::getDefault();
        if (!$default) {
            return $amount;
        }

        return static::convert($amount, $fromCode, $default->code);
    }

    /**
     * Format amount with currency symbol
     */
    public function format(float $amount): string
    {
        return number_format($amount, $this->decimal_places) . ' ' . $this->symbol;
    }

    // ===== Accessors =====

    /**
     * Get localized name
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }
}
