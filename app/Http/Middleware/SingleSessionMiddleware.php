<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SingleSessionMiddleware
{
    /**
     * مدة انتهاء الجلسة الافتراضية بالدقائق
     */
    protected $defaultSessionTimeout = 480;

    /**
     * Handle an incoming request.
     * 
     * تحديث وقت آخر نشاط للمستخدم
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // تحديث وقت آخر نشاط فقط
            $user->last_activity_at = Carbon::now();
            $user->save();
        }

        return $next($request);
    }
}
