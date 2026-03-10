<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use App\Models\Advance;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // استخدام عنوان التطبيق من .env (محلياً: مثلاً http://localhost/ConcreteERP | إنتاج: https://concreteerp.app) لصحة روابط النماذج والسايدبار والبطاقات
        if ($this->app->runningInConsole() === false) {
            $baseUrl = rtrim(config('app.url'), '/');
            if ($baseUrl !== '') {
                URL::forceRootUrl($baseUrl);
            }
        }

        // مشاركة عدد السلف المعلقة مع الـ sidebar
        View::composer('layouts.sidebar', function ($view) {
            $pendingAdvancesCount = 0;

            if (Auth::check()) {
                $user = Auth::user();
                $companyCode = session('company_code') ?? $user->company_code;
                $branchId = session('branch_id') ?? $user->branch_id;

                $query = Advance::where('status', 'pending');

                if ($companyCode) {
                    $query->where('company_code', $companyCode);
                }

                if ($branchId && $user->usertype_id != 'CM') {
                    $query->where('branch_id', $branchId);
                }

                $pendingAdvancesCount = $query->count();
            }

            $view->with('pendingAdvancesCount', $pendingAdvancesCount);
        });

        // مشاركة إعدادات SEO مع جميع الصفحات (للوسوم في head)
        View::composer(['layouts.app', 'layouts.auth'], function ($view) {
            try {
                $view->with('seo', SeoSetting::current());
            } catch (\Throwable $e) {
                $view->with('seo', null);
            }
        });
    }
}
