<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\CompanySubscription;

class CheckCompanySuspension
{
    /**
     * التحقق من أن شركة المستخدم ليست معطلة ولديها اشتراك نشط
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // إذا كان المستخدم من الشركة المالكة (SA)، السماح له بالمرور
            if ($user->company_code === 'SA') {
                return $next($request);
            }

            // التحقق من حالة الشركة والاشتراك
            if ($user->company_code) {
                $company = Company::where('code', $user->company_code)->first();

                // فحص إذا كانت الشركة غير مفعّلة
                if ($company && !$company->is_active) {
                    Auth::logout();
                    $companyName = $company->name;
                    return redirect('/login')
                        ->with('error', "🚫 حساب الشركة ({$companyName}) معطل. تم تسجيل خروجك. يرجى التواصل مع الإدارة.");
                }

                // فحص إذا كانت الشركة موقوفة
                if ($company && $company->is_suspended) {
                    Auth::logout();
                    $companyName = $company->name;
                    return redirect('/login')
                        ->with('error', "🚫 حساب شركة ({$companyName}) معطل حالياً. تم تسجيل خروجك تلقائياً. يرجى التواصل مع الإدارة.");
                }

                // فحص حساب مدير الشركة - إذا معطّل نمنع جميع مستخدمي الشركة
                $companyManager = \App\Models\User::where('company_code', $user->company_code)
                    ->where('usertype_id', 'CM')
                    ->first();
                if ($companyManager && !$companyManager->is_active) {
                    Auth::logout();
                    $companyName = $company->name ?? 'الشركة';
                    return redirect('/login')
                        ->with('error', "🚫 حساب الشركة ({$companyName}) معطل من قبل الإدارة. تم تسجيل خروجك.");
                }

                // فحص وجود اشتراك نشط
                $subscription = CompanySubscription::where('company_code', $user->company_code)
                    ->where('status', 'active')
                    ->first();

                if (!$subscription) {
                    Auth::logout();
                    $companyName = $company->name ?? 'الشركة';
                    return redirect('/login')
                        ->with('error', "⚠️ شركة ({$companyName}) لا تملك اشتراك نشط. تم تسجيل خروجك تلقائياً. يرجى التواصل مع الإدارة.");
                }
            }
        }

        return $next($request);
    }
}
