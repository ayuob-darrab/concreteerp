<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * تعبئة عناوين كانت فارغة في المحتوى الافتراضي لتسهيل التعرف على الكتل في لوحة التحكم والموقع.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('public_display_blocks')) {
            return;
        }

        $pairs = [
            ['landing', 1, 'صورة اليوم والعمليات'],
            ['landing', 2, 'الضغط اليومي وقرار سريع'],
            ['landing', 3, 'التركيز على الجودة والنمو'],
            ['landing', 5, 'استكشف بقية الموقع'],
            ['system_benefits', 1, 'رؤية موحّدة لدورة العمل'],
            ['system_benefits', 6, 'تنبيه الوصول والصلاحيات'],
            ['system_benefits', 13, 'تصميم مخصص لمصانع الخرسانة'],
            ['features', 1, 'منصة تشغيلية وتقارير'],
            ['features', 12, 'ملاحظة عن التفعيل والخطط'],
            ['about', 5, 'التطوير المستمر للمنتج'],
        ];

        foreach ($pairs as [$pageKey, $sortOrder, $title]) {
            DB::table('public_display_blocks')
                ->where('page_key', $pageKey)
                ->where('sort_order', $sortOrder)
                ->where(function ($q) {
                    $q->whereNull('title')->orWhere('title', '');
                })
                ->update(['title' => $title, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // لا نرجع العناوين تلقائياً لتفادي مسح عناوين عدّلها المستخدم
    }
};
