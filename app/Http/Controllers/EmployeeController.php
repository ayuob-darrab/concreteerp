<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Breanch;
use App\Models\Cars;
use App\Models\CompanySubscription;
use App\Models\DriverAssignment;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\EmployeeType;
use App\Models\ShiftTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        if ($request->active == "NewEmployee") {
            // التحقق من البيانات
            $companyCode = Auth::user()->company_code;
            $branchId = $request->branch_id;

            // منع التكرار - التحقق من وجود موظف بنفس الاسم في نفس الفرع
            $existingByName = Employee::where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->where('fullname', $request->fullname)
                ->first();

            if ($existingByName) {
                return back()->with('error', '⚠️ يوجد موظف بنفس الاسم "' . $request->fullname . '" في هذا الفرع')
                    ->withInput();
            }

            // منع التكرار - التحقق من رقم الهاتف (إذا تم إدخاله)
            if (!empty($request->phone)) {
                $existingByPhone = Employee::where('company_code', $companyCode)
                    ->where('phone', $request->phone)
                    ->first();

                if ($existingByPhone) {
                    return back()->with('error', '⚠️ رقم الهاتف "' . $request->phone . '" مسجل مسبقاً للموظف: ' . $existingByPhone->fullname)
                        ->withInput();
                }
            }

            // منع التكرار - التحقق من البريد الإلكتروني (إذا تم إدخاله)
            if (!empty($request->email)) {
                $existingByEmail = Employee::where('company_code', $companyCode)
                    ->where('email', $request->email)
                    ->first();

                if ($existingByEmail) {
                    return back()->with('error', '⚠️ البريد الإلكتروني "' . $request->email . '" مسجل مسبقاً للموظف: ' . $existingByEmail->fullname)
                        ->withInput();
                }
            }

            $salary = str_replace(',', '', $request->salary);
            $NewEmployee = new Employee();
            $NewEmployee->company_code          = $companyCode;
            $NewEmployee->branch_id          = $request->branch_id;
            $NewEmployee->fullname          = $request->fullname;
            $NewEmployee->employee_types_id = $request->employee_types_id;
            $NewEmployee->shift_id          = $request->shift_id;
            $NewEmployee->isactive          = true; // إذا لم يتم الاختيار افتراضياً تفعيل
            $NewEmployee->createdate        = now();
            $NewEmployee->phone             = $request->phone;
            $NewEmployee->salary            = $salary;
            $NewEmployee->email             = $request->email;

            $company_code = $companyCode;




            // رفع الملف إذا تم إرساله
            if ($request->hasFile('file')) {

                $uploadPath = public_path('uploads/' . $company_code . '/employees_files');

                $file = $request->file('file'); // الملف المرسل من الفورم
                $uploadResult = \App\Helpers\FileUploadHelper::uploadSecurely(
                    $file,
                    $uploadPath,
                    array_merge(
                        \App\Helpers\FileUploadHelper::IMAGE_EXTENSIONS,
                        \App\Helpers\FileUploadHelper::DOCUMENT_EXTENSIONS
                    )
                );

                if (!$uploadResult['success']) {
                    return back()->with('error', $uploadResult['error'])->withInput();
                }

                // حفظ المسار النسبي في قاعدة البيانات
                $NewEmployee->file = 'uploads/' . $company_code . '/employees_files/' . $uploadResult['filename'];
            }



            if ($request->hasFile('personImage')) {

                $uploadPath = public_path('uploads/' . $company_code . '/personImage');

                $file = $request->file('personImage'); // الملف المرسل من الفورم
                $uploadResult = \App\Helpers\FileUploadHelper::uploadSecurely(
                    $file,
                    $uploadPath,
                    \App\Helpers\FileUploadHelper::IMAGE_EXTENSIONS
                );

                if (!$uploadResult['success']) {
                    return back()->with('error', $uploadResult['error'])->withInput();
                }

                // حفظ المسار النسبي في قاعدة البيانات
                $NewEmployee->personImage = 'uploads/' . $company_code . '/personImage/' . $uploadResult['filename'];
            }



            $NewEmployee->save();

            // ✅ حفظ الشفتات المتعددة في جدول employee_shifts
            if ($request->has('shift_ids') && is_array($request->shift_ids)) {
                $isPrimarySet = false;
                foreach ($request->shift_ids as $shiftId) {
                    // الشفت الأول يكون الرئيسي، أو الذي تم تحديده
                    $isPrimary = !$isPrimarySet || ($request->primary_shift_id == $shiftId);
                    if ($isPrimary) $isPrimarySet = true;

                    EmployeeShift::create([
                        'company_code' => $companyCode,
                        'employee_id' => $NewEmployee->id,
                        'shift_id' => $shiftId,
                        'is_active' => true,
                        'is_primary' => $isPrimary && !$isPrimarySet ? false : ($request->primary_shift_id == $shiftId || (!$isPrimarySet && $shiftId == $request->shift_ids[0])),
                        'assigned_date' => now(),
                    ]);
                    $isPrimarySet = true;
                }
            } elseif ($request->shift_id) {
                // التوافق مع النظام القديم - شفت واحد
                EmployeeShift::create([
                    'company_code' => $companyCode,
                    'employee_id' => $NewEmployee->id,
                    'shift_id' => $request->shift_id,
                    'is_active' => true,
                    'is_primary' => true,
                    'assigned_date' => now(),
                ]);
            }

            return back()->with('success', 'تم إضافة الموظف بنجاح ✅');
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
        if ($id == 'ListEmployees') {
            $shiftTimes = ShiftTime::where('company_code', Auth::user()->company_code)->get();
            $branches = Branch::where('company_code', Auth::user()->company_code)->get();
            $employeeTypes = EmployeeType::all();
            $employees = Employee::where('company_code', Auth::user()->company_code)
                ->with(['activeShifts.shift', 'shift', 'employeeType', 'Branchesname'])
                ->get();
            return view('employee.Listemployees', compact('employees', 'shiftTimes', 'branches', 'employeeTypes'));
        }

        if ($id == 'listBranchemployees') {
            $employees = Employee::where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->with(['activeShifts.shift', 'shift', 'employeeType', 'Branchesname'])
                ->get();
            return view('employee.listBranchemployees', compact('employees'));
        }

        // صفحة إضافة موظف جديد للفرع
        if ($id == 'addBranchEmployee') {
            $shiftTimes = ShiftTime::where('company_code', Auth::user()->company_code)->get();
            $employeeTypes = EmployeeType::all();
            return view('employee.addBranchEmployee', compact('shiftTimes', 'employeeTypes'));
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
        $explode = explode('&', $id);
        if ($explode[1] == "EditEmployee" || $explode[1] == "EditEmployeeDetails") {
            $shiftTimes = ShiftTime::where('company_code', Auth::user()->company_code)->get();
            $branches = Branch::where('company_code', Auth::user()->company_code)->get();
            $employeeTypes = EmployeeType::all();
            $employee = Employee::where('id', $explode[0])
                ->with(['activeShifts.shift'])
                ->first();

            // جلب الشفتات الحالية للموظف
            $currentShiftIds = $employee->activeShifts->pluck('shift_id')->toArray();
            $primaryShiftId = $employee->activeShifts->where('is_primary', true)->first()?->shift_id ?? $employee->shift_id;

            return view('employee.editEmployee', compact('employee', 'shiftTimes', 'branches', 'employeeTypes', 'currentShiftIds', 'primaryShiftId'));
        }
        if ($explode[1] == "ViewEmployeeDetails") {

            $employee = Employee::where('id', $explode[0])
                ->with(['activeShifts.shift'])
                ->first();
            // dd($employee);

            return view('employee.ViewEmployeeDetails', compact('employee'));
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
        if ($request->active == 'editEmployeeInformation') {
            DB::beginTransaction();
            try {
                $salary = str_replace(',', '', $request->salary); // إزالة الفواصل من الراتب

                // معالجة الملف إذا تم رفعه (بشكل آمن)
                $filePath = null;
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $uploadPath = public_path('uploads/employees_files');
                    $uploadResult = \App\Helpers\FileUploadHelper::uploadSecurely(
                        $file,
                        $uploadPath,
                        array_merge(
                            \App\Helpers\FileUploadHelper::IMAGE_EXTENSIONS,
                            \App\Helpers\FileUploadHelper::DOCUMENT_EXTENSIONS
                        )
                    );

                    if ($uploadResult['success']) {
                        $filePath = 'uploads/employees_files/' . $uploadResult['filename'];
                    }
                }

                // تحديد الشفت الرئيسي للتوافقية
                $primaryShiftId = $request->primary_shift_id ?? ($request->shift_ids[0] ?? $request->shift_id);

                // تحديث الموظف
                $updateData = [
                    'branch_id'          => $request->branch_id,
                    'fullname'           => $request->fullname,
                    'employee_types_id'  => $request->employee_types_id,
                    'shift_id'           => $primaryShiftId, // للتوافقية مع النظام القديم
                    'isactive'           => $request->isactive ?? 1, // افتراضي مفعل
                    'createdate'         => $request->createdate,
                    'phone'              => $request->phone,
                    'salary'             => $salary,
                    'email'              => $request->email,
                ];

                // إضافة مسار الملف في حال تم رفعه
                if ($filePath) {
                    $updateData['file'] = $filePath;
                }

                Employee::where('id', $id)->update($updateData);

                // ✅ تحديث الشفتات المتعددة
                if ($request->has('shift_ids') && is_array($request->shift_ids)) {
                    // حذف السجلات غير النشطة القديمة لتجنب تعارض القيد الفريد
                    EmployeeShift::where('employee_id', $id)
                        ->where('is_active', false)
                        ->whereIn('shift_id', $request->shift_ids)
                        ->delete();

                    // إنهاء الشفتات السابقة النشطة
                    EmployeeShift::where('employee_id', $id)
                        ->where('is_active', true)
                        ->update([
                            'is_active' => false,
                            'end_date' => now(),
                            'end_reason' => 'تم التحديث من صفحة التعديل'
                        ]);

                    // إضافة الشفتات الجديدة
                    foreach ($request->shift_ids as $shiftId) {
                        $isPrimary = ($request->primary_shift_id == $shiftId) ||
                            (!$request->primary_shift_id && $shiftId == $request->shift_ids[0]);

                        EmployeeShift::create([
                            'company_code' => Auth::user()->company_code,
                            'employee_id' => $id,
                            'shift_id' => $shiftId,
                            'is_active' => true,
                            'is_primary' => $isPrimary,
                            'assigned_date' => now(),
                        ]);
                    }
                } elseif ($request->shift_id) {
                    // التوافق مع النظام القديم - شفت واحد
                    // حذف السجلات غير النشطة القديمة لتجنب تعارض القيد الفريد
                    EmployeeShift::where('employee_id', $id)
                        ->where('is_active', false)
                        ->where('shift_id', $request->shift_id)
                        ->delete();

                    // إنهاء الشفتات السابقة النشطة
                    EmployeeShift::where('employee_id', $id)
                        ->where('is_active', true)
                        ->update([
                            'is_active' => false,
                            'end_date' => now(),
                            'end_reason' => 'تم التحديث من صفحة التعديل'
                        ]);

                    EmployeeShift::create([
                        'company_code' => Auth::user()->company_code,
                        'employee_id' => $id,
                        'shift_id' => $request->shift_id,
                        'is_active' => true,
                        'is_primary' => true,
                        'assigned_date' => now(),
                    ]);
                }

                DB::commit();
                return redirect('Employees/ListEmployees')->with('success', 'تم تحديث معلومات الموظف بنجاح ✅');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
            }
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
     * عرض صفحة إنشاء حساب مستخدم للموظف
     */
    public function showCreateAccount($id)
    {
        $companyCode = Auth::user()->company_code;

        // التحقق من عدد المستخدمين في الاشتراك (لغير السوبر أدمن)
        if ($companyCode !== 'SA') {
            $subscription = CompanySubscription::where('company_code', $companyCode)
                ->where('status', 'active')
                ->first();

            if ($subscription && $subscription->users_count) {
                $activeUsersCount = User::forCompany($companyCode)->activeForSubscription()->count();
                if ($activeUsersCount >= $subscription->users_count) {
                    return back()->with('error', '⚠️ تم الوصول للحد الأقصى لعدد المستخدمين النشطين في الاشتراك (' . $subscription->users_count . '). يمكنك تعطيل حساب لتحرير مكان أو تحديث حد المستخدمين.');
                }
            }
        }

        $employee = Employee::with(['employeeType', 'shift', 'Branchesname'])
            ->where('id', $id)
            ->where('company_code', $companyCode)
            ->firstOrFail();

        // التحقق من أن الموظف ليس لديه حساب مسبقاً
        if ($employee->user_id) {
            return back()->with('error', 'هذا الموظف لديه حساب مسبقاً');
        }

        // جلب الآليات بدون سائق (للسائقين)
        $availableCars = Cars::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('driver_id')
                    ->orWhereNull('backup_driver_id');
            })
            ->with('carType')
            ->get();

        // الشفتات للعرض
        $shifts = ShiftTime::where('company_code', Auth::user()->company_code)->get();

        // جلب أنواع الموظفين (باستثناء مجهز ومقاول)
        $employeeTypes = EmployeeType::whereNotIn('name', ['مجهز', 'مقاول'])->get();

        return view('employee.create-account', compact('employee', 'availableCars', 'shifts', 'employeeTypes'));
    }

    /**
     * إنشاء حساب مستخدم للموظف
     */
    public function storeUserAccount(Request $request, $id)
    {
        $request->validate([
            'emp_type_id' => 'required|exists:employee_types,id',
            'username' => 'required|string|min:2|max:50|regex:/^[a-zA-Z0-9_\-\.]+$/|unique:users,username',
            'password' => 'required|min:6',
        ], [
            'emp_type_id.required' => 'نوع الموظف مطلوب',
            'emp_type_id.exists' => 'نوع الموظف غير صالح',
            'username.required' => 'اسم المستخدم مطلوب',
            'username.regex' => 'اسم المستخدم يجب أن يحتوي على أحرف إنجليزية وأرقام فقط',
            'username.unique' => 'اسم المستخدم مستخدم مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        $employee = Employee::where('id', $id)
            ->where('company_code', Auth::user()->company_code)
            ->firstOrFail();

        $companyCode = $employee->company_code;

        // التحقق من عدد المستخدمين في الاشتراك (لغير السوبر أدمن)
        if ($companyCode !== 'SA') {
            $subscription = CompanySubscription::where('company_code', $companyCode)
                ->where('status', 'active')
                ->first();

            if ($subscription && $subscription->users_count) {
                $activeUsersCount = User::forCompany($companyCode)->activeForSubscription()->count();
                if ($activeUsersCount >= $subscription->users_count) {
                    return back()->with('error', '⚠️ تم الوصول للحد الأقصى لعدد المستخدمين النشطين في الاشتراك (' . $subscription->users_count . '). يمكنك تعطيل حساب لتحرير مكان أو تحديث حد المستخدمين.')
                        ->withInput();
                }
            }
        }

        // التحقق من أن الموظف ليس لديه حساب
        if ($employee->user_id) {
            return back()->with('error', 'هذا الموظف لديه حساب مسبقاً');
        }

        $usernameClean = strtolower(trim($request->username));

        $user = User::create([
            'fullname' => $employee->fullname,
            'username' => $usernameClean,
            'email' => $usernameClean . '@system.local',
            'password' => Hash::make($request->password),
            'usertype_id' => 'US',
            'company_code' => $employee->company_code,
            'branch_id' => $employee->branch_id,
            'emp_type_id' => $request->emp_type_id,
            'account_code' => 'emp',
            'is_active' => true,
        ]);

        if ($employee->employee_types_id != $request->emp_type_id) {
            $employee->update(['employee_types_id' => $request->emp_type_id]);
        }

        $employee->update(['user_id' => $user->id]);

        // إذا كان سائق وتم تعيينه على آلية
        if ($request->car_id && $employee->is_driver) {
            $car = Cars::find($request->car_id);
            if ($car) {
                $assignmentType = $request->assignment_type ?? 'primary';

                if ($assignmentType === 'primary' && !$car->driver_id) {
                    $car->update([
                        'driver_id' => $employee->id,
                        'driver_name' => $employee->fullname,
                    ]);
                } elseif ($assignmentType === 'backup' && !$car->backup_driver_id) {
                    $car->update(['backup_driver_id' => $employee->id]);
                }

                // تسجيل التعيين
                DriverAssignment::create([
                    'company_code' => Auth::user()->company_code,
                    'branch_id' => Auth::user()->branch_id,
                    'car_id' => $car->id,
                    'driver_id' => $employee->id,
                    'assignment_type' => $assignmentType,
                    'start_date' => now(),
                    'assigned_by' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('Employees.show', 'listBranchemployees')
            ->with('success', 'تم إنشاء حساب المستخدم للموظف بنجاح ✅');
    }

    /**
     * جلب الآليات بدون سائق (للسائقين) - API
     */
    public function getAvailableCars(Request $request)
    {
        $type = $request->get('type', 'primary'); // primary أو backup

        $query = Cars::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('is_active', true)
            ->with('carType');

        if ($type === 'primary') {
            $query->whereNull('driver_id');
        } else {
            $query->whereNull('backup_driver_id');
        }

        $cars = $query->get()->map(function ($car) {
            return [
                'id' => $car->id,
                'car_number' => $car->car_number,
                'car_model' => $car->car_model,
                'car_type' => $car->carType->name ?? 'غير محدد',
                'has_primary' => !is_null($car->driver_id),
                'has_backup' => !is_null($car->backup_driver_id),
            ];
        });

        return response()->json($cars);
    }
}
