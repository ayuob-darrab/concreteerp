<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'notification_type',
        'app_enabled',
        'sms_enabled',
        'whatsapp_enabled',
        'email_enabled',
    ];

    protected $casts = [
        'app_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'whatsapp_enabled' => 'boolean',
        'email_enabled' => 'boolean',
    ];

    /**
     * المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الحصول على إعدادات مستخدم لنوع معين
     */
    public static function getForUser(int $userId, string $type): ?self
    {
        return static::where('user_id', $userId)
            ->where('notification_type', $type)
            ->first();
    }

    /**
     * الحصول على جميع إعدادات المستخدم
     */
    public static function getAllForUser(int $userId): array
    {
        $settings = static::where('user_id', $userId)->get()->keyBy('notification_type');
        $templates = NotificationTemplate::where('is_active', true)->get();

        $result = [];
        foreach ($templates as $template) {
            $setting = $settings->get($template->type);

            $result[$template->type] = [
                'type' => $template->type,
                'title' => $template->title_ar,
                'app_enabled' => $setting ? $setting->app_enabled : true,
                'sms_enabled' => $setting ? $setting->sms_enabled : false,
                'whatsapp_enabled' => $setting ? $setting->whatsapp_enabled : false,
                'email_enabled' => $setting ? $setting->email_enabled : false,
            ];
        }

        return $result;
    }

    /**
     * الحصول على القنوات المفعلة
     */
    public function getEnabledChannels(): array
    {
        $channels = [];

        if ($this->app_enabled) {
            $channels[] = 'app';
        }
        if ($this->sms_enabled) {
            $channels[] = 'sms';
        }
        if ($this->whatsapp_enabled) {
            $channels[] = 'whatsapp';
        }
        if ($this->email_enabled) {
            $channels[] = 'email';
        }

        return $channels;
    }

    /**
     * حفظ إعدادات المستخدم
     */
    public static function saveForUser(int $userId, string $type, array $channels): self
    {
        return static::updateOrCreate(
            ['user_id' => $userId, 'notification_type' => $type],
            [
                'app_enabled' => in_array('app', $channels),
                'sms_enabled' => in_array('sms', $channels),
                'whatsapp_enabled' => in_array('whatsapp', $channels),
                'email_enabled' => in_array('email', $channels),
            ]
        );
    }

    /**
     * تفعيل/تعطيل قناة معينة
     */
    public function toggleChannel(string $channel, bool $enabled): void
    {
        $field = "{$channel}_enabled";
        if (in_array($field, $this->fillable)) {
            $this->update([$field => $enabled]);
        }
    }
}
