<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        /** @var \App\Models\Company|null نفس كائن الشركة بعد نجاح الدخول دون استعلام مكرر */
        $companyPrecheck = null;

        // البحث عن المستخدم بالـ username
        $user = \App\Models\User::where('username', $username)->first();

        if ($user) {
            if (!$user->is_active) {
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
                $companyPrecheck = $company;

                // فحص is_active للشركة
                if ($company && !$company->is_active) {
                    return back()
                        ->with('error', "🚫 حساب الشركة ({$company->name}) معطل. لا يمكن تسجيل الدخول. يرجى التواصل مع الإدارة.")
                        ->withInput(['username' => $username]);
                }

                // فحص is_suspended للشركة
                if ($company && $company->is_suspended) {
                    return back()
                        ->with('error', "🚫 تم إيقاف حساب شركة ({$company->name}) من قبل الإدارة.")
                        ->withInput(['username' => $username]);
                }

                // فحص حساب مدير الشركة (CM) - إذا معطّل نمنع جميع مستخدمي الشركة
                $companyManager = \App\Models\User::where('company_code', $user->company_code)
                    ->where('usertype_id', 'CM')
                    ->first();
                if ($companyManager && !$companyManager->is_active) {
                    $companyDisplayName = $company?->name ?? $user->company_code;
                    return back()
                        ->with('error', "🚫 حساب الشركة ({$companyDisplayName}) معطل من قبل الإدارة. لا يمكن تسجيل الدخول.")
                        ->withInput(['username' => $username]);
                }
            }

            if ($user->is_logged_in) {
                if ($this->isSessionExpired($user)) {
                    $this->deactivateSession($user);
                } else {
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
                if ($user->deactivated_by_subscription) {
                    return back()->with('error', '🚫 حسابك معطل بسبب تجاوز حد المستخدمين في الاشتراك. يرجى التواصل مع مدير الشركة أو الإدارة.');
                }

                return back()->with('error', '🚫 حسابك معطل. يرجى التواصل مع المسؤول.');
            }

            // الشركة ومديرها تُفحصان قبل Auth::attempt — هنا فقط الاشتراك
            if ($user->company_code && $user->company_code !== 'SA') {
                $company = $companyPrecheck ?? \App\Models\Company::where('code', $user->company_code)->first();

                $subscription = \App\Models\CompanySubscription::where('company_code', $user->company_code)
                    ->where('status', 'active')
                    ->first();

                if (!$subscription) {
                    Auth::logout();
                    $companyName = $company?->name ?? 'الشركة';
                    return back()->with('error', "⚠️ شركة ({$companyName}) لا تملك اشتراك نشط.");
                }

                if (!$subscription->allowsApplicationAccess()) {
                    Auth::logout();
                    $companyName = $company?->name ?? 'الشركة';
                    return back()->with('error', "🚫 انتهى اشتراك شركة ({$companyName}) وانتهت فترة السماح. يرجى التجديد أو التواصل مع الإدارة.");
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

        // بعد تسجيل الخروج: الرجوع للصفحة الرئيسية
        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
