<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTimeline extends Model
{
    use HasFactory;

    protected $table = 'order_timeline';

    // أنواع الأحداث
    const EVENT_CREATED = 'created';
    const EVENT_UPDATED = 'updated';
    const EVENT_STATUS_CHANGED = 'status_changed';
    const EVENT_BRANCH_REVIEWED = 'branch_reviewed';
    const EVENT_OFFER_SENT = 'offer_sent';
    const EVENT_OFFER_ACCEPTED = 'offer_accepted';
    const EVENT_OFFER_REJECTED = 'offer_rejected';
    const EVENT_COUNTER_OFFER = 'counter_offer';
    const EVENT_FINAL_APPROVAL = 'final_approval';
    const EVENT_ASSIGNED = 'assigned';
    const EVENT_DISPATCHED = 'dispatched';
    const EVENT_COMPLETED = 'completed';
    const EVENT_CANCELLED = 'cancelled';
    const EVENT_NOTE_ADDED = 'note_added';
    const EVENT_ATTACHMENT_ADDED = 'attachment_added';

    const EVENTS = [
        self::EVENT_CREATED => 'تم إنشاء الطلب',
        self::EVENT_UPDATED => 'تم تحديث الطلب',
        self::EVENT_STATUS_CHANGED => 'تغيير الحالة',
        self::EVENT_BRANCH_REVIEWED => 'مراجعة الفرع',
        self::EVENT_OFFER_SENT => 'إرسال عرض سعر',
        self::EVENT_OFFER_ACCEPTED => 'قبول العرض',
        self::EVENT_OFFER_REJECTED => 'رفض العرض',
        self::EVENT_COUNTER_OFFER => 'عرض مضاد',
        self::EVENT_FINAL_APPROVAL => 'الموافقة النهائية',
        self::EVENT_ASSIGNED => 'تعيين سائق/سيارة',
        self::EVENT_DISPATCHED => 'إرسال للتنفيذ',
        self::EVENT_COMPLETED => 'اكتمال الطلب',
        self::EVENT_CANCELLED => 'إلغاء الطلب',
        self::EVENT_NOTE_ADDED => 'إضافة ملاحظة',
        self::EVENT_ATTACHMENT_ADDED => 'إضافة مرفق',
    ];

    // أنواع منشئ الحدث
    const CREATOR_SYSTEM = 'system';
    const CREATOR_EMPLOYEE = 'employee';
    const CREATOR_CONTRACTOR = 'contractor';
    const CREATOR_AGENT = 'agent';
    const CREATOR_CUSTOMER = 'customer';

    protected $fillable = [
        'order_id',
        'event_type',
        'title',
        'description',
        'old_values',
        'new_values',
        'latitude',
        'longitude',
        'created_by',
        'created_by_name',
        'created_by_type',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // العلاقات
    public function order()
    {
        return $this->belongsTo(WorkOrder::class, 'order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeEventType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeChronological($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    public function scopeReverseChronological($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // الدوال المساعدة
    public function getEventLabelAttribute()
    {
        return self::EVENTS[$this->event_type] ?? $this->event_type;
    }

    public function getEventIconAttribute()
    {
        $icons = [
            self::EVENT_CREATED => 'fa-plus-circle text-success',
            self::EVENT_UPDATED => 'fa-edit text-info',
            self::EVENT_STATUS_CHANGED => 'fa-exchange-alt text-warning',
            self::EVENT_BRANCH_REVIEWED => 'fa-check-circle text-primary',
            self::EVENT_OFFER_SENT => 'fa-paper-plane text-info',
            self::EVENT_OFFER_ACCEPTED => 'fa-thumbs-up text-success',
            self::EVENT_OFFER_REJECTED => 'fa-thumbs-down text-danger',
            self::EVENT_COUNTER_OFFER => 'fa-handshake text-warning',
            self::EVENT_FINAL_APPROVAL => 'fa-check-double text-success',
            self::EVENT_ASSIGNED => 'fa-user-check text-primary',
            self::EVENT_DISPATCHED => 'fa-truck text-info',
            self::EVENT_COMPLETED => 'fa-flag-checkered text-success',
            self::EVENT_CANCELLED => 'fa-times-circle text-danger',
            self::EVENT_NOTE_ADDED => 'fa-sticky-note text-secondary',
            self::EVENT_ATTACHMENT_ADDED => 'fa-paperclip text-secondary',
        ];
        return $icons[$this->event_type] ?? 'fa-circle text-muted';
    }

    public function hasLocation()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    // إضافة حدث جديد
    public static function addEvent($orderId, $eventType, $title, $description = null, $oldValues = null, $newValues = null, $coordinates = null)
    {
        $user = auth()->user();

        return self::create([
            'order_id' => $orderId,
            'event_type' => $eventType,
            'title' => $title,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'latitude' => $coordinates['lat'] ?? null,
            'longitude' => $coordinates['lng'] ?? null,
            'created_by' => $user ? $user->id : null,
            'created_by_name' => $user ? $user->name : 'النظام',
            'created_by_type' => $user ? self::CREATOR_EMPLOYEE : self::CREATOR_SYSTEM,
        ]);
    }

    // الحصول على الخط الزمني الكامل
    public static function getTimeline($orderId)
    {
        return self::forOrder($orderId)
            ->chronological()
            ->get();
    }
}
