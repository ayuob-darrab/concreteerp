<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * إعدادات صفحة «تواصل معنا» — صف واحد فقط (حقول ثابتة).
 */
class PublicContactSettings extends Model
{
    protected $table = 'public_contact_settings';

    protected $fillable = [
        'title',
        'intro_text',
        'hint_text',
        'email',
        'whatsapp',
        'telegram',
        'facebook',
        'instagram',
        'phone',
    ];

    public static function singleton(): self
    {
        return static::query()->first() ?? static::create([]);
    }

    /** روابط جاهزة للنقر — null إذا الحقل فارغ */
    public function emailHref(): ?string
    {
        $v = trim((string) $this->email);
        if ($v === '') {
            return null;
        }

        return 'mailto:' . preg_replace('/^mailto:/i', '', $v);
    }

    public function whatsappHref(): ?string
    {
        $v = trim((string) $this->whatsapp);
        if ($v === '') {
            return null;
        }
        $digits = preg_replace('/\D/', '', $v);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = '964' . substr($digits, 1);
        }

        return 'https://wa.me/' . $digits;
    }

    public function telegramHref(): ?string
    {
        $v = trim((string) $this->telegram);
        if ($v === '') {
            return null;
        }
        $v = preg_replace('#^https?://(t\.me|telegram\.me)/#i', '', $v);

        return 'https://t.me/' . ltrim($v, '@');
    }

    public function facebookHref(): ?string
    {
        $v = trim((string) $this->facebook);
        if ($v === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $v)) {
            return $v;
        }

        return 'https://facebook.com/' . ltrim($v, '/@');
    }

    public function instagramHref(): ?string
    {
        $v = trim((string) $this->instagram);
        if ($v === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $v)) {
            return $v;
        }

        return 'https://instagram.com/' . ltrim($v, '@');
    }

    public function phoneHref(): ?string
    {
        $v = trim((string) $this->phone);
        if ($v === '') {
            return null;
        }

        return 'tel:' . preg_replace('/[^\d+]/', '', $v);
    }
}
