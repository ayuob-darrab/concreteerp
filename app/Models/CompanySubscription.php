<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CompanySubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_code',
        'plan_type',
        'users_count',
        'price_per_user',
        'total_amount',
        'base_fee',
        'percentage_rate',
        'order_fee_type',
        'fixed_order_fee',
        'orders_limit',
        'orders_used',
        'start_date',
        'end_date',
        'auto_renew',
        'status',
        'notes',
        'created_by',
        // حقول الدفع
        'payment_status',
        'paid_amount',
        'paid_at',
        'payment_method',
        'payment_reference',
        // حقول التمديد
        'extension_days',
        'extension_deducted',
        // حقول فترة السماح
        'grace_days_used',
        'grace_period_start',
        'is_in_grace_period',
        'days_to_deduct',
        'last_invoice_date',
        // حقول مدة الاشتراك
        'duration_quantity',
        'years_count',
        'total_days',
    ];

    protected $casts = [
        'base_fee' => 'decimal:2',
        'percentage_rate' => 'decimal:2',
        'fixed_order_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'price_per_user' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'paid_at' => 'datetime',
        'grace_period_start' => 'date',
        'last_invoice_date' => 'date',
        'auto_renew' => 'boolean',
        'extension_deducted' => 'boolean',
        'is_in_grace_period' => 'boolean',
        'extension_days' => 'integer',
        'grace_days_used' => 'integer',
        'days_to_deduct' => 'integer',
        'duration_quantity' => 'integer',
        'years_count' => 'integer',
        'total_days' => 'integer',
        'users_count' => 'integer',
    ];

    /**
     * العلاقة مع جدول الشركات
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * التحقق من وجود اشتراك مكرر بنفس الفترة
     */
    public static function hasDuplicateSubscription($companyCode, $startDate, $endDate, $excludeId = null)
    {
        $query = self::where('company_code', $companyCode)
            ->where('start_date', $startDate)
            ->where('end_date', $endDate);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * حساب الأيام حسب نوع الخطة والكمية
     */
    public static function calculateDays($planType, $quantity = 1, $yearsCount = 1)
    {
        $trialDays = SubscriptionPricing::getSettings()->trial_days ?? 7;
        return match ($planType) {
            'monthly' => 30 * $quantity,
            'yearly' => 365 * $yearsCount,
            'trial' => (int) $trialDays * $quantity,
            'hybrid' => 30 * $quantity, // افتراضي شهري للهجين
            default => 0,
        };
    }

    /**
     * حساب إجمالي المبلغ بناءً على عدد المستخدمين
     */
    public static function calculateTotalAmount($planType, $usersCount, $pricePerUser, $durationQuantity = 1, $yearsCount = 1)
    {
        return match ($planType) {
            'monthly' => $usersCount * $pricePerUser * $durationQuantity,
            'yearly' => $usersCount * $pricePerUser * 12 * $yearsCount,
            'trial' => 0,
            'hybrid' => $usersCount * $pricePerUser * $durationQuantity,
            default => 0,
        };
    }

    /**
     * الحصول على سعر المستخدم المناسب للشركة
     */
    public static function getPricePerUser($companyCode, $planType)
    {
        if ($planType === 'yearly') {
            return CompanySubscriptionPrice::getYearlyPrice($companyCode);
        }
        return CompanySubscriptionPrice::getMonthlyPrice($companyCode);
    }

    /**
     * التحقق إذا كان الاشتراك مدفوع بالكامل
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * التحقق إذا كان الاشتراك قريب من الانتهاء
     */
    public function isExpiringSoon($days = null)
    {
        if (!$this->end_date) return false;
        $warningDays = $days ?? SubscriptionPricing::getSettings()->warning_days;
        return Carbon::now()->diffInDays($this->end_date, false) <= $warningDays && $this->status === 'active';
    }

    /**
     * التحقق إذا انتهى الاشتراك
     */
    public function isExpired()
    {
        if (!$this->end_date) return false;
        return Carbon::now()->greaterThan($this->end_date);
    }

    /**
     * التحقق إذا كان في فترة السماح
     */
    public function isInGracePeriod()
    {
        if (!$this->isExpired()) return false;
        
        $settings = SubscriptionPricing::getSettings();
        $daysSinceExpiry = Carbon::now()->diffInDays($this->end_date, false) * -1;
        
        return $daysSinceExpiry <= $settings->grace_period_days;
    }

    /**
     * الحصول على أيام السماح المتبقية
     */
    public function getRemainingGraceDaysAttribute()
    {
        if (!$this->isExpired()) return null;
        
        $settings = SubscriptionPricing::getSettings();
        $daysSinceExpiry = Carbon::now()->diffInDays($this->end_date, false) * -1;
        
        return max(0, $settings->grace_period_days - $daysSinceExpiry);
    }

    /**
     * الأيام المتبقية للاشتراك
     */
    public function getRemainingDaysAttribute()
    {
        if (!$this->end_date) return null;
        return max(0, Carbon::now()->diffInDays($this->end_date, false));
    }

    /**
     * المبلغ المتبقي للسداد
     */
    public function getRemainingAmountAttribute()
    {
        return max(0, ($this->total_amount ?? $this->base_fee) - $this->paid_amount);
    }

    /**
     * الحصول على حالة الاشتراك التفصيلية
     */
    public function getDetailedStatusAttribute()
    {
        $remaining = $this->remaining_days;
        $settings = SubscriptionPricing::getSettings();
        
        if ($this->status === 'suspended') {
            return [
                'status' => 'suspended',
                'color' => 'warning',
                'message' => 'الحساب معلق',
                'icon' => 'pause',
            ];
        }
        
        if ($this->isExpired()) {
            if ($this->isInGracePeriod()) {
                $graceDays = $this->remaining_grace_days;
                return [
                    'status' => 'grace_period',
                    'color' => 'warning',
                    'message' => "فترة السماح - متبقي {$graceDays} يوم",
                    'icon' => 'clock',
                    'days' => $graceDays,
                ];
            }
            return [
                'status' => 'expired',
                'color' => 'danger',
                'message' => 'انتهى الاشتراك',
                'icon' => 'x-circle',
            ];
        }
        
        if ($remaining <= $settings->warning_days) {
            return [
                'status' => 'expiring_soon',
                'color' => 'warning',
                'message' => "سينتهي خلال {$remaining} يوم",
                'icon' => 'alert-triangle',
                'days' => $remaining,
            ];
        }
        
        return [
            'status' => 'active',
            'color' => 'success',
            'message' => "متبقي {$remaining} يوم",
            'icon' => 'check-circle',
            'days' => $remaining,
        ];
    }

    /**
     * تمديد الاشتراك
     */
    public function extend($days)
    {
        $this->extension_days += $days;
        if ($this->end_date) {
            $this->end_date = Carbon::parse($this->end_date)->addDays($days);
        }
        $this->save();
        return $this;
    }

    /**
     * بدء فترة السماح
     */
    public function startGracePeriod()
    {
        $this->is_in_grace_period = true;
        $this->grace_period_start = now();
        $this->save();
        return $this;
    }

    /**
     * تحديث أيام السماح المستخدمة
     */
    public function updateGraceDaysUsed()
    {
        if ($this->grace_period_start) {
            $this->grace_days_used = Carbon::now()->diffInDays($this->grace_period_start);
            $this->days_to_deduct = $this->grace_days_used;
            $this->save();
        }
        return $this;
    }

    /**
     * تسجيل دفعة
     */
    public function recordPayment($amount, $method = null, $reference = null)
    {
        $this->paid_amount += $amount;
        $this->payment_method = $method;
        $this->payment_reference = $reference;

        $totalRequired = $this->total_amount ?? $this->base_fee;
        
        if ($this->paid_amount >= $totalRequired) {
            $this->payment_status = 'paid';
            $this->paid_at = now();
            
            // إذا كان الحساب معلق بسبب عدم الدفع، أعد تفعيله
            if ($this->status === 'suspended') {
                $this->status = 'active';
            }
        } else {
            $this->payment_status = 'partial';
        }

        $this->save();
        return $this;
    }

    /**
     * إضافة مستخدم جديد (يدفع شهر كامل)
     */
    public function addUser($count = 1)
    {
        $additionalAmount = $count * $this->price_per_user;
        
        $this->users_count += $count;
        $this->total_amount += $additionalAmount;
        $this->save();
        
        return [
            'users_added' => $count,
            'additional_amount' => $additionalAmount,
            'new_total' => $this->total_amount,
        ];
    }

    /**
     * تعليق الحساب بسبب عدم الدفع
     */
    public function suspendForNonPayment()
    {
        $this->status = 'suspended';
        $this->save();
        
        // تعطيل حساب الشركة
        Company::where('code', $this->company_code)->update(['is_suspended' => true]);
        
        return $this;
    }

    /**
     * إعادة تفعيل الحساب بعد الدفع
     */
    public function reactivateAfterPayment()
    {
        $this->status = 'active';
        $this->is_in_grace_period = false;
        $this->save();
        
        // تفعيل حساب الشركة
        Company::where('code', $this->company_code)->update(['is_suspended' => false]);
        
        return $this;
    }

    /**
     * التحقق من وجوب تعطيل الحساب
     */
    public function shouldBeSuspended()
    {
        $settings = SubscriptionPricing::getSettings();
        
        // إذا انتهت فترة السماح
        if ($this->isExpired() && !$this->isInGracePeriod()) {
            return true;
        }
        
        // إذا لم يتم الدفع خلال المهلة المحددة
        if ($this->payment_status !== 'paid' && $this->start_date) {
            $daysSinceStart = Carbon::now()->diffInDays($this->start_date);
            if ($daysSinceStart > $settings->payment_due_days) {
                return true;
            }
        }
        
        return false;
    }
}
