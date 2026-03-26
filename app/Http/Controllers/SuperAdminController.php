<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\City;
use App\Models\EmployeeType;
use App\Models\CarsType;
use App\Models\Role;
use App\Models\Setting;
use App\Models\SeoSetting;
use App\Models\Backup;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    /**
     * التحقق من صلاحيات السوبر أدمن
     */
    private function checkSuperAdmin()
    {
        $user = auth()->user();
        if ($user->usertype_id !== 'SA' || $user->company_code !== 'SA' || $user->account_code !== 'SA') {
            abort(403, 'غير مصرح لك بالوصول');
        }
    }

    // ============================================
    // إدارة المستخدمين
    // ============================================

    /**
     * عرض جميع المستخدمين
     */
    public function users(Request $request)
    {
        $this->checkSuperAdmin();

        // استثناء حسابات إدارة النظام (SA/AD) من قائمة المستخدمين العامة
        $query = User::with('CompanyName')
            ->where(function ($q) {
                $q->where('company_code', '!=', 'SA')
                    ->orWhereNotIn('usertype_id', ['SA', 'AD']);
            });

        // فلتر الحالة
        if ($request->filled('status')) {
            $query->where('is_active', (int) $request->status);
        }

        // فلتر الشركة
        if ($request->filled('company')) {
            $query->where('company_code', $request->company);
        }

        // فلتر نوع الحساب
        if ($request->filled('type')) {
            $query->where('usertype_id', $request->type);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        // قائمة الشركات للفلتر (استثناء شركة السوبر أدمن SA)
        $companies = Company::where('code', '!=', 'SA')->orderBy('name')->get(['code', 'name']);

        // الإحصائيات تعكس نفس الاستثناء
        $statsBase = User::where(function ($q) {
            $q->where('company_code', '!=', 'SA')
                ->orWhereNotIn('usertype_id', ['SA', 'AD']);
        });

        $stats = [
            'total' => (clone $statsBase)->count(),
            'active' => (clone $statsBase)->where('is_active', 1)->count(),
            'inactive' => (clone $statsBase)->where('is_active', 0)->count(),
            'companies' => (clone $statsBase)->where('usertype_id', 'CM')->count(),
            'branches' => (clone $statsBase)->where('usertype_id', 'BM')->count(),
            'contractors' => (clone $statsBase)->where('account_code', 'cont')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'companies'));
    }

    /**
     * عرض حسابات السوبر أدمن فقط
     */
    public function superAdminUsers(Request $request)
    {
        $this->checkSuperAdmin();

        $baseQuery = User::with('CompanyName')
            ->where('company_code', 'SA')
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('usertype_id', 'SA')->where('account_code', 'SA');
                })->orWhere(function ($q2) {
                    $q2->where('usertype_id', 'AD')->where('account_code', 'admin');
                });
            });

        // فلتر الحالة (نشط / غير نشط)
        if ($request->filled('status')) {
            $baseQuery->where('is_active', (int) $request->status);
        }

        $users = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(50)
            ->withQueryString();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->where('is_active', 1)->count(),
            'inactive' => (clone $baseQuery)->where('is_active', 0)->count(),
        ];

        return view('admin.users.super-admin', compact('users', 'stats'));
    }

    /**
     * نموذج إنشاء حساب سوبر أدمن جديد
     */
    public function createSuperAdminUser()
    {
        $this->checkSuperAdmin();

        return view('admin.users.super-admin-create');
    }

    /**
     * حفظ حساب سوبر أدمن جديد
     */
    public function storeSuperAdminUser(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'account_type' => 'required|in:SA,AD',
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'is_active' => 'nullable|boolean',
        ], [
            'account_type.required' => 'نوع الحساب مطلوب',
            'account_type.in' => 'نوع الحساب غير صالح',
            'fullname.required' => 'الاسم مطلوب',
            'username.required' => 'اسم المستخدم مطلوب',
            'username.unique' => 'اسم المستخدم مستخدم مسبقاً',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        $accountType = $request->account_type;
        if ($accountType === 'SA') {
            $usertypeId = 'SA';
            $companyCode = 'SA';
            $accountCode = 'SA';
        } else {
            $usertypeId = 'AD';
            $companyCode = 'SA';
            $accountCode = 'admin';
        }

        User::create([
            'fullname' => $request->fullname,
            'username' => strtolower(trim($request->username)),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'usertype_id' => $usertypeId,
            'company_code' => $companyCode,
            'account_code' => $accountCode,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $typeLabel = $accountType === 'SA' ? 'سوبر أدمن' : 'أدمن';
        return redirect()->route('admin.super-admin-users')->with('success', "تم إنشاء حساب {$typeLabel} جديد بنجاح");
    }

    /**
     * نموذج تعديل حساب سوبر أدمن
     */
    public function editSuperAdminUser($id)
    {
        $this->checkSuperAdmin();

        $user = User::where('id', $id)
            ->where('company_code', 'SA')
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('usertype_id', 'SA')->where('account_code', 'SA');
                })->orWhere(function ($q2) {
                    $q2->where('usertype_id', 'AD')->where('account_code', 'admin');
                });
            })
            ->firstOrFail();

        return view('admin.users.super-admin-edit', compact('user'));
    }

    /**
     * تحديث حساب سوبر أدمن
     */
    public function updateSuperAdminUser(Request $request, $id)
    {
        $this->checkSuperAdmin();

        $user = User::where('id', $id)
            ->where('company_code', 'SA')
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('usertype_id', 'SA')->where('account_code', 'SA');
                })->orWhere(function ($q2) {
                    $q2->where('usertype_id', 'AD')->where('account_code', 'admin');
                });
            })
            ->firstOrFail();

        $request->validate([
            'account_type' => 'required|in:SA,AD',
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'is_active' => 'nullable|boolean',
        ], [
            'account_type.required' => 'نوع الحساب مطلوب',
            'account_type.in' => 'نوع الحساب غير صالح',
            'fullname.required' => 'الاسم مطلوب',
            'username.required' => 'اسم المستخدم مطلوب',
            'username.unique' => 'اسم المستخدم مستخدم مسبقاً',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        $accountType = $request->account_type;
        if ($accountType === 'SA') {
            $user->usertype_id = 'SA';
            $user->company_code = 'SA';
            $user->account_code = 'SA';
        } else {
            $user->usertype_id = 'AD';
            $user->company_code = 'SA';
            $user->account_code = 'admin';
        }

        $user->fullname = $request->fullname;
        $user->username = strtolower(trim($request->username));
        $user->email = $request->email;
        $user->is_active = $request->boolean('is_active', true);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $typeLabel = $user->usertype_id === 'SA' ? 'السوبر أدمن' : 'الأدمن';
        return redirect()->route('admin.super-admin-users')->with('success', "تم تحديث حساب {$typeLabel} بنجاح");
    }

    /**
     * الأدوار والصلاحيات
     */
    public function roles()
    {
        $this->checkSuperAdmin();

        // محاولة جلب الأدوار من قاعدة البيانات
        try {
            $roles = Role::orderBy('is_system', 'desc')->orderBy('name')->get();

            // إضافة عدد المستخدمين لكل دور
            $roles = $roles->map(function ($role) {
                $role->user_count = User::where('usertype_id', $role->code)
                    ->orWhere('account_code', $role->code)
                    ->count();
                return $role;
            });
        } catch (\Exception $e) {
            // إذا لم يكن الجدول موجوداً، استخدم البيانات الثابتة
            $roles = collect([
                (object)['id' => 1, 'code' => 'SA', 'name' => 'سوبر أدمن', 'description' => 'صلاحيات كاملة على النظام', 'is_system' => true, 'user_count' => User::where('usertype_id', 'SA')->count()],
                (object)['id' => 2, 'code' => 'CM', 'name' => 'مدير شركة', 'description' => 'إدارة شركة كاملة', 'is_system' => true, 'user_count' => User::where('usertype_id', 'CM')->count()],
                (object)['id' => 3, 'code' => 'BM', 'name' => 'مدير فرع', 'description' => 'إدارة فرع واحد', 'is_system' => true, 'user_count' => User::where('usertype_id', 'BM')->count()],
                (object)['id' => 4, 'code' => 'cont', 'name' => 'مقاول', 'description' => 'حساب مقاول', 'is_system' => true, 'user_count' => User::where('account_code', 'cont')->count()],
                (object)['id' => 5, 'code' => 'delegate', 'name' => 'مندوب', 'description' => 'حساب مندوب', 'is_system' => true, 'user_count' => User::where('account_code', 'delegate')->count()],
            ]);
        }

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * إنشاء دور جديد
     */
    public function storeRole(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'code' => 'required|string|max:20|unique:roles,code|regex:/^[a-zA-Z0-9_]+$/',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ], [
            'code.required' => 'رمز الدور مطلوب',
            'code.unique' => 'رمز الدور موجود مسبقاً',
            'code.regex' => 'رمز الدور يجب أن يحتوي على أحرف إنجليزية وأرقام فقط',
            'name.required' => 'اسم الدور مطلوب',
        ]);

        try {
            Role::create([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'description' => $request->description,
                'is_system' => false,
                'is_active' => true,
            ]);

            return redirect()->route('admin.roles')->with('success', 'تم إنشاء الدور بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الدور: ' . $e->getMessage());
        }
    }

    /**
     * حذف دور
     */
    public function deleteRole($id)
    {
        $this->checkSuperAdmin();

        try {
            $role = Role::findOrFail($id);

            if ($role->is_system) {
                return redirect()->back()->with('error', 'لا يمكن حذف أدوار النظام الأساسية');
            }

            // التحقق من عدم وجود مستخدمين بهذا الدور
            $usersCount = User::where('usertype_id', $role->code)
                ->orWhere('account_code', $role->code)
                ->count();

            if ($usersCount > 0) {
                return redirect()->back()->with('error', "لا يمكن حذف الدور لأنه مستخدم من قبل {$usersCount} مستخدم");
            }

            $role->delete();

            return redirect()->route('admin.roles')->with('success', 'تم حذف الدور بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الدور');
        }
    }

    /**
     * سجلات النشاط
     */
    public function activityLogs()
    {
        $this->checkSuperAdmin();

        // جلب آخر عمليات تسجيل الدخول
        $recentLogins = User::whereNotNull('updated_at')
            ->orderBy('updated_at', 'desc')
            ->limit(100)
            ->get(['id', 'fullname', 'email', 'usertype_id', 'company_code', 'updated_at']);

        return view('admin.activity-logs.index', compact('recentLogins'));
    }

    // ============================================
    // التقارير والإحصائيات
    // ============================================

    /**
     * إحصائيات النظام
     */
    public function statistics()
    {
        $this->checkSuperAdmin();

        $stats = [
            'companies' => [
                'total' => Company::where('code', '!=', 'SA')->count(),
                'active' => Company::where('code', '!=', 'SA')->where('is_active', 1)->count(),
            ],
            'users' => [
                'total' => User::count(),
                'by_type' => [
                    'SA' => User::where('usertype_id', 'SA')->count(),
                    'CM' => User::where('usertype_id', 'CM')->count(),
                    'BM' => User::where('usertype_id', 'BM')->count(),
                ],
            ],
            'branches' => DB::table('branches')->count(),
            'cars' => DB::table('cars')->count(),
            'employees' => DB::table('employees')->count(),
        ];

        return view('admin.statistics.index', compact('stats'));
    }

    /**
     * تقارير الأداء
     */
    public function performance()
    {
        $this->checkSuperAdmin();

        // التحقق من حالة التخزين المؤقت
        $cacheStatus = [
            'config' => file_exists(base_path('bootstrap/cache/config.php')),
            'routes' => file_exists(base_path('bootstrap/cache/routes-v7.php')),
            'views' => is_dir(storage_path('framework/views')) && count(glob(storage_path('framework/views/*.php'))) > 0,
        ];

        $performance = [
            'database_size' => $this->getDatabaseSize(),
            'total_tables' => $this->getTableCount(),
            'server_info' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
            ],
            'cache_status' => $cacheStatus,
            'cache_enabled' => $cacheStatus['config'] && $cacheStatus['routes'],
        ];

        return view('admin.performance.index', compact('performance'));
    }

    /**
     * تفعيل التخزين المؤقت
     */
    public function enableCache()
    {
        $this->checkSuperAdmin();

        try {
            // تشغيل أوامر التخزين المؤقت
            \Artisan::call('config:cache');
            \Artisan::call('route:cache');
            \Artisan::call('view:cache');

            return redirect()->back()->with('success', 'تم تفعيل التخزين المؤقت بنجاح! الموقع الآن أسرع 🚀');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * إيقاف التخزين المؤقت
     */
    public function disableCache()
    {
        $this->checkSuperAdmin();

        try {
            // مسح التخزين المؤقت
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
            \Artisan::call('cache:clear');

            return redirect()->back()->with('success', 'تم إيقاف التخزين المؤقت بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // ============================================
    // إعدادات النظام
    // ============================================

    /**
     * الإعدادات العامة
     */
    public function settings()
    {
        $this->checkSuperAdmin();

        try {
            $settings = Setting::all()->pluck('value', 'key')->toArray();
        } catch (\Exception $e) {
            $settings = [
                'app_name' => config('app.name'),
                'support_email' => '',
                'timezone' => 'Asia/Baghdad',
                'currency' => 'دينار عراقي',
                'font_family' => 'Cairo',
                'font_size' => '14',
                'force_https' => '0',
                'enable_2fa' => '0',
                'session_lifetime' => '120',
            ];
        }

        $ownerCompany = Company::where('code', 'SA')->first();

        return view('admin.settings.index', compact('settings', 'ownerCompany'));
    }

    /**
     * تحديث معلومات الشركة المالكة (SA) من جدول companies
     */
    public function updateOwnerCompany(Request $request)
    {
        $this->checkSuperAdmin();

        $ownerCompany = Company::where('code', 'SA')->first();
        if (!$ownerCompany) {
            return redirect()->back()->with('error', 'لم يتم العثور على الشركة المالكة (SA).');
        }

        $data = $request->validate([
            'owner_name' => ['nullable', 'string', 'max:255'],
            'owner_managername' => ['nullable', 'string', 'max:255'],
            'owner_phone' => ['nullable', 'string', 'max:50'],
            'owner_email' => ['nullable', 'email', 'max:255'],
            'owner_address' => ['nullable', 'string', 'max:255'],
            'owner_note' => ['nullable', 'string', 'max:1000'],
            'owner_logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $update = [
            'name' => $data['owner_name'] ?? $ownerCompany->name,
            'managername' => $data['owner_managername'] ?? $ownerCompany->managername,
            'phone' => $data['owner_phone'] ?? $ownerCompany->phone,
            'email' => $data['owner_email'] ?? $ownerCompany->email,
            'address' => $data['owner_address'] ?? $ownerCompany->address,
            'note' => $data['owner_note'] ?? $ownerCompany->note,
        ];

        // رفع اللوكو (يحفظ نفس أسلوب الشركات: uploads/{code}/companies_logo/...)
        if ($request->hasFile('owner_logo')) {
            $file = $request->file('owner_logo');
            $folderPath = public_path('uploads/SA/companies_logo');
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            // حذف اللوكو القديم لتجنب زيادة حجم الملفات
            $oldLogo = (string) ($ownerCompany->logo ?? '');
            if ($oldLogo !== '' && str_starts_with($oldLogo, 'uploads/')) {
                $oldLogoPath = public_path($oldLogo);
                if (File::exists($oldLogoPath)) {
                    try {
                        File::delete($oldLogoPath);
                    } catch (\Throwable $e) {
                        // تجاهل خطأ الحذف (لا يمنع حفظ اللوكو الجديد)
                    }
                }
            }

            $ext = $file->getClientOriginalExtension();
            $filenameOnly = 'owner_logo_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
            $file->move($folderPath, $filenameOnly);
            $update['logo'] = 'uploads/SA/companies_logo/' . $filenameOnly;
        }

        $ownerCompany->update($update);

        return redirect()->back()->with('success', 'تم تحديث معلومات الشركة المالكة بنجاح ✅');
    }

    /**
     * تحديث الإعدادات
     */
    public function updateSettings(Request $request)
    {
        $this->checkSuperAdmin();

        try {
            // الإعدادات العامة
            Setting::set('app_name', $request->app_name ?? 'ConcreteERP');
            Setting::set('support_email', $request->support_email ?? '');
            Setting::set('timezone', $request->timezone ?? 'Asia/Baghdad');
            Setting::set('currency', $request->currency ?? 'دينار عراقي');
            Setting::set('font_family', $request->font_family ?? 'Cairo');
            Setting::set('font_size', $request->font_size ?? '14');

            // إعدادات الأمان
            Setting::set('force_https', $request->has('force_https') ? '1' : '0');
            Setting::set('enable_2fa', $request->has('enable_2fa') ? '1' : '0');
            Setting::set('session_lifetime', $request->session_lifetime ?? '120');

            // مسح كاش الإعدادات
            \Illuminate\Support\Facades\Cache::forget('settings');

            return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح ✅');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حفظ الإعدادات: ' . $e->getMessage());
        }
    }

    /**
     * إعدادات SEO (تحسين محركات البحث)
     */
    public function seo()
    {
        $this->checkSuperAdmin();
        $seo = SeoSetting::current();
        if (!$seo) {
            $seo = new SeoSetting();
        }

        // قيم افتراضية لتحسين ظهور الموقع في محركات البحث (تُستخدم عند فراغ الحقول)
        $defaults = [
            'site_name' => 'ConcreteERP - برنامج إدارة مصانع الخرسانة الجاهزة',
            'meta_title' => 'برنامج إدارة مصانع الخرسانة الجاهزة | ConcreteERP',
            'meta_description' => 'ConcreteERP نظام ERP لإدارة مصانع ومحطات الخرسانة الجاهزة: إدارة الطلبات، التسعير، أوامر العمل، الشحنات، الأسطول، المخزون، الفوترة والتقارير التشغيلية والمالية.',
            'meta_keywords' => 'برنامج إدارة مصانع الخرسانة الجاهزة، نظام ERP للخرسانة الجاهزة، برنامج محاسبة الخرسانة الجاهزة، إدارة محطات الخرسانة، إدارة طلبات الخرسانة، إدارة شحنات الخرسانة، إدارة أسطول الخرسانة، إدارة مخزون الخرسانة، تقارير مصانع الخرسانة، ConcreteERP',
            'og_title' => 'ConcreteERP | نظام إدارة مصانع الخرسانة الجاهزة',
            'og_description' => 'حل متكامل لإدارة محطات الخرسانة: الطلبات، العقود، الشحنات، الفوترة، المخزون، الأسطول والتقارير في منصة واحدة.',
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'robots' => 'index, follow',
            'locale' => 'ar_IQ',
            'locale_alternate' => 'ar',
            'extra_meta' => '<meta name="theme-color" content="#0d9488">' . "\n" . '<meta name="author" content="ConcreteERP">',
            'structured_data' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'SoftwareApplication',
                'name' => 'ConcreteERP',
                'applicationCategory' => 'BusinessApplication',
                'description' => 'نظام إدارة متكامل لشركات الخرسانة الجاهزة - الطلبات، الأفرع، المقاولين، المخزون، الشحنات والمحاسبة.',
                'operatingSystem' => 'Web',
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        ];

        foreach ($defaults as $key => $value) {
            if (empty(trim((string) ($seo->$key ?? '')))) {
                $seo->$key = $value;
            }
        }

        return view('admin.seo.index', compact('seo'));
    }

    /**
     * تحديث إعدادات SEO
     */
    public function updateSeo(Request $request)
    {
        $this->checkSuperAdmin();
        $seo = SeoSetting::current();
        if (!$seo) {
            $seo = new SeoSetting();
        }
        $seo->fill($request->only([
            'site_name', 'meta_title', 'meta_description', 'meta_keywords',
            'og_title', 'og_description', 'og_image', 'og_type',
            'twitter_card', 'twitter_site', 'canonical_domain', 'robots',
            'locale', 'locale_alternate', 'extra_meta', 'structured_data',
        ]));
        $seo->save();
        return redirect()->route('admin.seo')->with('success', 'تم حفظ إعدادات SEO بنجاح ✅');
    }

    /**
     * النسخ الاحتياطي
     */
    public function backups()
    {
        $this->checkSuperAdmin();

        try {
            $backups = Backup::with('creator')->orderBy('created_at', 'desc')->get();
            $lastBackupDate = $backups->first()?->created_at?->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            $backups = collect([]);
            $lastBackupDate = null;
        }

        $zipAvailable = class_exists('ZipArchive');
        $uploadsPath = public_path('uploads');

        return view('admin.backups.index', compact('backups', 'lastBackupDate', 'zipAvailable', 'uploadsPath'));
    }

    /**
     * تنسيق حجم الملف
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * إنشاء نسخة احتياطية (قاعدة البيانات + مجلد uploads)
     */
    public function createBackup(Request $request)
    {
        $this->checkSuperAdmin();

        if (!class_exists('ZipArchive')) {
            return redirect()->back()->with('error', 'امتداد Zip غير مفعّل في PHP. لتفعيله: Laragon → PHP → php.ini → ابحث عن extension=zip وأزل التعليق (;). بعد التفعيل يجب إعادة تشغيل Laragon بالكامل (Stop All ثم Start) حتى يُحمّل التعديل.');
        }

        try {
            // إنشاء مجلد مؤقت للنسخ
            $tempPath = storage_path('app/temp');
            if (!File::exists($tempPath)) {
                File::makeDirectory($tempPath, 0755, true);
            }

            // اسم الملف بالتاريخ والوقت
            $timestamp = date('Y-m-d_H-i-s');
            $backupFileName = "backup_database_{$timestamp}";

            // مسار ملف SQL
            $sqlFile = $tempPath . '/' . $backupFileName . '.sql';

            // قراءة معلومات الاتصال من .env
            $host = env('DB_HOST', '127.0.0.1');
            $database = env('DB_DATABASE', 'concreteerp');
            $username = env('DB_USERNAME', 'root');
            $password = env('DB_PASSWORD', '');

            // إنشاء نسخة من قاعدة البيانات باستخدام mysqldump
            $mysqldumpPath = $this->getMysqldumpPath();

            if ($password) {
                $command = "\"{$mysqldumpPath}\" --host={$host} --user={$username} --password={$password} {$database} > \"{$sqlFile}\"";
            } else {
                $command = "\"{$mysqldumpPath}\" --host={$host} --user={$username} {$database} > \"{$sqlFile}\"";
            }

            // تنفيذ الأمر
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar !== 0 || !File::exists($sqlFile) || File::size($sqlFile) == 0) {
                // إذا فشل mysqldump، استخدم طريقة PHP البديلة
                $this->createDatabaseBackupPHP($sqlFile);
            }

            // إنشاء ملف مضغوط
            $zipFileName = "backup_full_{$timestamp}.zip";
            $zipFilePath = $tempPath . '/' . $zipFileName;

            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {

                // إضافة ملف قاعدة البيانات
                if (File::exists($sqlFile)) {
                    $zip->addFile($sqlFile, "database/{$backupFileName}.sql");
                }

                // إضافة مجلد uploads
                $uploadsPath = public_path('uploads');
                if (File::exists($uploadsPath)) {
                    $this->addFolderToZip($zip, $uploadsPath, 'uploads');
                }

                $zip->close();

                // حساب حجم الملف
                $fileSize = $this->formatFileSize(File::size($zipFilePath));

                // حساب الإحصائيات
                $companiesCount = Company::where('code', '!=', 'SA')->count();
                $usersCount = User::count();
                $tablesCount = count(DB::select('SHOW TABLES'));

                // تحديد نوع النسخة (يدوي أو تلقائي)
                $backupType = $request->input('type', 'manual');
                $notes = $backupType === 'auto'
                    ? "نسخة تلقائية (قاعدة بيانات + ملفات)"
                    : "نسخة يدوية (قاعدة بيانات + ملفات)";

                // حفظ معلومات النسخة في الجدول
                Backup::create([
                    'name' => $zipFileName,
                    'size' => $fileSize,
                    'companies_count' => $companiesCount,
                    'users_count' => $usersCount,
                    'tables_count' => $tablesCount,
                    'notes' => $notes,
                    'created_by' => auth()->id(),
                ]);

                // حذف ملف SQL المؤقت
                if (File::exists($sqlFile)) {
                    File::delete($sqlFile);
                }

                // تنزيل الملف ثم حذفه من الخادم
                return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
            } else {
                return redirect()->back()->with('error', 'فشل في إنشاء الملف المضغوط');
            }
        } catch (\Exception $e) {
            Log::error('Backup Error: ' . $e->getMessage());
            $msg = 'حدث خطأ أثناء إنشاء النسخة الاحتياطية: ' . $e->getMessage();
            if (str_contains($e->getMessage(), 'ZipArchive')) {
                $msg .= ' — تأكد من تفعيل extension=zip في php.ini ثم أعد تشغيل Laragon بالكامل (Stop All ثم Start).';
            }
            return redirect()->back()->with('error', $msg);
        }
    }

    /**
     * الحصول على مسار mysqldump
     */
    private function getMysqldumpPath()
    {
        // Laragon: البحث في مجلد mysql عن أي إصدار
        $laragonMysql = 'C:/laragon/bin/mysql';
        if (is_dir($laragonMysql)) {
            $found = glob($laragonMysql . '/*/bin/mysqldump.exe');
            if (!empty($found) && file_exists($found[0])) {
                return $found[0];
            }
        }

        // مسارات شائعة لـ mysqldump
        $paths = [
            'C:/xampp/mysql/bin/mysqldump.exe',
            'C:/wamp/bin/mysql/mysql5.7.26/bin/mysqldump.exe',
            'C:/wamp64/bin/mysql/mysql5.7.26/bin/mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            'mysqldump', // إذا كان في PATH
        ];

        foreach ($paths as $path) {
            if (file_exists($path) || $path === 'mysqldump') {
                return $path;
            }
        }

        return 'mysqldump';
    }

    /**
     * إنشاء نسخة من قاعدة البيانات باستخدام PHP (بديل)
     */
    private function createDatabaseBackupPHP($filePath)
    {
        $tables = DB::select('SHOW TABLES');
        $database = env('DB_DATABASE');

        $sql = "-- ConcreteERP Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$database}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];

            // الحصول على بنية الجدول
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "\n-- Table structure for `{$tableName}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            // الحصول على البيانات
            $rows = DB::table($tableName)->get();

            if (count($rows) > 0) {
                $sql .= "-- Data for `{$tableName}`\n";

                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        return "'" . addslashes($value) . "'";
                    }, (array)$row);

                    $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        File::put($filePath, $sql);
    }

    /**
     * إضافة مجلد للملف المضغوط
     */
    private function addFolderToZip($zip, $folder, $zipPath)
    {
        $files = File::allFiles($folder);

        foreach ($files as $file) {
            $relativePath = $zipPath . '/' . $file->getRelativePathname();
            $zip->addFile($file->getPathname(), $relativePath);
        }

        // إضافة المجلدات الفارغة
        $directories = File::directories($folder);
        foreach ($directories as $dir) {
            $dirName = basename($dir);
            $this->addFolderToZip($zip, $dir, $zipPath . '/' . $dirName);
        }
    }

    /**
     * تنزيل نسخة احتياطية
     */
    public function downloadBackup($filename)
    {
        $this->checkSuperAdmin();

        // التحقق من صحة اسم الملف (منع Directory Traversal)
        $validation = \App\Helpers\FileUploadHelper::validateBackupFilename($filename);
        if (!$validation['valid']) {
            return redirect()->back()->with('error', $validation['error']);
        }

        $filePath = storage_path('app/backups/' . $validation['sanitized']);

        if (File::exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->back()->with('error', 'الملف غير موجود');
    }

    /**
     * حذف نسخة احتياطية
     */
    public function deleteBackup($filename)
    {
        $this->checkSuperAdmin();

        // التحقق من صحة اسم الملف (منع Directory Traversal)
        $validation = \App\Helpers\FileUploadHelper::validateBackupFilename($filename);
        if (!$validation['valid']) {
            return redirect()->back()->with('error', $validation['error']);
        }

        $filePath = storage_path('app/backups/' . $validation['sanitized']);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->back()->with('success', 'تم حذف النسخة الاحتياطية بنجاح');
        }

        return redirect()->back()->with('error', 'الملف غير موجود');
    }

    /**
     * إدارة الإشعارات
     */
    public function notifications()
    {
        $this->checkSuperAdmin();

        $companies = Company::where('code', '!=', 'SA')->get();

        return view('admin.notifications.index', compact('companies'));
    }

    /**
     * إرسال إشعار
     */
    public function sendNotification(Request $request)
    {
        $this->checkSuperAdmin();

        // التحقق من صحة البيانات
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,success,danger',
            'company_code' => 'required|string',
        ], [
            'title.required' => 'عنوان الإشعار مطلوب',
            'message.required' => 'نص الإشعار مطلوب',
            'type.required' => 'نوع الإشعار مطلوب',
            'company_code.required' => 'يجب اختيار المستلمين',
        ]);

        // منع التكرار - التحقق من وجود إشعار مماثل خلال آخر دقيقة
        $existingNotification = Notification::where('title', $request->title)
            ->where('message', $request->message)
            ->where('created_at', '>=', now()->subMinute())
            ->first();

        if ($existingNotification) {
            return redirect()->back()->with('warning', 'تم إرسال هذا الإشعار بالفعل');
        }

        try {
            $sentCount = 0;

            // إذا كان الإرسال لجميع الشركات
            if ($request->company_code === 'all') {
                // إنشاء إشعار واحد للجميع بدلاً من إشعار لكل شركة
                Notification::create([
                    'company_code' => 'ALL',
                    'title' => $request->title,
                    'message' => $request->message,
                    'type' => $request->type,
                    'sent_by' => 'SA',
                ]);
                $sentCount = Company::where('code', '!=', 'SA')->count();
            }
            // إذا كان الإرسال لشركة محددة
            else {
                Notification::create([
                    'company_code' => $request->company_code,
                    'title' => $request->title,
                    'message' => $request->message,
                    'type' => $request->type,
                    'sent_by' => 'SA',
                ]);
                $sentCount = 1;
            }

            $message = $sentCount > 1
                ? "تم إرسال الإشعار إلى {$sentCount} شركة بنجاح"
                : "تم إرسال الإشعار بنجاح";

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('خطأ في إرسال الإشعار: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء إرسال الإشعار: ' . $e->getMessage());
        }
    }

    /**
     * عرض قائمة الإشعارات المرسلة
     */
    public function notificationsList(Request $request)
    {
        $this->checkSuperAdmin();

        // جلب الإشعارات مع معلومات الشركة
        $notifications = Notification::with('company')
            ->select('notifications.*')
            ->orderBy('created_at', 'desc');

        // فلتر حسب النوع
        if ($request->filled('type') && $request->type !== 'all') {
            $notifications->where('type', $request->type);
        }

        // فلتر حسب الحالة (مقروء/غير مقروء)
        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $notifications->where('is_read', true);
            } elseif ($request->status === 'unread') {
                $notifications->where('is_read', false);
            }
        }

        // فلتر حسب التاريخ
        if ($request->filled('date_from')) {
            $notifications->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $notifications->whereDate('created_at', '<=', $request->date_to);
        }

        $notifications = $notifications->paginate(20);

        // إحصائيات عامة
        $stats = [
            'total_sent' => Notification::count(),
            'total_read' => Notification::where('is_read', true)->count(),
            'total_unread' => Notification::where('is_read', false)->count(),
            'companies_count' => Company::where('code', '!=', 'SA')->count(),
        ];

        // جلب جميع الشركات للنموذج
        $companies = Company::where('code', '!=', 'SA')->get();

        return view('admin.notifications.list', compact('notifications', 'stats', 'companies'));
    }

    /**
     * عرض تفاصيل إشعار معين
     */
    public function notificationDetails($id)
    {
        $this->checkSuperAdmin();

        $notification = Notification::findOrFail($id);

        // جلب جميع الإشعارات المشابهة (نفس العنوان والتاريخ)
        $relatedNotifications = Notification::where('title', $notification->title)
            ->where('message', $notification->message)
            ->whereDate('created_at', $notification->created_at->format('Y-m-d'))
            ->with('company')
            ->get();

        $stats = [
            'total_sent' => $relatedNotifications->count(),
            'read_count' => $relatedNotifications->where('is_read', true)->count(),
            'unread_count' => $relatedNotifications->where('is_read', false)->count(),
        ];

        return view('admin.notifications.details', compact('notification', 'relatedNotifications', 'stats'));
    }

    // ============================================
    // البيانات الأساسية
    // ============================================

    /**
     * المحافظات
     */
    public function cities()
    {
        $this->checkSuperAdmin();

        $cities = City::orderBy('name_ar')->get();

        return view('admin.cities.index', compact('cities'));
    }

    /**
     * إضافة مدينة
     */
    public function storeCity(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
        ]);

        City::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
        ]);

        return redirect()->back()->with('success', 'تم إضافة المحافظة بنجاح');
    }

    /**
     * تعديل مدينة
     */
    public function updateCity(Request $request, $id)
    {
        $this->checkSuperAdmin();

        $city = City::findOrFail($id);
        $city->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
        ]);

        return redirect()->back()->with('success', 'تم تعديل المحافظة بنجاح');
    }

    /**
     * حذف مدينة
     */
    public function deleteCity($id)
    {
        $this->checkSuperAdmin();

        City::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'تم حذف المحافظة بنجاح');
    }

    /**
     * أنواع الموظفين
     */
    public function employeeTypes()
    {
        $this->checkSuperAdmin();

        $types = EmployeeType::orderBy('name')->get();

        return view('admin.employee-types.index', compact('types'));
    }

    /**
     * إضافة نوع موظف
     */
    public function storeEmployeeType(Request $request)
    {
        $this->checkSuperAdmin();

        EmployeeType::create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'تم إضافة نوع الموظف بنجاح');
    }

    /**
     * تعديل نوع موظف
     */
    public function updateEmployeeType(Request $request, $id)
    {
        $this->checkSuperAdmin();

        $type = EmployeeType::findOrFail($id);
        $type->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'تم تعديل نوع الموظف بنجاح');
    }

    /**
     * حذف نوع موظف
     */
    public function deleteEmployeeType($id)
    {
        $this->checkSuperAdmin();

        EmployeeType::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'تم حذف نوع الموظف بنجاح');
    }

    /**
     * أنواع السيارات
     */
    public function carTypes()
    {
        $companyCode = Auth::user()->company_code ?? session('company_code');
        $types = CarsType::where('company_code', $companyCode)->orderBy('name')->get();

        return view('admin.car-types.index', compact('types'));
    }

    /**
     * إضافة نوع سيارة
     */
    public function storeCarType(Request $request)
    {
        $companyCode = Auth::user()->company_code ?? session('company_code');

        // توليد كود فريد
        $code = CarsType::generateUniqueCode($companyCode);

        CarsType::create([
            'code' => $code,
            'name' => $request->name,
            'capacity' => $request->capacity,
            'company_code' => $companyCode,
        ]);

        return redirect()->back()->with('success', 'تم إضافة نوع السيارة بنجاح - الكود: ' . $code);
    }

    /**
     * تعديل نوع سيارة
     */
    public function updateCarType(Request $request, $id)
    {
        $companyCode = Auth::user()->company_code ?? session('company_code');

        $type = CarsType::where('id', $id)
            ->where('company_code', $companyCode)
            ->firstOrFail();

        $type->update([
            'name' => $request->name,
            'capacity' => $request->capacity,
        ]);

        return redirect()->back()->with('success', 'تم تعديل نوع السيارة بنجاح');
    }

    /**
     * حذف نوع سيارة
     */
    public function deleteCarType($id)
    {
        $companyCode = Auth::user()->company_code ?? session('company_code');

        CarsType::where('id', $id)
            ->where('company_code', $companyCode)
            ->firstOrFail()
            ->delete();

        return redirect()->back()->with('success', 'تم حذف نوع السيارة بنجاح');
    }

    // ============================================
    // الدعم والصيانة
    // ============================================

    /**
     * تذاكر الدعم
     */
    public function tickets(Request $request)
    {
        $this->checkSuperAdmin();

        $query = \App\Models\SupportTicket::with(['company', 'user'])
            ->withCount(['replies' => function ($q) {
                $q->where('is_internal', false);
            }])
            ->orderBy('created_at', 'desc');

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }

        // فلترة حسب الشركة
        if ($request->has('company') && $request->company != 'all') {
            $query->where('company_code', $request->company);
        }

        // بحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(20);

        // إحصائيات
        $stats = [
            'total' => \App\Models\SupportTicket::count(),
            'open' => \App\Models\SupportTicket::where('status', 'open')->count(),
            'in_progress' => \App\Models\SupportTicket::where('status', 'in_progress')->count(),
            'pending' => \App\Models\SupportTicket::where('status', 'pending_response')->count(),
            'resolved' => \App\Models\SupportTicket::where('status', 'resolved')->count(),
            'closed' => \App\Models\SupportTicket::where('status', 'closed')->count(),
        ];

        $companies = \App\Models\Company::orderBy('name')->get();

        return view('admin.tickets.index', compact('tickets', 'stats', 'companies'));
    }

    /**
     * عرض تذكرة
     */
    public function showTicket($id)
    {
        $this->checkSuperAdmin();

        $ticket = \App\Models\SupportTicket::with(['company', 'user', 'replies' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * الرد على تذكرة
     */
    public function replyTicket(Request $request, $id)
    {
        $this->checkSuperAdmin();

        $ticket = \App\Models\SupportTicket::findOrFail($id);

        $request->validate([
            'message' => 'required|string',
            'is_internal' => 'nullable|boolean'
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            $uploadPath = 'uploads/tickets/' . date('Y-m');
            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0755, true);
            }

            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $fileName);
                $attachments[] = [
                    'name' => $originalName,
                    'path' => $uploadPath . '/' . $fileName,
                    'size' => $fileSize
                ];
            }
        }

        \App\Models\TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_type' => 'support',
            'user_id' => Auth::id(),
            'user_name' => 'فريق الدعم الفني',
            'message' => $request->message,
            'attachments' => $attachments,
            'is_internal' => $request->is_internal ?? false
        ]);

        // تحديث وقت أول رد إذا لم يكن موجود
        if (!$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        // تحديث الحالة إلى بانتظار الرد
        if ($ticket->status === 'open' || $ticket->status === 'in_progress') {
            $ticket->update(['status' => 'pending_response']);
        }

        return redirect()->back()->with('success', 'تم إرسال الرد بنجاح');
    }

    /**
     * تحديث حالة التذكرة
     */
    public function updateTicketStatus(Request $request, $id)
    {
        $this->checkSuperAdmin();

        $ticket = \App\Models\SupportTicket::findOrFail($id);

        $request->validate([
            'status' => 'required|in:open,in_progress,pending_response,resolved,closed'
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
        } elseif ($request->status === 'closed') {
            $data['closed_at'] = now();
        }

        $ticket->update($data);

        return redirect()->back()->with('success', 'تم تحديث حالة التذكرة بنجاح');
    }

    /**
     * سجل الأخطاء
     */
    public function errorLogs()
    {
        $this->checkSuperAdmin();

        $logs = [];
        $logPath = storage_path('logs/laravel.log');

        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $lines = explode("\n", $content);
            $logs = array_slice(array_reverse($lines), 0, 100);
        }

        return view('admin.error-logs.index', compact('logs'));
    }

    /**
     * مسح سجل الأخطاء
     */
    public function clearErrorLogs()
    {
        $this->checkSuperAdmin();

        $logPath = storage_path('logs/laravel.log');
        File::put($logPath, '');

        return redirect()->back()->with('success', 'تم مسح سجل الأخطاء بنجاح');
    }

    /**
     * صحة النظام
     */
    public function systemHealth()
    {
        $this->checkSuperAdmin();

        $health = [
            'database' => $this->checkDatabaseConnection(),
            'storage' => $this->checkStorageSpace(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
        ];

        return view('admin.system-health.index', compact('health'));
    }

    // ============================================
    // Helper Methods
    // ============================================

    private function getDatabaseSize()
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $result = DB::select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            return round($result[0]->size ?? 0, 2) . ' MB';
        } catch (\Exception $e) {
            return 'غير متاح';
        }
    }

    private function getTableCount()
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $result = DB::select("SELECT COUNT(*) as count FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            return $result[0]->count ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'متصل', 'class' => 'success'];
        } catch (\Exception $e) {
            return ['status' => 'غير متصل', 'class' => 'danger'];
        }
    }

    private function checkStorageSpace()
    {
        $free = disk_free_space(storage_path());
        $total = disk_total_space(storage_path());
        $used = $total - $free;
        $percentage = round(($used / $total) * 100, 1);

        return [
            'free' => round($free / 1024 / 1024 / 1024, 2) . ' GB',
            'total' => round($total / 1024 / 1024 / 1024, 2) . ' GB',
            'percentage' => $percentage,
            'class' => $percentage > 90 ? 'danger' : ($percentage > 70 ? 'warning' : 'success'),
        ];
    }
}
