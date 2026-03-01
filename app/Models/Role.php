<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * عدد المستخدمين لهذا الدور
     */
    public function getUserCountAttribute()
    {
        return User::where('usertype_id', $this->code)
            ->orWhere('account_code', $this->code)
            ->count();
    }

    /**
     * المستخدمين بهذا الدور
     */
    public function users()
    {
        return User::where('usertype_id', $this->code)
            ->orWhere('account_code', $this->code);
    }
}
