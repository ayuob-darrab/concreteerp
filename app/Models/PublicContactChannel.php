<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicContactChannel extends Model
{
    protected $table = 'public_contact_channels';

    protected $fillable = [
        'channel_type',
        'label',
        'value',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActiveOrdered($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function resolvedUrl(): string
    {
        $v = trim($this->value);
        $type = strtolower($this->channel_type);

        return match ($type) {
            'email' => 'mailto:' . preg_replace('/^mailto:/i', '', $v),
            'whatsapp' => $this->whatsappUrl($v),
            'telegram' => $this->telegramUrl($v),
            'instagram' => $this->instagramUrl($v),
            'twitter' => $this->twitterUrl($v),
            'phone' => 'tel:' . preg_replace('/[^\d+]/', '', $v),
            default => $v,
        };
    }

    private function whatsappUrl(string $v): string
    {
        $digits = preg_replace('/\D/', '', $v);
        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = '964' . substr($digits, 1);
        }

        return 'https://wa.me/' . $digits;
    }

    private function telegramUrl(string $v): string
    {
        $v = preg_replace('#^https?://(t\.me|telegram\.me)/#i', '', $v);
        $v = ltrim($v, '@');

        return 'https://t.me/' . $v;
    }

    private function instagramUrl(string $v): string
    {
        if (preg_match('#^https?://#i', $v)) {
            return $v;
        }
        $v = ltrim($v, '@');

        return 'https://instagram.com/' . $v;
    }

    private function twitterUrl(string $v): string
    {
        if (preg_match('#^https?://#i', $v)) {
            return $v;
        }
        $v = ltrim($v, '@');

        return 'https://twitter.com/' . $v;
    }

    public function iconClass(): string
    {
        return match (strtolower($this->channel_type)) {
            'email' => 'fas fa-envelope',
            'whatsapp' => 'fab fa-whatsapp',
            'telegram' => 'fab fa-telegram',
            'instagram' => 'fab fa-instagram',
            'twitter' => 'fab fa-twitter',
            'phone' => 'fas fa-phone',
            default => 'fas fa-link',
        };
    }

    public function defaultLabel(): string
    {
        if ($this->label) {
            return $this->label;
        }

        return match (strtolower($this->channel_type)) {
            'email' => 'البريد الإلكتروني',
            'whatsapp' => 'واتساب',
            'telegram' => 'تيليجرام',
            'instagram' => 'إنستغرام',
            'twitter' => 'تويتر / X',
            'phone' => 'هاتف',
            default => 'رابط',
        };
    }
}
