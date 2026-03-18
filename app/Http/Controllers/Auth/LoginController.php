<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * مدة انتهاء الجلسة الافتراضية بالدقائق (8 ساعات)
     */
    protected $defaultSessionTimeout = 480;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * التحقق من انتهاء صلاحية الجلسة بناءً على last_activity_at
     */
    protected function isSessionExpired($user)
    {
        if (!$user->last_activity_at) {
            return true;
        }

        $timeoutMinutes = $user->session_timeout_minutes ?? $this->defaultSessionTimeout;
        $lastActivity = Carbon::parse($user->last_activity_at);

        return Carbon::now()->diffInMinutes($lastActivity) > $timeoutMinutes;
    }

    /**
     * إنشاء بصمة الجهاز من معلومات المتصفح
     * بديل عن MAC Address (لا يمكن الحصول عليه من المتصفح)
     */
    protected function generateDeviceFingerprint(Request $request)
    {
        $data = [
            $request->header('User-Agent'),
            $request->ip(),
        ];
        return md5(implode('|', $data));
    }

    /**
     * تسجيل الدخول وتفعيل الجلسة
     */
    protected function activateSession($user, Request $request)
    {
        $user->is_logged_in = true;
        $user->device_fingerprint = $this->generateDeviceFingerprint($request);
        $user->last_activity_at = Carbon::now();
        $user->current_session_id = session()->getId();
        $user->save();
    }

    /**
     * إنهاء الجلسة
     */
    protected function deactivateSession($user)
    {
        $user->is_logged_in = false;
        $user->device_fingerprint = null;
        $user->last_activity_at = null;
        $user->current_session_id = null;
        $user->save();
    }

    public function loginuser(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        logger()->info('LOGIN FUNCTION CALLED', ['username' => $username]);

        // البحث عن المستخدم بالـ username
        $user = \App\Models\User::where('username', $username)->first();

        if ($user) {
            \Log::info('=== LOGIN CHECK ===', [
                'username' => $username,
                'is_active' => $user->is_active,
                'deactivated_by_subscription' => $user->deactivated_by_subscription,
                'is_logged_in' => $user->is_logged_in,
                'last_activity' => $user->last_activity_at,
            ]);

            if (!$user->is_active) {
                \Log::info('Account is DEACTIVATED - blocking login', ['user_id' => $user->id]);

                if ($user->deactivated_by_subscription) {
                    return back()
                        ->with('error', '🚫 حسابك معطل بسبب تجاوز حد المستخدمين في الاشتراك. يرجى التواصل مع مدير الشركة أو الإدارة.')
                        ->withInput(['username' => $username]);
                }

                return back()
                    ->with('error', '🚫 حسابك معطل. يرجى التواصل مع المسؤول.')
                    ->withInput(['username' => $username]);
            }

            // التحقق من تفعيل الشركة قبل السماح بالدخول
            if ($user->company_code && $user->company_code !== 'SA') {
                $company = \App\Models\Company::where('code', $user->company_code)->first();

                // فحص is_active للشركة
                if ($company && !$company->is_active) {
                    \Log::info('Company is_active=false - blocking login', ['company_code' => $user->company_code, 'is_active' => $company->is_active]);
                    return back()
                        ->with('error', "🚫 حساب الشركة ({$company->name}) معطل. لا يمكن تسجيل الدخول. يرجى التواصل مع الإدارة.")
                        ->withInput(['username' => $username]);
                }

                // فحص is_suspended للشركة
                if ($company && $company->is_suspended) {
                    \Log::info('Company is_suspended - blocking login', ['company_code' => $user->company_code]);
                    return back()
                        ->with('error', "🚫 تم إيقاف حساب شركة ({$company->name}) من قبل الإدارة.")
                        ->withInput(['username' => $username]);
                }

                // فحص حساب مدير الشركة (CM) - إذا معطّل نمنع جميع مستخدمي الشركة
                $companyManager = \App\Models\User::where('company_code', $user->company_code)
                    ->where('usertype_id', 'CM')
                    ->first();
                if ($companyManager && !$companyManager->is_active) {
                    \Log::info('Company manager is_active=false - blocking all company users', ['company_code' => $user->company_code]);
                    $companyDisplayName = $company?->name ?? $user->company_code;
                    return back()
                        ->with('error', "🚫 حساب الشركة ({$companyDisplayName}) معطل من قبل الإدارة. لا يمكن تسجيل الدخول.")
                        ->withInput(['username' => $username]);
                }
            }

            if ($user->is_logged_in) {
                \Log::info('User is logged in - checking expiry');

                if ($this->isSessionExpired($user)) {
                    \Log::info('Session expired - allowing login');
                    $this->deactivateSession($user);
                } else {
                    \Log::info('Session active - blocking login');
                    $lastActivity = Carbon::parse($user->last_activity_at)->diffForHumans();
                    return back()
                        ->with('error', "⚠️ الحساب مستخدم حالياً. آخر نشاط: {$lastActivity}")
                        ->withInput(['username' => $username]);
                }
            }
        }

        // تسجيل الدخول بالـ username
        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                \Log::warning('User passed Auth::attempt but is_active=false', ['user_id' => $user->id]);

                if ($user->deactivated_by_subscription) {
                    return back()->with('error', '🚫 حسابك معطل بسبب تجاوز حد المستخدمين في الاشتراك. يرجى التواصل مع مدير الشركة أو الإدارة.');
                }

                return back()->with('error', '🚫 حسابك معطل. يرجى التواصل مع المسؤول.');
            }

            if ($user->company_code && $user->company_code !== 'SA') {
                $company = \App\Models\Company::where('code', $user->company_code)->first();

                if ($company && !$company->is_active) {
                    Auth::logout();
                    return back()->with('error', "🚫 حساب الشركة ({$company->name}) معطل. لا يمكن تسجيل الدخول. يرجى التواصل مع الإدارة.");
                }

                if ($company && $company->is_suspended) {
                    Auth::logout();
                    return back()->with('error', "🚫 تم إيقاف حساب شركة ({$company->name}) من قبل الإدارة.");
                }

                // فحص حساب مدير الشركة
                $companyManager = \App\Models\User::where('company_code', $user->company_code)
                    ->where('usertype_id', 'CM')
                    ->first();
                if ($companyManager && !$companyManager->is_active) {
                    Auth::logout();
                    $companyDisplayName = $company?->name ?? $user->company_code;
                    return back()->with('error', "🚫 حساب الشركة ({$companyDisplayName}) معطل من قبل الإدارة. لا يمكن تسجيل الدخول.");
                }

                $subscription = \App\Models\CompanySubscription::where('company_code', $user->company_code)
                    ->where('status', 'active')
                    ->first();

                if (!$subscription) {
                    Auth::logout();
                    $companyName = $company?->name ?? 'الشركة';
                    return back()->with('error', "⚠️ شركة ({$companyName}) لا تملك اشتراك نشط.");
                }
            }

            $this->activateSession($user, $request);

            if ($user->isDriver()) {
                return redirect()->route('driver.dashboard');
            }
            return redirect('/home');
        }

        return back()->with('error', 'اسم المستخدم أو كلمة المرور غير صحيحة');
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // ✅ إلغاء تفعيل الجلسة
            $this->deactivateSession($user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // استخدم route() لتجنب مشاكل مسار المشروع الفرعي (مثل /ConcreteERP)
        return redirect()->route('system-benefits')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
