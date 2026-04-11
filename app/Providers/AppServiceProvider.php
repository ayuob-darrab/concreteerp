<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use App\Models\Advance;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

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
        // كشف N+1 عند التفعيل فقط (خطة-تطوير / eager-loading) — قد يكسر صفحات حتى تُصلح الاستعلامات
        if (filter_var(env('PERFORMANCE_DETECT_N1', false), FILTER_VALIDATE_BOOLEAN) && ! $this->app->isProduction()) {
            Model::preventLazyLoading(true);
        }

        // عنوان الجذر للروابط المولّدة: في الإنتاج من APP_URL؛ في local إن فتحت من IP الشبكة يُستخدم نفس المضيف والمسار
        if ($this->app->runningInConsole() === false) {
            $configured = rtrim((string) config('app.url'), '/');
            if ($configured !== '') {
                $request = request();
                $baseUrl = App::environment('local')
                    ? $this->resolveLocalRootUrl($request, $configured)
                    : $configured;
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

        // خط وحجم ولون الخط من إعدادات النظام (صفحة الإعدادات العامة) لجميع واجهات layouts
        View::composer(['layouts.app', 'layouts.auth'], function ($view) {
            try {
                $view->with(Setting::getLayoutFontSettings());
            } catch (\Throwable $e) {
                $view->with([
                    'app_font_family' => 'Cairo',
                    'app_font_size' => '14',
                    'app_font_color_light' => '#000000',
                    'app_font_color_dark' => '#ffffff',
                ]);
            }
        });
    }

    /**
     * في التطوير المحلي: إن كان الطلب من مضيف يختلف عن APP_URL (مثلاً IP الشبكة بدل localhost)
     * نُبقي مسار المشروع من APP_URL ونستبدل المخطط والمضيف بعنوان الطلب الحقيقي.
     */
    private function resolveLocalRootUrl(?\Illuminate\Http\Request $request, string $configured): string
    {
        if (!$request) {
            return $configured;
        }

        $configuredHost = parse_url($configured, PHP_URL_HOST);
        if (!$configuredHost) {
            return $configured;
        }

        $requestHost = $request->getHost();
        if (strcasecmp($configuredHost, $requestHost) === 0) {
            return $configured;
        }

        if ($this->isLocalHostPair($configuredHost, $requestHost)) {
            return $configured;
        }

        $path = parse_url($configured, PHP_URL_PATH) ?? '';
        $path = ($path === '/' || $path === null) ? '' : rtrim((string) $path, '/');

        return rtrim($request->getSchemeAndHttpHost(), '/') . $path;
    }

    private function isLocalHostPair(string $a, string $b): bool
    {
        $locals = ['localhost', '127.0.0.1', '::1'];
        $aLocal = in_array(strtolower($a), $locals, true);
        $bLocal = in_array(strtolower($b), $locals, true);

        return $aLocal && $bLocal;
    }
}
