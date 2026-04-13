<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;

class ForceHttps
{
    /**
     * فرض HTTPS على جميع الطلبات (فقط على الإنترنت)
     *
     * أولويات: 1) env FORCE_HTTPS  2) APP_URL يبدأ بـ https في الإنتاج  3) إعداد force_https من لوحة النظام
     */
    public function handle(Request $request, Closure $next)
    {
        // تجاهل localhost - لا نفرض HTTPS عليه
        $host = $request->getHost();
        if ($this->isLocalhost($host)) {
            return $next($request);
        }

        $envFlag = env('FORCE_HTTPS');
        $forceFromAppUrl = App::isProduction()
            && str_starts_with(rtrim((string) config('app.url'), '/'), 'https://');
        $forceFromEnv = $envFlag === null
            ? $forceFromAppUrl
            : filter_var($envFlag, FILTER_VALIDATE_BOOLEAN);

        // كاش ساعة — يقلّل استعلام قاعدة البيانات في كل طلب (عند النشر خارج localhost)
        $forceFromSettings = false;
        if (! $forceFromEnv) {
            try {
                $forceFromSettings = Cache::remember('settings.force_https_flag', 3600, function () {
                    return (bool) Setting::get('force_https', false);
                });
            } catch (\Throwable $e) {
                $forceFromSettings = false;
            }
        }

        $mustUseHttps = $forceFromEnv || $forceFromSettings;

        if ($mustUseHttps && ! $request->secure()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }

    /**
     * التحقق هل الموقع يعمل على localhost
     */
    private function isLocalhost($host): bool
    {
        $localHosts = [
            'localhost',
            '127.0.0.1',
            '::1',
        ];

        // تحقق من localhost أو أي IP محلي
        if (in_array($host, $localHosts)) {
            return true;
        }

        // تحقق من 192.168.x.x أو 10.x.x.x (شبكات محلية)
        if (preg_match('/^(192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[01])\.)/', $host)) {
            return true;
        }

        return false;
    }
}
