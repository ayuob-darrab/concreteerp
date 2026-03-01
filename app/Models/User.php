<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'email',
        'username',
        'password',
        'usertype_id',
        'company_code',
        'branch_id',
        'emp_type_id',
        'account_code',
        'is_active',
        'created_by',
        'current_session_id',
        'last_activity_at',
        'session_timeout_minutes',
        'is_logged_in',
        'device_fingerprint',
        'deactivated_by_subscription',
        'subscription_deactivated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'current_session_id',
        'device_fingerprint',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_logged_in' => 'boolean',
        'deactivated_by_subscription' => 'boolean',
        'subscription_deactivated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    /**
     * نوع المستخدم
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'usertype_id', 'code');
    }

    /**
     * نوع الموظف
     */
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'emp_type_id');
    }

    /**
     * الفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * للتوافق مع الكود القديم
     */
    public function BranchName()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * نوع الحساب
     */
    public function accountType()
    {
        return $this->belongsTo(accountsType::class, 'account_code', 'code');
    }

    /**
     * الشركة
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * للتوافق مع الكود القديم
     */
    public function CompanyName()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * المقاول (إذا كان المستخدم مقاول)
     */
    public function contractor()
    {
        return $this->hasOne(Contractor::class, 'user_id');
    }

    /**
     * من أنشأ هذا المستخدم
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==========================================
    // التحقق من الأدوار (Role Checks)
    // ==========================================

    /**
     * هل المستخدم سوبر أدمن؟
     */
    public function isSuperAdmin()
    {
        return $this->usertype_id === 'SA' && $this->company_code === 'SA';
    }

    /**
     * هل المستخدم مدير شركة؟
     */
    public function isCompanyManager()
    {
        return $this->usertype_id === 'CM';
    }

    /**
     * هل المستخدم مدير فرع؟
     */
    public function isBranchManager()
    {
        return $this->usertype_id === 'BM';
    }

    /**
     * هل المستخدم مقاول؟
     */
    public function isContractor()
    {
        return $this->account_code === 'cont';
    }

    /**
     * هل المستخدم مندوب؟
     */
    public function isDelegate()
    {
        return $this->account_code === 'delegate';
    }

    /**
     * هل المستخدم موظف عادي؟
     */
    public function isEmployee()
    {
        return $this->account_code === 'emp';
    }

    /**
     * هل المستخدم أدمن؟
     */
    public function isAdmin()
    {
        return $this->usertype_id === 'AD' || $this->account_code === 'admin';
    }

    /**
     * هل المستخدم مشرف؟
     */
    public function isSupervisor()
    {
        return $this->usertype_id === 'SU' || $this->account_code === 'supervisor';
    }

    /**
     * هل المستخدم منسق؟
     */
    public function isCoordinator()
    {
        return $this->usertype_id === 'CO' || $this->account_code === 'coordinator';
    }

    /**
     * هل المستخدم سائق؟
     */
    public function isDriver()
    {
        // التحقق من account_code أو من ربط الموظف
        if ($this->account_code === 'driver') {
            return true;
        }

        // التحقق من الموظف المرتبط
        $employee = Employee::where('user_id', $this->id)->first();
        if ($employee && $employee->is_driver) {
            return true;
        }

        return $this->emp_type_id === 'driver' || $this->employeeType?->name === 'driver';
    }

    /**
     * الحصول على الموظف المرتبط بهذا المستخدم
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    /**
     * هل المستخدم يمكنه إدارة الشركة؟
     */
    public function canManageCompany()
    {
        return $this->isSuperAdmin() || $this->isCompanyManager();
    }

    /**
     * هل المستخدم يمكنه إدارة الفرع؟
     */
    public function canManageBranch()
    {
        return $this->isSuperAdmin() || $this->isCompanyManager() || $this->isBranchManager();
    }

    /**
     * هل المستخدم نشط؟
     */
    public function isActive()
    {
        return $this->is_active === true;
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * المستخدمين النشطين فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * المستخدمون النشطون ضمن حد الاشتراك (يُحتسبون في العدد المسموح)
     * من يمكنه تسجيل الدخول: is_active = true وليس معطلاً بسبب الاشتراك
     */
    public function scopeActiveForSubscription($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->where('deactivated_by_subscription', false)->orWhereNull('deactivated_by_subscription');
            });
    }

    /**
     * مستخدمي شركة معينة
     */
    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * مستخدمي فرع معين
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * مدراء الشركات
     */
    public function scopeCompanyManagers($query)
    {
        return $query->where('usertype_id', 'CM');
    }

    /**
     * مدراء الفروع
     */
    public function scopeBranchManagers($query)
    {
        return $query->where('usertype_id', 'BM');
    }

    /**
     * المقاولين
     */
    public function scopeContractors($query)
    {
        return $query->where('account_code', 'cont');
    }

    /**
     * المندوبين
     */
    public function scopeDelegates($query)
    {
        return $query->where('account_code', 'delegate');
    }

    /**
     * البحث بالاسم أو الإيميل
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('fullname', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orWhere('username', 'like', "%{$term}%");
    }

    // ==========================================
    // Accessors
    // ==========================================

    /**
     * الحصول على اسم الدور
     */
    public function getRoleNameAttribute()
    {
        if ($this->isSuperAdmin()) return 'سوبر أدمن';
        if ($this->isCompanyManager()) return 'مدير شركة';
        if ($this->isBranchManager()) return 'مدير فرع';
        if ($this->isContractor()) return 'مقاول';
        if ($this->isDelegate()) return 'مندوب';
        return 'موظف';
    }
}
