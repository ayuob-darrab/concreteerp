<?php

namespace App\Services;

use App\Models\Company;
use App\Models\PublicContactChannel;
use App\Models\PublicContactSettings;
use App\Models\PublicDisplayBlock;
use App\Models\PublicDisplayVideo;

class PublicDisplayPageService
{
    public const PAGE_KEYS = ['landing', 'system_benefits', 'features', 'about', 'contact'];

    public function blocksOrdered(string $pageKey)
    {
        return PublicDisplayBlock::forPage($pageKey)->orderBy('sort_order')->get();
    }

    public function videosFor(string $pageKey)
    {
        return PublicDisplayVideo::forPage($pageKey)->orderBy('sort_order')->get();
    }

    public function contactChannels()
    {
        return PublicContactChannel::activeOrdered()->get();
    }

    public function whatsappLink(?string $presetMessage = null): ?string
    {
        $settings = PublicContactSettings::query()->first();
        $base = null;
        if ($settings && trim((string) $settings->whatsapp) !== '') {
            $href = $settings->whatsappHref();
            $base = $href ? preg_replace('/\?.*$/', '', $href) : null;
        }
        if (! $base) {
            $ch = PublicContactChannel::activeOrdered()->where('channel_type', 'whatsapp')->first();
            if ($ch) {
                $base = preg_replace('/\?.*$/', '', $ch->resolvedUrl());
            }
        }
        if (! $base) {
            $owner = Company::where('code', 'SA')->first();
            $raw = optional($owner)->phone ?? '';
            $digits = preg_replace('/\D/', '', $raw);
            if ($digits && str_starts_with($digits, '0') && strlen($digits) === 11) {
                $digits = '964' . substr($digits, 1);
            }
            if ($digits !== '') {
                $base = 'https://wa.me/' . $digits;
            }
        }
        if (! $base) {
            return null;
        }
        $msg = $presetMessage ?? 'مرحباً، أريد الاستفسار عن نظام ConcreteERP.';

        return $base . '?text=' . rawurlencode($msg);
    }
}
