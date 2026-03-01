<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class ForceHttps
{
    /**
     * فرض HTTPS على جميع الطلبات (فقط على الإنترنت)
     */
    public function handle(Request $request, Closure $next)
    {
        // تجاهل localhost - لا نفرض HTTPS عليه
        $host = $request->getHost();
        if ($this->isLocalhost($host)) {
            return $next($request);
        }

        // التحقق من إعداد فرض HTTPS
        try {
            $forceHttps = Setting::get('force_https', false);
        } catch (\Exception $e) {
            // إذا لم يكن جدول الإعدادات موجود
            $forceHttps = false;
        }

        // إذا كان الإعداد مفعّل والاتصال غير آمن → حوّل لـ HTTPS
        if ($forceHttps && !$request->secure()) {
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
