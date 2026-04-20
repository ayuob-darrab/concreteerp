<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Audit Log Model
 * 
 * تسجيل جميع العمليات الحساسة في النظام:
 * - تسجيل الدخول والخروج
 * - إضافة/تعديل/حذف البيانات
 * - العمليات المالية
 * - تغييرات الصلاحيات
 */
class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'company_code',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (AuditLog $log) {
            $log->created_at = now();
        });
    }

    // ══════════════════════════════════════════════════════════════════
    // طرق مساعدة لإنشاء سجلات التدقيق
    // ══════════════════════════════════════════════════════════════════

    /**
     * تسجيل عملية تسجيل دخول
     */
    public static function logLogin(User $user, bool $success = true): void
    {
        static::create([
            'user_id' => $user->id,
            'company_code' => $user->company_code,
            'action' => $success ? 'login_success' : 'login_failed',
            'model_type' => User::class,
            'model_id' => $user->id,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'description' => $success ? 'تسجيل دخول ناجح' : 'محاولة تسجيل دخول فاشلة',
        ]);
    }

    /**
     * تسجيل عملية تسجيل خروج
     */
    public static function logLogout(?User $user = null): void
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return;
        }

        static::create([
            'user_id' => $user->id,
            'company_code' => $user->company_code,
            'action' => 'logout',
            'model_type' => User::class,
            'model_id' => $user->id,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'description' => 'تسجيل خروج',
        ]);
    }

    /**
     * تسجيل إنشاء سجل جديد
     */
    public static function logCreate(Model $model, ?string $description = null): void
    {
        if (!Auth::check()) {
            return;
        }

        static::create([
            'user_id' => Auth::id(),
            'company_code' => Auth::user()->company_code ?? null,
            'action' => 'create',
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'new_values' => $model->toArray(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'description' => $description ?? 'إنشاء سجل جديد: ' . class_basename($model),
        ]);
    }

    /**
     * تسجيل تحديث سجل
     */
    public static function logUpdate(Model $model, array $oldValues, ?string $description = null): void
    {
        if (!Auth::check()) {
            return;
        }

        $changedFields = array_keys(array_diff_assoc($model->toArray(), $oldValues));
        $sensitiveFields = ['password', 'remember_token', 'api_token'];
        $changedFields = array_diff($changedFields, $sensitiveFields);

        if (empty($changedFields)) {
            return;
        }

        $filteredOld = array_intersect_key($oldValues, array_flip($changedFields));
        $filteredNew = array_intersect_key($model->toArray(), array_flip($changedFields));

        static::create([
            'user_id' => Auth::id(),
            'company_code' => Auth::user()->company_code ?? null,
            'action' => 'update',
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => $filteredOld,
            'new_values' => $filteredNew,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'description' => $description ?? 'تحديث: ' . class_basename($model) . ' - الحقول: ' . implode(', ', $changedFields),
        ]);
    }

    /**
     * تسجيل حذف سجل
     */
    public static function logDelete(Model $model, ?string $description = null): void
    {
        if (!Auth::check()) {
            return;
        }

        static::create([
            'user_id' => Auth::id(),
            'company_code' => Auth::user()->company_code ?? null,
            'action' => 'delete',
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => $model->toArray(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'description' => $description ?? 'حذف: ' . class_basename($model),
        ]);
    }

    /**
     * تسجيل عملية مخصصة
     */
    public static function logCustom(string $action, string $description, ?Model $model = null, array $data = []): void
    {
        static::create([
            'user_id' => Auth::id(),
            'company_code' => Auth::user()->company_code ?? null,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'new_values' => $data,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method(),
            'description' => $description,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // العلاقات
    // ══════════════════════════════════════════════════════════════════

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ══════════════════════════════════════════════════════════════════
    // Scopes للاستعلامات
    // ══════════════════════════════════════════════════════════════════

    public function scopeForCompany($query, string $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeLoginAttempts($query)
    {
        return $query->whereIn('action', ['login_success', 'login_failed']);
    }

    public function scopeSensitiveActions($query)
    {
        return $query->whereIn('action', [
            'delete',
            'update_permissions',
            'password_change',
            'financial_transaction',
            'export_data',
        ]);
    }
}
