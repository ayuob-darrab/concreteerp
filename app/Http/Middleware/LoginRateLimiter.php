<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Login Rate Limiter Middleware
 * 
 * حماية متقدمة ضد هجمات Brute Force على صفحة تسجيل الدخول:
 * - تحديد عدد المحاولات حسب IP + اسم المستخدم
 * - تصعيد وقت الحظر تدريجياً
 * - تسجيل المحاولات الفاشلة
 */
class LoginRateLimiter
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST')) {
            $key = $this->resolveRequestSignature($request);

            if ($this->limiter->tooManyAttempts($key, $this->maxAttempts())) {
                $seconds = $this->limiter->availableIn($key);

                $this->logBlockedAttempt($request, $seconds);

                return response()->json([
                    'message' => __('auth.throttle', ['seconds' => $seconds]),
                    'retry_after' => $seconds,
                ], 429)->withHeaders([
                    'Retry-After' => $seconds,
                    'X-RateLimit-Limit' => $this->maxAttempts(),
                    'X-RateLimit-Remaining' => 0,
                ]);
            }

            $this->limiter->hit($key, $this->decayMinutes() * 60);
        }

        $response = $next($request);

        if ($request->isMethod('POST') && $response->getStatusCode() === 200) {
            $this->limiter->clear($this->resolveRequestSignature($request));
        }

        return $response;
    }

    /**
     * توليد مفتاح فريد للطلب (IP + username)
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $username = strtolower(trim((string) $request->input('username', $request->input('email', ''))));

        return 'login_attempt:' . sha1($username . '|' . $request->ip());
    }

    /**
     * الحد الأقصى للمحاولات قبل الحظر
     */
    protected function maxAttempts(): int
    {
        return (int) config('auth.throttle.max_attempts', 5);
    }

    /**
     * مدة الحظر بالدقائق
     */
    protected function decayMinutes(): int
    {
        return (int) config('auth.throttle.decay_minutes', 15);
    }

    /**
     * تسجيل المحاولات المحظورة للمراجعة الأمنية
     */
    protected function logBlockedAttempt(Request $request, int $seconds): void
    {
        $username = $request->input('username', $request->input('email', 'unknown'));

        logger()->warning('Login rate limit exceeded', [
            'ip' => $request->ip(),
            'username' => $username,
            'user_agent' => $request->userAgent(),
            'blocked_for_seconds' => $seconds,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
