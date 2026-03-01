<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'size',
        'companies_count',
        'users_count',
        'tables_count',
        'notes',
        'created_by',
    ];

    /**
     * العلاقة مع المستخدم الذي قام بالنسخ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
