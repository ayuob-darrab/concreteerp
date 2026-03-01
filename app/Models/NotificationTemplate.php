<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'type',
        'title_ar',
        'body_ar',
        'title_en',
        'body_en',
        'variables',
        'default_channels',
        'default_priority',
        'default_icon',
        'action_route',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'default_channels' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * الحصول على قالب حسب النوع
     */
    public static function getByType(string $type): ?self
    {
        return static::where('type', $type)->where('is_active', true)->first();
    }

    /**
     * تجهيز محتوى الإشعار مع استبدال المتغيرات
     */
    public function render(array $data = [], string $locale = 'ar'): array
    {
        $title = $locale === 'ar' ? $this->title_ar : ($this->title_en ?? $this->title_ar);
        $body = $locale === 'ar' ? $this->body_ar : ($this->body_en ?? $this->body_ar);

        // استبدال المتغيرات
        foreach ($data as $key => $value) {
            $title = str_replace("{{$key}}", $value, $title);
            $body = str_replace("{{$key}}", $value, $body);
        }

        return [
            'title' => $title,
            'body' => $body,
            'icon' => $this->default_icon,
            'channels' => $this->default_channels,
            'priority' => $this->default_priority,
            'action_route' => $this->action_route,
        ];
    }

    /**
     * الحصول على جميع القوالب حسب الفئة
     */
    public static function getGrouped(): array
    {
        $templates = static::where('is_active', true)->get();

        $groups = [
            'orders' => [
                'label' => 'الطلبات',
                'types' => ['new_order', 'order_offer_sent', 'order_accepted', 'order_rejected', 'order_final_approved']
            ],
            'work' => [
                'label' => 'أوامر العمل',
                'types' => ['work_started', 'work_completed', 'shipment_departed']
            ],
            'financial' => [
                'label' => 'المالية',
                'types' => ['payment_received', 'payment_due']
            ],
            'advances' => [
                'label' => 'السلف',
                'types' => ['advance_approved', 'advance_deducted']
            ],
            'subscription' => [
                'label' => 'الاشتراك',
                'types' => ['subscription_expiring', 'subscription_expired']
            ],
            'maintenance' => [
                'label' => 'الصيانة',
                'types' => ['maintenance_due', 'maintenance_overdue']
            ],
        ];

        $result = [];
        foreach ($groups as $key => $group) {
            $result[$key] = [
                'label' => $group['label'],
                'templates' => $templates->whereIn('type', $group['types'])->values()
            ];
        }

        return $result;
    }
}
