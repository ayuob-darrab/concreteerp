<?php

namespace App\Http\Controllers;

use App\Models\CarMaintenance;
use App\Models\Cars;
use App\Models\CarDriver;
use App\Models\CarsType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CarMaintenanceController extends Controller
{
    /**
     * عرض قائمة سيارات الفرع مع إحصائيات الصيانة
     */
    public function index()
    {
        $companyCode = Auth::user()->company_code;
        $branchId = Auth::user()->branch_id;

        // جلب سيارات الفرع مع إحصائيات الصيانة
        $cars = Cars::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->with(['carType', 'driver'])
            ->withCount(['maintenances as maintenance_count'])
            ->withSum(['maintenances as total_maintenance_cost' => function ($query) {
                $query->where('status', 'completed');
            }], 'total_cost')
            ->orderBy('created_at', 'desc')
            ->get();

        // إحصائيات عامة
        $stats = [
            'total_cars' => $cars->count(),
            'active_cars' => $cars->where('is_active', true)->count(),
            'in_maintenance_cars' => $cars->where('operational_status', 'in_maintenance')->count(),
            'total_maintenances' => CarMaintenance::where('company_code', $companyCode)
                ->where('branch_id', $branchId)->count(),
            'total_cost' => CarMaintenance::where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->where('status', 'completed')
                ->sum('total_cost'),
            'scheduled_maintenances' => CarMaintenance::where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->where('status', 'scheduled')
                ->count(),
            'in_progress_maintenances' => CarMaintenance::where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->where('status', 'in_progress')
                ->count(),
        ];

        return view('car-maintenance.index', compact('cars', 'stats'));
    }

    /**
     * عرض تفاصيل السيارة الكاملة مع التاريخ
     */
    public function carDetails($carId)
    {
        $companyCode = Auth::user()->company_code;
        $branchId = Auth::user()->branch_id;

        $car = Cars::where('id', $carId)
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->with(['carType', 'driver', 'backupDriver', 'BranchName'])
            ->firstOrFail();

        // سجل السائقين (التعيينات والإنهاءات)
        $driverHistory = CarDriver::where('car_id', $carId)
            ->with(['driver', 'shift'])
            ->orderBy('assigned_date', 'desc')
            ->get();

        // إحصائيات السائقين
        $driverStats = [
            'total_assigned' => $driverHistory->count(),
            'currently_active' => $driverHistory->where('is_active', true)->count(),
            'ended_assignments' => $driverHistory->where('is_active', false)->count(),
        ];

        // سجل الصيانات
        $maintenances = CarMaintenance::where('car_id', $carId)
            ->orderBy('maintenance_date', 'desc')
            ->get();

        // إحصائيات الصيانة
        $maintenanceStats = [
            'total_count' => $maintenances->count(),
            'completed_count' => $maintenances->where('status', 'completed')->count(),
            'total_cost' => $maintenances->where('status', 'completed')->sum('total_cost'),
            'last_maintenance' => $maintenances->first()?->maintenance_date,
            'next_scheduled' => $maintenances->where('status', 'scheduled')->sortBy('maintenance_date')->first()?->maintenance_date,
        ];

        // حساب عمر السيارة بالأيام
        $carAge = $car->add_date ? now()->diffInDays($car->add_date) : 0;

        return view('car-maintenance.car-details', compact(
            'car',
            'driverHistory',
            'driverStats',
            'maintenances',
            'maintenanceStats',
            'carAge'
        ));
    }

    /**
     * صفحة إضافة صيانة جديدة
     */
    public function create($carId)
    {
        $companyCode = Auth::user()->company_code;
        $branchId = Auth::user()->branch_id;

        $car = Cars::where('id', $carId)
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->with(['carType'])
            ->firstOrFail();

        $maintenanceTypes = CarMaintenance::getMaintenanceTypes();
        $statuses = CarMaintenance::getStatuses();

        // آخر صيانة للسيارة (لعرض قراءة العداد السابقة)
        $lastMaintenance = CarMaintenance::where('car_id', $carId)
            ->orderBy('maintenance_date', 'desc')
            ->first();

        // عرض صفحة بدء الصيانة بدلاً من النموذج الكامل
        return view('car-maintenance.start-maintenance', compact('car', 'maintenanceTypes', 'lastMaintenance'));
    }

    /**
     * حفظ صيانة جديدة
     */
    public function store(Request $request, $carId)
    {
        $request->validate([
            'maintenance_type' => 'required|in:periodic,emergency,repair,inspection,oil_change,tires,other',
            'title' => 'required|string|max:255',
            'maintenance_date' => 'required|date',
            'total_cost' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ], [
            'maintenance_type.required' => 'نوع الصيانة مطلوب',
            'title.required' => 'عنوان الصيانة مطلوب',
            'maintenance_date.required' => 'تاريخ الصيانة مطلوب',
            'total_cost.required' => 'التكلفة مطلوبة',
        ]);

        $companyCode = Auth::user()->company_code;
        $branchId = Auth::user()->branch_id;

        $car = Cars::where('id', $carId)
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        // رفع المرفق إن وجد (بشكل آمن)
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $uploadPath = public_path('uploads/' . $companyCode . '/car_maintenances');

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

            $attachmentPath = 'uploads/' . $companyCode . '/car_maintenances/' . $uploadResult['filename'];
        }

        CarMaintenance::create([
            'company_code' => $companyCode,
            'branch_id' => $branchId,
            'car_id' => $carId,
            'maintenance_type' => $request->maintenance_type,
            'title' => $request->title,
            'description' => $request->description,
            'total_cost' => $request->total_cost,
            'parts_cost' => $request->parts_cost ?? 0,
            'labor_cost' => $request->labor_cost ?? 0,
            'maintenance_date' => $request->maintenance_date,
            'next_maintenance_date' => $request->next_maintenance_date,
            'odometer_reading' => $request->odometer_reading,
            'performed_by' => $request->performed_by,
            'workshop_name' => $request->workshop_name,
            'invoice_number' => $request->invoice_number,
            'status' => $request->status,
            'notes' => $request->notes,
            'attachment' => $attachmentPath,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('car-maintenance.car-details', $carId)
            ->with('success', '✅ تم إضافة الصيانة بنجاح');
    }

    /**
     * صفحة تعديل صيانة
     */
    public function edit($id)
    {
        $companyCode = Auth::user()->company_code;

        $maintenance = CarMaintenance::where('id', $id)
            ->where('company_code', $companyCode)
            ->with(['car.carType'])
            ->firstOrFail();

        $car = $maintenance->car;
        $maintenanceTypes = CarMaintenance::getMaintenanceTypes();
        $statuses = CarMaintenance::getStatuses();

        // إذا كانت الصيانة قيد التنفيذ، عرض صفحة إكمال الصيانة
        if ($maintenance->status === 'in_progress') {
            return view('car-maintenance.complete-maintenance', compact('maintenance', 'car'));
        }

        return view('car-maintenance.form', compact('maintenance', 'car', 'maintenanceTypes', 'statuses'));
    }

    /**
     * تحديث صيانة
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'maintenance_type' => 'required|in:periodic,emergency,repair,inspection,oil_change,tires,other',
            'title' => 'required|string|max:255',
            'maintenance_date' => 'required|date',
            'total_cost' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $companyCode = Auth::user()->company_code;

        $maintenance = CarMaintenance::where('id', $id)
            ->where('company_code', $companyCode)
            ->firstOrFail();

        // رفع المرفق إن وجد (بشكل آمن)
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $uploadPath = public_path('uploads/' . $companyCode . '/car_maintenances');

            $uploadResult = \App\Helpers\FileUploadHelper::uploadSecurely(
                $file,
                $uploadPath,
                array_merge(
                    \App\Helpers\FileUploadHelper::IMAGE_EXTENSIONS,
                    \App\Helpers\FileUploadHelper::DOCUMENT_EXTENSIONS
                )
            );

            if ($uploadResult['success']) {
                $maintenance->attachment = 'uploads/' . $companyCode . '/car_maintenances/' . $uploadResult['filename'];
            }
        }

        $maintenance->update([
            'maintenance_type' => $request->maintenance_type,
            'title' => $request->title,
            'description' => $request->description,
            'total_cost' => $request->total_cost,
            'parts_cost' => $request->parts_cost ?? 0,
            'labor_cost' => $request->labor_cost ?? 0,
            'maintenance_date' => $request->maintenance_date,
            'next_maintenance_date' => $request->next_maintenance_date,
            'odometer_reading' => $request->odometer_reading,
            'performed_by' => $request->performed_by,
            'workshop_name' => $request->workshop_name,
            'invoice_number' => $request->invoice_number,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('car-maintenance.car-details', $maintenance->car_id)
            ->with('success', '✅ تم تحديث الصيانة بنجاح');
    }

    /**
     * حذف صيانة
     */
    public function destroy($id)
    {
        $companyCode = Auth::user()->company_code;

        $maintenance = CarMaintenance::where('id', $id)
            ->where('company_code', $companyCode)
            ->firstOrFail();

        $carId = $maintenance->car_id;
        $maintenance->delete();

        return redirect()->route('car-maintenance.car-details', $carId)
            ->with('success', '✅ تم حذف الصيانة بنجاح');
    }

    /**
     * تقرير صيانات الفرع
     */
    public function report(Request $request)
    {
        $companyCode = Auth::user()->company_code;
        $branchId = Auth::user()->branch_id;

        $query = CarMaintenance::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->with(['car.carType']);

        // فلترة بالتاريخ
        if ($request->from_date) {
            $query->where('maintenance_date', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->where('maintenance_date', '<=', $request->to_date);
        }

        // فلترة بنوع الصيانة
        if ($request->maintenance_type) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        // فلترة بالسيارة
        if ($request->car_id) {
            $query->where('car_id', $request->car_id);
        }

        $maintenances = $query->orderBy('maintenance_date', 'desc')->get();

        // إحصائيات
        $stats = [
            'total_count' => $maintenances->count(),
            'total_cost' => $maintenances->where('status', 'completed')->sum('total_cost'),
            'by_type' => $maintenances->groupBy('maintenance_type')->map->count(),
        ];

        $cars = Cars::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->get();

        $maintenanceTypes = CarMaintenance::getMaintenanceTypes();

        return view('car-maintenance.report', compact('maintenances', 'stats', 'cars', 'maintenanceTypes'));
    }

    /**
     * بدء الصيانة - تغيير حالة السيارة إلى "في الصيانة"
     */
    public function startMaintenance(Request $request, $carId)
    {
        $request->validate([
            'maintenance_type' => 'required|in:periodic,emergency,repair,inspection,oil_change,tires,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'maintenance_type.required' => 'نوع الصيانة مطلوب',
            'title.required' => 'عنوان الصيانة مطلوب',
        ]);

        $companyCode = Auth::user()->company_code;
        $branchId = Auth::user()->branch_id;

        $car = Cars::where('id', $carId)
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        // التحقق من أن السيارة متاحة
        if ($car->operational_status === 'in_maintenance') {
            return back()->with('error', '❌ السيارة في الصيانة بالفعل');
        }

        DB::beginTransaction();
        try {
            // إنشاء سجل صيانة جديد بحالة "قيد التنفيذ"
            $maintenance = CarMaintenance::create([
                'company_code' => $companyCode,
                'branch_id' => $branchId,
                'car_id' => $carId,
                'maintenance_type' => $request->maintenance_type,
                'title' => $request->title,
                'description' => $request->description,
                'total_cost' => 0,
                'maintenance_date' => now(),
                'status' => 'in_progress',
                'created_by' => Auth::id(),
            ]);

            // تحديث حالة السيارة إلى "في الصيانة"
            $car->update([
                'operational_status' => 'in_maintenance',
                'status_reason' => 'صيانة: ' . $request->title,
                'status_changed_at' => now(),
                'status_changed_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('car-maintenance.edit', $maintenance->id)
                ->with('success', '✅ تم بدء الصيانة - السيارة الآن في وضع الصيانة ولا يمكن حجزها');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * إكمال الصيانة - تغيير حالة السيارة إلى "متاحة"
     */
    public function completeMaintenance(Request $request, $id)
    {
        $request->validate([
            'total_cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'performed_by' => 'nullable|string',
            'workshop_name' => 'nullable|string',
        ], [
            'total_cost.required' => 'التكلفة الإجمالية مطلوبة',
        ]);

        $companyCode = Auth::user()->company_code;

        $maintenance = CarMaintenance::where('id', $id)
            ->where('company_code', $companyCode)
            ->firstOrFail();

        if ($maintenance->status === 'completed') {
            return back()->with('error', '❌ هذه الصيانة مكتملة بالفعل');
        }

        $car = Cars::find($maintenance->car_id);

        DB::beginTransaction();
        try {
            // تحديث سجل الصيانة
            $maintenance->update([
                'status' => 'completed',
                'total_cost' => $request->total_cost,
                'parts_cost' => $request->parts_cost ?? 0,
                'labor_cost' => $request->labor_cost ?? 0,
                'description' => $request->description,
                'notes' => $request->notes,
                'performed_by' => $request->performed_by,
                'workshop_name' => $request->workshop_name,
                'invoice_number' => $request->invoice_number,
                'odometer_reading' => $request->odometer_reading,
                'next_maintenance_date' => $request->next_maintenance_date,
            ]);

            // تحديث حالة السيارة إلى "متاحة"
            if ($car) {
                $car->update([
                    'operational_status' => 'available',
                    'status_reason' => null,
                    'status_changed_at' => now(),
                    'status_changed_by' => Auth::id(),
                    'last_maintenance_date' => now(),
                    'next_maintenance_date' => $request->next_maintenance_date,
                    'odometer_reading' => $request->odometer_reading ?? $car->odometer_reading,
                ]);
            }

            DB::commit();

            return redirect()->route('car-maintenance.car-details', $maintenance->car_id)
                ->with('success', '✅ تم إكمال الصيانة بنجاح - السيارة متاحة الآن للحجز');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ حدث خطأ: ' . $e->getMessage());
        }
    }
}
