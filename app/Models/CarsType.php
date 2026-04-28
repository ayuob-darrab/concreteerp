<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CarsType extends Model
{
    use HasFactory;
    protected $table = 'cars_types'; // اسم الجدول إذا كان مختلف
    protected $fillable = ['id', 'code', 'name', 'company_code', 'capacity', 'hose_length', 'note'];

    protected $casts = [
        'capacity' => 'float',
        'hose_length' => 'float',
    ];

    public function isMixerType(): bool
    {
        return (string) ($this->code ?? '') === 'CT-MIXER';
    }

    public function isPumpType(): bool
    {
        return (string) ($this->code ?? '') === 'CT-PUMP';
    }


    public function cars()
    {
        return $this->hasMany(cars::class, 'car_type_id', 'id');
    }

    /**
     * توليد كود فريد لنوع السيارة
     * @param string $companyCode
     * @return string
     */
    public static function generateUniqueCode($companyCode)
    {
        do {
            // توليد كود من 6 أحرف وأرقام
            $code = 'CT-' . strtoupper(Str::random(5));
        } while (self::where('company_code', $companyCode)->where('code', $code)->exists());

        return $code;
    }
}
