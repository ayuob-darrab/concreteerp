<?php

use App\Models\PageSeoSetting;
use App\Models\PublicContactSettings;
use App\Models\PublicDisplayBlock;
use App\Models\PublicDisplayVideo;
use App\Models\SeoSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * استرجاع محتوى صفحات العرض و SEO من نسخة SQL القديمة (القاعدة القديمه.sql)
     * — الكتل والفيديو: firstOrCreate (page_key + sort_order) لتجنب التكرار وعدم تعديل ما وُجود مسبقاً
     * — إعدادات التواصل و SEO العام و SEO للصفحات: تحديث من القيم المُصدَّرة
     */
    public function up(): void
    {
        $preferExisting = static function ($existing, $incoming) {
            $val = trim((string) $existing);
            return $val !== '' ? $existing : $incoming;
        };

        $base = __DIR__.'/../seeders/legacy';
        if (! is_file($base.'/marketing_meta_from_iraq_sql_dump_2026.php')) {
            return;
        }

        $meta = require $base.'/marketing_meta_from_iraq_sql_dump_2026.php';
        $allBlocks = array_merge(
            require $base.'/blocks_1_18.php',
            require $base.'/blocks_19_37.php',
        );

        if (Schema::hasTable('public_contact_settings') && ! empty($meta['public_contact_settings'])) {
            $row = PublicContactSettings::query()->orderBy('id')->first();
            if ($row) {
                $payload = $meta['public_contact_settings'];
                foreach (['email', 'whatsapp', 'telegram', 'facebook', 'instagram', 'phone'] as $field) {
                    $payload[$field] = $preferExisting($row->{$field}, $payload[$field] ?? null);
                }
                $row->update($payload);
            } else {
                PublicContactSettings::query()->create($meta['public_contact_settings']);
            }
        }

        if (Schema::hasTable('seo_settings') && ! empty($meta['seo_settings'])) {
            $seo = SeoSetting::query()->orderBy('id')->first();
            if ($seo) {
                $payload = $meta['seo_settings'];
                foreach (['canonical_domain', 'og_image', 'twitter_site'] as $field) {
                    $payload[$field] = $preferExisting($seo->{$field}, $payload[$field] ?? null);
                }
                $seo->update($payload);
            } else {
                SeoSetting::query()->create($meta['seo_settings']);
            }
        }

        if (Schema::hasTable('page_seo_settings') && ! empty($meta['page_seo_settings'])) {
            foreach ($meta['page_seo_settings'] as $row) {
                $k = $row['page_key'];
                $existing = PageSeoSetting::query()->where('page_key', $k)->first();
                if ($existing) {
                    $row['canonical_url'] = $preferExisting($existing->canonical_url, $row['canonical_url'] ?? null);
                    $existing->update($row);
                    continue;
                }
                PageSeoSetting::query()->create($row);
            }
        }

        if (Schema::hasTable('public_display_videos') && ! empty($meta['public_display_videos'])) {
            foreach ($meta['public_display_videos'] as $v) {
                PublicDisplayVideo::query()->firstOrCreate(
                    [
                        'page_key' => $v['page_key'],
                        'sort_order' => $v['sort_order'],
                    ],
                    $v
                );
            }
        }

        if (Schema::hasTable('public_display_blocks')) {
            foreach ($allBlocks as $b) {
                $sort = (int) $b['sort_order'];
                $pageKey = $b['page_key'];
                PublicDisplayBlock::query()->firstOrCreate(
                    [
                        'page_key' => $pageKey,
                        'sort_order' => $sort,
                    ],
                    $b
                );
            }
        }
    }

    public function down(): void
    {
    }
};
