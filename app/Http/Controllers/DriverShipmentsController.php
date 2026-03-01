<?php

namespace App\Http\Controllers;

use App\Models\Employee;
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
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'لم يتم العثور على بيانات الموظف');
        }

        // التصفية حسب الحالة
        $status = $request->input('status', 'active');
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        // جلب الشحنات المخصصة للسائق (كسائق خلاطة أو سائق شاحنة أو سائق مضخة)
        $query = WorkShipment::with(['job', 'job.branch', 'mixer.carType', 'truck.carType', 'pump.carType'])
            ->where(function ($q) use ($employee) {
                $q->where('mixer_driver_id', $employee->id)
                    ->orWhere('truck_driver_id', $employee->id)
                    ->orWhere('pump_driver_id', $employee->id);
            });

        // تصفية حسب الحالة
        if ($status === 'active') {
            // الشحنات النشطة - تشمل completed لأن السائق يحتاج للضغط على "الوصول للمقر"
            // فقط returned و cancelled تعتبر منتهية تماماً
            $query->whereNotIn('status', [WorkShipment::STATUS_RETURNED, WorkShipment::STATUS_CANCELLED]);
        } elseif ($status === 'completed') {
            // الشحنات المكتملة نهائياً (وصل للمقر)
            $query->where('status', WorkShipment::STATUS_RETURNED);
            // تصفية حسب التاريخ إذا تم تحديده
            if ($date) {
                $query->whereHas('job', function ($q) use ($date) {
                    $q->whereDate('scheduled_date', $date);
                });
            }
        } elseif ($status === 'all') {
            // جميع الشحنات - مع فلتر تاريخ اختياري
            if ($date) {
                $query->whereHas('job', function ($q) use ($date) {
                    $q->whereDate('scheduled_date', $date);
                });
            }
        }

        $shipments = $query->orderBy('created_at', 'desc')->get();

        // إحصائيات اليوم
        $todayStats = $this->getTodayStats($employee->id);

        // تحديد نوع السائق (خلاطة/شاحنة/مضخة)
        $driverRole = $this->getDriverRole($employee);

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
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'لم يتم العثور على بيانات الموظف');
        }

        $shipment = WorkShipment::with(['job', 'job.branch', 'job.concreteType', 'mixer', 'truck', 'pump', 'mixerDriver', 'truckDriver', 'pumpDriver'])
            ->where(function ($q) use ($employee) {
                $q->where('mixer_driver_id', $employee->id)
                    ->orWhere('truck_driver_id', $employee->id)
                    ->orWhere('pump_driver_id', $employee->id);
            })
            ->findOrFail($id);

        return view('driver.shipments.show', compact('employee', 'shipment'));
    }

    /**
     * تحديث حالة الشحنة من قبل السائق
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على بيانات الموظف'
            ], 404);
        }

        $shipment = WorkShipment::where(function ($q) use ($employee) {
            $q->where('mixer_driver_id', $employee->id)
                ->orWhere('truck_driver_id', $employee->id)
                ->orWhere('pump_driver_id', $employee->id);
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
     * الحصول على إحصائيات اليوم للسائق
     */
    private function getTodayStats($employeeId)
    {
        $today = Carbon::today();

        // جلب جميع الشحنات المخصصة للسائق (بدون فلتر تاريخ للإحصائيات العامة)
        $allShipments = WorkShipment::where(function ($q) use ($employeeId) {
            $q->where('mixer_driver_id', $employeeId)
                ->orWhere('truck_driver_id', $employeeId)
                ->orWhere('pump_driver_id', $employeeId);
        })->whereNotIn('status', [WorkShipment::STATUS_CANCELLED])->get();

        return [
            'total' => $allShipments->count(),
            // المكتملة = فقط التي وصلت للمقر (returned)
            'completed' => $allShipments->where('status', WorkShipment::STATUS_RETURNED)->count(),
            // النشطة = كل شيء ما عدا returned و cancelled
            'active' => $allShipments->whereNotIn('status', [WorkShipment::STATUS_RETURNED, WorkShipment::STATUS_CANCELLED])->count(),
            'total_quantity' => $allShipments->sum('planned_quantity'),
            'delivered_quantity' => $allShipments->where('status', WorkShipment::STATUS_RETURNED)->sum('actual_quantity'),
        ];
    }

    /**
     * تحديد نوع/دور السائق
     */
    private function getDriverRole($employee)
    {
        // يمكن تحديد الدور بناءً على نوع الموظف أو بيانات أخرى
        $typeName = $employee->employeeType->name ?? '';

        if (str_contains($typeName, 'خلاط') || str_contains($typeName, 'ميكسر')) {
            return 'mixer';
        } elseif (str_contains($typeName, 'شاحن') || str_contains($typeName, 'قلاب')) {
            return 'truck';
        } elseif (str_contains($typeName, 'مضخ') || str_contains($typeName, 'بمب')) {
            return 'pump';
        }

        return 'driver'; // عام
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
}
