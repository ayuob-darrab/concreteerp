<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorCheckStatusHistory extends Model
{
    protected $table = 'contractor_check_status_history';

    public $timestamps = false;

    protected $fillable = [
        'check_id',
        'status',
        'notes',
        'changed_by',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // =====================
    // العلاقات
    // =====================

    public function check()
    {
        return $this->belongsTo(ContractorCheck::class, 'check_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
