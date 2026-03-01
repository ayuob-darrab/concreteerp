<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Check extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'check_number',
        'check_type',
        'account_id',
        'company_code',
        'branch_id',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'drawer_name',
        'drawer_id_number',
        'beneficiary_name',
        'amount',
        'currency',
        'exchange_rate',
        'issue_date',
        'due_date',
        'deposit_date',
        'collection_date',
        'status',
        'rejection_reason',
        'rejection_count',
        'endorsements',
        'current_holder',
        'image_front',
        'image_back',
        'invoice_id',
        'notes',
        'created_by',
        'deposited_by',
        'collected_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'issue_date' => 'date',
        'due_date' => 'date',
        'deposit_date' => 'date',
        'collection_date' => 'date',
        'endorsements' => 'array',
    ];

    // أنواع الشيكات
    const TYPE_INCOMING = 'incoming';      // وارد
    const TYPE_OUTGOING = 'outgoing';      // صادر

    // حالات الشيكات
    const STATUS_PENDING = 'pending';           // قيد الانتظار
    const STATUS_DEPOSITED = 'deposited';       // مودع
    const STATUS_COLLECTED = 'collected';       // تم التحصيل
    const STATUS_REJECTED = 'rejected';         // مرفوض
    const STATUS_RETURNED = 'returned';         // مرتجع
    const STATUS_CANCELLED = 'cancelled';       // ملغي
    const STATUS_ENDORSED = 'endorsed';         // مظهر

    // ═══════════════════════════════════════════════════════════════
    // Boot
    // ═══════════════════════════════════════════════════════════════

    protected static function booted()
    {
        static::creating(function (Check $check) {
            if (empty($check->status)) {
                $check->status = self::STATUS_PENDING;
            }
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // العلاقات
    // ═══════════════════════════════════════════════════════════════

    /**
     * الحساب
     */
    public function account()
    {
        return $this->belongsTo(ContractorAccount::class, 'account_id');
    }

    /**
     * المقاول عبر الحساب
     */
    public function contractor()
    {
        return $this->hasOneThrough(
            Contractor::class,
            ContractorAccount::class,
            'id',          // FK on accounts
            'id',          // FK on contractors
            'account_id',  // Local key on checks
            'contractor_id' // Local key on accounts
        );
    }

    /**
     * الفاتورة
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * سجل الحالات
     */
    public function statusLogs()
    {
        return $this->hasMany(CheckStatusLog::class);
    }

    /**
     * من أنشأ الشيك
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ═══════════════════════════════════════════════════════════════
    // Scopes
    // ═══════════════════════════════════════════════════════════════

    /**
     * حسب الشركة
     */
    public function scopeForCompany($query, string $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * حسب الفرع
     */
    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * الشيكات الواردة
     */
    public function scopeIncoming($query)
    {
        return $query->where('check_type', self::TYPE_INCOMING);
    }

    /**
     * الشيكات الصادرة
     */
    public function scopeOutgoing($query)
    {
        return $query->where('check_type', self::TYPE_OUTGOING);
    }

    /**
     * حسب الحالة
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * المستحقة اليوم
     */
    public function scopeDueToday($query)
    {
        return $query->where('due_date', today())
            ->where('status', self::STATUS_PENDING);
    }

    /**
     * المستحقة خلال فترة
     */
    public function scopeDueWithinDays($query, int $days)
    {
        return $query->whereBetween('due_date', [today(), today()->addDays($days)])
            ->where('status', self::STATUS_PENDING);
    }

    /**
     * المتأخرة
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', today())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_DEPOSITED]);
    }

    // ═══════════════════════════════════════════════════════════════
    // Accessors
    // ═══════════════════════════════════════════════════════════════

    /**
     * نص النوع
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->check_type) {
            self::TYPE_INCOMING => 'وارد',
            self::TYPE_OUTGOING => 'صادر',
            default => 'غير معروف',
        };
    }

    /**
     * نص الحالة
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_DEPOSITED => 'مودع في البنك',
            self::STATUS_COLLECTED => 'تم التحصيل',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_RETURNED => 'مرتجع',
            self::STATUS_CANCELLED => 'ملغي',
            self::STATUS_ENDORSED => 'مظهر',
            default => 'غير معروف',
        };
    }

    /**
     * لون الحالة
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_DEPOSITED => 'blue',
            self::STATUS_COLLECTED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_RETURNED => 'orange',
            self::STATUS_CANCELLED => 'gray',
            self::STATUS_ENDORSED => 'purple',
            default => 'gray',
        };
    }

    /**
     * هل متأخر
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() &&
            in_array($this->status, [self::STATUS_PENDING, self::STATUS_DEPOSITED]);
    }

    /**
     * أيام حتى الاستحقاق
     */
    public function getDaysUntilDueAttribute(): int
    {
        return today()->diffInDays($this->due_date, false);
    }

    // ═══════════════════════════════════════════════════════════════
    // Methods
    // ═══════════════════════════════════════════════════════════════

    /**
     * إيداع الشيك
     */
    public function deposit(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_DEPOSITED,
            'deposit_date' => today(),
            'deposited_by' => auth()->id(),
        ]);

        $this->logStatus(self::STATUS_DEPOSITED, 'تم إيداع الشيك في البنك');

        return true;
    }

    /**
     * تحصيل الشيك
     */
    public function collect(): bool
    {
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_DEPOSITED])) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_COLLECTED,
            'collection_date' => today(),
            'collected_by' => auth()->id(),
        ]);

        $this->logStatus(self::STATUS_COLLECTED, 'تم تحصيل الشيك');

        // تحديث رصيد الحساب
        if ($this->account) {
            $this->account->addTransaction(
                $this->check_type === self::TYPE_INCOMING ? 'credit' : 'debit',
                $this->amount,
                'check_collection',
                "تحصيل شيك رقم: {$this->check_number}"
            );
        }

        return true;
    }

    /**
     * رفض الشيك
     */
    public function reject(string $reason): bool
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'rejection_count' => ($this->rejection_count ?? 0) + 1,
        ]);

        $this->logStatus(self::STATUS_REJECTED, $reason);

        return true;
    }

    /**
     * إرجاع الشيك
     */
    public function returnCheck(string $reason = null): bool
    {
        $this->update([
            'status' => self::STATUS_RETURNED,
        ]);

        $this->logStatus(self::STATUS_RETURNED, $reason ?? 'تم إرجاع الشيك');

        return true;
    }

    /**
     * إلغاء الشيك
     */
    public function cancel(string $reason = null): bool
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);

        $this->logStatus(self::STATUS_CANCELLED, $reason ?? 'تم إلغاء الشيك');

        return true;
    }

    /**
     * تظهير الشيك
     */
    public function endorse(string $newHolder, string $notes = null): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $endorsements = $this->endorsements ?? [];
        $endorsements[] = [
            'from' => $this->current_holder ?? $this->drawer_name,
            'to' => $newHolder,
            'date' => now()->toDateTimeString(),
            'notes' => $notes,
        ];

        $this->update([
            'endorsements' => $endorsements,
            'current_holder' => $newHolder,
            'status' => self::STATUS_ENDORSED,
        ]);

        $this->logStatus(self::STATUS_ENDORSED, "تم التظهير إلى: {$newHolder}");

        return true;
    }

    /**
     * تسجيل حالة
     */
    protected function logStatus(string $status, string $notes = null): void
    {
        $this->statusLogs()->create([
            'status' => $status,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }
}
