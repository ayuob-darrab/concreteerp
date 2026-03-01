<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftTime extends Model
{
    use HasFactory;
    protected $table = 'shift_times';

    protected $fillable = [
        'id','company_code','name','start_time','end_time','notes'
    ];
}
