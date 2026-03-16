<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'company_code',
        'branch_name',
        'branch_admin',
        'city_id',
        'phone',
        'email',
        'address',
        'latitude',
        'longitude',
        'is_active',
        'created_by',
        'created_date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    /**
     * الشركة الأم
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * اسم الشركة (للتوافق مع الكود القديم)
     */
    public function companyName()
    {
        return $this->belongsTo(Company::class, 'company_code', 'code');
    }

    /**
     * المحافظة
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * اسم المحافظة (للتوافق مع الكود القديم)
     */
    public function cityName()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * مدير الفرع
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'branch_admin');
    }

    /**
     * المستخدمين في الفرع
     */
    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    /**
     * الموظفين في الفرع
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }

    /**
     * السيارات في الفرع
     */
    public function cars()
    {
        return $this->hasMany(Cars::class, 'branch_id');
    }

    /**
     * المخزون في الفرع
     */
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'branch_id');
    }

    /**
     * من أنشأ الفرع
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==========================================
    // Accessors & Mutators
    // ==========================================

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
     * عدد السيارات
     */
    public function getCarsCountAttribute()
    {
        return $this->cars()->count();
    }

    /**
     * اسم الفرع (للتوافق مع الكود الجديد)
     */
    public function getNameAttribute()
    {
        return $this->branch_name;
    }

    // ==========================================
    // Scopes
    // ==========================================

    /**
     * الفروع النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * فروع شركة معينة
     */
    public function scopeForCompany($query, $companyCode)
    {
        return $query->where('company_code', $companyCode);
    }

    /**
     * البحث بالاسم
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('branch_name', 'like', "%{$term}%");
    }
}
