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
        Cache::forget('settings.layout_fonts');
        Cache::forget('settings.force_https_flag');
    }

    /**
     * إعدادات الخط للواجهات (استعلام واحد + كاش — يستبدل أربع استدعاءات لـ get())
     */
    public static function getLayoutFontSettings(): array
    {
        return Cache::remember('settings.layout_fonts', 3600, function () {
            $defaults = [
                'font_family' => 'Cairo',
                'font_size' => '14',
                'font_color_light' => '#000000',
                'font_color_dark' => '#ffffff',
            ];
            $rows = self::whereIn('key', array_keys($defaults))->get()->keyBy('key');
            $resolved = [];
            foreach ($defaults as $key => $default) {
                $resolved[$key] = $rows->has($key)
                    ? self::castValue($rows[$key]->value, $rows[$key]->type)
                    : $default;
            }

            return [
                'app_font_family' => $resolved['font_family'],
                'app_font_size' => $resolved['font_size'],
                'app_font_color_light' => $resolved['font_color_light'],
                'app_font_color_dark' => $resolved['font_color_dark'],
            ];
        });
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
