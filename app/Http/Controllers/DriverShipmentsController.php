<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeType;
use App\Models\WorkShipment;
use App\Models\WorkJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * DriverShipmentsController - متحكم شحنات السائق
 * 
 * يعرض الشحنات المخصصة للسائق المسجل دخوله
 */
class DriverShipmentsController extends Controller
{
    /**
     * عرض الشحنات المخصصة للسائق
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$this->isDriverAccount()) {
            return redirect('/')->with('error', 'هذه الصفحة مخصصة لحسابات السائقين فقط.');
        }
        
        $userId = $user->id;
        
        // جلب بيانات الموظف للعرض (اختياري)
        $employee = Employee::where('user_id', $userId)->first();

        // التصفية حسب الحالة
        $status = $request->input('status', 'active');
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        // جلب الشحنات المخصصة للسائق باستخدام user_id
        $query = WorkShipment::with(['job', 'job.branch', 'mixer.carType', 'truck.carType', 'pump.carType'])
            ->where(function ($q) use ($userId) {
                $q->where('mixer_driver_id', $userId)
                    ->orWhere('truck_driver_id', $userId)
                    ->orWhere('pump_driver_id', $userId);
            });

        // تصفية حسب الحالة
        if ($status === 'active') {
            $query->whereNotIn('status', [WorkShipment::STATUS_RETURNED, WorkShipment::STATUS_CANCELLED]);
        } elseif ($status === 'completed') {
            $query->where('status', WorkShipment::STATUS_RETURNED);
            if ($date) {
                $query->whereHas('job', function ($q) use ($date) {
                    $q->whereDate('scheduled_date', $date);
                });
            }
        } elseif ($status === 'all') {
            if ($date) {
                $query->whereHas('job', function ($q) use ($date) {
                    $q->whereDate('scheduled_date', $date);
                });
            }
        }

        $shipments = $query->orderBy('created_at', 'desc')->get();

        // إحصائيات السائق
        $todayStats = $this->getTodayStats($userId);

        // تحديد نوع السائق
        $driverRole = $this->getDriverRoleByUser($user);

        return view('driver.shipments.index', compact(
            'employee',
            'shipments',
            'todayStats',
            'driverRole',
            'status',
            'date'
        ));
    }

    /**
     * عرض تفاصيل شحنة معينة
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$this->isDriverAccount()) {
            return redirect('/')->with('error', 'هذه الصفحة مخصصة لحسابات السائقين فقط.');
        }
        
        $userId = $user->id;
        $employee = Employee::where('user_id', $userId)->first();

        $shipment = WorkShipment::with(['job', 'job.branch', 'job.concreteType', 'mixer', 'truck', 'pump', 'mixerDriver', 'truckDriver', 'pumpDriver'])
            ->where(function ($q) use ($userId) {
                $q->where('mixer_driver_id', $userId)
                    ->orWhere('truck_driver_id', $userId)
                    ->orWhere('pump_driver_id', $userId);
            })
            ->findOrFail($id);

        return view('driver.shipments.show', compact('employee', 'shipment', 'user'));
    }

    /**
     * تحديث حالة الشحنة من قبل السائق
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!$this->isDriverAccount()) {
            return response()->json([
                'success' => false,
                'message' => 'هذه الصفحة مخصصة لحسابات السائقين فقط.'
            ], 403);
        }
        
        $userId = $user->id;

        $shipment = WorkShipment::where(function ($q) use ($userId) {
            $q->where('mixer_driver_id', $userId)
                ->orWhere('truck_driver_id', $userId)
                ->orWhere('pump_driver_id', $userId);
        })->findOrFail($id);

        $request->validate([
            'status' => 'required|in:departed,arrived,working,completed,returned',
            'notes' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $newStatus = $request->input('status');
        $now = Carbon::now();

        // تحديث الحالة والوقت المناسب
        $updateData = [
            'status' => $newStatus,
            'driver_notes' => $request->input('notes'),
        ];

        switch ($newStatus) {
            case 'departed':
                $updateData['departure_time'] = $now;
                break;
            case 'arrived':
                $updateData['arrival_time'] = $now;
                break;
            case 'working':
                $updateData['work_start_time'] = $now;
                break;
            case 'completed':
                $updateData['work_end_time'] = $now;
                // تعيين الكمية الفعلية مساوية للمخططة إذا لم يتم تحديدها
                if (!$shipment->actual_quantity) {
                    $updateData['actual_quantity'] = $shipment->planned_quantity;
                }
                break;
            case 'returned':
                $updateData['return_time'] = $now;
                break;
        }

        $shipment->update($updateData);

        // تحديث حالة أمر العمل إذا لزم الأمر
        $this->updateJobStatus($shipment->job);

        // رسالة مخصصة حسب الحالة
        $statusMessages = [
            'departed' => 'تم تسجيل الانطلاق بنجاح 🚀',
            'arrived' => 'تم تسجيل الوصول للموقع بنجاح 📍',
            'working' => 'تم بدء التفريغ بنجاح 🔨',
            'completed' => 'تم اكتمال التفريغ بنجاح ✅',
            'returned' => 'تم الوصول للمقر بنجاح وتحرير الآلية 🏠',
        ];

        return response()->json([
            'success' => true,
            'message' => $statusMessages[$newStatus] ?? 'تم تحديث حالة الشحنة بنجاح',
            'shipment' => $shipment->fresh()
        ]);
    }

    /**
     * الحصول على إحصائيات السائق باستخدام user_id
     */
    private function getTodayStats($userId)
    {
        // جلب جميع الشحنات المخصصة للسائق
        $allShipments = WorkShipment::where(function ($q) use ($userId) {
            $q->where('mixer_driver_id', $userId)
                ->orWhere('truck_driver_id', $userId)
                ->orWhere('pump_driver_id', $userId);
        })->whereNotIn('status', [WorkShipment::STATUS_CANCELLED])->get();

        return [
            'total' => $allShipments->count(),
            'completed' => $allShipments->where('status', WorkShipment::STATUS_RETURNED)->count(),
            'active' => $allShipments->whereNotIn('status', [WorkShipment::STATUS_RETURNED, WorkShipment::STATUS_CANCELLED])->count(),
            'total_quantity' => $allShipments->sum('planned_quantity'),
            'delivered_quantity' => $allShipments->where('status', WorkShipment::STATUS_RETURNED)->sum('actual_quantity'),
        ];
    }

