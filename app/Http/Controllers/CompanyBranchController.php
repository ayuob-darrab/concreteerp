<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\City;
use App\Models\Company;
use App\Models\ConcreteMix;
use App\Models\ConcreteMixCategoryPrice;
use App\Models\ContractorAccount;
use App\Models\DailyCashSummary;
use App\Models\EmployeeType;
use App\Models\Inventory;
use App\Models\PaymentReceipt;
use App\Models\PricingCategory;
use App\Models\ShiftTime;
use App\Models\WorkOrder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanyBranchController extends Controller
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


        // if ($request->active == 'Newbranch') {
        //     // تحقق من صحة البيانات
        //     // dd("Newbranch");


        //     // فحص التكرار قبل الإضافة
        //     $exists = Branch::where('branch_name', $request->branch_name)
        //         ->where('email', $request->email)
        //         ->where('company_code', Auth::user()->company_code)
        //         ->where('branch_admin', $request->branch_admin)
        //         ->exists();

        //     if ($exists) {
        //         return back()->with('error', '⚠ هذا الفرع موجود مسبقاً ولا يمكن إضافته مرة أخرى!')
        //             ->withInput();
        //     }

        //     // إضافة سجل جديد
        //     $NewBranch = new Branch();

        //     $NewBranch->city_id       = $request->city_id;
        //     $NewBranch->branch_name   = $request->branch_name;
        //     $NewBranch->company_code  = Auth::user()->company_code;
        //     $NewBranch->branch_admin  = $request->branch_admin;
        //     $NewBranch->phone         = $request->phone;
        //     $NewBranch->email         = $request->email;
        //     $NewBranch->address       = $request->address;
        //     $NewBranch->created_date  = now();
        //     $NewBranch->is_active     = true;

        //     $NewBranch->save();


        //     $existscement = Inventory::where([
        //         'company_code' => auth()->user()->company_code,
        //         'name'         => 'اسمنت',
        //         'branch_id'    => $request->branches_id,
        //     ])->exists();

        //     if ($existscement) {
        //         return back()->with('error', 'هذه المادة مضافة مسبقًا إلى هذا الفرع.');
        //     }
        //     do {
        //         // توليد كود عشوائي من 5 أحرف وأرقام
        //         $code = strtoupper(Str::random(5));
        //     } while (Inventory::where('code', $code)->exists()); // تحقق من عدم التكرار



        //     $cement_code = new Inventory();

        //     $cement_code->company_code     = auth()->user()->company_code;
        //     $cement_code->code            = $code;
        //     $cement_code->name            = 'اسمنت';
        //     $cement_code->branch_id       = $NewBranch->id;
        //     $cement_code->unit            = 'ton';
        //     $cement_code->quantity_total  = 0;
        //     $cement_code->note            = $request->note;
        //     $cement_code->save();


        //     $existssand = Inventory::where([
        //         'company_code' => auth()->user()->company_code,
        //         'name'         => 'رمل',
        //         'branch_id'    => $request->branches_id,
        //     ])->exists();

        //     if ($existssand) {
        //         return back()->with('error', 'هذه المادة مضافة مسبقًا إلى هذا الفرع.');
        //     }

        //     do {
        //         // توليد كود عشوائي من 5 أحرف وأرقام
        //         $code = strtoupper(Str::random(5));
        //     } while (Inventory::where('code', $code)->exists()); // تحقق من عدم التكرار


        //     $sand_code = new Inventory();
        //     $sand_code->company_code     = auth()->user()->company_code;
        //     $sand_code->name            = 'رمل';
        //     $cement_code->code            = $code;
        //     $sand_code->branch_id       = $NewBranch->id;
        //     $sand_code->unit            = 'm3';
        //     $sand_code->quantity_total  = 0;
        //     $sand_code->note            = $request->note;
        //     $sand_code->save();

        //     $existsgravel = Inventory::where([
        //         'company_code' => auth()->user()->company_code,
        //         'name'         => 'حصى',
        //         'branch_id'    => $request->branches_id,
        //     ])->exists();

        //     if ($existsgravel) {
        //         return back()->with('error', 'هذه المادة مضافة مسبقًا إلى هذا الفرع.');
        //     }

        //     do {
        //         // توليد كود عشوائي من 5 أحرف وأرقام
        //         $code = strtoupper(Str::random(5));
        //     } while (Inventory::where('code', $code)->exists()); // تحقق من عدم التكرار

        //     $gravel_code = new Inventory();
        //     $gravel_code->company_code     = auth()->user()->company_code;
        //     $gravel_code->name            = 'حصى';
        //     $cement_code->code            = $code;
        //     $gravel_code->branch_id       = $NewBranch->id;
        //     $gravel_code->unit            = 'm3';
        //     $gravel_code->quantity_total  = 0;
        //     $gravel_code->note            = $request->note;
        //     $gravel_code->save();

        //     $existswater = Inventory::where([
        //         'company_code' => auth()->user()->company_code,
        //         'name'         => 'مياه',
        //         'branch_id'    => $request->branches_id,
        //     ])->exists();

        //     if ($existswater) {
        //         return back()->with('error', 'هذه المادة مضافة مسبقًا إلى هذا الفرع.');
        //     }
        //     do {
        //         // توليد كود عشوائي من 5 أحرف وأرقام
        //         $code = strtoupper(Str::random(5));
        //     } while (Inventory::where('code', $code)->exists()); // تحقق من عدم التكرار

        //     $water_code = new Inventory();
        //     $water_code->company_code     = auth()->user()->company_code;
        //     $water_code->name            = 'مياه';
        //     $cement_code->code            = $code;
        //     $water_code->branch_id       = $NewBranch->id;
        //     $water_code->unit            = 'liter';
        //     $water_code->quantity_total  = 0;
        //     $water_code->note            = $request->note;
        //     $water_code->save();


        //     // 1️⃣ جلب الخلطات الأصلية مرة واحدة
        //     $ConcreteMix = ConcreteMix::where('company_code', auth()->user()->company_code)->get();

        //     // 2️⃣ جلب أنواع الخلطات مرة واحدة

        //     foreach ($ConcreteMix as $item) {

        //         // 3️⃣ تحقق قبل إنشاء سجل جديد (منع التكرار)
        //         $exists = ConcreteMix::where('classification', $item->classification)
        //             ->where('mix_type_id', $item->mix_type_id)
        //             ->where('company_code', auth()->user()->company_code)
        //             ->where('branch_id', $NewBranch->id)
        //             ->exists();

        //         if ($exists) {
        //             continue; // تخطي السجل
        //         }

        //         // 4️⃣ إنشاء الخلطة الجديدة
        //         ConcreteMix::create([
        //             'classification' => $item->classification,
        //             'mix_type_id'     => $item->mix_type_id,
        //             'company_code'    => auth()->user()->company_code,
        //             'branch_id'       => $NewBranch->id,
        //             'cement'          => $item->cement,
        //             'sand'            => $item->sand,
        //             'gravel'          => $item->gravel,
        //             'water'           => $item->water,
        //             'notes'           => $item->notes,
        //             'cement_code' => $cement_code->code,
        //             'sand_code' => $sand_code->code,
        //             'gravel_code' => $gravel_code->code,
        //             'water_code' => $water_code->code,
        //         ]);
        //     }

        //     return back()->with('success', '✓ تم إضافة الفرع بنجاح!');
        // }
        if ($request->active == 'Newbranch') {

            // فحص التكرار قبل إضافة الفرع
            $exists = Branch::where('branch_name', $request->branch_name)
                ->where('email', $request->email)
                ->where('company_code', Auth::user()->company_code)
                ->where('branch_admin', $request->branch_admin)
                ->exists();

            if ($exists) {
                return back()->with('error', '⚠ هذا الفرع موجود مسبقاً!')
                    ->withInput();
            }

            // إنشاء الفرع
            $NewBranch = Branch::create([
                'city_id'      => $request->city_id,
                'branch_name'  => $request->branch_name,
                'company_code' => Auth::user()->company_code,
                'branch_admin' => $request->branch_admin,
                'phone'        => $request->phone,
                'email'        => $request->email,
                'address'      => $request->address,
                'created_date' => now(),
                'is_active'    => true,
            ]);

            // ------------------------------------------------
            // 🔵 دالة مساعدة لإضافة مادة إلى المخزن
            // ------------------------------------------------
            $addMaterial = function ($name, $unit) use ($NewBranch, $request) {

                // تحقق من وجود المادة مسبقاً
                $exists = Inventory::where([
                    'company_code' => auth()->user()->company_code,
                    'name'         => $name,
                    'branch_id'    => $NewBranch->id,
                ])->exists();

                if ($exists) {
                    throw new Exception("المادة '$name' مضافة مسبقًا لهذا الفرع");
                }

                // توليد كود فريد
                do {
                    $code = strtoupper(Str::random(5));
                } while (Inventory::where('code', $code)->exists());

                // إنشاء السجل
                $record = Inventory::create([
                    'company_code'    => auth()->user()->company_code,
                    'code'            => $code,
                    'name'            => $name,
                    'branch_id'       => $NewBranch->id,
                    'unit'            => $unit,
                    'quantity_total'  => 0,
                    'note'            => 'لا يوجد',
                ]);

                return $record->code;
            };

            try {
                // إضافة المواد الأساسية
                $cement_code = $addMaterial('اسمنت', 'ton');
                $sand_code   = $addMaterial('رمل', 'm3');
                $gravel_code = $addMaterial('حصى', 'm3');
                $water_code  = $addMaterial('مياه', 'liter');
            } catch (Exception $e) {
                return back()->with('error', $e->getMessage());
            }

            // ------------------------------------------------
            // 🔵 نسخ الخلطات الأصلية إلى الفرع الجديد
            // ------------------------------------------------
            $ConcreteMix = ConcreteMix::where('company_code', auth()->user()->company_code)
                ->whereNull('branch_id')  // الأصلية فقط
                ->get();

            foreach ($ConcreteMix as $item) {

                // منع التكرار
                $exists = ConcreteMix::where([
                    'classification' => $item->classification,
                    'company_code'   => auth()->user()->company_code,
                    'branch_id'      => $NewBranch->id,
                ])->exists();

                if ($exists) continue;

                // إنشاء الخلطة الجديدة
                ConcreteMix::create([
                    'classification' => $item->classification,
                    'company_code'   => auth()->user()->company_code,
                    'branch_id'      => $NewBranch->id,
                    'cement'         => $item->cement,
                    'sand'           => $item->sand,
                    'gravel'         => $item->gravel,
                    'water'          => $item->water,
                    'notes'          => $item->notes,
                    'cement_code'    => $cement_code,
                    'sand_code'      => $sand_code,
                    'gravel_code'    => $gravel_code,
                    'water_code'     => $water_code,
                ]);
            }

            return back()->with('success', '✓ تم إضافة الفرع بنجاح!');
        }

        // ==========================================
        // طلب مباشر من الفرع - بدون مراجعة (موافقة تلقائية)
        // ==========================================
        if ($request->active == 'DirectRequest') {

            $request->validate([
                'classification_id' => 'required|exists:concrete_mixes,id',
                'quantity' => 'required|numeric|min:1',
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'location' => 'required|string|max:500',
                'execution_date' => 'required|date',
                'execution_time' => 'required',
            ]);

            // السعر من حقل السعر في النموذج (قابل للتعديل من المستخدم)
            $unitPrice = $request->unit_price ?? 0;
            $totalPrice = $unitPrice * $request->quantity;
            // work_order.price يُخزن السعر الإجمالي المتفق عليه
            $agreedTotalPrice = $totalPrice;

            // التحقق من عدم وجود طلب مكرر (نفس العميل، نفس النوع، نفس التاريخ، نفس الفرع)
            $duplicate = WorkOrder::where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->where('classification', $request->classification_id)
                ->where('customer_phone', $request->customer_phone)
                ->where('execution_date', $request->execution_date)
                ->where('quantity', $request->quantity)
                ->whereIn('status_code', ['new', 'in_progress'])
                ->where('created_at', '>=', now()->subMinutes(30))
                ->first();

            if ($duplicate) {
                return back()->withInput()->with('error', '⚠️ يوجد طلب مشابه مسجل مسبقاً (رقم ' . $duplicate->id . ') لنفس العميل والنوع والتاريخ والكمية. يرجى التأكد قبل الإضافة.');
            }

            // إنشاء الطلب مباشرة بحالة "قيد العمل" (in_progress)
            $order = WorkOrder::create([
                'sender_type' => 'branch',
                'sender_id' => Auth::user()->id,

                'company_code' => Auth::user()->company_code,
                'branch_id' => Auth::user()->branch_id,

                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,

                'status_code' => 'in_progress', // مباشرة قيد العمل
                'classification' => $request->classification_id,
                'request_type' => 'direct', // طلب مباشر

                'quantity' => $request->quantity,
                'price' => $agreedTotalPrice,
                'location' => $request->location,
                'location_map_url' => $request->location_map_url,
                'location_lat' => $request->location_lat,
                'location_lng' => $request->location_lng,
                'delivery_datetime' => $request->execution_date . ' ' . $request->execution_time,
                'execution_date' => $request->execution_date,
                'execution_time' => $request->execution_time,
                'note' => $request->note,

                // الموافقات التلقائية
                'branch_approval_status' => 'approved',
                'branch_approval_user_id' => Auth::user()->id,
                'branch_approval_date' => now(),
                'branch_approval_note' => 'طلب مباشر - موافقة تلقائية',

                'requester_approval_status' => 'approved',
                'requester_approval_date' => now(),

                'accept_user' => Auth::user()->id,
                'accept_date' => now(),
                'accept_note' => 'طلب مباشر من الفرع',

                'request_date' => now(),
                'created_by' => Auth::id(),
            ]);

            // إنشاء أمر عمل جديد (WorkJob) للطلب
            $lastJob = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
                ->orderBy('id', 'desc')
                ->first();
            $jobNumber = 'WJ-' . date('Ymd') . '-' . str_pad(($lastJob ? $lastJob->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            \App\Models\WorkJob::create([
                'job_number' => $jobNumber,
                'company_code' => Auth::user()->company_code,
                'branch_id' => Auth::user()->branch_id,
                'order_id' => $order->id,
                'customer_type' => 'direct_customer',
                'customer_id' => Auth::user()->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'concrete_type_id' => $request->classification_id,
                'total_quantity' => $request->quantity,
                'executed_quantity' => 0,
                'completion_percentage' => 0,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'final_price' => $totalPrice,
                'location_address' => $request->location ?? '',
                'latitude' => $request->location_lat,
                'longitude' => $request->location_lng,
                'scheduled_date' => $request->execution_date,
                'scheduled_time' => $request->execution_time,
                'status' => 'pending',
                'notes' => $request->note,
                'created_by' => Auth::user()->id,
            ]);

            return redirect('companyBranch/ordersInProgress')->with('success', 'تم إضافة الطلب المباشر بنجاح وتحويله لقيد العمل ✅');
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

        if ($id == 'Allbranch') {
            $allbranchs = Branch::where('company_code', Auth::user()->company_code)->get();
            $cities = City::all();
            return view('branch.allbranch', compact('allbranchs', 'cities'));
        }

        if ($id == 'BranchManage') {
            return view('branch.BranchManage');
        }

        // صفحة إضافة طلب مباشر (بدون مراجعة)
        if ($id == 'directRequest') {
            $ConcreteMixes = ConcreteMix::where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->with(['categoryPrices' => function ($q) {
                    $q->where('company_code', Auth::user()->company_code)
                        ->where('is_active', true)
                        ->with('pricingCategory');
                }])
                ->get();

            $pricingCategories = PricingCategory::active()->ordered()->get();

            return view('branch.directRequest', compact('ConcreteMixes', 'pricingCategories'));
        }

        if ($id == 'listNewRequestOrders') {

            $listNewRequestOrders = WorkOrder::with([
                'company',
                'branch',
                'concreteMix',
                'sender'
            ])->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->where('status_code', 'new')
                ->where('branch_approval_status', null)
                ->get();


            return view('branch.listNewRequestOrders', compact('listNewRequestOrders'));
        }

        // قائمة الطلبات المعتمدة من المقاول - بانتظار الموافقة النهائية
        if ($id == 'listApprovedByContractor') {
            $orders = WorkOrder::with([
                'company',
                'branch',
                'concreteMix',
                'sender'
            ])->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->where('branch_approval_status', 'approved')
                ->where('requester_approval_status', 'approved')
                ->where('status_code', 'new') // لم يتم تحويلها للعمل بعد
                ->orderBy('requester_approval_date', 'desc')
                ->get();

            return view('branch.listApprovedByContractor', compact('orders'));
        }

        // الطلبات قيد العمل (تم الموافقة النهائية)
        if ($id == 'ordersInProgress') {
            $orders = WorkOrder::with([
                'company',
                'branch',
                'concreteMix',
                'sender'
            ])->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->where('status_code', 'in_progress')
                ->orderBy('accept_date', 'desc')
                ->get();

            return view('branch.ordersInProgress', compact('orders'));
        }

        // الطلبات المكتملة
        if ($id == 'ordersCompleted') {
            $orders = WorkOrder::with([
                'company',
                'branch',
                'concreteMix',
                'sender'
            ])->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->where('status_code', 'completed')
                ->orderBy('updated_at', 'desc')
                ->get();

            return view('branch.ordersCompleted', compact('orders'));
        }

        // ==========================================
        // أوامر العمل والتنفيذ
        // ==========================================

        // أعمال اليوم
        if ($id == 'workJobs/today') {
            $jobs = \App\Models\WorkJob::with(['order', 'branch', 'concreteType', 'supervisor'])
                ->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->whereDate('scheduled_date', today())
                ->whereIn('status', ['pending', 'materials_reserved', 'in_progress', 'partially_completed'])
                ->orderBy('scheduled_time')
                ->get();

            return view('branch.workJobs.today', compact('jobs'));
        }

        // أوامر العمل بانتظار التنفيذ
        if ($id == 'workJobs/pending') {
            $jobs = \App\Models\WorkJob::with(['order', 'branch', 'concreteType', 'supervisor'])
                ->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->whereIn('status', ['pending', 'materials_reserved'])
                ->orderBy('scheduled_date')
                ->orderBy('scheduled_time')
                ->get();

            return view('branch.workJobs.pending', compact('jobs'));
        }

        // أوامر العمل قيد التنفيذ
        if ($id == 'workJobs/active') {
            $jobs = \App\Models\WorkJob::with(['order', 'branch', 'concreteType', 'supervisor', 'shipments'])
                ->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->whereIn('status', ['in_progress', 'partially_completed'])
                ->orderBy('actual_start_date', 'desc')
                ->get();

            return view('branch.workJobs.active', compact('jobs'));
        }

        // أوامر العمل المكتملة
        if ($id == 'workJobs/completed') {
            $jobs = \App\Models\WorkJob::with(['order', 'branch', 'concreteType', 'supervisor'])
                ->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->where('status', 'completed')
                ->orderBy('actual_end_date', 'desc')
                ->paginate(25);

            return view('branch.workJobs.completed', compact('jobs'));
        }

        // الشحنات
        if ($id == 'workShipments') {
            $shipments = \App\Models\WorkShipment::with(['job', 'mixer', 'truck', 'pump', 'mixerDriver', 'truckDriver'])
                ->whereHas('job', function ($q) {
                    $q->where('company_code', Auth::user()->company_code)
                        ->where('branch_id', Auth::user()->branch_id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(25);

            return view('branch.workJobs.shipments', compact('shipments'));
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

        if ($explode[1] == 'edit_branch') {
            $branch = Branch::where('id', $explode[0])->first();
            $cities = City::all();
            // dd($branch);
            return view('branch.editbranch', compact('branch', 'cities'));
        }

        if ($explode[1] == 'Location') {
            $Branch = Branch::where('id', $explode[0])->first();
            $addresCompany = false;
            return view('map', compact('Branch', 'addresCompany'));
        }
        if ($explode[1] == 'ReviewRequest') {
            $WorkOrder = WorkOrder::with([
                'company',
                'branch',
                'concreteMix.categoryPrices.pricingCategory',
                'concreteMix.cementInventory.MeasurementUnit',
                'concreteMix.sandInventory.MeasurementUnit',
                'concreteMix.gravelInventory.MeasurementUnit',
                'concreteMix.waterInventory.MeasurementUnit',
                'concreteMix.chemicals.MeasurementUnit',
                'sender'
            ])->where('id', $explode[0])->first();

            // جلب الفئات السعرية مع أسعارها للخلطة المحددة
            $pricingCategories = [];
            if ($WorkOrder && $WorkOrder->concreteMix) {
                $categoryPrices = ConcreteMixCategoryPrice::where('concrete_mix_id', $WorkOrder->concreteMix->id)
                    ->where('is_active', true)
                    ->with('pricingCategory')
                    ->get();

                foreach ($categoryPrices as $catPrice) {
                    if ($catPrice->pricingCategory && $catPrice->pricingCategory->is_active) {
                        $pricingCategories[] = [
                            'id' => $catPrice->pricingCategory->id,
                            'name' => $catPrice->pricingCategory->name,
                            'price_per_meter' => $catPrice->price_per_meter,
                            'total_price' => $catPrice->price_per_meter * ($WorkOrder->quantity ?? 0)
                        ];
                    }
                }
            }

            // حساب تكلفة المتر من أسعار المواد الفعلية
            // تكلفة المتر = (كمية الأسمنت × سعره) + (كمية الرمل × سعره) + (كمية الحصى × سعره) + (كمية الماء × سعره) + تكاليف الكيماويات
            $totalMaterialsCostPerMeter = 0;
            $totalMaterialsCost = 0;

            if ($WorkOrder && $WorkOrder->concreteMix) {
                $mix = $WorkOrder->concreteMix;
                $quantity = $WorkOrder->quantity ?? 0;

                // تحميل علاقات المخزون
                $mix->load(['cementInventory', 'sandInventory', 'gravelInventory', 'waterInventory', 'chemicals']);

                // حساب تكلفة المواد الأساسية للمتر الواحد
                $cementCost = ($mix->cement ?? 0) * ($mix->cementInventory->unit_cost ?? 0);
                $sandCost = ($mix->sand ?? 0) * ($mix->sandInventory->unit_cost ?? 0);
                $gravelCost = ($mix->gravel ?? 0) * ($mix->gravelInventory->unit_cost ?? 0);
                $waterCost = ($mix->water ?? 0) * ($mix->waterInventory->unit_cost ?? 0);

                // حساب تكلفة الكيماويات للمتر الواحد
                $chemicalsCost = 0;
                if ($mix->chemicals) {
                    foreach ($mix->chemicals as $chemical) {
                        $chemicalQty = $chemical->pivot->quantity ?? 0;
                        $chemicalUnitCost = $chemical->unit_cost ?? 0;
                        $chemicalsCost += $chemicalQty * $chemicalUnitCost;
                    }
                }

                // إجمالي تكلفة المتر الواحد
                $totalMaterialsCostPerMeter = $cementCost + $sandCost + $gravelCost + $waterCost + $chemicalsCost;

                // إجمالي التكلفة = تكلفة المتر × الكمية
                $totalMaterialsCost = $totalMaterialsCostPerMeter * $quantity;
            }

            return view('branch.ReviewRequest', compact('WorkOrder', 'pricingCategories', 'totalMaterialsCostPerMeter', 'totalMaterialsCost'));
        }

        // صفحة الموافقة النهائية وتحويل للعمل
        if ($explode[1] == 'FinalApproval') {
            $WorkOrder = WorkOrder::with([
                'company',
                'branch',
                'concreteMix',
                'sender'
            ])->where('id', $explode[0])
                ->where('branch_approval_status', 'approved')
                ->where('requester_approval_status', 'approved')
                ->first();

            if (!$WorkOrder) {
                return redirect('companyBranch/listApprovedByContractor')->with('error', 'الطلب غير موجود أو لم تكتمل الموافقات');
            }

            return view('branch.FinalApproval', compact('WorkOrder'));
        }

        // صفحة تسديد/تحصيل الطلب
        if ($explode[1] == 'orderPayment') {
            $order = WorkOrder::with(['company', 'branch', 'concreteMix', 'sender'])
                ->where('id', $explode[0])
                ->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->first();

            if (!$order) {
                return redirect('companyBranch/ordersInProgress')->with('error', 'الطلب غير موجود');
            }

            $totalAmount = $order->price ?? 0;
            $remainingAmount = $totalAmount - ($order->paid_amount ?? 0);

            // سجل المدفوعات (سندات القبض) المرتبطة بهذا الطلب
            $paymentReceipts = PaymentReceipt::where('related_type', 'work_order')
                ->where('related_id', $order->id)
                ->where('status', '!=', 'cancelled')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('branch.orderPayment', compact('order', 'totalAmount', 'remainingAmount', 'paymentReceipts'));
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
        if ($request->active == 'updateInformationBranch') {

            $informatonBranch = Branch::where('id', $id)->update([
                'city_id' => $request->city_id,
                'branch_name' => $request->branch_name,
                'branch_admin' => $request->branch_admin,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'is_active' => $request->is_active,

            ]);

            return redirect('/companyBranch/Allbranch')->with('success', 'تم تحديث بيانات الفرع بنجاح.');
        }

        if ($request->active == 'AddaddresCompanyOnGoogle') {
            Branch::where('id', $id)->update([
                'latitude'  =>  $request->latitude,
                'longitude'  =>  $request->longitude,
            ]);
            return redirect('companyBranch/Allbranch')->with('success', 'تم إضافة موقع الفرع على خرائط كوكل بنجاح ✅');
        }



        if ($request->active == 'branch_approval_status') {

            $price = str_replace(',', '', $request->price);
            WorkOrder::where('id', $id)->update([

                'branch_approval_status' => 'approved',
                'branch_approval_user_id' => Auth::user()->id,
                'branch_approval_date' => now(),
                'branch_approval_note' => $request->branch_approval_note,
                'price' => $price,

            ]);
            return redirect('companyBranch/listNewRequestOrders')->with('success', 'تم قبول الطلب و ارسال السعر والملاحظات الى صاحب الطلب  ✅');
        }

        if ($request->active == 'branch_reject') {
            // set branch approval as rejected

            WorkOrder::where('id', $id)->update([
                'branch_approval_status' => 'rejected',
                'branch_approval_user_id' => Auth::user()->id,
                'branch_approval_date' => now(),
                'branch_approval_note' => $request->branch_reject_note,
            ]);

            return redirect('companyBranch/listNewRequestOrders')->with('success', 'تم رفض الطلب وارسال ملاحظة الرفض الى صاحب الطلب ✅');
        }

        // الموافقة النهائية وتحويل الطلب للعمل
        if ($request->active == 'FinalApproval') {
            $order = WorkOrder::where('id', $id)
                ->where('branch_approval_status', 'approved')
                ->where('requester_approval_status', 'approved')
                ->first();

            if (!$order) {
                return redirect('companyBranch/listApprovedByContractor')->with('error', 'الطلب غير موجود أو لم تكتمل الموافقات');
            }

            // تحديث الطلب - الموافقة النهائية وتحويل للعمل
            // استخدام الأعمدة الموجودة: accept_user, accept_date, accept_note للموافقة النهائية
            $order->update([
                'status_code' => 'in_progress', // تحويل للعمل
                'accept_user' => Auth::user()->id,
                'accept_date' => now(),
                'accept_note' => $request->final_approval_note,
                'execution_date' => $request->execution_date,
                'execution_time' => $request->execution_time,
                'final_price' => $order->price, // تثبيت السعر النهائي
            ]);

            // إنشاء أمر عمل جديد (WorkJob) للطلب
            $lastJob = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
                ->orderBy('id', 'desc')
                ->first();
            $jobNumber = 'WJ-' . date('Ymd') . '-' . str_pad(($lastJob ? $lastJob->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            \App\Models\WorkJob::create([
                'job_number' => $jobNumber,
                'company_code' => Auth::user()->company_code,
                'branch_id' => Auth::user()->branch_id,
                'order_id' => $order->id,
                'customer_type' => 'contractor',
                'customer_id' => $order->sender_id,
                'customer_name' => $order->sender->fullname ?? '',
                'customer_phone' => $order->sender->phone ?? '',
                'concrete_type_id' => $order->classification,
                'total_quantity' => $order->quantity,
                'executed_quantity' => 0,
                'completion_percentage' => 0,
                'unit_price' => ($order->quantity > 0) ? round($order->price / $order->quantity, 2) : $order->price,
                'total_price' => $order->price,
                'final_price' => $order->price,
                'location_address' => $order->location ?? '',
                'latitude' => $order->latitude,
                'longitude' => $order->longitude,
                'scheduled_date' => $request->execution_date,
                'scheduled_time' => $request->execution_time,
                'status' => 'pending',
                'notes' => $request->final_approval_note,
                'created_by' => Auth::user()->id,
            ]);

            return redirect('companyBranch/listApprovedByContractor')->with('success', 'تمت الموافقة النهائية وتم إنشاء أمر العمل بنجاح ✅');
        }

        // ===== تسديد/تحصيل دفعة للطلب =====
        if ($request->active == 'recordPayment') {
            $request->validate([
                'payment_amount' => 'required|numeric|min:1',
                'payment_method' => 'required|in:cash,bank_transfer,check,card',
                'payment_note'   => 'nullable|string|max:500',
            ]);

            $order = WorkOrder::with(['sender', 'concreteMix'])
                ->where('id', $id)
                ->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id)
                ->first();

            if (!$order) {
                return redirect('companyBranch/ordersInProgress')->with('error', 'الطلب غير موجود');
            }

            $totalAmount = $order->price ?? 0;
            $remainingAmount = $totalAmount - ($order->paid_amount ?? 0);
            $paymentAmount = (float) $request->payment_amount;

            if ($paymentAmount > $remainingAmount) {
                return redirect()->back()->with('error', 'المبلغ المدخل أكبر من المتبقي');
            }

            try {
                DB::beginTransaction();

                // 1) إنشاء سند قبض
                $isDirectOrder = $order->request_type === 'direct';
                $payerType = $isDirectOrder ? 'customer' : 'contractor';
                $payerName = $isDirectOrder
                    ? ($order->customer_name ?? 'عميل مباشر')
                    : ($order->sender->fullname ?? 'مقاول');
                $payerPhone = $isDirectOrder
                    ? ($order->customer_phone ?? '')
                    : ($order->sender->phone ?? '');
                $payerId = $isDirectOrder ? null : $order->sender_id;

                $receiptNumber = PaymentReceipt::generateReceiptNumber(
                    Auth::user()->company_code,
                    Auth::user()->branch_id
                );

                $receipt = PaymentReceipt::create([
                    'receipt_number'  => $receiptNumber,
                    'company_code'    => Auth::user()->company_code,
                    'branch_id'       => Auth::user()->branch_id,
                    'payer_type'      => $payerType,
                    'payer_id'        => $payerId,
                    'payer_name'      => $payerName,
                    'payer_phone'     => $payerPhone,
                    'amount'          => $paymentAmount,
                    'currency_code'   => 'IQD',
                    'exchange_rate'   => 1,
                    'amount_in_default' => $paymentAmount,
                    'payment_method'  => $request->payment_method,
                    'description'     => $request->payment_note ?? ('تسديد طلب #' . $order->id),
                    'related_type'    => 'work_order',
                    'related_id'      => $order->id,
                    'status'          => 'confirmed',
                    'received_by'     => Auth::user()->id,
                    'received_at'     => now(),
                ]);

                // 2) تحديث الطلب
                $newPaidAmount = ($order->paid_amount ?? 0) + $paymentAmount;
                $newStatus = $newPaidAmount >= $totalAmount ? 'paid' : 'partial';

                $order->update([
                    'paid_amount'    => $newPaidAmount,
                    'payment_status' => $newStatus,
                    'payment_method' => $request->payment_method,
                    'payment_note'   => $request->payment_note,
                    'paid_at'        => now(),
                    'paid_by'        => Auth::user()->id,
                ]);

                // 3) تحديث صندوق اليوم (إضافة للفرع)
                $dailyCash = DailyCashSummary::getOrCreateToday(
                    Auth::user()->company_code,
                    Auth::user()->branch_id,
                    'IQD'
                );
                $dailyCash->addReceipt($paymentAmount);

                // 4) للطلبات غير المباشرة: تحديث حساب المقاول
                if (!$isDirectOrder && $order->sender_id) {
                    $contractorAccount = ContractorAccount::where('contractor_id', $order->sender_id)
                        ->where('company_code', Auth::user()->company_code)
                        ->where('branch_id', Auth::user()->branch_id)
                        ->first();

                    if ($contractorAccount) {
                        $contractorAccount->addCredit($paymentAmount);
                    }
                }

                DB::commit();

                return redirect('companyBranch/' . $order->id . '&orderPayment/edit')
                    ->with('success', 'تم تسجيل الدفعة بنجاح ✅ (سند قبض: ' . $receiptNumber . ')');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'حدث خطأ أثناء تسجيل الدفعة: ' . $e->getMessage());
            }
        }

        // إكمال الطلب
        if ($request->active == 'markCompleted') {
            $order = WorkOrder::where('id', $id)
                ->where('status_code', 'in_progress')
                ->first();

            if (!$order) {
                return redirect('companyBranch/ordersInProgress')->with('error', 'الطلب غير موجود أو ليس قيد العمل');
            }

            $order->update([
                'status_code' => 'completed',
                'completed_at' => now(),
                'completed_by' => Auth::user()->id,
            ]);

            return redirect('companyBranch/ordersInProgress')->with('success', 'تم إكمال الطلب بنجاح ✅');
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

    // ==========================================
    // وظائف أوامر العمل
    // ==========================================

    /**
     * عرض تفاصيل أمر العمل
     */
    public function viewWorkJob($id)
    {
        $job = \App\Models\WorkJob::with([
            'order.sender',
            'order.concreteMix',
            'branch',
            'concreteType',
            'supervisor',
            'shipments.mixer.carType',
            'shipments.mixerDriver',
            'shipments.losses',
            'losses'
        ])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($id);

        // جلب الخباطات المتاحة (السيارات من نوع خباطة) - مع فلترة المشغولة والصيانة
        $mixers = \App\Models\Cars::with(['carType', 'driver', 'backupDriver', 'activeShipments'])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('is_active', true)
            ->where(function ($query) {
                // الخباطات: إما لها mixer_capacity أو نوعها يحتوي على كلمة خباط
                $query->where('mixer_capacity', '>', 0)
                    ->orWhereHas('carType', function ($q) {
                        $q->where('name', 'like', '%خباط%')
                            ->orWhere('name', 'like', '%mixer%')
                            ->orWhere('capacity', '>', 0);
                    });
            })
            ->get()
            ->map(function ($mixer) {
                // التحقق من حالة الصيانة أولاً
                if ($mixer->operational_status === 'in_maintenance') {
                    $mixer->is_busy = false;
                    $mixer->is_reserved = false;
                    $mixer->is_in_maintenance = true;
                    $mixer->active_shipment = null;
                    $mixer->status_text = 'في الصيانة';
                    return $mixer;
                }

                $mixer->is_in_maintenance = false;

                // التحقق من الشحنات النشطة (departed, arrived, working)
                $activeShipment = $mixer->activeShipments->first();

                // التحقق من الشحنات المحجوزة (planned)
                $reservedShipment = \App\Models\WorkShipment::where('mixer_id', $mixer->id)
                    ->where('status', 'planned')
                    ->first();

                if ($activeShipment) {
                    $mixer->is_busy = true;
                    $mixer->is_reserved = false;
                    $mixer->active_shipment = $activeShipment;
                    $mixer->status_text = 'غير متاحة';
                } elseif ($reservedShipment) {
                    $mixer->is_busy = false;
                    $mixer->is_reserved = true;
                    $mixer->active_shipment = $reservedShipment;
                    $mixer->status_text = 'تم الحجز';
                } else {
                    $mixer->is_busy = false;
                    $mixer->is_reserved = false;
                    $mixer->active_shipment = null;
                    $mixer->status_text = 'متاحة';
                }

                return $mixer;
            });

        // جلب السائقين المتاحين
        $drivers = \App\Models\Employee::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->get();

        return view('branch.workJobs.view', compact('job', 'mixers', 'drivers'));
    }

    /**
     * بدء تنفيذ أمر العمل
     */
    public function startWorkJob(Request $request, $id)
    {
        $job = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->whereIn('status', ['pending', 'materials_reserved'])
            ->findOrFail($id);

        $job->update([
            'status' => 'in_progress',
            'actual_start_date' => now(),
            'started_by' => Auth::user()->id,
        ]);

        return back()->with('success', 'تم بدء تنفيذ أمر العمل بنجاح ✅');
    }

    /**
     * إكمال أمر العمل
     */
    public function completeWorkJob(Request $request, $id)
    {
        $job = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->whereIn('status', ['in_progress', 'partially_completed'])
            ->findOrFail($id);

        // حساب الكمية الفعلية المنفذة
        $deliveredQuantity = $job->shipments()->where('status', 'completed')->sum('actual_quantity');

        $job->update([
            'status' => 'completed',
            'actual_end_date' => now(),
            'delivered_quantity' => $deliveredQuantity,
            'completed_by' => Auth::user()->id,
        ]);

        // تحديث حالة الطلب الأصلي إذا كانت جميع أوامر العمل مكتملة
        $order = $job->workOrder;
        if ($order) {
            $pendingJobs = $order->workJobs()->where('status', '!=', 'completed')->count();
            if ($pendingJobs == 0) {
                $order->update([
                    'status_code' => 'completed',
                    'completed_at' => now(),
                ]);
            }
        }

        return redirect('/ConcreteERP/companyBranch/workJobs/completed')
            ->with('success', 'تم إكمال أمر العمل بنجاح ✅');
    }

    /**
     * إضافة شحنة جديدة لأمر العمل
     */
    public function addShipment(Request $request, $jobId)
    {
        $request->validate([
            'mixer_id' => 'required|exists:cars,id',
            'driver_id' => 'required|exists:employees,id',
            'quantity' => 'required|numeric|min:0.5',
        ]);

        $job = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($jobId);

        // التحقق من أن الخباطة غير مشغولة
        $mixer = \App\Models\Cars::find($request->mixer_id);
        if ($mixer && $mixer->is_busy) {
            return back()->with('error', 'هذه الخباطة مشغولة حالياً في شحنة أخرى! ❌');
        }

        // إنشاء رقم شحنة تسلسلي (1, 2, 3, ...)
        $lastShipment = \App\Models\WorkShipment::where('job_id', $jobId)->orderBy('shipment_number', 'desc')->first();
        $shipmentNumber = $lastShipment ? $lastShipment->shipment_number + 1 : 1;

        \App\Models\WorkShipment::create([
            'shipment_number' => $shipmentNumber,
            'job_id' => $jobId,
            'mixer_id' => $request->mixer_id,
            'mixer_driver_id' => $request->driver_id,
            'planned_quantity' => $request->quantity,
            'status' => 'planned',
            'created_by' => Auth::user()->id,
        ]);

        return back()->with('success', 'تم إضافة الشحنة بنجاح ✅');
    }

    // ==========================================
    // وظائف الشحنات
    // ==========================================

    /**
     * بدء انطلاق الشحنة - للمخضض بالشحنة فقط (من أنشأها)
     */
    public function departShipment(Request $request, $id)
    {
        $shipment = \App\Models\WorkShipment::whereHas('job', function ($q) {
            $q->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id);
        })->findOrFail($id);

        if ($shipment->status !== 'planned' && $shipment->status !== 'preparing') {
            return back()->with('error', 'لا يمكن انطلاق الشحنة من هذه الحالة.');
        }

        if ((int) $shipment->created_by !== (int) Auth::id()) {
            return back()->with('error', 'فقط المخضض بالشحنة (من أضافها) يمكنه تنفيذ الانطلاق.');
        }

        $shipment->update([
            'status' => 'departed',
            'departure_time' => now(),
        ]);

        return back()->with('success', 'تم تسجيل انطلاق الشحنة ✅');
    }

    /**
     * تسجيل وصول الشحنة
     */
    public function arriveShipment(Request $request, $id)
    {
        $shipment = \App\Models\WorkShipment::whereHas('job', function ($q) {
            $q->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id);
        })->findOrFail($id);

        $shipment->update([
            'status' => 'arrived',
            'arrival_time' => now(),
        ]);

        return back()->with('success', 'تم تسجيل وصول الشحنة ✅');
    }

    /**
     * إكمال الشحنة
     */
    public function completeShipment(Request $request, $id)
    {
        $shipment = \App\Models\WorkShipment::whereHas('job', function ($q) {
            $q->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id);
        })->findOrFail($id);

        // الحصول على الكمية الفعلية من الطلب أو استخدام الكمية المخططة
        $actualQuantity = $request->input('actual_quantity', $shipment->planned_quantity);

        $shipment->update([
            'status' => 'completed',
            'work_end_time' => now(),
            'actual_quantity' => $actualQuantity,
        ]);

        // تحديث الكمية المنفذة في أمر العمل
        $job = $shipment->job;
        if ($job) {
            $totalDelivered = $job->shipments()->where('status', 'completed')->sum('actual_quantity');
            $completionPercentage = ($job->total_quantity > 0) ? ($totalDelivered / $job->total_quantity) * 100 : 0;

            $job->update([
                'executed_quantity' => $totalDelivered,
                'completion_percentage' => min(100, $completionPercentage),
            ]);
        }

        return back()->with('success', 'تم إكمال الشحنة بنجاح ✅ - الكمية المسلمة: ' . $actualQuantity . ' م³');
    }

    /**
     * بدء العمل في الشحنة (بعد الوصول)
     */
    public function startShipmentWork(Request $request, $id)
    {
        $shipment = \App\Models\WorkShipment::whereHas('job', function ($q) {
            $q->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id);
        })->findOrFail($id);

        $shipment->update([
            'status' => 'working',
            'work_start_time' => now(),
        ]);

        return back()->with('success', 'تم بدء العمل في الشحنة ✅');
    }

    /**
     * تسجيل تلف/خسارة للشحنة
     */
    public function reportShipmentLoss(Request $request, $id)
    {
        $request->validate([
            'loss_type' => 'required|string',
            'quantity_lost' => 'required|numeric|min:0.1',
            'description' => 'nullable|string|max:500',
        ]);

        $shipment = \App\Models\WorkShipment::whereHas('job', function ($q) {
            $q->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id);
        })->findOrFail($id);

        // تسجيل الخسارة
        \App\Models\WorkLoss::create([
            'company_code' => Auth::user()->company_code,
            'branch_id' => Auth::user()->branch_id,
            'job_id' => $shipment->job_id,
            'shipment_id' => $shipment->id,
            'vehicle_id' => $shipment->mixer_id,
            'loss_type' => $request->loss_type,
            'quantity_lost' => $request->quantity_lost,
            'description' => $request->description,
            'status' => 'reported',
            'reported_by' => Auth::user()->id,
            'reported_at' => now(),
        ]);

        // تحديث الشحنة - إنهاء بالخسارة
        $actualDelivered = max(0, $shipment->planned_quantity - $request->quantity_lost);
        $shipment->update([
            'status' => 'completed',
            'work_end_time' => now(),
            'actual_quantity' => $actualDelivered,
            'driver_notes' => ($shipment->driver_notes ? $shipment->driver_notes . "\n" : '') .
                'تلف: ' . $request->quantity_lost . ' م³ - ' . ($request->description ?? ''),
        ]);

        // تحديث الكمية المنفذة في أمر العمل
        $job = $shipment->job;
        if ($job) {
            $totalDelivered = $job->shipments()->where('status', 'completed')->sum('actual_quantity');
            $totalLosses = $job->losses()->sum('quantity_lost');
            $completionPercentage = ($job->total_quantity > 0) ? ($totalDelivered / $job->total_quantity) * 100 : 0;

            $job->update([
                'executed_quantity' => $totalDelivered,
                'completion_percentage' => min(100, $completionPercentage),
            ]);
        }

        return back()->with('warning', 'تم تسجيل التلف: ' . $request->quantity_lost . ' م³ ⚠️');
    }

    /**
     * إلغاء الشحنة - لمدير الفرع فقط، وقبل البدء (حالة مخطط) فقط
     */
    public function cancelShipment(Request $request, $id)
    {
        $shipment = \App\Models\WorkShipment::with('mixer')->whereHas('job', function ($q) {
            $q->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id);
        })->findOrFail($id);

        if (Auth::user()->usertype_id !== 'BM') {
            return back()->with('error', 'فقط مدير الفرع يمكنه إلغاء الشحنة.');
        }

        if ($shipment->status !== 'planned' && $shipment->status !== 'preparing') {
            return back()->with('error', 'لا يمكن إلغاء الشحنة بعد البدء (الانطلاق).');
        }

        // حفظ رقم الشحنة لعرضه في الرسالة
        $shipmentNumber = $shipment->shipment_number;
        $mixerNumber = $shipment->mixer->car_number ?? null;

        // حذف قيد الشحنة نهائياً (تحرير المركبة يتم تلقائياً لأن العلاقة محذوفة)
        $shipment->delete();

        $message = 'تم إلغاء الشحنة رقم ' . $shipmentNumber . ' وحذفها بنجاح';
        if ($mixerNumber) {
            $message .= ' وتحرير الآلية ' . $mixerNumber;
        }
        $message .= ' ✅';

        return back()->with('success', $message);
    }

    /**
     * عرض تفاصيل الشحنة
     */
    public function viewShipment($id)
    {
        $shipment = \App\Models\WorkShipment::with([
            'job.workOrder',
            'mixer',
            'mixerDriver',
            'truck',
            'pump'
        ])->whereHas('job', function ($q) {
            $q->where('company_code', Auth::user()->company_code)
                ->where('branch_id', Auth::user()->branch_id);
        })->findOrFail($id);

        return view('branch.workJobs.shipmentView', compact('shipment'));
    }

    /**
     * عرض فاتورة أمر العمل
     */
    public function workJobInvoice($id)
    {
        $job = \App\Models\WorkJob::with([
            'workOrder.customer',
            'workOrder.project',
            'shipments',
            'branch'
        ])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('status', 'completed')
            ->findOrFail($id);

        return view('branch.workJobs.invoice', compact('job'));
    }

    /**
     * صفحة تخصيص الآليات والسائقين
     */
    public function assignWorkJob($id)
    {
        $job = \App\Models\WorkJob::with(['concreteType'])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($id);

        // جلب الخلاطات المتاحة
        $mixers = \App\Models\Cars::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('is_active', true)
            ->with('carType')
            ->get();

        // جلب السائقين
        $drivers = \App\Models\Employee::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('isactive', true)
            ->whereHas('employeeType', function ($q) {
                $q->where('name', 'like', '%سائق%');
            })
            ->with('employeeType')
            ->get();

        // جلب المشرفين (المهندسين)
        $supervisors = \App\Models\Employee::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('isactive', true)
            ->whereHas('employeeType', function ($q) {
                $q->where('name', 'like', '%مهندس%');
            })
            ->get();

        return view('branch.workJobs.assign', compact('job', 'mixers', 'drivers', 'supervisors'));
    }

    /**
     * حفظ تخصيص الآليات والسائقين
     */
    public function saveAssignment(Request $request, $id)
    {
        $job = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($id);

        // تحديث المشرف
        if ($request->supervisor_id) {
            $job->update([
                'supervisor_id' => $request->supervisor_id,
                'internal_notes' => $request->assignment_notes,
            ]);
        }

        // إنشاء شحنات للخلاطات المختارة
        if ($request->mixers && $request->drivers) {
            $mixerIds = $request->mixers;
            $driverIds = $request->drivers;

            foreach ($mixerIds as $index => $mixerId) {
                $driverId = $driverIds[$index] ?? $driverIds[0] ?? null;

                if ($driverId) {
                    // إنشاء رقم شحنة تسلسلي (1, 2, 3, ...)
                    $lastShipment = \App\Models\WorkShipment::where('job_id', $id)->orderBy('shipment_number', 'desc')->first();
                    $shipmentNumber = $lastShipment ? $lastShipment->shipment_number + 1 : 1;

                    // حساب الكمية لكل شحنة
                    $mixer = \App\Models\Cars::find($mixerId);
                    $quantity = 8; // افتراضي 8 متر مكعب

                    \App\Models\WorkShipment::create([
                        'shipment_number' => $shipmentNumber,
                        'job_id' => $id,
                        'mixer_id' => $mixerId,
                        'mixer_driver_id' => $driverId,
                        'pump_id' => $job->default_pump_id, // استخدام البَم الافتراضي للعمل
                        'pump_driver_id' => $job->default_pump_driver_id, // استخدام سائق البَم الافتراضي
                        'planned_quantity' => $quantity,
                        'status' => 'planned',
                        'created_by' => Auth::user()->id,
                    ]);
                }
            }
        }

        // بدء العمل إذا طُلب ذلك
        if ($request->start_work) {
            $job->update([
                'status' => 'in_progress',
                'actual_start_date' => now(),
                'started_by' => Auth::user()->id,
            ]);
            return redirect('companyBranch/workJob/' . $id . '/view')
                ->with('success', 'تم حفظ التخصيص وبدء العمل بنجاح ✅');
        }

        return redirect('companyBranch/workJob/' . $id . '/view')
            ->with('success', 'تم حفظ التخصيص بنجاح ✅');
    }

    /**
     * لوحة تحكم التنفيذ
     */
    public function executionDashboard()
    {
        if (Auth::user()->usertype_id === 'CM') {
            return redirect()->route('companyBranch.company.orders.dashboard');
        }

        $branchId = Auth::user()->branch_id;
        $companyCode = Auth::user()->company_code;

        // إحصائيات أوامر العمل
        $todayJobsCount = \App\Models\WorkJob::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->whereDate('scheduled_date', today())
            ->count();

        $activeJobsCount = \App\Models\WorkJob::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->whereIn('status', ['in_progress', 'partially_completed'])
            ->count();

        $pendingJobsCount = \App\Models\WorkJob::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->whereIn('status', ['pending', 'materials_reserved'])
            ->count();

        $completedTodayCount = \App\Models\WorkJob::where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->where('status', 'completed')
            ->whereDate('actual_end_date', today())
            ->count();

        // الشحنات النشطة
        $activeShipments = \App\Models\WorkShipment::with(['job', 'mixer', 'mixerDriver'])
            ->whereHas('job', function ($q) use ($companyCode, $branchId) {
                $q->where('company_code', $companyCode)
                    ->where('branch_id', $branchId);
            })
            ->whereIn('status', ['departed', 'arrived', 'working'])
            ->get();

        // أعمال اليوم
        $todayJobs = \App\Models\WorkJob::with(['concreteType'])
            ->where('company_code', $companyCode)
            ->where('branch_id', $branchId)
            ->whereDate('scheduled_date', today())
            ->whereIn('status', ['pending', 'materials_reserved', 'in_progress'])
            ->orderBy('scheduled_time')
            ->limit(5)
            ->get();

        return view('branch.workJobs.dashboard', compact(
            'todayJobsCount',
            'activeJobsCount',
            'pendingJobsCount',
            'completedTodayCount',
            'activeShipments',
            'todayJobs'
        ));
    }

    /**
     * لوحة طلبات وأوامر العمل لمدير الشركة - كل الأفرع
     */
    public function companyOrdersDashboard()
    {
        $user = Auth::user();
        $companyCode = $user->company_code;

        $branchQuery = Branch::where('company_code', $companyCode)->where('is_active', true)->orderBy('branch_name');
        if ($user->usertype_id === 'BM') {
            $branchQuery->where('id', $user->branch_id);
        }
        $branches = $branchQuery->get();

        $branchesData = [];
        foreach ($branches as $branch) {
            $branchId = $branch->id;

            $newJobs = \App\Models\WorkJob::with(['order', 'concreteType'])
                ->where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->whereIn('status', ['pending', 'materials_reserved'])
                ->orderBy('scheduled_date')
                ->orderBy('scheduled_time')
                ->get();

            $activeJobs = \App\Models\WorkJob::with(['order', 'concreteType'])
                ->where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->whereIn('status', ['in_progress', 'partially_completed'])
                ->orderBy('actual_start_date', 'desc')
                ->get();

            $completedJobs = \App\Models\WorkJob::with(['order', 'concreteType'])
                ->where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->where('status', 'completed')
                ->orderBy('actual_end_date', 'desc')
                ->limit(10)
                ->get();

            $branchesData[] = (object)[
                'branch' => $branch,
                'newJobs' => $newJobs,
                'activeJobs' => $activeJobs,
                'completedJobs' => $completedJobs,
                'newCount' => $newJobs->count(),
                'activeCount' => $activeJobs->count(),
                'completedCount' => \App\Models\WorkJob::where('company_code', $companyCode)->where('branch_id', $branchId)->where('status', 'completed')->count(),
            ];
        }

        return view('branch.workJobs.company-orders-dashboard', compact('branchesData'));
    }

    // ==========================================
    // وظائف صفحات أوامر العمل المنفصلة
    // ==========================================

    /**
     * أعمال اليوم
     */
    public function workJobsToday()
    {
        $jobs = \App\Models\WorkJob::with(['order', 'branch', 'concreteType', 'supervisor'])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->whereDate('scheduled_date', today())
            ->whereIn('status', ['pending', 'materials_reserved', 'in_progress', 'partially_completed'])
            ->orderBy('scheduled_time')
            ->get();

        return view('branch.workJobs.today', compact('jobs'));
    }

    /**
     * أوامر العمل بانتظار التنفيذ
     */
    public function workJobsPending()
    {
        $jobs = \App\Models\WorkJob::with(['order', 'branch', 'concreteType', 'supervisor'])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->whereIn('status', ['pending', 'materials_reserved'])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->get();

        // الطلبات قيد العمل (المعتمدة) التي تنتظر تحويلها لأوامر عمل
        $approvedOrders = \App\Models\WorkOrder::with(['company', 'branch', 'concreteMix', 'sender'])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('status_code', 'in_progress')
            ->whereDoesntHave('workJobs') // لم يتم إنشاء أمر عمل لها بعد
            ->orderBy('accept_date', 'desc')
            ->get();

        return view('branch.workJobs.pending', compact('jobs', 'approvedOrders'));
    }

    /**
     * أوامر العمل قيد التنفيذ
     */
    public function workJobsActive()
    {
        $jobs = \App\Models\WorkJob::with(['order', 'branch', 'concreteType', 'supervisor', 'shipments'])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->whereIn('status', ['in_progress', 'partially_completed'])
            ->orderBy('actual_start_date', 'desc')
            ->get();

        return view('branch.workJobs.active', compact('jobs'));
    }

    /**
     * أوامر العمل المكتملة
     */
    public function workJobsCompleted()
    {
        $jobs = \App\Models\WorkJob::with(['order', 'branch', 'concreteType', 'supervisor'])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('status', 'completed')
            ->orderBy('actual_end_date', 'desc')
            ->paginate(25);

        return view('branch.workJobs.completed', compact('jobs'));
    }

    /**
     * الشحنات
     */
    public function workShipments()
    {
        $shipments = \App\Models\WorkShipment::with(['job', 'mixer', 'truck', 'pump', 'mixerDriver', 'truckDriver'])
            ->whereHas('job', function ($q) {
                $q->where('company_code', Auth::user()->company_code)
                    ->where('branch_id', Auth::user()->branch_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('branch.workJobs.shipments', compact('shipments'));
    }

    /**
     * عرض صفحة تعيين البَم
     */
    public function assignPump($id)
    {
        $job = \App\Models\WorkJob::with(['defaultPump', 'defaultPumpDriver', 'concreteType'])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($id);

        // جلب البمات المتاحة في الفرع
        $pumps = \App\Models\Cars::with(['carType', 'driver', 'backupDriver'])
            ->where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('is_active', true)
            ->whereHas('carType', function ($q) {
                $q->where('name', 'like', '%بم%')
                    ->orWhere('name', 'like', '%pump%')
                    ->orWhere('name', 'like', '%مضخ%');
            })
            ->get()
            ->map(function ($pump) use ($id) {
                // التحقق من حالة الصيانة
                if ($pump->operational_status === 'in_maintenance') {
                    $pump->is_available = false;
                    $pump->status_text = 'في الصيانة';
                    return $pump;
                }

                // التحقق من البمات المشغولة في أوامر عمل أخرى
                $assignedToOtherJob = \App\Models\WorkJob::where('default_pump_id', $pump->id)
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->where('id', '!=', $id)
                    ->exists();

                if ($assignedToOtherJob) {
                    $pump->is_available = false;
                    $pump->status_text = 'مخصص لعمل آخر';
                } else {
                    $pump->is_available = true;
                    $pump->status_text = 'متاح';
                }

                return $pump;
            });

        // جلب السائقين المتاحين (سائقي بمات)
        $drivers = \App\Models\Employee::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->where('isactive', true)
            ->whereHas('employeeType', function ($q) {
                $q->where('name', 'like', '%سائق%');
            })
            ->with('employeeType')
            ->get()
            ->map(function ($driver) use ($id) {
                // التحقق من ارتباط السائق بمركبة أخرى كسائق أساسي
                $assignedToCar = \App\Models\Cars::where('driver_id', $driver->id)
                    ->where('is_active', true)
                    ->exists();

                // التحقق من ارتباط السائق بعمل آخر نشط
                $assignedToOtherJob = \App\Models\WorkJob::where('default_pump_driver_id', $driver->id)
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->where('id', '!=', $id)
                    ->exists();

                $driver->is_available = !$assignedToCar && !$assignedToOtherJob;
                $driver->status_text = $assignedToCar ? 'مرتبط بمركبة' : ($assignedToOtherJob ? 'مخصص لعمل آخر' : 'متاح');

                return $driver;
            });

        return view('branch.workJobs.assignPump', compact('job', 'pumps', 'drivers'));
    }

    /**
     * حفظ البَم المخصص للعمل
     */
    public function savePump(Request $request, $id)
    {
        $job = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($id);

        $request->validate([
            'pump_id' => 'required|exists:cars,id',
            'pump_driver_id' => 'nullable|exists:employees,id',
            'pump_notes' => 'nullable|string|max:500',
        ], [
            'pump_id.required' => 'يجب اختيار البَم',
            'pump_id.exists' => 'البَم المختار غير موجود',
        ]);

        // التحقق من أن البَم ليس مخصص لعمل آخر نشط
        $alreadyAssigned = \App\Models\WorkJob::where('default_pump_id', $request->pump_id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('id', '!=', $id)
            ->first();

        if ($alreadyAssigned) {
            return back()->with('error', 'البَم مخصص بالفعل لعمل آخر: ' . $alreadyAssigned->job_number);
        }

        // التحقق من توفر السائق إذا تم اختياره
        if ($request->pump_driver_id) {
            // التحقق من ارتباط السائق بمركبة أخرى
            $driverAssignedToCar = \App\Models\Cars::where('driver_id', $request->pump_driver_id)
                ->where('is_active', true)
                ->where('id', '!=', $request->pump_id) // استثناء المضخة الحالية
                ->first();

            if ($driverAssignedToCar) {
                return back()->with('error', 'السائق مرتبط بالفعل بمركبة أخرى: ' . $driverAssignedToCar->car_number);
            }

            // التحقق من ارتباط السائق بعمل آخر نشط
            $driverAssignedToJob = \App\Models\WorkJob::where('default_pump_driver_id', $request->pump_driver_id)
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->where('id', '!=', $id)
                ->first();

            if ($driverAssignedToJob) {
                return back()->with('error', 'السائق مخصص بالفعل لعمل آخر: ' . $driverAssignedToJob->job_number);
            }
        }

        // حفظ البَم
        $pump = \App\Models\Cars::find($request->pump_id);
        $oldPump = $job->defaultPump;

        $job->update([
            'default_pump_id' => $request->pump_id,
            'default_pump_driver_id' => $request->pump_driver_id,
            'pump_assigned_at' => now(),
            'pump_notes' => $request->pump_notes,
        ]);

        // تطبيق البَم على جميع الشحنات القادمة (اختياري)
        if ($request->apply_to_shipments) {
            \App\Models\WorkShipment::where('job_id', $id)
                ->whereIn('status', ['planned', 'preparing'])
                ->update([
                    'pump_id' => $request->pump_id,
                    'pump_driver_id' => $request->pump_driver_id,
                ]);
        }

        $message = $oldPump
            ? "تم تغيير البَم من {$oldPump->car_number} إلى {$pump->car_number} بنجاح ✅"
            : "تم تخصيص البَم {$pump->car_number} للعمل بنجاح ✅";

        return redirect()->route('companyBranch.workJob.view', $id)->with('success', $message);
    }

    /**
     * إزالة البَم من العمل
     */
    public function removePump($id)
    {
        $job = \App\Models\WorkJob::where('company_code', Auth::user()->company_code)
            ->where('branch_id', Auth::user()->branch_id)
            ->findOrFail($id);

        if (!$job->default_pump_id) {
            return back()->with('error', 'لا يوجد بَم مخصص لهذا العمل');
        }

        $job->update([
            'default_pump_id' => null,
            'default_pump_driver_id' => null,
            'pump_assigned_at' => null,
            'pump_notes' => null,
        ]);

        return back()->with('success', 'تم إزالة البَم من العمل بنجاح ✅');
    }
}
