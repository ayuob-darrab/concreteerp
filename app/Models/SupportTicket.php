<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'company_code',
        'user_id',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'rating',
        'feedback',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * تصنيفات التذاكر
     */
    public static $categories = [
        'technical' => 'مشكلة تقنية',
        'billing' => 'استفسار مالي',
        'feature_request' => 'طلب ميزة',
        'bug' => 'بلاغ خطأ',
        'general' => 'استفسار عام'
    ];

    /**
     * أولويات التذاكر
     */
    public static $priorities = [
        'low' => 'منخفضة',
        'medium' => 'متوسطة',
        'high' => 'عالية',
        'urgent' => 'عاجلة'
    ];

    /**
     * حالات التذاكر
     */
    public static $statuses = [
        'open' => 'مفتوحة',
        'in_progress' => 'قيد المعالجة',
        'pending_response' => 'بانتظار الرد',
        'resolved' => 'محلولة',
        'closed' => 'مغلقة'
    ];

    /**
     * ألوان الحالات
     */
    public static $statusColors = [
        'open' => 'info',
        'in_progress' => 'warning',
        'pending_response' => 'secondary',
        'resolved' => 'success',
        'closed' => 'dark'
    ];

    /**
     * ألوان الأولويات
     */
    public static $priorityColors = [
        'low' => 'success',
        'medium' => 'info',
        'high' => 'warning',
        'urgent' => 'danger'
    ];

    /**
     * علاقة مع الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * علاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة مع الردود
     */
    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    /**
     * توليد رقم تذكرة جديد
     */
    public static function generateTicketNumber()
    {
        $year = date('Y');
        $lastTicket = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastTicket ? intval(substr($lastTicket->ticket_number, -4)) + 1 : 1;

        return 'TKT-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * الحصول على اسم التصنيف
     */
    public function getCategoryNameAttribute()
    {
        return self::$categories[$this->category] ?? $this->category;
    }

    /**
     * الحصول على اسم الأولوية
     */
    public function getPriorityNameAttribute()
    {
        return self::$priorities[$this->priority] ?? $this->priority;
    }

    /**
     * الحصول على اسم الحالة
     */
    public function getStatusNameAttribute()
    {
        return self::$statuses[$this->status] ?? $this->status;
    }

    /**
     * الحصول على لون الحالة
     */
    public function getStatusColorAttribute()
    {
        return self::$statusColors[$this->status] ?? 'secondary';
    }

    /**
     * الحصول على لون الأولوية
     */
    public function getPriorityColorAttribute()
    {
        return self::$priorityColors[$this->priority] ?? 'secondary';
    }

    /**
     * تحديث حالة التذكرة
     */
    public function updateStatus($status)
    {
        $data = ['status' => $status];

        if ($status === 'resolved') {
            $data['resolved_at'] = now();
        } elseif ($status === 'closed') {
            $data['closed_at'] = now();
        }

        return $this->update($data);
    }

    /**
     * تعيين موظف للتذكرة
     */
    public function assignTo($userId)
    {
        return $this->update([
            'assigned_to' => $userId,
            'status' => 'in_progress'
        ]);
    }
}
