<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_id',
        'status',
        'notes',
        'created_by',
    ];

    // ═══════════════════════════════════════════════════════════════
    // العلاقات
    // ═══════════════════════════════════════════════════════════════

    /**
     * الشيك
     */
    public function check()
    {
        return $this->belongsTo(Check::class);
    }

    /**
     * من سجل الحالة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ═══════════════════════════════════════════════════════════════
    // Accessors
    // ═══════════════════════════════════════════════════════════════

    /**
     * نص الحالة
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            Check::STATUS_PENDING => 'قيد الانتظار',
            Check::STATUS_DEPOSITED => 'مودع في البنك',
            Check::STATUS_COLLECTED => 'تم التحصيل',
            Check::STATUS_REJECTED => 'مرفوض',
            Check::STATUS_RETURNED => 'مرتجع',
            Check::STATUS_CANCELLED => 'ملغي',
            Check::STATUS_ENDORSED => 'مظهر',
            default => 'غير معروف',
        };
    }
}
