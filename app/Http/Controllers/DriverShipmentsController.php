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
     * استخراج employee IDs المرتبطة بحساب السائق مع fallback لبيانات قديمة.
     */
    private function resolveDriverEmployeeIds($user)
    {
        $ids = Employee::where('user_id', $user->id)->pluck('id');
        if ($ids->isNotEmpty()) {
            return $ids;
        }

        // fallback 1: بعض البيانات القديمة كانت تساوي id المستخدم مع id الموظف
        $legacyBySameId = Employee::where('id', $user->id)->pluck('id');
        if ($legacyBySameId->isNotEmpty()) {
            return $legacyBySameId;
        }

        // fallback 2: محاولة مطابقة الاسم عند غياب user_id (للبيانات غير المربوطة)
        $nameCandidates = collect([$user->fullname, $user->username, $user->name])
            ->filter()
            ->unique()
            ->values();

        if ($nameCandidates->isNotEmpty()) {
            return Employee::whereIn('fullname', $nameCandidates->all())->pluck('id');
        }

        return collect();
    }

    /**
     * تطبيق فلترة الشحنات الخاصة بالسائق (mixer/truck/pump)
     * مع دعم سيناريو سائق البَم المربوط على مستوى أمر العمل.
     */
    private function applyDriverShipmentsScope($query, int $userId, $driverEmployeeIds)
    {
        return $query->where(function ($q) use ($userId, $driverEmployeeIds) {
            $q->where('mixer_driver_id', $userId)
                ->orWhere('truck_driver_id', $userId)
                ->orWhere('pump_driver_id', $userId);

            if ($driverEmployeeIds->isNotEmpty()) {
                // توافقية البيانات القديمة: قد يكون pump_driver_id = employees.id
                $q->orWhereIn('pump_driver_id', $driverEmployeeIds);

                // دعم حالة ربط سائق البَم على مستوى أمر العمل فقط
                $q->orWhere(function ($sub) use ($driverEmployeeIds) {
                    $sub->whereHas('job', function ($jobQ) use ($driverEmployeeIds) {
                            $jobQ->whereIn('default_pump_driver_id', $driverEmployeeIds);
                        });
                });
            }
        });
    }

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
        $driverEmployeeIds = $this->resolveDriverEmployeeIds($user);
        
        // جلب بيانات الموظف للعرض (اختياري)
        $employee = Employee::where('user_id', $userId)->first();

        // التصفية حسب الحالة
        $status = $request->input('status', 'active');
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        // تحديد إذا كان سائق بَم
        $isPumpDriver = in_array($user->emp_type_code, [EmployeeType::CODE_PUMP_DRIVER], true);

        // سائق البَم: جلب أوامر العمل بدلاً من الشحنات
        $pumpJobs = collect();
        if ($isPumpDriver && $driverEmployeeIds->isNotEmpty()) {
            $pumpJobsQuery = WorkJob::with(['branch', 'defaultPump.carType', 'shipments'])
                ->whereIn('default_pump_driver_id', $driverEmployeeIds);

            if ($status === 'active') {
                $pumpJobsQuery->whereNotIn('pump_status', ['returned', 'cancelled']);
            } elseif ($status === 'completed') {
                $pumpJobsQuery->where('pump_status', 'returned');
                if ($date) {
                    $pumpJobsQuery->whereDate('scheduled_date', $date);
                }
            } elseif ($status === 'all' && $date) {
                $pumpJobsQuery->whereDate('scheduled_date', $date);
            }

            $pumpJobs = $pumpJobsQuery->orderBy('created_at', 'desc')->get();
        }

        // جلب الشحنات المخصصة للسائق باستخدام user_id (للخباطات/الشاحنات)
        $query = WorkShipment::with(['job', 'job.branch', 'job.defaultPump.carType', 'mixer.carType', 'truck.carType', 'pump.carType']);
        
        // إذا كان سائق بَم فقط، لا نجلب الشحنات (نستخدم pumpJobs بدلاً منها)
        if ($isPumpDriver) {
            // فقط شحنات الخلاطة/الشاحنة إذا كان أيضاً سائق خلاطة
            $query->where(function ($q) use ($userId) {
                $q->where('mixer_driver_id', $userId)
                    ->orWhere('truck_driver_id', $userId);
            });
        } else {
            $this->applyDriverShipmentsScope($query, $userId, $driverEmployeeIds);
        }

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
        $todayStats = $this->getTodayStats($userId, $driverEmployeeIds);

        // تحديد نوع السائق
        $driverRole = $this->getDriverRoleByUser($user);

        return view('driver.shipments.index', compact(
            'employee',
            'shipments',
            'pumpJobs',
            'isPumpDriver',
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
        $driverEmployeeIds = $this->resolveDriverEmployeeIds($user);
        $employee = Employee::where('user_id', $userId)->first();

        $shipmentQuery = WorkShipment::with(['job', 'job.branch', 'job.concreteType', 'mixer', 'truck', 'pump', 'mixerDriver', 'truckDriver', 'pumpDriver']);
        $this->applyDriverShipmentsScope($shipmentQuery, $userId, $driverEmployeeIds);
        $shipment = $shipmentQuery->findOrFail($id);

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
        $driverEmployeeIds = $this->resolveDriverEmployeeIds($user);

        $shipmentQuery = WorkShipment::query();
        $this->applyDriverShipmentsScope($shipmentQuery, $userId, $driverEmployeeIds);
        $shipment = $shipmentQuery->findOrFail($id);

        $request->validate([
            'status' => 'required|in:departed,arrived,working,completed,returned',
            'notes' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $newStatus = $request->input('status');
        $now = Carbon::now();
        $shipment->loadMissing('job');
        $isPumpDriverForShipment =
            ((int) ($shipment->pump_driver_id ?? 0) === (int) $userId) ||
            ($driverEmployeeIds->isNotEmpty() && in_array((int) ($shipment->job->default_pump_driver_id ?? 0), $driverEmployeeIds->map(fn($v) => (int) $v)->all(), true));
        $isMixerOrTruckDriverForShipment =
            ((int) ($shipment->mixer_driver_id ?? 0) === (int) $userId) ||
            ((int) ($shipment->truck_driver_id ?? 0) === (int) $userId);
        $jobHasPumpDriver = (int) ($shipment->job->default_pump_driver_id ?? 0) > 0;

        // سائق البَم: يسمح فقط (انطلاق -> وصول -> بدء العمل -> رجوع)
        // الانطلاق وبدء العمل مرة واحدة فقط لكل أمر عمل لأن البَم ثابت في موقع العمل
        if ($isPumpDriverForShipment && !$isMixerOrTruckDriverForShipment) {
            $allowedPumpStatuses = ['departed', 'arrived', 'working', 'returned'];
            if (!in_array($newStatus, $allowedPumpStatuses, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'مراحل سائق البَم هي: انطلاق، وصول، بدء العمل، رجوع فقط.'
                ], 422);
            }

            // انطلاق البَم مرة واحدة فقط لكل أمر عمل
            if ($newStatus === 'departed') {
                $pumpAlreadyDepartedInJob = WorkShipment::where('job_id', $shipment->job_id)
                    ->whereNotNull('departure_time')
                    ->exists();
                if ($pumpAlreadyDepartedInJob) {
                    return response()->json([
                        'success' => false,
                        'message' => 'تم انطلاق البَم سابقاً في هذا المشروع. يسمح بانطلاق واحد فقط.'
                    ], 422);
                }
            }

            // بدء البَم مرة واحدة فقط لكل أمر عمل
            if ($newStatus === 'working') {
                $pumpAlreadyStartedInJob = WorkShipment::where('job_id', $shipment->job_id)
                    ->whereNotNull('work_start_time')
                    ->exists();
                if ($pumpAlreadyStartedInJob) {
                    return response()->json([
                        'success' => false,
                        'message' => 'تم بدء عمل البَم سابقاً في هذا المشروع. يسمح ببدء واحد فقط.'
                    ], 422);
                }
            }
        }

        // الخباطة/الشاحنة: لا يبدأ التفريغ قبل بدء البَم (إذا كان المشروع يحتوي سائق بَم)
        if (!$isPumpDriverForShipment && $isMixerOrTruckDriverForShipment && $newStatus === 'working' && $jobHasPumpDriver) {
            // التحقق من pump_work_start_time في أمر العمل (الطريقة الجديدة)
            $pumpStartedInJob = !is_null($shipment->job->pump_work_start_time);
            if (!$pumpStartedInJob) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب أن يبدأ سائق البَم العمل أولاً قبل بدء التفريغ بالخباطة.'
                ], 422);
            }
        }

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
                if ($isPumpDriverForShipment && !$shipment->work_end_time) {
                    $updateData['work_end_time'] = $now;
                }
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
     * تحديث حالة البَم على مستوى أمر العمل (للبَم فقط)
     * البَم يقوم بـ: انطلاق، وصول، بدء عمل، رجوع - مرة واحدة لكل أمر عمل
     */
    public function updatePumpStatus(Request $request, $jobId)
    {
        $user = Auth::user();
        if (!$this->isDriverAccount()) {
            return response()->json([
                'success' => false,
                'message' => 'هذه الصفحة مخصصة لحسابات السائقين فقط.'
            ], 403);
        }

        $driverEmployeeIds = $this->resolveDriverEmployeeIds($user);
        if ($driverEmployeeIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على بيانات الموظف.'
            ], 404);
        }

        // التحقق أن هذا سائق البَم لهذا الأمر
        $job = WorkJob::with('defaultPump')
            ->whereIn('default_pump_driver_id', $driverEmployeeIds)
            ->findOrFail($jobId);

        $request->validate([
            'status' => 'required|in:departed,arrived,working,returned',
            'notes' => 'nullable|string|max:500',
        ]);

        $newStatus = $request->input('status');
        $now = Carbon::now();

        // التحقق من صحة التسلسل
        $currentStatus = $job->pump_status ?? 'pending';
        $validTransitions = [
            'pending' => ['departed'],
            'departed' => ['arrived'],
            'arrived' => ['working'],
            'working' => ['returned'],
            'returned' => [],
        ];

        if (!in_array($newStatus, $validTransitions[$currentStatus] ?? [], true)) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن الانتقال من الحالة الحالية إلى هذه الحالة.'
            ], 422);
        }

        // تحديث الحالة والوقت المناسب على مستوى أمر العمل
        $updateData = ['pump_status' => $newStatus];

        switch ($newStatus) {
            case 'departed':
                $updateData['pump_departure_time'] = $now;
                break;
            case 'arrived':
                $updateData['pump_arrival_time'] = $now;
                break;
            case 'working':
                $updateData['pump_work_start_time'] = $now;
                break;
            case 'returned':
                $updateData['pump_return_time'] = $now;
                break;
        }

        $job->update($updateData);

        // رسالة مخصصة حسب الحالة
        $statusMessages = [
            'departed' => 'تم تسجيل انطلاق البَم بنجاح 🚀',
            'arrived' => 'تم تسجيل وصول البَم للموقع بنجاح 📍',
            'working' => 'تم بدء عمل البَم بنجاح 🔨',
            'returned' => 'تم تسجيل رجوع البَم للمقر بنجاح 🏠',
        ];

        return response()->json([
            'success' => true,
            'message' => $statusMessages[$newStatus] ?? 'تم تحديث حالة البَم بنجاح',
            'job' => $job->fresh()
        ]);
    }

    /**
     * الحصول على إحصائيات السائق باستخدام user_id
     */
    private function getTodayStats($userId, $driverEmployeeIds = null)
    {
        $driverEmployeeIds = $driverEmployeeIds ?? collect();
        // جلب جميع الشحنات المخصصة للسائق
        $statsQuery = WorkShipment::query();
        $this->applyDriverShipmentsScope($statsQuery, $userId, $driverEmployeeIds);
        $allShipments = $statsQuery->whereNotIn('status', [WorkShipment::STATUS_CANCELLED])->get();

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
     * ملاحظة: لا يتم إكمال أمر العمل تلقائياً - يجب الضغط على "إكمال العمل" يدوياً
     */
    private function updateJobStatus($job)
    {
        if (!$job) return;

        // لا نغير حالة أمر عمل مكتمل أو ملغي
        if (in_array($job->status, ['completed', 'cancelled'], true)) {
            return;
        }

        $allShipments = $job->shipments;
        $completedShipments = $allShipments->whereIn('status', [WorkShipment::STATUS_COMPLETED, WorkShipment::STATUS_RETURNED]);

        // حساب الكمية المنفذة
        $executedQuantity = $completedShipments->sum('actual_quantity');
        $job->update(['executed_quantity' => $executedQuantity]);

        // تحديث حالة أمر العمل - يبقى "قيد التنفيذ" حتى يتم إكماله يدوياً
        if ($allShipments->count() > 0) {
            // إذا كانت هناك شحنات تعمل أو مكتملة، نجعل الحالة "قيد التنفيذ"
            $hasActiveOrCompleted = $allShipments->whereIn('status', [
                WorkShipment::STATUS_DEPARTED,
                WorkShipment::STATUS_ARRIVED,
                WorkShipment::STATUS_WORKING,
                WorkShipment::STATUS_COMPLETED,
                WorkShipment::STATUS_RETURNED,
            ])->count() > 0;

            if ($hasActiveOrCompleted && $job->status !== 'in_progress') {
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

        if (in_array($user->emp_type_code, [EmployeeType::CODE_DRIVER, EmployeeType::CODE_PUMP_DRIVER], true)) {
            return true;
        }

        return (bool) $user->isDriver();
    }
}
