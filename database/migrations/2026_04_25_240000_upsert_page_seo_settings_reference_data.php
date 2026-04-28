<?php

use App\Models\PageSeoSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * مزامنة بيانات SEO للصفحات العامة (مفتاح page_key) دون تكرار.
     * يطابق بيانات INSERT المرجعية — إن وُجد السجل فيُحدَّث، وإلا يُنشأ.
     */
    public function up(): void
    {
        if (! Schema::hasTable('page_seo_settings')) {
            return;
        }

        $pages = [
            [
                'page_key' => 'home',
                'page_title' => 'ConcreteERP | نظام إدارة مصانع الخرسانة الجاهزة',
                'meta_title' => 'ConcreteERP | نظام إدارة مصانع الخرسانة الجاهزة',
                'meta_description' => 'نظام ConcreteERP هو الحل المتكامل لإدارة مصانع الخرسانة الجاهزة - إدارة الطلبات، أسطول الميكسر، المخزون، المقاولين، والمحاسبة في منصة واحدة.',
                'meta_keywords' => 'نظام إدارة مصانع الخرسانة الجاهزة، برنامج ERP للخرسانة، نظام مقاولات وخرسانة، إدارة أسطول الميكسر، نظام محاسبة مصنع خرسانة',
                'og_title' => 'ConcreteERP | نظام إدارة مصانع الخرسانة الجاهزة',
                'og_description' => 'نظام ConcreteERP هو الحل المتكامل لإدارة مصانع الخرسانة الجاهزة - إدارة الطلبات، أسطول الميكسر، المخزون، المقاولين، والمحاسبة.',
                'canonical_url' => 'https://concreteerp.app/',
                'schema_markup' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type' => 'SoftwareApplication',
                    'name' => 'ConcreteERP',
                    'applicationCategory' => 'BusinessApplication',
                    'operatingSystem' => 'Web',
                    'description' => 'نظام إدارة متكامل لمصانع الخرسانة الجاهزة',
                    'inLanguage' => 'ar',
                    'url' => 'https://concreteerp.app',
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => '0',
                        'priceCurrency' => 'IQD',
                    ],
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'sitemap_priority' => 1.0,
                'sitemap_changefreq' => 'monthly',
            ],
            [
                'page_key' => 'system-benefits',
                'page_title' => 'فوائد النظام | ConcreteERP — برنامج ERP للخرسانة',
                'meta_title' => 'فوائد النظام | ConcreteERP — برنامج ERP للخرسانة',
                'meta_description' => 'اكتشف فوائد نظام ConcreteERP لإدارة مصانع الخرسانة الجاهزة: تقليل الهدر، تحسين الكفاءة، تتبع الشحنات، وإدارة المقاولين بسهولة.',
                'meta_keywords' => 'فوائد نظام ERP، برنامج إدارة الخرسانة، تحسين كفاءة المصنع، إدارة أسطول الميكسر، نظام مقاولات وخرسانة',
                'og_title' => 'فوائد النظام | ConcreteERP — برنامج ERP للخرسانة',
                'og_description' => 'اكتشف فوائد نظام ConcreteERP لإدارة مصانع الخرسانة الجاهزة: تقليل الهدر، تحسين الكفاءة، وإدارة المقاولين.',
                'canonical_url' => 'https://concreteerp.app/system-benefits',
                'schema_markup' => null,
                'sitemap_priority' => 0.8,
                'sitemap_changefreq' => 'monthly',
            ],
            [
                'page_key' => 'features',
                'page_title' => 'مميزات النظام | ConcreteERP — نظام مقاولات وخرسانة',
                'meta_title' => 'مميزات النظام | ConcreteERP — نظام مقاولات وخرسانة',
                'meta_description' => 'استكشف مميزات ConcreteERP: إدارة الطلبات، تتبع الشحنات، إدارة المخزون، الخلطات الخرسانية، المقاولين، الحضور، والتقارير المالية.',
                'meta_keywords' => 'مميزات نظام الخرسانة، إدارة الطلبات، تتبع الشحنات، إدارة المخزون، نظام محاسبة مصنع خرسانة، إدارة أسطول الميكسر',
                'og_title' => 'مميزات النظام | ConcreteERP — نظام مقاولات وخرسانة',
                'og_description' => 'استكشف مميزات ConcreteERP: إدارة الطلبات، تتبع الشحنات، إدارة المخزون، والتقارير المالية في منصة واحدة.',
                'canonical_url' => 'https://concreteerp.app/features',
                'schema_markup' => null,
                'sitemap_priority' => 0.8,
                'sitemap_changefreq' => 'monthly',
            ],
            [
                'page_key' => 'about',
                'page_title' => 'عن النظام | ConcreteERP — إدارة مصانع الخرسانة',
                'meta_title' => 'عن النظام | ConcreteERP — إدارة مصانع الخرسانة',
                'meta_description' => 'تعرف على فلسفة ConcreteERP وأهدافه في دعم مصانع الخرسانة الجاهزة ورقمنة العمليات التشغيلية والمالية في العراق والشرق الأوسط.',
                'meta_keywords' => 'عن ConcreteERP، نظام إدارة مصانع الخرسانة، برنامج ERP للخرسانة، رقمنة مصانع الخرسانة، نظام مقاولات وخرسانة',
                'og_title' => 'عن النظام | ConcreteERP — إدارة مصانع الخرسانة',
                'og_description' => 'تعرف على فلسفة ConcreteERP وأهدافه في دعم مصانع الخرسانة الجاهزة ورقمنة العمليات التشغيلية والمالية.',
                'canonical_url' => 'https://concreteerp.app/about',
                'schema_markup' => null,
                'sitemap_priority' => 0.8,
                'sitemap_changefreq' => 'monthly',
            ],
            [
                'page_key' => 'contact',
                'page_title' => 'تواصل معنا | ConcreteERP — نظام الخرسانة الجاهزة',
                'meta_title' => 'تواصل معنا | ConcreteERP — نظام الخرسانة الجاهزة',
                'meta_description' => 'تواصل مع فريق ConcreteERP للاستفسار عن الاشتراك أو الدعم الفني أو طلب عرض توضيحي لنظام إدارة مصانع الخرسانة الجاهزة.',
                'meta_keywords' => 'تواصل ConcreteERP، دعم فني، طلب عرض، اشتراك نظام الخرسانة، نظام إدارة مصانع الخرسانة الجاهزة',
                'og_title' => 'تواصل معنا | ConcreteERP — نظام الخرسانة الجاهزة',
                'og_description' => 'تواصل مع فريق ConcreteERP للاستفسار عن الاشتراك أو الدعم الفني أو طلب عرض توضيحي.',
                'canonical_url' => 'https://concreteerp.app/contact',
                'schema_markup' => json_encode([
                    '@context' => 'https://schema.org',
                    '@type' => 'LocalBusiness',
                    'name' => 'ConcreteERP',
                    'email' => 'ninesoftware1@gmail.com',
                    'telephone' => '+9647713863214',
                    'url' => 'https://concreteerp.app/contact',
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'sitemap_priority' => 0.8,
                'sitemap_changefreq' => 'monthly',
            ],
        ];

        foreach ($pages as $row) {
            $key = $row['page_key'];
            PageSeoSetting::query()->updateOrCreate(
                ['page_key' => $key],
                $row
            );
        }
    }

    public function down(): void
    {
        // عدم حذف البيانات — قد تكون معدّلة من لوحة التحكم
    }
};
