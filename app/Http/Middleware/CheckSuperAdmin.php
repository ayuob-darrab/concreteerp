<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSuperAdmin
{
    /**
     * التحقق من أن المستخدم سوبر أدمن
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

        // سوبر أدمن أو أدمن (نوع AD مع company_code SA)
        if ($user->isSuperAdmin()) {
            return $next($request);
        }
        if ($user->usertype_id === 'AD' && $user->company_code === 'SA') {
            return $next($request);
        }

        // غير مصرح
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول. صلاحيات السوبر أدمن مطلوبة.'
            ], 403);
        }

        return redirect()->back()->with('error', 'غير مصرح لك بالوصول. صلاحيات السوبر أدمن مطلوبة.');
    }
}
