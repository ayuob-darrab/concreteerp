<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 255)->default('ConcreteERP');
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('og_title', 255)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 500)->nullable();
            $table->string('og_type', 50)->default('website');
            $table->string('twitter_card', 50)->default('summary_large_image');
            $table->string('twitter_site', 100)->nullable();
            $table->string('canonical_domain', 500)->nullable();
            $table->string('robots', 100)->default('index, follow');
            $table->string('locale', 10)->default('ar_IQ');
            $table->string('locale_alternate', 50)->nullable();
            $table->text('extra_meta')->nullable();
            $table->text('structured_data')->nullable();
            $table->timestamps();
        });

        DB::table('seo_settings')->insert([
            'site_name' => 'ConcreteERP - نظام إدارة شركات الخرسانة الجاهزة',
            'meta_title' => 'ConcreteERP | نظام ERP متكامل لإدارة شركات الخرسانة الجاهزة',
            'meta_description' => 'نظام ConcreteERP يساعد شركات الخرسانة الجاهزة في إدارة الطلبات، الأفرع، المقاولين، المخزون، الشحنات، الرواتب والحضور. حل متكامل للإنتاج والمبيعات والمحاسبة.',
            'meta_keywords' => 'نظام خرسانة جاهزة، ERP خرسانة، إدارة شركات خرسانة، طلبات خرسانة، مقاولين، خلطات خرسانية، إدارة مصانع خرسانة، العراق',
            'og_title' => 'ConcreteERP - نظام إدارة شركات الخرسانة الجاهزة',
            'og_description' => 'نظام ERP متكامل لإدارة شركات الخرسانة: الطلبات، الأفرع، المقاولين، المخزون والشحنات.',
            'og_image' => null,
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'twitter_site' => null,
            'canonical_domain' => null,
            'robots' => 'index, follow',
            'locale' => 'ar_IQ',
            'locale_alternate' => 'ar',
            'extra_meta' => null,
            'structured_data' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('seo_settings');
    }
};
