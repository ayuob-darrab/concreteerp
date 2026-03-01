<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CarDriver;
use App\Models\Cars;
use App\Models\CarsType;
use App\Models\DriverAssignment;
use App\Models\Employee;
use App\Models\ShiftTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarsController extends Controller
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


        if ($request->active == "NewCarType") {
            $newCartype = new CarsType();
            $newCartype->name = $request->car_type_name;
            $newCartype->company_code =  Auth::user()->company_code;
            $newCartype->note = $request->note;
            $newCartype->save();
            return back()->with('success', 'تم اضافة نوع السياره بنجاح');
        }
        if ($request->active == "AddnewCar") {
            DB::beginTransaction();
            try {
                // ✅ التحقق من عدم تكرار السيارة (نفس النوع والاسم والرقم)
                $existingCar = Cars::where('company_code', Auth::user()->company_code)
                    ->where('car_type_id', $request->car_type_id)
                    ->where('car_name', $request->car_name)
                    ->where('car_number', $request->car_number)
                    ->first();

                if ($existingCar) {
                    return back()->withInput()->with('error', '⚠️ السيارة موجودة مسبقاً بنفس النوع والاسم والرقم');
                }

                // ✅ إنشاء سجل جديد للسيارة
                $newCar = new Cars();
                $newCar->branch_id    = $request->branch_id;
                $newCar->company_code =  Auth::user()->company_code;
                $newCar->car_type_id  = $request->car_type_id;
                $newCar->car_name     = $request->car_name; // اسم السيارة
                $newCar->car_number   = $request->car_number;
                $newCar->car_model    = $request->car_model;
                $newCar->mixer_capacity = $request->mixer_capacity; // سعة الخباطة
                $newCar->driver_name  = ''; // سيتم تحديثه من الجدول الجديد
                $newCar->add_date     = now();
                $newCar->note         = $request->note;
                $newCar->is_active    = true;

                $newCar->save();

                // ✅ حفظ السائقين في الجدول الجديد car_drivers (دعم عدة شفتات)
                if ($request->has('drivers') && is_array($request->drivers)) {
                    foreach ($request->drivers as $shiftId => $driverData) {
                        // حفظ السائق الرئيسي
                        if (!empty($driverData['primary'])) {
                            CarDriver::create([
                                'company_code' => Auth::user()->company_code,
                                'car_id' => $newCar->id,
                                'driver_id' => $driverData['primary'],
                                'shift_id' => $shiftId,
                                'driver_type' => CarDriver::TYPE_PRIMARY,
                                'is_active' => true,
                                'assigned_date' => now(),
                            ]);

                            // تحديث driver_id و driver_name للتوافق مع النظام القديم (أول سائق رئيسي)
                            if (empty($newCar->driver_id)) {
                                $newCar->driver_id = $driverData['primary'];
                                $driver = Employee::find($driverData['primary']);
                                $newCar->driver_name = $driver ? $driver->fullname : '';
                            }
                        }

                        // حفظ السائق الاحتياطي
                        if (!empty($driverData['backup'])) {
                            CarDriver::create([
                                'company_code' => Auth::user()->company_code,
                                'car_id' => $newCar->id,
                                'driver_id' => $driverData['backup'],
                                'shift_id' => $shiftId,
                                'driver_type' => CarDriver::TYPE_BACKUP,
                                'is_active' => true,
                                'assigned_date' => now(),
                            ]);

                            // تحديث backup_driver_id للتوافق مع النظام القديم (أول سائق احتياطي)
                            if (empty($newCar->backup_driver_id)) {
                                $newCar->backup_driver_id = $driverData['backup'];
                            }
                        }
                    }

                    $newCar->save(); // حفظ التحديثات للتوافقية
                }
                // الطريقة القديمة (driver_id مباشرة) - للتوافق مع النماذج القديمة
                elseif ($request->driver_id) {
                    $newCar->driver_id = $request->driver_id;
                    $driver = Employee::find($request->driver_id);
                    $newCar->driver_name = $driver ? $driver->fullname : '';
                    $newCar->backup_driver_id = $request->backup_driver_id;
                    $newCar->save();
                }

                DB::commit();

                // ✅ التوجيه حسب المصدر
                if ($request->redirect_to == 'branch') {
                    return back()->with('success', 'تمت إضافة السيارة بنجاح ✅ مع جميع السائقين لكل الشفتات');
                }

                // ✅ رسالة نجاح
                return back()->with('success', 'تمت إضافة السيارة بنجاح ✅');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'حدث خطأ أثناء إضافة السيارة: ' . $e->getMessage());
            }
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
        if ($id == "add-type") {
            return view('cars.addType');
        }

        if ($id == "list-types") {
            // $CarsType = CarsType::all();
            $CarsType = CarsType::where('company_code', Auth::user()->company_code)->withCount('cars')->get();
            return view('cars.listTypecar', compact('CarsType'));
        }

        if ($id == "ListCar") {
            $listCars = Cars::where('company_code', Auth::user()->company_code)->get();
            $carstype = CarsType::where('company_code', Auth::user()->company_code)->get();
            $branches = Branch::where('company_code', Auth::user()->company_code)->get();
            return view('cars.ListCar', compact('listCars', 'carstype', 'branches'));
        }

        if ($id == "ListBranchCar") {
            $listCars = Cars::where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->with(['driver.shift', 'backupDriver.shift', 'carType', 'activeCarDrivers.driver', 'activeCarDrivers.shift'])
                ->get();
            $carstype = CarsType::where('company_code', Auth::user()->company_code)->get();
            $shifts = ShiftTime::where('company_code', Auth::user()->company_code)->get();
            return view('cars.ListBranchCar', compact('listCars', 'carstype', 'shifts'));
        }

        // صفحة إضافة سيارة جديدة للفرع
        if ($id == "addBranchCar") {
            $carstype = CarsType::where('company_code', Auth::user()->company_code)->get();

            // جلب موظفين الفرع الحالي مع الشفتات النشطة من جدول employee_shifts
            $employees = Employee::where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->where('isactive', true)
                ->with(['employeeType', 'activeShifts'])
                ->get();

            // بناء مصفوفة الموظفين لكل شفت من جدول employee_shifts
            $employeesByShift = [];
            foreach ($employees as $employee) {
                foreach ($employee->activeShifts as $employeeShift) {
                    if (!isset($employeesByShift[$employeeShift->shift_id])) {
                        $employeesByShift[$employeeShift->shift_id] = [];
                    }
                    $employeesByShift[$employeeShift->shift_id][] = [
                        'id' => $employee->id,
                        'name' => $employee->fullname,
                        'shift_id' => $employeeShift->shift_id,
                    ];
                }
            }

            // جلب الشفتات
            $shifts = ShiftTime::where('company_code', Auth::user()->company_code)->get();

            // جلب السائقين المعينين حالياً (لمنع التكرار)
            $assignedDrivers = CarDriver::where('company_code', Auth::user()->company_code)
                ->where('is_active', true)
                ->get()
                ->groupBy('shift_id')
                ->map(function ($items) {
                    return $items->pluck('driver_id')->toArray();
                })
                ->toArray();

            return view('cars.addBranchCar', compact('carstype', 'employees', 'shifts', 'assignedDrivers', 'employeesByShift'));
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

        if ($explode[1] == "edit_cartype") {
            $CarType = CarsType::where('id', $explode[0])->first();
            return view('cars.editType', compact('CarType'));
        }

        if ($explode[1] == "EditCarInformation") {

            $car = Cars::where('id', $explode[0])
                ->with(['activeCarDrivers.driver', 'activeCarDrivers.shift'])
                ->first();
            $carstype = CarsType::where('company_code', Auth::user()->company_code)->get();
            $branches = Branch::where('company_code', Auth::user()->company_code)->get();
            $shifts = ShiftTime::where('company_code', Auth::user()->company_code)->get();

            // جلب السائقين من جدول الحسابات (users) - حسابات الموظفين للفرع
            $driverAccounts = User::where('company_code', Auth::user()->company_code)
                ->where('branch_id', $car->branch_id)
                ->where('account_code', 'emp')
                ->where('is_active', true)
                ->orderBy('fullname')
                ->get();

            // بناء مصفوفة السائقين (الحسابات) لكل شفت - نفس القائمة لكل الشفتات
            $employeesByShift = [];
            foreach ($shifts as $shift) {
                $employeesByShift[$shift->id] = $driverAccounts->map(function ($user) use ($shift) {
                    return [
                        'id' => $user->id,
                        'name' => $user->fullname,
                        'shift_id' => $shift->id,
                    ];
                })->values()->toArray();
            }

            // تجهيز بيانات السائقين الحاليين حسب الشفتات
            $currentDrivers = [];
            foreach ($car->activeCarDrivers as $cd) {
                if (!isset($currentDrivers[$cd->shift_id])) {
                    $currentDrivers[$cd->shift_id] = ['primary' => null, 'backup' => null];
                }
                $currentDrivers[$cd->shift_id][$cd->driver_type] = $cd->driver_id;
            }

            // جلب السائقين المعينين حالياً (لمنع التكرار) - مع استثناء السيارة الحالية
            $assignedDrivers = CarDriver::where('company_code', Auth::user()->company_code)
                ->where('is_active', true)
                ->where('car_id', '!=', $car->id) // استثناء السيارة الحالية
                ->get()
                ->groupBy('shift_id')
                ->map(function ($items) {
                    return $items->pluck('driver_id')->toArray();
                })
                ->toArray();

            return view('cars.editcar', compact('car', 'carstype', 'branches', 'shifts', 'currentDrivers', 'assignedDrivers', 'employeesByShift'));
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
        if ($request->active == "editCarType") {
            CarsType::where('id', $id)->update([
                'name' => $request->car_type_name,
                'note' => $request->note,
            ]);
            return redirect('cars/list-types')->with('success', 'تم تحديث النوع بنجاح');
        }
        if ($request->active == "UpdateCarInformation") {
            DB::beginTransaction();
            try {
                $car = Cars::find($id);

                // تحديث البيانات الأساسية
                $updateData = [
                    'branch_id'   => $request->branch_id,
                    'car_type_id' => $request->car_type_id,
                    'car_name'    => $request->car_name,
                    'car_number'  => $request->car_number,
                    'car_model'   => $request->car_model,
                    'mixer_capacity' => $request->mixer_capacity,
                    'note'        => $request->note,
                    'is_active'   => $request->is_active,
                ];

                // ✅ تحديث السائقين في جدول car_drivers الجديد (دعم عدة شفتات)
                if ($request->has('drivers') && is_array($request->drivers)) {
                    // إنهاء جميع التكليفات السابقة لهذه السيارة
                    CarDriver::where('car_id', $id)
                        ->where('is_active', true)
                        ->update([
                            'is_active' => false,
                            'end_date' => now(),
                            'end_reason' => 'تم التحديث من صفحة التعديل'
                        ]);

                    $firstPrimaryDriver = null;
                    $firstBackupDriver = null;

                    foreach ($request->drivers as $shiftId => $driverData) {
                        // حفظ السائق الرئيسي
                        if (!empty($driverData['primary'])) {
                            CarDriver::create([
                                'company_code' => Auth::user()->company_code,
                                'car_id' => $id,
                                'driver_id' => $driverData['primary'],
                                'shift_id' => $shiftId,
                                'driver_type' => CarDriver::TYPE_PRIMARY,
                                'is_active' => true,
                                'assigned_date' => now(),
                            ]);

                            // للتوافقية مع النظام القديم
                            if ($firstPrimaryDriver === null) {
                                $firstPrimaryDriver = $driverData['primary'];
                            }
                        }

                        // حفظ السائق الاحتياطي
                        if (!empty($driverData['backup'])) {
                            CarDriver::create([
                                'company_code' => Auth::user()->company_code,
                                'car_id' => $id,
                                'driver_id' => $driverData['backup'],
                                'shift_id' => $shiftId,
                                'driver_type' => CarDriver::TYPE_BACKUP,
                                'is_active' => true,
                                'assigned_date' => now(),
                            ]);

                            // للتوافقية مع النظام القديم
                            if ($firstBackupDriver === null) {
                                $firstBackupDriver = $driverData['backup'];
                            }
                        }
                    }

                    // تحديث الأعمدة القديمة للتوافقية (معرف السائق من جدول الحسابات users)
                    $updateData['driver_id'] = $firstPrimaryDriver;
                    $updateData['backup_driver_id'] = $firstBackupDriver;
                    if ($firstPrimaryDriver) {
                        $driverUser = User::find($firstPrimaryDriver);
                        $updateData['driver_name'] = $driverUser ? $driverUser->fullname : '';
                    } else {
                        $updateData['driver_name'] = '';
                    }
                }

                Cars::where('id', $id)->update($updateData);

                DB::commit();

                return redirect('cars/ListBranchCar')->with('success', 'تم تحديث معلومات السيارة بنجاح ✅');
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
     * إنهاء تكليف السائقين (يدعم عدة شفتات)
     */
    public function endDriverAssignment(Request $request, $id)
    {
        $car = Cars::where('id', $id)
            ->where('company_code', Auth::user()->company_code)
            ->firstOrFail();

        $endedDrivers = [];
        $reason = $request->end_reason ?? 'إنهاء تكليف من قبل الإدارة';

        // ✅ معالجة الإنهاء من جدول car_drivers الجديد (عدة شفتات)
        if ($request->has('end_drivers') && is_array($request->end_drivers)) {
            foreach ($request->end_drivers as $shiftId => $driverTypes) {
                // إنهاء السائق الرئيسي لهذا الشفت
                if (!empty($driverTypes['primary'])) {
                    $carDriver = CarDriver::where('car_id', $id)
                        ->where('shift_id', $shiftId)
                        ->where('driver_type', CarDriver::TYPE_PRIMARY)
                        ->where('is_active', true)
                        ->with('driver', 'shift')
                        ->first();

                    if ($carDriver) {
                        $driverName = $carDriver->driver ? $carDriver->driver->fullname : 'غير معروف';
                        $shiftName = $carDriver->shift ? $carDriver->shift->name : 'غير محدد';

                        $carDriver->update([
                            'is_active' => false,
                            'end_date' => now(),
                            'end_reason' => $reason
                        ]);

                        $endedDrivers[] = "رئيسي ({$shiftName}): {$driverName}";
                    }
                }

                // إنهاء السائق الاحتياطي لهذا الشفت
                if (!empty($driverTypes['backup'])) {
                    $carDriver = CarDriver::where('car_id', $id)
                        ->where('shift_id', $shiftId)
                        ->where('driver_type', CarDriver::TYPE_BACKUP)
                        ->where('is_active', true)
                        ->with('driver', 'shift')
                        ->first();

                    if ($carDriver) {
                        $driverName = $carDriver->driver ? $carDriver->driver->fullname : 'غير معروف';
                        $shiftName = $carDriver->shift ? $carDriver->shift->name : 'غير محدد';

                        $carDriver->update([
                            'is_active' => false,
                            'end_date' => now(),
                            'end_reason' => $reason
                        ]);

                        $endedDrivers[] = "احتياط ({$shiftName}): {$driverName}";
                    }
                }
            }

            // تحديث الأعمدة القديمة للتوافقية
            $firstActiveDriver = CarDriver::where('car_id', $id)
                ->where('is_active', true)
                ->where('driver_type', CarDriver::TYPE_PRIMARY)
                ->first();

            $firstActiveBackup = CarDriver::where('car_id', $id)
                ->where('is_active', true)
                ->where('driver_type', CarDriver::TYPE_BACKUP)
                ->first();

            $car->update([
                'driver_id' => $firstActiveDriver ? $firstActiveDriver->driver_id : null,
                'driver_name' => $firstActiveDriver && $firstActiveDriver->driver ? $firstActiveDriver->driver->fullname : null,
                'backup_driver_id' => $firstActiveBackup ? $firstActiveBackup->driver_id : null,
            ]);
        }

        if (empty($endedDrivers)) {
            return back()->with('error', 'لم يتم تحديد أي سائق لإنهاء تكليفه');
        }

        $message = 'تم إنهاء تكليف: ' . implode(' ، ', $endedDrivers);
        return back()->with('success', $message);
    }

    /**
     * تصفير جدول السيارات: إنهاء جميع تكليفات السائقين وتحريرهم لجميع سيارات الشركة
     */
    public function clearAllDrivers(Request $request)
    {
        $companyCode = Auth::user()->company_code;

        DB::beginTransaction();
        try {
            // إنهاء جميع التكليفات النشطة في car_drivers للشركة
            CarDriver::where('company_code', $companyCode)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'end_date' => now(),
                    'end_reason' => 'تصفير من صفحة قائمة السيارات - تحرير السائقين',
                ]);

            // مسح أعمدة السائقين من جدول السيارات
            Cars::where('company_code', $companyCode)->update([
                'driver_id' => null,
                'backup_driver_id' => null,
                'driver_name' => '',
            ]);

            DB::commit();
            return redirect()->route('cars.show', 'ListCar')->with('success', 'تم تصفير الجدول وتحرير جميع السائقين بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cars.show', 'ListCar')->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * جلب السائقين حسب الشفت - API
     * يدعم الموظفين الذين يعملون في أكثر من شفت
     */
    public function getDriversByShift(Request $request)
    {
        $branchId = $request->get('branch_id', Auth::user()->branch_id);
        $shiftId = $request->get('shift_id');

        $query = Employee::where('company_code', Auth::user()->company_code)
            ->where('branch_id', $branchId)
            ->where('isactive', true)
            ->with(['employeeType', 'shift', 'activeShifts.shift']);

        $employees = $query->get()->map(function ($emp) use ($shiftId) {
            // جمع الشفتات من الجدول الجديد
            $employeeShiftIds = $emp->activeShifts->pluck('shift_id')->toArray();

            // fallback للنظام القديم
            if (empty($employeeShiftIds) && $emp->shift_id) {
                $employeeShiftIds = [$emp->shift_id];
            }

            return [
                'id' => $emp->id,
                'fullname' => $emp->fullname,
                'employee_type' => $emp->employeeType ? $emp->employeeType->name : 'غير محدد',
                'shift' => $emp->shift ? $emp->shift->name : 'غير محدد',
                'shift_id' => $emp->shift_id,
                'shift_ids' => $employeeShiftIds,
                'shift_names' => $emp->shift_names,
                'is_driver' => $emp->is_driver,
                'works_in_shift' => !$shiftId || $shiftId === 'all' || in_array($shiftId, $employeeShiftIds),
            ];
        });

        // فلترة حسب الشفت إذا تم تحديده
        if ($shiftId && $shiftId !== 'all') {
            $employees = $employees->filter(function ($emp) {
                return $emp['works_in_shift'];
            })->values();
        }

        // تجميع حسب الشفت
        $grouped = $employees->groupBy('shift');

        return response()->json([
            'employees' => $employees,
            'grouped' => $grouped,
        ]);
    }
}