    /**
     * تحديد نوع/دور السائق بناءً على بيانات المستخدم
     */
    private function getDriverRoleByUser($user)
    {
        // يمكن تحديد الدور بناءً على emp_type_code أو بيانات أخرى
        $empTypeCode = $user->emp_type_code ?? '';
        
        // جلب اسم نوع الموظف
        $employeeType = EmployeeType::where('code', $empTypeCode)->first();
        $typeName = $employeeType->name ?? '';

        if (str_contains($typeName, 'خلاط') || str_contains($typeName, 'ميكسر')) {
            return 'mixer';
        } elseif (str_contains($typeName, 'شاحن') || str_contains($typeName, 'قلاب')) {
            return 'truck';
        } elseif (str_contains($typeName, 'مضخ') || str_contains($typeName, 'بمب')) {
            return 'pump';
        }

        return 'driver';
    }

    /**
     * تحديث حالة أمر العمل بناءً على الشحنات
     */
    private function updateJobStatus($job)
    {
        if (!$job) return;

        $allShipments = $job->shipments;
        $completedShipments = $allShipments->whereIn('status', [WorkShipment::STATUS_COMPLETED, WorkShipment::STATUS_RETURNED]);

        // حساب الكمية المنفذة
        $executedQuantity = $completedShipments->sum('actual_quantity');
        $job->update(['executed_quantity' => $executedQuantity]);

        // تحديث حالة أمر العمل
        if ($allShipments->count() > 0) {
            if ($completedShipments->count() === $allShipments->count()) {
                // جميع الشحنات مكتملة
                if ($executedQuantity >= $job->total_quantity) {
                    $job->update(['status' => 'completed']);
                }
            } elseif ($allShipments->where('status', WorkShipment::STATUS_WORKING)->count() > 0) {
                $job->update(['status' => 'in_progress']);
            }
        }
    }

    /**
     * التحقق أن الحساب مُسجّل كسائق.
     */
    private function isDriverAccount(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        if ($user->emp_type_code === EmployeeType::CODE_DRIVER) {
            return true;
        }

        return (bool) $user->isDriver();
    }
}
