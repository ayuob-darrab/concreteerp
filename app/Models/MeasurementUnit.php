<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{
    use HasFactory;

    // اسم الجدول (اختياري إذا كان الاسم الافتراضي متطابق)
    protected $table = 'measurement_units';

    // الحقول القابلة للتعبئة
    protected $fillable = ['id','name','code','note',
    ];
}
