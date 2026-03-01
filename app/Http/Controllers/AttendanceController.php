<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\ShiftTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * AttendanceController - متحكم الحضور والانصراف
 * 
 * يدير تسجيل حضور وانصراف الموظفين ويعرض التقارير
 */
class AttendanceController extends Controller
{
    /**
     * عرض صفحة تسجيل الحضور للموظف
     */
    public function index()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'لم يتم العثور على بيانات الموظف');
        }

        // جلب شفت الموظف
        $shift = $employee->shift;

        // التحقق من تسجيل الحضور اليوم
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        // جلب سجل الحضور الأخير (آخر 7 أيام)
        $recentAttendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('attendance_date', 'desc')
            ->take(7)
            ->get();

        // إحصائيات الشهر الحالي
        $monthStats = Attendance::getEmployeeStats(
            $employee->id,
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        );

        return view('attendance.index', compact(
            'employee',
            'shift',
            'todayAttendance',
            'recentAttendances',
            'monthStats'
        ));
    }

    /**
     * تسجيل الحضور
     */
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على بيانات الموظف'
            ], 404);
        }

        // التحقق من عدم تسجيل الحضور مسبقاً اليوم
        if (Attendance::hasCheckedInToday($employee->id)) {
            return response()->json([
                'success' => false,
                'message' => 'تم تسجيل حضورك مسبقاً اليوم'
            ], 400);
        }

        $currentTime = Carbon::now();
        $currentDate = Carbon::today();

        // جلب شفت الموظف
        $shift = $employee->shift;
        $shiftStartTime = $shift ? $shift->start_time : '08:00:00';

        // حساب التأخير
        $lateMinutes = Attendance::calculateLateMinutes($currentTime->format('H:i:s'), $shiftStartTime);
        $earlyMinutes = Attendance::calculateEarlyMinutes($currentTime->format('H:i:s'), $shiftStartTime);

        // تحديد الحالة
        $status = Attendance::determineStatus($lateMinutes);

        try {
            DB::beginTransaction();

            $attendance = Attendance::create([
                'company_code' => $user->company_code,
                'branch_id' => $employee->branch_id,
                'employee_id' => $employee->id,
                'user_id' => $user->id,
                'shift_id' => $shift ? $shift->id : null,
                'attendance_date' => $currentDate,
                'check_in_time' => $currentTime->format('H:i:s'),
                'shift_start_time' => $shiftStartTime,
                'late_minutes' => $lateMinutes,
                'early_minutes' => $earlyMinutes,
                'status' => $status,
                'check_in_ip' => $request->ip(),
                'check_in_latitude' => $request->input('latitude'),
                'check_in_longitude' => $request->input('longitude'),
                'notes' => $request->input('notes'),
            ]);

            DB::commit();

            $message = $status === Attendance::STATUS_LATE
                ? "تم تسجيل حضورك متأخراً بـ {$attendance->late_human}"
                : "تم تسجيل حضورك بنجاح";

            return response()->json([
                'success' => true,
                'message' => $message,
                'attendance' => $attendance,
                'status' => $status,
                'late_minutes' => $lateMinutes
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الحضور: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تسجيل الانصراف
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على بيانات الموظف'
            ], 404);
        }

        // جلب سجل حضور اليوم
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', Carbon::today())
            ->whereNull('check_out_time')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على سجل حضور لليوم أو تم تسجيل الانصراف مسبقاً'
            ], 400);
        }

        $currentTime = Carbon::now();

        // جلب شفت الموظف
        $shift = $employee->shift;
        $shiftEndTime = $shift ? $shift->end_time : '17:00:00';

        // حساب الخروج المبكر والعمل الإضافي
        $earlyLeaveMinutes = Attendance::calculateEarlyLeaveMinutes($currentTime->format('H:i:s'), $shiftEndTime);
        $overtimeMinutes = Attendance::calculateOvertimeMinutes($currentTime->format('H:i:s'), $shiftEndTime);

        // حساب إجمالي وقت العمل
        $totalWorkMinutes = Attendance::calculateTotalWorkMinutes(
            $attendance->check_in_time,
            $currentTime->format('H:i:s')
        );

        try {
            DB::beginTransaction();

            $attendance->update([
                'check_out_time' => $currentTime->format('H:i:s'),
                'shift_end_time' => $shiftEndTime,
                'early_leave_minutes' => $earlyLeaveMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'total_work_minutes' => $totalWorkMinutes,
                'check_out_ip' => $request->ip(),
                'check_out_latitude' => $request->input('latitude'),
                'check_out_longitude' => $request->input('longitude'),
            ]);

            DB::commit();

            $message = "تم تسجيل انصرافك بنجاح. إجمالي وقت العمل: {$attendance->total_work_human}";

            if ($overtimeMinutes > 0) {
                $message .= " (عمل إضافي: {$attendance->overtime_human})";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'attendance' => $attendance->fresh(),
                'total_work_minutes' => $totalWorkMinutes,
                'overtime_minutes' => $overtimeMinutes
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الانصراف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض سجل حضور الموظف
     */
    public function myHistory(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'لم يتم العثور على بيانات الموظف');
        }

        // الفلترة حسب الشهر
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->orderBy('attendance_date', 'desc')
            ->paginate(31);

        // إحصائيات الشهر
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        $stats = Attendance::getEmployeeStats($employee->id, $startDate, $endDate);

        return view('attendance.history', compact(
            'employee',
            'attendances',
            'stats',
            'month',
            'year'
        ));
    }

    /**
     * ====================================
     * وظائف المدير / الإدارة
     * ====================================
     */

    /**
     * لوحة تحكم الحضور للمدير
     */
    public function adminDashboard()
    {
        $user = Auth::user();

        // التحقق من صلاحيات المدير
        if (!in_array($user->usertype_id, ['CM', 'BM', 'SA'])) {
            return redirect()->back()->with('error', 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $query = Attendance::with(['employee', 'shift'])
            ->where('company_code', $user->company_code)
            ->whereDate('attendance_date', Carbon::today());

        // مدير الفرع يرى فرعه فقط
        if ($user->usertype_id === 'BM') {
            $query->where('branch_id', $user->branch_id);
        }

        $todayAttendances = $query->orderBy('check_in_time', 'desc')->get();

        // جلب الموظفين الغائبين
        $absentEmployees = $this->getAbsentEmployees($user);

        // إحصائيات اليوم
        $stats = [
            'total_employees' => $this->getTotalEmployees($user),
            'present_today' => $todayAttendances->whereIn('status', ['present', 'late'])->count(),
            'late_today' => $todayAttendances->where('status', 'late')->count(),
            'absent_today' => $absentEmployees->count(),
            'on_leave' => $todayAttendances->whereIn('status', ['on_leave', 'sick_leave'])->count(),
        ];

        return view('attendance.admin.dashboard', compact('todayAttendances', 'absentEmployees', 'stats'));
    }

    /**
     * تقرير الحضور للمدير
     */
    public function adminReport(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->usertype_id, ['CM', 'BM', 'SA'])) {
            return redirect()->back()->with('error', 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $startDate = $request->input('date_from', Carbon::now()->format('Y-m-d'));
        $endDate = $request->input('date_to', Carbon::now()->format('Y-m-d'));
        $employeeId = $request->input('employee_id');
        $status = $request->input('status');
        $branchId = $request->input('branch_id');
        $employeeTypeId = $request->input('employee_type_id');

        $query = Attendance::with(['employee.employeeType', 'shift', 'branch'])
            ->where('company_code', $user->company_code)
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        // مدير الفرع يرى فرعه فقط
        if ($user->usertype_id === 'BM') {
            $query->where('branch_id', $user->branch_id);
        } elseif ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($employeeTypeId) {
            $query->whereHas('employee', fn($q) => $q->where('employee_types_id', $employeeTypeId));
        }

        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate(50);

        // قائمة الفروع (لمدير الشركة والسوبر أدمن) — لاختيار فرع واحد أو عرض كل الفروع
        $branches = collect();
        if (in_array($user->usertype_id, ['CM', 'SA'])) {
            $branches = \App\Models\Branch::where('company_code', $user->company_code)->where('is_active', 1)->orderBy('branch_name')->get();
        }

        // قائمة الموظفين للفلترة
        $employeesQuery = Employee::with('employeeType')->where('company_code', $user->company_code);
        if ($user->usertype_id === 'BM') {
            $employeesQuery->where('branch_id', $user->branch_id);
        } elseif ($branchId) {
            $employeesQuery->where('branch_id', $branchId);
        }
        if ($employeeTypeId) {
            $employeesQuery->where('employee_types_id', $employeeTypeId);
        }
        $employees = $employeesQuery->orderBy('fullname')->get();

        // أنواع الموظفين (أقسام) للفلترة
        $typeIds = Employee::where('company_code', $user->company_code)
            ->when($user->usertype_id === 'BM', fn($q) => $q->where('branch_id', $user->branch_id))
            ->whereNotNull('employee_types_id')
            ->distinct()
            ->pluck('employee_types_id');
        $employeeTypes = $typeIds->isNotEmpty() ? EmployeeType::whereIn('id', $typeIds)->orderBy('name')->get() : collect();

        // حساب الملخص
        $summaryQuery = Attendance::where('company_code', $user->company_code)
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($user->usertype_id === 'BM') {
            $summaryQuery->where('branch_id', $user->branch_id);
        } elseif ($branchId) {
            $summaryQuery->where('branch_id', $branchId);
        }
        if ($employeeId) {
            $summaryQuery->where('employee_id', $employeeId);
        }
        if ($employeeTypeId) {
            $summaryQuery->whereHas('employee', fn($q) => $q->where('employee_types_id', $employeeTypeId));
        }
        if ($status) {
            $summaryQuery->where('status', $status);
        }

        $allRecords = $summaryQuery->get();
        $totalLateMinutes = $allRecords->sum('late_minutes');
        $lateHours = floor($totalLateMinutes / 60);
        $lateMinutes = $totalLateMinutes % 60;

        $summary = [
            'total_records' => $allRecords->count(),
            'present' => $allRecords->where('status', 'present')->count(),
            'late' => $allRecords->where('status', 'late')->count(),
            'absent' => $allRecords->where('status', 'absent')->count(),
            'on_leave' => $allRecords->whereIn('status', ['on_leave', 'sick_leave'])->count(),
            'total_late_hours' => sprintf('%d:%02d', $lateHours, $lateMinutes),
        ];

        return view('attendance.admin.report', compact(
            'attendances',
            'employees',
            'branches',
            'employeeTypes',
            'summary',
            'startDate',
            'endDate',
            'employeeId',
            'branchId',
            'employeeTypeId',
            'status'
        ));
    }

    /**
     * تعديل سجل حضور (للمدير)
     */
    public function adminUpdate(Request $request, $id)
    {
        $user = Auth::user();

        // التحقق من صلاحيات المدير
        if (!in_array($user->usertype_id, ['CM', 'BM', 'SA'])) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $request->validate([
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,late,absent,on_leave,on_mission,sick_leave,half_day',
            'manager_notes' => 'nullable|string|max:500',
            'modification_reason' => 'required|string|max:255',
        ]);

        $attendance = Attendance::where('company_code', $user->company_code)
            ->findOrFail($id);

        // مدير الفرع يعدل فرعه فقط
        if ($user->usertype_id === 'BM' && $attendance->branch_id !== $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل سجلات موظفي فروع أخرى'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $updateData = [
                'status' => $request->input('status'),
                'manager_notes' => $request->input('manager_notes'),
                'modified_by' => $user->id,
                'modified_at' => Carbon::now(),
                'modification_reason' => $request->input('modification_reason'),
            ];

            if ($request->filled('check_in_time')) {
                $updateData['check_in_time'] = $request->input('check_in_time') . ':00';

                // إعادة حساب التأخير
                $shift = $attendance->shift;
                $shiftStartTime = $shift ? $shift->start_time : '08:00:00';
                $updateData['late_minutes'] = Attendance::calculateLateMinutes(
                    $updateData['check_in_time'],
                    $shiftStartTime
                );
            }

            if ($request->filled('check_out_time')) {
                $updateData['check_out_time'] = $request->input('check_out_time') . ':00';

                // إعادة حساب العمل الإضافي وإجمالي وقت العمل
                $shift = $attendance->shift;
                $shiftEndTime = $shift ? $shift->end_time : '17:00:00';

                $updateData['overtime_minutes'] = Attendance::calculateOvertimeMinutes(
                    $updateData['check_out_time'],
                    $shiftEndTime
                );

                $checkInTime = $request->filled('check_in_time')
                    ? $updateData['check_in_time']
                    : $attendance->check_in_time;

                $updateData['total_work_minutes'] = Attendance::calculateTotalWorkMinutes(
                    $checkInTime,
                    $updateData['check_out_time']
                );
            }

            $attendance->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث سجل الحضور بنجاح',
                'attendance' => $attendance->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث السجل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تسجيل غياب (للمدير)
     */
    public function markAbsent(Request $request)
    {
        $user = Auth::user();

        // التحقق من صلاحيات المدير
        if (!in_array($user->usertype_id, ['CM', 'BM', 'SA'])) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بهذا الإجراء'
            ], 403);
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:absent,on_leave,on_mission,sick_leave',
            'notes' => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($request->input('employee_id'));

        // التحقق من أن الموظف تابع لنفس الشركة
        if ($employee->company_code !== $user->company_code) {
            return response()->json([
                'success' => false,
                'message' => 'الموظف لا ينتمي لشركتك'
            ], 403);
        }

        // مدير الفرع يسجل لفرعه فقط
        if ($user->usertype_id === 'BM' && $employee->branch_id !== $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتسجيل غياب موظفي فروع أخرى'
            ], 403);
        }

        // التحقق من عدم وجود سجل مسبق
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $request->input('date'))
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'يوجد سجل حضور مسبق لهذا اليوم'
            ], 400);
        }

        try {
            $attendance = Attendance::create([
                'company_code' => $user->company_code,
                'branch_id' => $employee->branch_id,
                'employee_id' => $employee->id,
                'user_id' => $user->id, // المدير الذي سجل الغياب
                'shift_id' => $employee->shift_id,
                'attendance_date' => $request->input('date'),
                'status' => $request->input('status'),
                'manager_notes' => $request->input('notes'),
                'modified_by' => $user->id,
                'modified_at' => Carbon::now(),
                'modification_reason' => 'تسجيل يدوي من الإدارة',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الحالة بنجاح',
                'attendance' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على عدد الموظفين الكلي
     */
    private function getTotalEmployees($user)
    {
        $query = Employee::where('company_code', $user->company_code)
            ->where('isactive', 1);

        if ($user->usertype_id === 'BM') {
            $query->where('branch_id', $user->branch_id);
        }

        return $query->count();
    }

    /**
     * الحصول على عدد الغائبين اليوم
     */
    private function getAbsentCount($user)
    {
        $totalEmployees = $this->getTotalEmployees($user);

        $presentQuery = Attendance::where('company_code', $user->company_code)
            ->whereDate('attendance_date', Carbon::today());

        if ($user->usertype_id === 'BM') {
            $presentQuery->where('branch_id', $user->branch_id);
        }

        $presentCount = $presentQuery->count();

        return max(0, $totalEmployees - $presentCount);
    }

    /**
     * الحصول على قائمة الموظفين الغائبين اليوم
     */
    private function getAbsentEmployees($user)
    {
        // جلب IDs الموظفين الذين سجلوا حضورهم اليوم
        $presentEmployeeIds = Attendance::where('company_code', $user->company_code)
            ->whereDate('attendance_date', Carbon::today())
            ->when($user->usertype_id === 'BM', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
            ->pluck('employee_id')
            ->toArray();

        // جلب الموظفين الذين لم يسجلوا حضورهم
        return Employee::with('employeeType')
            ->where('company_code', $user->company_code)
            ->where('isactive', 1)
            ->when($user->usertype_id === 'BM', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
            ->whereNotIn('id', $presentEmployeeIds)
            ->get();
    }

    /**
     * تصدير تقرير الحضور
     */
    public function exportReport(Request $request)
    {
        $user = Auth::user();

        // التحقق من صلاحيات المدير
        if (!in_array($user->usertype_id, ['CM', 'BM', 'SA'])) {
            return redirect()->back()->with('error', 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $startDate = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('date_to', Carbon::now()->format('Y-m-d'));

        $query = Attendance::with(['employee', 'shift', 'branch'])
            ->where('company_code', $user->company_code)
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($user->usertype_id === 'BM') {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->input('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        $attendances = $query->orderBy('attendance_date', 'desc')
            ->orderBy('employee_id')
            ->get();

        // إنشاء ملف CSV
        $filename = "attendance_report_{$startDate}_to_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');

            // إضافة BOM لدعم العربية في Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // العناوين
            fputcsv($file, [
                'التاريخ',
                'الموظف',
                'الفرع',
                'الشفت',
                'وقت الحضور',
                'وقت الانصراف',
                'التأخير (دقيقة)',
                'العمل الإضافي (دقيقة)',
                'إجمالي العمل (دقيقة)',
                'الحالة',
                'ملاحظات'
            ]);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->attendance_date->format('Y-m-d'),
                    $attendance->employee->fullname ?? '',
                    $attendance->branch->name ?? '',
                    $attendance->shift->name ?? '',
                    $attendance->check_in_time ? Carbon::parse($attendance->check_in_time)->format('H:i') : '',
                    $attendance->check_out_time ? Carbon::parse($attendance->check_out_time)->format('H:i') : '',
                    $attendance->late_minutes,
                    $attendance->overtime_minutes,
                    $attendance->total_work_minutes,
                    $attendance->status_label,
                    $attendance->notes ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
