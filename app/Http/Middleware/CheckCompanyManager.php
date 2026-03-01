<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCompanyManager
{
    /**
     * التحقق من أن المستخدم مدير شركة أو سوبر أدمن
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = auth()->user();

        // السوبر أدمن يمكنه الوصول دائماً
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // مدير الشركة يمكنه الوصول
        if ($user->isCompanyManager()) {
            return $next($request);
        }

        // غير مصرح
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول. يجب أن تكون مدير شركة.'
            ], 403);
        }

        return redirect()->back()->with('error', 'غير مصرح لك بالوصول. يجب أن تكون مدير شركة.');
    }
}
