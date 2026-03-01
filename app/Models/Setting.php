<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    /**
     * الحصول على قيمة إعداد معين
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * تعيين قيمة إعداد
     */
    public static function set($key, $value)
    {
        $setting = self::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            self::create(['key' => $key, 'value' => $value]);
        }

        // مسح الكاش
        Cache::forget('settings');
    }

    /**
     * الحصول على جميع الإعدادات
     */
    public static function getAllSettings()
    {
        return Cache::remember('settings', 3600, function () {
            return self::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * الحصول على إعدادات مجموعة معينة
     */
    public static function getByGroup($group)
    {
        return self::where('group', $group)->get();
    }

    /**
     * تحويل القيمة حسب النوع
     */
    private static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}
