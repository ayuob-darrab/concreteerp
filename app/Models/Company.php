<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [
        'code',
        'name',
        'managername',
        'city_id',
        'phone',
        'email',
        'address',
        'logo',
        'latitude',
        'longitude',
        'note',
        'creation_price',
        'is_active',
        'is_suspended',
        'userAdmin',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_suspended' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    /**
     * الفروع التابعة للشركة
     */
    public function branches()
    {
        return $this->hasMany(Branch::class, 'company_code', 'code');
    }

    /**
     * المستخدمين التابعين للشركة
     */
    public function users()
    {
        return $this->hasMany(User::class, 'company_code', 'code');
    }

    /**
     * المدينة
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * من أنشأ الشركة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الموظفين في الشركة
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'company_code', 'code');
    }

    /**
     * المقاولين التابعين للشركة
     */
    public function contractors()
    {
        return $this->hasMany(Contractor::class, 'company_code', 'code');
    }

    /**
     * السيارات التابعة للشركة
     */
    public function cars()
    {
        return $this->hasMany(Cars::class, 'company_code', 'code');
    }

    /**
     * المخزون
     */
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'company_code', 'code');
    }

    /**
     * السعر الخاص للاشتراك
     */
    public function customPricing()
    {
        return $this->hasOne(CompanySubscriptionPrice::class, 'company_code', 'code');
    }

    /**
     * الاشتراك الحالي
     */
    public function subscription()
    {
        return $this->hasOne(CompanySubscription::class, 'company_code', 'code');
    }

    // ==========================================
    // Accessors & Mutators
    // ==========================================

    /**
     * عدد الفروع
     */
    public function getBranchesCountAttribute()
    {
        return $this->branches()->count();
    }

    /**
     * عدد المستخدمين
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * عدد الموظفين
     */
    public function getEmployeesCountAttribute()
    {
        return $this->employees()->count();
    }

    /**
     * هل الشركة نشطة؟
     */
    public function getIsActiveStatusAttribute()
    {
        return $this->is_active && !$this->is_suspended;
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * الشركات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_suspended', false);
    }

    /**
     * الشركات المعلقة
     */
    public function scopeSuspended($query)
    {
        return $query->where('is_suspended', true);
    }

    /**
     * البحث بالاسم أو الكود
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('code', 'like', "%{$term}%");
    }
}
