<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Headers Middleware
 * 
 * يضيف رؤوس HTTP الأمنية لحماية التطبيق من:
 * - Clickjacking (X-Frame-Options)
 * - XSS attacks (X-XSS-Protection, Content-Security-Policy)
 * - MIME sniffing (X-Content-Type-Options)
 * - Information leakage (Referrer-Policy, Permissions-Policy)
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ══════════════════════════════════════════════════════════════════
        // CRITICAL: حماية من Clickjacking - منع تضمين الصفحة في iframe
        // ══════════════════════════════════════════════════════════════════
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // ══════════════════════════════════════════════════════════════════
        // CRITICAL: منع MIME sniffing - يمنع المتصفح من تخمين نوع المحتوى
        // ══════════════════════════════════════════════════════════════════
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ══════════════════════════════════════════════════════════════════
        // HIGH: حماية XSS المدمجة في المتصفح (للمتصفحات القديمة)
        // ══════════════════════════════════════════════════════════════════
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // ══════════════════════════════════════════════════════════════════
        // HIGH: التحكم في المعلومات المُرسلة في Referrer header
        // ══════════════════════════════════════════════════════════════════
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ══════════════════════════════════════════════════════════════════
        // MEDIUM: تقييد صلاحيات المتصفح (كاميرا، ميكروفون، موقع جغرافي)
        // ══════════════════════════════════════════════════════════════════
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // ══════════════════════════════════════════════════════════════════
        // HIGH: Content Security Policy - حماية شاملة من XSS
        // ══════════════════════════════════════════════════════════════════
        if (config('app.env') === 'production') {
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
                "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:",
                "img-src 'self' data: blob: https:",
                "connect-src 'self'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                "base-uri 'self'",
                "object-src 'none'",
            ]);
            $response->headers->set('Content-Security-Policy', $csp);
        }

        // ══════════════════════════════════════════════════════════════════
        // CRITICAL: فرض HTTPS (HSTS) - في الإنتاج فقط
        // ══════════════════════════════════════════════════════════════════
        if (config('app.env') === 'production' && $request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // ══════════════════════════════════════════════════════════════════
        // MEDIUM: إخفاء معلومات السيرفر
        // ══════════════════════════════════════════════════════════════════
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // ══════════════════════════════════════════════════════════════════
        // HIGH: منع التخزين المؤقت للصفحات الحساسة
        // ══════════════════════════════════════════════════════════════════
        if ($this->isSensitivePath($request)) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }

    /**
     * تحديد المسارات الحساسة التي يجب عدم تخزينها مؤقتاً
     */
    private function isSensitivePath(Request $request): bool
    {
        $sensitivePaths = [
            'login',
            'logout',
            'password',
            'accounts',
            'admin',
            'employees',
            'payroll',
            'subscriptions',
            'companies',
        ];

        foreach ($sensitivePaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return true;
            }
        }

        return false;
    }
}
