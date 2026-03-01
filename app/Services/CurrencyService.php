<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    /**
     * Get exchange rate between two currencies
     */
    public function getRate(string $fromCode, string $toCode): float
    {
        if ($fromCode === $toCode) {
            return 1;
        }

        $cacheKey = "exchange_rate_{$fromCode}_{$toCode}";

        return Cache::remember($cacheKey, 3600, function () use ($fromCode, $toCode) {
            $from = Currency::where('code', $fromCode)->first();
            $to = Currency::where('code', $toCode)->first();

            if (!$from || !$to) {
                return 1;
            }

            // Rate = to_rate / from_rate
            return $to->exchange_rate / $from->exchange_rate;
        });
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert(float $amount, string $fromCode, string $toCode): float
    {
        $rate = $this->getRate($fromCode, $toCode);
        $to = Currency::where('code', $toCode)->first();
        $decimals = $to ? $to->decimal_places : 2;

        return round($amount / $rate, $decimals);
    }

    /**
     * Convert to default currency (IQD)
     */
    public function convertToDefault(float $amount, string $fromCode): float
    {
        $default = Currency::getDefault();
        return $this->convert($amount, $fromCode, $default ? $default->code : 'IQD');
    }

    /**
     * Format amount with currency
     */
    public function format(float $amount, string $currencyCode): string
    {
        $currency = Currency::where('code', $currencyCode)->first();

        if (!$currency) {
            return number_format($amount, 2) . ' ' . $currencyCode;
        }

        return $currency->format($amount);
    }

    /**
     * Convert amount to words in Arabic
     */
    public function toWords(float $amount, string $currencyCode = 'IQD'): string
    {
        $currency = Currency::where('code', $currencyCode)->first();
        $currencyName = $currency ? $currency->name_ar : 'دينار';

        $amount = (int) $amount;

        if ($amount == 0) {
            return "صفر {$currencyName} فقط لا غير";
        }

        $ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'];
        $tens = ['', 'عشرة', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
        $teens = ['عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
        $hundreds = ['', 'مائة', 'مائتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];

        $words = [];

        // Handle billions
        if ($amount >= 1000000000) {
            $billions = (int) ($amount / 1000000000);
            if ($billions == 1) {
                $words[] = 'مليار';
            } elseif ($billions == 2) {
                $words[] = 'ملياران';
            } elseif ($billions <= 10) {
                $words[] = $ones[$billions] . ' مليارات';
            } else {
                $words[] = $this->convertNumberPart($billions, $ones, $tens, $teens, $hundreds) . ' مليار';
            }
            $amount %= 1000000000;
        }

        // Handle millions
        if ($amount >= 1000000) {
            $millions = (int) ($amount / 1000000);
            if ($millions == 1) {
                $words[] = 'مليون';
            } elseif ($millions == 2) {
                $words[] = 'مليونان';
            } elseif ($millions <= 10) {
                $words[] = $ones[$millions] . ' ملايين';
            } else {
                $words[] = $this->convertNumberPart($millions, $ones, $tens, $teens, $hundreds) . ' مليون';
            }
            $amount %= 1000000;
        }

        // Handle thousands
        if ($amount >= 1000) {
            $thousands = (int) ($amount / 1000);
            if ($thousands == 1) {
                $words[] = 'ألف';
            } elseif ($thousands == 2) {
                $words[] = 'ألفان';
            } elseif ($thousands <= 10) {
                $words[] = $ones[$thousands] . ' آلاف';
            } else {
                $words[] = $this->convertNumberPart($thousands, $ones, $tens, $teens, $hundreds) . ' ألف';
            }
            $amount %= 1000;
        }

        // Handle remaining
        if ($amount > 0) {
            $words[] = $this->convertNumberPart($amount, $ones, $tens, $teens, $hundreds);
        }

        return implode(' و', $words) . ' ' . $currencyName . ' فقط لا غير';
    }

    /**
     * Convert number part to Arabic words
     */
    private function convertNumberPart(int $number, array $ones, array $tens, array $teens, array $hundreds): string
    {
        $parts = [];

        if ($number >= 100) {
            $parts[] = $hundreds[(int) ($number / 100)];
            $number %= 100;
        }

        if ($number >= 20) {
            $t = (int) ($number / 10);
            $o = $number % 10;
            if ($o > 0) {
                $parts[] = $ones[$o] . ' و' . $tens[$t];
            } else {
                $parts[] = $tens[$t];
            }
        } elseif ($number >= 10) {
            $parts[] = $teens[$number - 10];
        } elseif ($number > 0) {
            $parts[] = $ones[$number];
        }

        return implode(' و', $parts);
    }

    /**
     * Update exchange rate
     */
    public function updateRate(string $code, float $rate): Currency
    {
        $currency = Currency::where('code', $code)->first();

        if (!$currency) {
            throw new \Exception("Currency {$code} not found");
        }

        $currency->update([
            'exchange_rate' => $rate,
            'rate_updated_at' => now(),
        ]);

        // Clear cache
        Cache::forget("exchange_rate_{$code}_IQD");
        Cache::forget("exchange_rate_IQD_{$code}");

        return $currency;
    }

    /**
     * Get all active currencies
     */
    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Currency::active()->get();
    }
}
