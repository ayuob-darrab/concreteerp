<?php

namespace App\Http\Controllers;

use App\Models\accountsType;
use App\Models\Branch;
use App\Models\EmployeeType;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->active == 'AddNewUser') {

            // التحقق من حد المستخدمين في الاشتراك
            $companyCode = Auth::user()->company_code;

            // استثناء السوبر أدمن من التحقق
            if ($companyCode !== 'SA') {
                $subscription = \App\Models\CompanySubscription::where('company_code', $companyCode)
                    ->where('status', 'active')
                    ->first();

                if ($subscription && $subscription->users_count) {
                    $activeCount = User::forCompany($companyCode)->activeForSubscription()->count();
                    if ($activeCount >= $subscription->users_count) {
                        return redirect()->back()->withInput()->with(
                            'error',
                            "⚠️ لا يمكن إضافة مستخدمين جدد! الحد المسموح في اشتراكك هو {$subscription->users_count} مستخدمين نشطين وأنت تستخدم حالياً {$activeCount}. يمكنك تعطيل حساب لتحرير مكان أو زيادة حد الاشتراك."
                        );
                    }
                }
            }

            // التحقق من البيانات
            $request->validate([
                'fullname' => 'required|string|min:3|max:100',
                'username' => 'required|string|min:2|max:50|regex:/^[a-zA-Z0-9_\-\.]+$/',
                'password' => 'required|string|min:6',
                'branchId' => 'required|exists:branches,id',
                'user_type' => 'required|string',
                'employee_type' => 'required|exists:employee_types,id',
            ], [
                'fullname.required' => 'الاسم الثلاثي مطلوب',
                'fullname.min' => 'الاسم يجب أن يكون 3 أحرف على الأقل',
                'username.required' => 'اسم المستخدم مطلوب',
                'username.regex' => 'اسم المستخدم يجب أن يحتوي على أحرف إنجليزية وأرقام فقط',
                'password.required' => 'كلمة المرور مطلوبة',
                'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
                'password.confirmed' => 'كلمتا المرور غير متطابقتين',
                'branchId.required' => 'يجب اختيار الفرع',
                'user_type.required' => 'يجب اختيار صلاحيات المستخدم',
                'employee_type.required' => 'يجب اختيار نوع الموظف',
            ]);

            $checkUser = User::where('username', strtolower(trim($request->username)))
                ->first();

            if ($checkUser) {
                return redirect()->back()->withInput()->with('error', 'اسم المستخدم مستخدم مسبقاً!');
            }

            $addNewUser = new User();
            $addNewUser->fullname = trim($request->fullname);
            $addNewUser->company_code = Auth::user()->company_code;
            $addNewUser->username = strtolower(trim($request->username));
            $addNewUser->email = $request->username . '@system.local';
            $addNewUser->password = Hash::make($request->password);
            $addNewUser->usertype_id = $request->user_type;
            $addNewUser->emp_type_id = $request->employee_type;
            $addNewUser->branch_id = $request->branchId;
            $addNewUser->account_code = 'emp';
            $addNewUser->is_active = true;
            $addNewUser->save();

            return redirect('accounts/listaccount')->with('success', 'تم إضافة المستخدم بنجاح');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        if ($id == 'listaccount') {
            $companyCode = Auth::user()->company_code;

            // جلب جميع المستخدمين (بما فيهم المعطلين بسبب الاشتراك)
            $users = User::where('company_code', $companyCode)->get();

            // جلب معلومات الاشتراك لعرض حد المستخدمين
            $subscription = \App\Models\CompanySubscription::where('company_code', $companyCode)
                ->where('status', 'active')
                ->first();

            $usersLimit = $subscription ? $subscription->users_count : null;

            // عدد المستخدمين النشطين (يُحتسبون في حد الاشتراك: يمكنهم تسجيل الدخول)
            $activeUsersCount = User::forCompany($companyCode)->activeForSubscription()->count();
            $currentUsersCount = $users->count(); // إجمالي الحسابات
            $canAddMore = !$usersLimit || $activeUsersCount < $usersLimit;

            // حساب عدد المستخدمين الزائدين عن الاشتراك
            $excessUsers = 0;
            $needsDeactivation = false;
            if ($usersLimit && $activeUsersCount > $usersLimit) {
                $excessUsers = $activeUsersCount - $usersLimit;
                $needsDeactivation = true;
            }

            // هل يمكن تفعيل مستخدمين معطلين؟ (عند زيادة الاشتراك)
            $deactivatedBySubscriptionCount = User::where('company_code', $companyCode)
                ->where('deactivated_by_subscription', true)
                ->count();
            $canActivateMore = $canAddMore && $deactivatedBySubscriptionCount > 0;

            // هل المستخدم الحالي مدير شركة؟
            $isCompanyManager = Auth::user()->usertype_id == 'CM' || Auth::user()->usertype_id == 1;

            // هل المستخدم الحالي سوبر أدمن؟
            $isSuperAdmin = Auth::user()->company_code === 'SA';

            return view('accounts.listaccount', compact(
                'users',
                'usersLimit',
                'currentUsersCount',
                'activeUsersCount',
                'canAddMore',
                'excessUsers',
                'needsDeactivation',
                'isCompanyManager',
                'isSuperAdmin',
                'canActivateMore'
            ));
        }

        if ($id == 'GoToAddUser') {
            // التحقق من حد المستخدمين النشطين قبل السماح بفتح صفحة الإضافة
            $companyCode = Auth::user()->company_code;

            if ($companyCode !== 'SA') {
                $subscription = \App\Models\CompanySubscription::where('company_code', $companyCode)
                    ->where('status', 'active')
                    ->first();

                if ($subscription && $subscription->users_count) {
                    $activeCount = User::forCompany($companyCode)->activeForSubscription()->count();
                    if ($activeCount >= $subscription->users_count) {
                        return redirect('accounts/listaccount')->with(
                            'error',
                            "⚠️ لا يمكن إضافة مستخدمين جدد! الحد المسموح هو {$subscription->users_count} مستخدمين نشطين وحالياً {$activeCount}. يمكن تعطيل حساب لتحرير مكان أو زيادة حد الاشتراك."
                        );
                    }
                }
            }

            $baranches = Branch::where('company_code', Auth::user()->company_code)->get();
            // صلاحيات المستخدم: مدير فرع (BM) ومستخدم (US) فقط
            $typeUser = UserType::whereIn('code', ['BM', 'US'])->orderBy('name')->get();
            $employeeType = EmployeeType::all();
            return view('auth.register', compact('typeUser', 'employeeType', 'baranches'));
        }

        if ($id == 'listBranchaccounts') {

            $users = User::where('company_code', Auth::user()->company_code)->where('branch_id', auth()->user()->branch_id)->get();
            return view('accounts.listBranchaccounts', compact('users'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $exploded = explode('&', $id);

        $action = $exploded[1];
        if ($action === 'editUserInformation') {

            $user = User::where('id', $exploded[0])->first();
            $typeUser = UserType::where('code', '!=', 'SA')->get();
            $employeeType = EmployeeType::all();

            $reactivationBlocked = false;
            $hoursUntilReactivate = 0;
            if (!$user->is_active && $user->subscription_deactivated_at) {
                $deadline = $user->subscription_deactivated_at->copy()->addHours(48);
                if ($deadline->gt(now())) {
                    $reactivationBlocked = true;
                    $hoursUntilReactivate = max(1, (int) ceil($deadline->diffInMinutes(now(), false) / 60));
                }
            }

            return view('accounts.editUserInformation', compact('typeUser', 'employeeType', 'user', 'reactivationBlocked', 'hoursUntilReactivate'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->active  == 'UpadteUserInformation') {
            $user = User::findOrFail($id);
            $email = $user->email;

            $wasActive = (bool) $user->is_active;
            $newActive = (int) $request->is_active;

            // تعطيل الحساب: تسجيل وقت التعطيل لقاعدة الـ 48 ساعة
            if ($wasActive && !$newActive) {
                $user->update([
                    'fullname'  => $request->fullname,
                    'email'  => $email,
                    'usertype_id'  => $request->user_type,
                    'emp_type_id'  => $request->employee_type,
                    'is_active'  => false,
                    'deactivated_by_subscription' => true,
                    'subscription_deactivated_at' => now(),
                ]);
                return redirect('accounts/listaccount')->with('success', "تم تعطيل حساب ({$user->fullname}). لا يمكن إعادة التفعيل قبل 48 ساعة.");
            }

            // إعادة تفعيل الحساب: التحقق من قاعدة الـ 48 ساعة
            if (!$wasActive && $newActive) {
                if ($user->subscription_deactivated_at && $user->subscription_deactivated_at->gt(now()->subHours(48))) {
                    $deadline = $user->subscription_deactivated_at->copy()->addHours(48);
                    $hoursLeft = max(1, (int) ceil($deadline->diffInMinutes(now(), false) / 60));
                    return redirect()->back()->with('error', "لا يمكن إعادة تفعيل الحساب قبل مرور 48 ساعة من التعطيل. المتبقي تقريباً: {$hoursLeft} ساعة.");
                }

                // التحقق من حد الاشتراك
                $companyCode = $user->company_code;
                if ($companyCode !== 'SA') {
                    $subscription = \App\Models\CompanySubscription::where('company_code', $companyCode)
                        ->where('status', 'active')
                        ->first();
                    if ($subscription && $subscription->users_count) {
                        $activeCount = User::forCompany($companyCode)->activeForSubscription()->count();
                        if ($activeCount >= $subscription->users_count) {
                            return redirect()->back()->with('error', "لا يمكن التفعيل. الاشتراك يسمح بـ {$subscription->users_count} مستخدمين نشطين فقط وهناك {$activeCount} حالياً.");
                        }
                    }
                }

                $user->update([
                    'fullname'  => $request->fullname,
                    'email'  => $email,
                    'usertype_id'  => $request->user_type,
                    'emp_type_id'  => $request->employee_type,
                    'is_active'  => true,
                    'deactivated_by_subscription' => false,
                    'subscription_deactivated_at' => null,
                ]);
                return redirect('accounts/listaccount')->with('success', "تم إعادة تفعيل حساب ({$user->fullname}) بنجاح.");
            }

            // لم يتغير حالة التفعيل -- تحديث البيانات فقط
            $user->update([
                'fullname'  => $request->fullname,
                'email'  => $email,
                'usertype_id'  => $request->user_type,
                'emp_type_id'  => $request->employee_type,
            ]);

            return redirect('accounts/listaccount')->with('success', 'تم تحديث التفاصيل بنجاح');
        }
        if ($request->active  == 'UpdatePassword') {
            User::where('id', $id)->update([
                'password'  =>  Hash::make($request->newPassword),
            ]);
            return back()->with('success', 'تم تحديث كلمة المرور بنجاح');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * تعطيل مستخدم بسبب تقليل عدد الاشتراك
     * فقط مدير الشركة يمكنه ذلك
     */
    public function deactivateForSubscription($id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        // التحقق من أن المستخدم من نفس الشركة
        if ($user->company_code !== $currentUser->company_code) {
            return back()->with('error', 'لا يمكنك تعديل مستخدم من شركة أخرى');
        }

        // التحقق من أن المستخدم الحالي مدير شركة
        if (!in_array($currentUser->usertype_id, ['CM', 1])) {
            return back()->with('error', 'فقط مدير الشركة يمكنه تعطيل المستخدمين بسبب الاشتراك');
        }

        // لا يمكن تعطيل نفسك
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'لا يمكنك تعطيل حسابك الخاص');
        }

        // لا يمكن تعطيل مدير الشركة
        if (in_array($user->usertype_id, ['CM', 1])) {
            return back()->with('error', 'لا يمكن تعطيل حساب مدير الشركة');
        }

        // تعطيل المستخدم
        $user->is_active = false;
        $user->deactivated_by_subscription = true;
        $user->subscription_deactivated_at = now();
        $user->save();

        return back()->with('success', "تم تعطيل حساب ({$user->fullname}) بنجاح. هذا الحساب لن يتمكن من تسجيل الدخول حتى يتم زيادة عدد المستخدمين في الاشتراك.");
    }

    /**
     * إعادة تفعيل مستخدم معطل بسبب الاشتراك
     * السوبر أدمن أو مدير الشركة (إذا كان هناك مجال في الاشتراك)
     */
    public function reactivateFromSubscription($id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        $isSuperAdmin = $currentUser->company_code === 'SA';
        $isCompanyManager = in_array($currentUser->usertype_id, ['CM', 1]);
        $isSameCompany = $user->company_code === $currentUser->company_code;

        // التحقق من الصلاحيات: سوبر أدمن أو مدير شركة من نفس الشركة
        if (!$isSuperAdmin && !($isCompanyManager && $isSameCompany)) {
            return back()->with('error', 'ليس لديك صلاحية لإعادة تفعيل هذا المستخدم');
        }

        // التحقق من أن المستخدم معطل بسبب الاشتراك
        if (!$user->deactivated_by_subscription) {
            return back()->with('error', 'هذا المستخدم غير معطل بسبب الاشتراك');
        }

        // لا يمكن التفعيل قبل مرور 48 ساعة من التعطيل
        if ($user->subscription_deactivated_at && $user->subscription_deactivated_at->gt(now()->subHours(48))) {
            $deadline = $user->subscription_deactivated_at->copy()->addHours(48);
            $hoursLeft = max(1, (int) ceil($deadline->diffInMinutes(now(), false) / 60));
            return back()->with('error', "لا يمكن إعادة التفعيل قبل مرور 48 ساعة من التعطيل. المتبقي تقريباً: {$hoursLeft} ساعة.");
        }

        // التحقق من أن الاشتراك يسمح بالتفعيل (عدد النشطين أقل من الحد)
        $subscription = \App\Models\CompanySubscription::where('company_code', $user->company_code)
            ->where('status', 'active')
            ->first();

        if ($subscription) {
            $activeUsersCount = User::forCompany($user->company_code)->activeForSubscription()->count();
            if ($activeUsersCount >= $subscription->users_count) {
                return back()->with('error', "لا يمكن تفعيل المستخدم. الاشتراك يسمح بـ {$subscription->users_count} مستخدمين نشطين فقط وهناك {$activeUsersCount} حالياً. يجب زيادة حد المستخدمين في الاشتراك أولاً.");
            }
        }

        // إعادة تفعيل المستخدم
        $user->is_active = true;
        $user->deactivated_by_subscription = false;
        $user->subscription_deactivated_at = null;
        $user->save();

        return back()->with('success', "تم إعادة تفعيل حساب ({$user->fullname}) بنجاح.");
    }

    /**
     * تحديث حد المستخدمين النشطين للشركة (مدير الشركة فقط)
     */
    public function updateUsersLimit(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->usertype_id, ['CM', 1])) {
            return back()->with('error', 'فقط مدير الشركة يمكنه تحديث حد المستخدمين.');
        }

        $companyCode = $user->company_code;
        if ($companyCode === 'SA') {
            return back()->with('error', 'لا ينطبق على هذه الحساب.');
        }

        $subscription = \App\Models\CompanySubscription::where('company_code', $companyCode)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return back()->with('error', 'لا يوجد اشتراك نشط للشركة. يرجى التواصل مع الإدارة.');
        }

        $activeCount = User::forCompany($companyCode)->activeForSubscription()->count();
        $request->validate([
            'users_count' => 'required|integer|min:1',
        ], [
            'users_count.required' => 'عدد المستخدمين مطلوب.',
            'users_count.min' => 'الحد الأدنى مستخدم واحد.',
        ]);

        $newLimit = (int) $request->users_count;
        if ($newLimit < $activeCount) {
            return back()->with('error', "لا يمكن تحديد حد أقل من عدد النشطين الحالي ({$activeCount}). يمكنك تعطيل حسابات أولاً.");
        }

        $subscription->users_count = $newLimit;
        $subscription->save();

        return back()->with('success', "تم تحديث حد المستخدمين إلى {$newLimit} بنجاح.");
    }
}
