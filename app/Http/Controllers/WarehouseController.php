<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Chemical;
use App\Models\Company;
use App\Models\ConcreteMix;
use App\Models\ConcreteMixChemical;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\InventoryLoss;
use App\Models\MeasurementUnit;
use App\Models\PricingCategory;
use App\Models\ConcreteMixCategoryPrice;
use App\Models\Supplier;
use App\Models\EmployeeType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class WarehouseController extends Controller
{
    public function printLoss($loss)
    {
        $loss = InventoryLoss::with(['creator', 'branch', 'company'])->findOrFail($loss);
        return view('warehouse.loss-invoice', compact('loss'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // تجنب صفحة بيضاء: لا توجد لوحة رئيسية لـ /warehouse، نحوّل لأول شاشة مستودع مفيدة
        return redirect()->route('warehouse.show', 'addMainMaterials');
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
        if ($request->active == "AddNewMainMaterials") {
            $materialName = trim((string) $request->name);
            if ($materialName === '') {
                return back()->with('error', 'اسم المادة مطلوب.');
            }

            if ($request->branches_id === 'allbranches') {

                $branches = Branch::where('company_code', auth()->user()->company_code)->get();

                foreach ($branches as $branch) {

                    $exists = Inventory::where([
                        'company_code' => auth()->user()->company_code,
                        'name'         => $materialName,
                        'branch_id'    => $branch->id,
                    ])->exists();

                    if (!$exists) {
                        do {
                            $code = strtoupper(Str::random(5));
                        } while (Inventory::where('code', $code)->exists());

                        Inventory::create([
                            'company_code'    => auth()->user()->company_code,
                            'name'            => $materialName,
                            'branch_id'       => $branch->id,
                            'unit'            => $request->unit,
                            'code'  => $code,
                            'quantity_total'  => 0,
                            'note'            => $request->note,
                        ]);
                    }
                }

                return back()->with('success', 'تم إضافة المادة الرئيسية بنجاح إلى جميع الفروع.');
            } else {

                $exists = Inventory::where([
                    'company_code' => auth()->user()->company_code,
                    'name'         => $materialName,
                    'branch_id'    => $request->branches_id,
                ])->exists();

                if ($exists) {
                    return back()->with('error', 'هذه المادة مضافة مسبقًا إلى هذا الفرع.');
                }

                do {
                    $code = strtoupper(Str::random(5));
                } while (Inventory::where('code', $code)->exists());

                Inventory::create([
                    'company_code'    => auth()->user()->company_code,
                    'name'            => $materialName,
                    'branch_id'       => $request->branches_id,
                    'unit'            => $request->unit,
                    'quantity_total'  => 0,
                    'code'  => $code,
                    'note'            => $request->note,
                ]);

                return back()->with('success', 'تم إضافة المادة الرئيسية بنجاح إلى الفرع المحدد.');
            }
        }

        if (trim((string) $request->input('active', '')) === 'AddNewSupplier') {
            $request->validate([
                'supplier_name' => 'required|string|max:150',
                'branch_id' => 'required|exists:branches,id',
                'company_name' => 'nullable|string|max:150',
                'opening_balance' => 'nullable',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:255',
                'note' => 'nullable|string|max:500',
            ]);

            $rawOpening = str_replace([',', ' '], '', trim((string) $request->opening_balance));
            $price = ($rawOpening === '' || ! is_numeric($rawOpening)) ? 0 : $rawOpening;
            $normalizedSupplierName = trim((string) $request->supplier_name);
            $normalizedCompanyName = trim((string) $request->company_name);
            $normalizedPhone = preg_replace('/\D+/', '', (string) $request->phone);
            $normalizedAddress = trim((string) $request->address);
            $normalizedNote = trim((string) $request->note);
            $companyCode = auth()->user()->company_code;

            // منع التكرار: اسم المورد + الفرع + الهاتف + العنوان
            $exists = Supplier::where('company_code', $companyCode)
                ->where('supplier_name', $normalizedSupplierName)
                ->where('branch_id', $request->branch_id)
                ->where('phone', $normalizedPhone)
                ->where('address', $normalizedAddress)
                ->exists();

            if ($exists) {
                return $this->redirectAfterAddSupplier(
                    $request,
                    'error',
                    'المورد مضاف مسبقًا بنفس البيانات (الاسم، الفرع، الهاتف، العنوان). لا يمكن تكرار القيد.',
                    true
                );
            }

            // تحقق إضافي: نفس الاسم والفرع فقط (تحذير)
            $sameName = Supplier::where('company_code', $companyCode)
                ->where('supplier_name', $normalizedSupplierName)
                ->where('branch_id', $request->branch_id)
                ->exists();

            if ($sameName) {
                return $this->redirectAfterAddSupplier(
                    $request,
                    'error',
                    'يوجد مورد بنفس الاسم في هذا الفرع. تحقق من البيانات أو استخدم اسماً مختلفاً.',
                    true
                );
            }

            try {
                Supplier::create([
                    'supplier_name' => $normalizedSupplierName,
                    'company_code' => $companyCode,
                    'branch_id' => $request->branch_id,
                    'company_name' => $normalizedCompanyName,
                    'opening_balance' => (float) $price,
                    'phone' => $normalizedPhone,
                    'address' => $normalizedAddress,
                    'note' => $normalizedNote === '' ? null : $normalizedNote,
                ]);
            } catch (QueryException $e) {
                if ((string) $e->getCode() === '23000') {
                    return $this->redirectAfterAddSupplier(
                        $request,
                        'error',
                        'المورد موجود مسبقاً. تم منع تكرار القيد.',
                        true
                    );
                }
                throw $e;
            }

            return $this->redirectAfterAddSupplier($request, 'success', 'تمت إضافة المورد بنجاح.');
        }

        if ($request->active == "AddNewChemical") {
            $chemicalName = trim((string) $request->name);
            if ($chemicalName === '') {
                return back()->with('error', 'اسم المادة الكيميائية مطلوب.');
            }

            $companyCode = auth()->user()->company_code;

            $dupTemplate = Chemical::where('name', $chemicalName)
                ->whereNull('branch_id')
                ->where('company_code', $companyCode)
                ->exists();

            if ($dupTemplate) {
                return back()->with('error', 'هذه المادة موجودة كقالب شركة مسبقاً.');
            }

            $branches = Branch::where('company_code', $companyCode)->get();

            DB::beginTransaction();
            try {
                // قالب شركة (لهذا يُنشأ فرعاً جديداً لاحقاً ويُنسخ إليه تلقائياً)
                Chemical::create([
                    'company_code' => $companyCode,
                    'branch_id' => null,
                    'name' => $chemicalName,
                    'unit' => $request->unit,
                    'quantity_total' => 0,
                    'unit_cost' => 0,
                    'description' => $request->description,
                ]);

                foreach ($branches as $branch) {
                    $existsBranch = Chemical::where('company_code', $companyCode)
                        ->where('branch_id', $branch->id)
                        ->where('name', $chemicalName)
                        ->exists();

                    if ($existsBranch) {
                        continue;
                    }

                    Chemical::create([
                        'company_code' => $companyCode,
                        'branch_id' => $branch->id,
                        'name' => $chemicalName,
                        'unit' => $request->unit,
                        'quantity_total' => 0,
                        'unit_cost' => 0,
                        'description' => $request->description,
                    ]);
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();

                throw $e;
            }

            return back()->with(
                'success',
                'تم إضافة المادة لجميع فروع الشركة الحالية؛ وسيُنشأ نفس التعريف تلقائياً عند إضافة فرع جديد.'
            );
        }

        return redirect()->route('warehouse.show', 'addMainMaterials')
            ->with('error', 'تعذر تنفيذ العملية: نوع الطلب غير معروف.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id == "addMainMaterials") {
            $allmaterials = Inventory::with('companyName')->where('company_code', auth()->user()->company_code)->get();
            $MeasurementUnit = MeasurementUnit::all();
            $Branches = Branch::where('company_code', auth()->user()->company_code)->get();
            return view('warehouse.allMainMaterials', compact('allmaterials', 'MeasurementUnit', 'Branches'));
        }
        if ($id == "addSupplier") {
            $allSuppliers = Supplier::where('company_code', auth()->user()->company_code)
                ->with('branchName')
                ->withSum('payments', 'amount')
                ->get();
            $Branches = Branch::where('company_code', auth()->user()->company_code)->get();
            return view('warehouse.allSupplier', compact('allSuppliers', 'Branches'));
        }

        if ($id == 'listchemicals') {
            $companyCode = auth()->user()->company_code;

            // تجنّب تكرار الاسم بالجمع بين القالب وسجلات الفروع: إن كان هناك سجلات مرتبطة بفروع نعرضها فقط؛ وإلا نعرض قوالب الشركة (توافق ترقية أو عدم وجود فروع بعد)
            $branchChemicalCount = Chemical::where('company_code', $companyCode)->whereNotNull('branch_id')->count();

            $listChemical = Chemical::where('company_code', $companyCode)
                ->with(['branchName', 'MeasurementUnit'])
                ->when(
                    $branchChemicalCount > 0,
                    fn ($q) => $q->whereNotNull('branch_id'),
                    fn ($q) => $q->whereNull('branch_id')
                )
                ->get();
            $MeasurementUnit = MeasurementUnit::all();
            $Branches = Branch::where('company_code', auth()->user()->company_code)->get();
            return view('concretemix.listchemicals', compact('listChemical', 'Branches', 'MeasurementUnit'));
        }

        if ($id == 'CompanyListConcreteMix') {

            $ConcreteMix = ConcreteMix::where('company_code', Auth::user()->company_code)
                ->whereNull('branch_id')
                ->with(['categoryPrices.pricingCategory'])
                ->orderBy('branch_id', 'desc')
                ->get();

            $categories = PricingCategory::active()->ordered()->get();

            return view('concretemix.CompanyListConcreteMix', compact('ConcreteMix', 'categories'));
        }

        if ($id == 'BranchConcreteMix') {

            $ConcreteMix = ConcreteMix::where('company_code', Auth::user()->company_code)
                ->whereNull('branch_id')
                ->with([
                    'categoryPrices.pricingCategory',
                    'cementInventory',
                    'sandInventory',
                    'gravelInventory',
                    'waterInventory',
                    'chemicals',
                ])
                ->orderBy('branch_id', 'desc')
                ->get();

            $categories = PricingCategory::active()->ordered()->get();

            return view('concretemix.BranchConcreteMix', compact('ConcreteMix', 'categories'));
        }

        if ($id == 'Branchlistchemicals') {

            $listChemical = Chemical::where('company_code', auth()->user()->company_code)->where('branch_id', auth()->user()->branch_id)->get();
            $MeasurementUnit = MeasurementUnit::all();
            return view('concretemix.Branchlistchemicals', compact('listChemical', 'MeasurementUnit'));
        }

        if ($id == "addMainMaterialsBranch") {
            $allmaterials = Inventory::with('companyName')->where('company_code', auth()->user()->company_code)
                ->where('branch_id', auth()->user()->branch_id)->get();
            $MeasurementUnit = MeasurementUnit::all();
            return view('warehouse.addMainMaterialsBranch', compact('allmaterials', 'MeasurementUnit'));
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

        if (isset($explode[1]) && $explode[1] === "reportLoss") {
            $accessCheck = $this->ensureWarehouseAccess();
            if ($accessCheck) {
                return $accessCheck;
            }
        }

        if ($explode[1] == "edit_MainMaterials") {
            $material = Inventory::where('id', $explode[0])->first();
            $MeasurementUnit = MeasurementUnit::all();
            return view('warehouse.editMainMaterials', compact('material', 'MeasurementUnit'));
        }

        if ($explode[1] == "edit_Supplier") {
            $Supplier = Supplier::where('id', $explode[0])->first();
            $Branches = Branch::where('company_code', auth()->user()->company_code)->get();
            $hasHistory = $Supplier->Supplier_InventoryHistory()->exists();
            return view('warehouse.editSupplier', compact('Supplier', 'Branches', 'hasHistory'));
        }

        if ($explode[1] == "addShipment") {
            $material = Inventory::where('code', $explode[0])->first();
            // dd();
            $Supplier = Supplier::where('company_code', auth()->user()->company_code)->where('branch_id', $material->branch_id)->get();

            if ($Supplier->isEmpty()) {
                return back()->with('warning', 'لا يوجد مورد مواد في الفرع');
            }
            $ReturnUrl = $explode[2];



            $companyCards = \App\Models\CompanyPaymentCard::where('company_code', auth()->user()->company_code)
                ->where('is_active', true)
                ->orderBy('card_name')
                ->get();

            return view('warehouse.addShipment', compact('material', 'Supplier', 'ReturnUrl', 'companyCards'));
        }

        if ($explode[1] == "reportLoss") {
            $material = Inventory::where('code', $explode[0])
                ->where('company_code', auth()->user()->company_code)
                ->where('branch_id', auth()->user()->branch_id)
                ->firstOrFail();

            $name = mb_strtolower((string) $material->name);
            $isCement = str_contains($name, 'اسمنت') || str_contains($name, 'إسمنت') || str_contains($name, 'cement');

            // ملاحظة: في النظام الحالي المواد ذات unit=ton يتم تخزينها غالباً "بالأكياس" (×20 عند إضافة الشحنات).
            // المطلوب للأسمنت: العرض بالأكياس، والسعر على الكيس.
            if ($isCement) {
                $qtyDisplayAvailable = (float) $material->quantity_total; // عدد الأكياس
                $unitPriceDisplay = (float) $material->unit_cost; // سعر الكيس
                $displayUnitLabel = 'كيس';
            } else {
                $qtyDisplayAvailable = $material->unit === 'ton'
                    ? ((float) $material->quantity_total / 20)
                    : (float) $material->quantity_total;
                $unitPriceDisplay = (float) $material->unit_cost;
                $displayUnitLabel = $material->MeasurementUnit?->name ?? $material->unit;
            }

            return view('warehouse.reportLoss', compact('material', 'qtyDisplayAvailable', 'unitPriceDisplay', 'displayUnitLabel'));
        }

        if ($explode[1] == "ViewInventoryHistories") {

            $ViewInventoryHistories = InventoryHistory::where('material_code', $explode[0])->get();
            if ($ViewInventoryHistories->isEmpty()) {
                return redirect('warehouse/addMainMaterials')->with('warning', ' لا توجد شحنات في المادة.');
            }
            return view('warehouse.ViewInventoryHistories', compact('ViewInventoryHistories'));
        }

        if ($explode[1] == "EditChemical") {
            $EditChemical = Chemical::where('id', $explode[0])->first();
            $MeasurementUnit = MeasurementUnit::all();
            return view('concretemix.EditChemical', compact('EditChemical', 'MeasurementUnit'));
        }

        if ($explode[1] == "AddChemicalShipment") {


            $Chemical = Chemical::where('id', $explode[0])->first();

            $Supplier = Supplier::where('company_code', auth()->user()->company_code)->where('branch_id', $Chemical->branch_id)->get();

            if ($Supplier->isEmpty()) {
                return back()->with('warning', 'لا يوجد مورد مواد في الفرع');
            }

            $ReturnUrl = $explode[2];
            $companyCards = \App\Models\CompanyPaymentCard::where('company_code', auth()->user()->company_code)
                ->where('is_active', true)
                ->orderBy('card_name')
                ->get();

            return view('warehouse.AddChemicalShipment', compact('Chemical', 'Supplier', 'ReturnUrl', 'companyCards'));
        }

        if ($explode[1] == "reportChemicalLoss") {
            $chemical = Chemical::where('id', $explode[0])
                ->where('company_code', auth()->user()->company_code)
                ->where('branch_id', auth()->user()->branch_id)
                ->firstOrFail();

            $qtyDisplayAvailable = (float) $chemical->quantity_total;
            $unitPriceDisplay = (float) $chemical->unit_cost;

            return view('warehouse.reportChemicalLoss', compact('chemical', 'qtyDisplayAvailable', 'unitPriceDisplay'));
        }

        if ($explode[1] == "ViewChemicalInventoryHistories") {

            $ViewInventoryHistories = InventoryHistory::where('material_code', $explode[0])->get();
            if ($ViewInventoryHistories->isEmpty()) {
                return redirect('warehouse/listchemicals')->with('warning', ' لا توجد شحنات في المادة.');
            }
            return view('warehouse.ViewInventoryHistories', compact('ViewInventoryHistories'));
        }


        if ($explode[1] == "EditQuantitiesConcreteMix") {

            $editConcreteMix = ConcreteMix::where('id', $explode[0])->first();
            // $listChemical = Chemical::where('company_code', auth()->user()->company_code)->where('branch_id', $editConcreteMix->branch_id)->get();
            $chemicalList = Chemical::where('company_code', auth()->user()->company_code)
                ->when(
                    is_null($editConcreteMix->branch_id),
                    fn($q) => $q->whereNull('branch_id'),
                    fn($q) => $q->where('branch_id', $editConcreteMix->branch_id)
                )
                ->with(['concreteMixes' => function ($q) use ($editConcreteMix) {
                    $q->where('concrete_mix_id', $editConcreteMix->id);
                }])
                ->get();

            $categories = PricingCategory::active()->ordered()->get();
            $categoryPrices = ConcreteMixCategoryPrice::forCompany(auth()->user()->company_code)
                ->forMix($editConcreteMix->id)
                ->get()
                ->keyBy('pricing_category_id');

            return view('materials.EditQuantitiesConcreteMix', compact('editConcreteMix', 'chemicalList', 'categories', 'categoryPrices'));
        }

        if ($explode[1] == "ViewQuantitiesConcreteMix") {

            $ConcreteMix = ConcreteMix::where('id', $explode[0])->first();


            $ConcreteMixChemical = ConcreteMixChemical::where('concrete_mix_id', $ConcreteMix->id)->get();


            return view('concretemix.DetailsConcreteMix', compact('ConcreteMixChemical', 'ConcreteMix'));
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
        if ($request->active == "EditMainMaterials") {
            Inventory::where('id', $id)->update([
                'name' => $request->name,
                'unit' => $request->unit,
                'note' => $request->note,
            ]);
            return redirect('warehouse/addMainMaterials')->with('success', 'تم تحديث بيانات المادة الرئيسية بنجاح.');
        }
        if ($request->active == "UpdateSupplierinformation") {

            $price = str_replace(',', '', $request->opening_balance);


            Supplier::where('id', $id)->update([
                'supplier_name' => $request->supplier_name,
                'branch_id' => $request->branch_id,
                'opening_balance' => $price,
                'company_name' => $request->company_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'note' => $request->note,

            ]);
            return redirect('warehouse/addSupplier')->with('success', 'تم تحديث بيانات د بنجاح.');
        }


        if ($request->active == "AddNewShipment") {

            // نفس منطق شحنات المواد الكيميائية: كمية رقم واحد + (آجل/فوري) مع دفع جزئي ونقدي/إلكتروني
            $request->merge([
                'price' => str_replace(',', '', (string) $request->input('price')),
                'paid_amount' => str_replace(',', '', (string) $request->input('paid_amount')),
            ]);

            $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'quantity' => 'required|numeric|min:0.0001',
                'price' => 'required|numeric|min:0.01',
                'payment_term' => 'required|in:deferred,immediate',
                'payment_method' => 'required_if:payment_term,immediate|nullable|in:cash,online',
                'paid_amount' => 'required_if:payment_term,immediate|nullable|numeric|min:0.01',
                'company_payment_card_id' => 'required_if:payment_method,online|nullable|exists:company_payment_cards,id',
                'note' => 'nullable|string|max:500',
            ]);

            $price = (float) str_replace(',', '', (string) $request->price);
            $paidAmount = $request->payment_term === 'immediate'
                ? (float) str_replace(',', '', (string) $request->paid_amount)
                : 0.0;

            if ($request->payment_term === 'immediate' && $paidAmount > $price) {
                return back()->withInput()->with('error', 'مبلغ الدفع الفوري لا يمكن أن يكون أكبر من قيمة الشحنة.');
            }

            $qtyBase = (float) $request->quantity;
            if ($qtyBase <= 0) {
                return back()->withInput()->with('error', 'الكمية يجب أن تكون أكبر من صفر.');
            }

            $unitCost = $price / $qtyBase;
            $materialUnit = (string) $request->material_unit;

            DB::beginTransaction();
            try {
                InventoryHistory::create([
                    'material_code' => $id,
                    'company_code' => auth()->user()->company_code,
                    'branch_id' => $request->branch_id,
                    'supplier_id' => $request->supplier_id,
                    'unit_capacity' => 1,
                    'unit_code' => $materialUnit,
                    'countUnit' => $qtyBase,
                    'total_cost' => $price,
                    'shipment_date' => now(),
                    'user_id' => auth()->user()->id,
                    'note' => $request->note,
                ]);

                $supplier = Supplier::where('id', $request->supplier_id)->lockForUpdate()->firstOrFail();
                $supplier->increment('opening_balance', $price);

                $inventory = Inventory::where('code', $id)->lockForUpdate()->firstOrFail();
                if ((float) $inventory->quantity_total != 0) {
                    $avgUnitCost = ($inventory->quantity_total * $inventory->unit_cost + $qtyBase * $unitCost)
                        / ($inventory->quantity_total + $qtyBase);
                    $inventory->unit_cost = $avgUnitCost;
                } else {
                    $inventory->unit_cost = $unitCost;
                }
                $inventory->quantity_total = (float) $inventory->quantity_total + $qtyBase;
                $inventory->save();

                if ($request->payment_term === 'immediate') {
                    $supplier->refresh();
                    $balanceBefore = (float) $supplier->remaining_balance;
                    $payAmount = min($paidAmount, $balanceBefore);

                    if ($payAmount > 0) {
                        $payment = \App\Models\SupplierPayment::create([
                            'payment_number' => \App\Models\SupplierPayment::generatePaymentNumber(auth()->user()->company_code),
                            'supplier_id' => $supplier->id,
                            'company_code' => auth()->user()->company_code,
                            'branch_id' => $supplier->branch_id,
                            'amount' => $payAmount,
                            'balance_before' => $balanceBefore,
                            'balance_after' => $balanceBefore - $payAmount,
                            'payment_method' => $request->payment_method,
                            'company_payment_card_id' => $request->payment_method === 'online' ? $request->company_payment_card_id : null,
                            'notes' => $request->note,
                            'created_by' => auth()->id(),
                        ]);

                        if ($request->payment_method === 'online' && $request->company_payment_card_id) {
                            // الدفع الإلكتروني: خصم من البطاقة
                            $card = \App\Models\CompanyPaymentCard::where('id', $request->company_payment_card_id)
                                ->where('company_code', auth()->user()->company_code)
                                ->lockForUpdate()
                                ->firstOrFail();

                            $card->withdraw(
                                $payAmount,
                                'دفع فوري لشحنة مادة (مخزن) - إيصال ' . $payment->payment_number,
                                'supplier_payment',
                                $payment->id,
                                $supplier->branch_id
                            );
                        } elseif ($request->payment_method === 'cash') {
                            // الدفع النقدي: تسجيل في سجل الصندوق + سند صرف
                            $branchId = $request->branch_id ?? $supplier->branch_id ?? auth()->user()->branch_id;
                            
                            // إنشاء سند صرف للمورد
                            \App\Models\PaymentVoucher::create([
                                'voucher_number' => \App\Models\PaymentVoucher::generateVoucherNumber(auth()->user()->company_code, $branchId),
                                'company_code' => auth()->user()->company_code,
                                'branch_id' => $branchId,
                                'payee_type' => 'supplier',
                                'payee_id' => $supplier->id,
                                'payee_name' => $supplier->supplier_name,
                                'amount' => $payAmount,
                                'currency_code' => 'IQD',
                                'exchange_rate' => 1,
                                'amount_in_default' => $payAmount,
                                'payment_method' => 'cash',
                                'description' => 'دفع نقدي لشحنة مادة (مخزن) - إيصال ' . $payment->payment_number,
                                'related_type' => 'supplier_payment',
                                'related_id' => $payment->id,
                                'requires_approval' => false,
                                'status' => 'paid',
                                'paid_by' => auth()->id(),
                                'paid_at' => now(),
                                'created_by' => auth()->id(),
                            ]);

                            // تسجيل حركة خروج من الصندوق
                            \App\Models\CashRegister::addEntry(
                                $branchId,
                                'cash_out',
                                $payAmount,
                                [
                                    'company_code' => auth()->user()->company_code,
                                    'description' => 'دفع نقدي للمورد: ' . $supplier->supplier_name . ' - شحنة مخزن',
                                    'payment_id' => $payment->id,
                                    'handled_by' => auth()->id(),
                                ]
                            );
                        }
                    }
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->withInput()->with('error', 'تعذر حفظ الشحنة: ' . $e->getMessage());
            }

            if ($request->ReturnUrl == "caompanyAdmin") {
                return redirect('warehouse/addMainMaterials')->with('success', 'تم اضافة تفاصيل الشحنة الجديدة.');
            }
            if ($request->ReturnUrl == "branch") {
                return redirect('warehouse/addMainMaterialsBranch')->with('success', 'تم اضافة تفاصيل الشحنة الجديدة.');
            }

            return back();
        }

        if ($request->active == "AddNewChemicalShipment") {
            $request->merge([
                'price' => str_replace(',', '', (string) $request->input('price')),
                'paid_amount' => str_replace(',', '', (string) $request->input('paid_amount')),
            ]);

            $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'quantity' => 'required|numeric|min:0.0001',
                'price' => 'required|numeric|min:0.01',
                'payment_term' => 'required|in:deferred,immediate',
                'payment_method' => 'required_if:payment_term,immediate|nullable|in:cash,online',
                'paid_amount' => 'required_if:payment_term,immediate|nullable|numeric|min:0.01',
                'company_payment_card_id' => 'required_if:payment_method,online|nullable|exists:company_payment_cards,id',
                'note' => 'nullable|string|max:500',
            ]);

            $price = (float) str_replace(',', '', (string) $request->price);
            $paidAmount = $request->payment_term === 'immediate'
                ? (float) str_replace(',', '', (string) $request->paid_amount)
                : 0.0;

            if ($request->payment_term === 'immediate' && $paidAmount > $price) {
                return back()->withInput()->with('error', 'مبلغ الدفع الفوري لا يمكن أن يكون أكبر من قيمة الشحنة.');
            }

            $quantity_total = (float) $request->quantity;
            $unit_cost = $quantity_total > 0 ? ($price / $quantity_total) : 0;

            DB::beginTransaction();
            try {
                InventoryHistory::create([
                    'material_code' => $id,
                    'company_code' => auth()->user()->company_code,
                    'branch_id' => $request->branch_id,
                    'supplier_id' => $request->supplier_id,
                    'unit_capacity' => 1,
                    'unit_code' => $request->material_unit,
                    'countUnit' => $quantity_total,
                    'total_cost' => $price,
                    'shipment_date' => now(),
                    'user_id' => auth()->user()->id,
                    'note' => $request->note,
                ]);

                // تُسجّل تكلفة الشحنة كرصيد مستحق على المورد
                $supplier = Supplier::where('id', $request->supplier_id)->lockForUpdate()->firstOrFail();
                $supplier->increment('opening_balance', $price);

                $Chemical_qt = Chemical::where('id', $id)->lockForUpdate()->firstOrFail();
                if ((float) $Chemical_qt->quantity_total != 0) {
                    $avar_unit_cost = ($Chemical_qt->quantity_total * $Chemical_qt->unit_cost + $quantity_total * $unit_cost) / ($Chemical_qt->quantity_total + $quantity_total);
                    $Chemical_qt->unit_cost = $avar_unit_cost;
                } else {
                    $Chemical_qt->unit_cost = $unit_cost;
                }
                $Chemical_qt->quantity_total = (float) $Chemical_qt->quantity_total + $quantity_total;
                $Chemical_qt->save();

                // الدفع الفوري: ينشئ دفعة للمورد مباشرة (نقدي أو إلكتروني)
                if ($request->payment_term === 'immediate') {
                    $supplier->refresh();
                    $balanceBefore = (float) $supplier->remaining_balance;
                    $payAmount = min($paidAmount, $balanceBefore);

                    if ($payAmount > 0) {
                        $payment = \App\Models\SupplierPayment::create([
                            'payment_number' => \App\Models\SupplierPayment::generatePaymentNumber(auth()->user()->company_code),
                            'supplier_id' => $supplier->id,
                            'company_code' => auth()->user()->company_code,
                            'branch_id' => $supplier->branch_id,
                            'amount' => $payAmount,
                            'balance_before' => $balanceBefore,
                            'balance_after' => $balanceBefore - $payAmount,
                            'payment_method' => $request->payment_method,
                            'company_payment_card_id' => $request->payment_method === 'online' ? $request->company_payment_card_id : null,
                            'notes' => $request->note,
                            'created_by' => auth()->id(),
                        ]);

                        if ($request->payment_method === 'online' && $request->company_payment_card_id) {
                            // الدفع الإلكتروني: خصم من البطاقة
                            $card = \App\Models\CompanyPaymentCard::where('id', $request->company_payment_card_id)
                                ->where('company_code', auth()->user()->company_code)
                                ->lockForUpdate()
                                ->firstOrFail();

                            $card->withdraw(
                                $payAmount,
                                'دفع فوري لشحنة مادة كيميائية - إيصال ' . $payment->payment_number,
                                'supplier_payment',
                                $payment->id,
                                $supplier->branch_id
                            );
                        } elseif ($request->payment_method === 'cash') {
                            // الدفع النقدي: تسجيل في سجل الصندوق + سند صرف
                            $branchId = $request->branch_id ?? $supplier->branch_id ?? auth()->user()->branch_id;
                            
                            // إنشاء سند صرف للمورد
                            \App\Models\PaymentVoucher::create([
                                'voucher_number' => \App\Models\PaymentVoucher::generateVoucherNumber(auth()->user()->company_code, $branchId),
                                'company_code' => auth()->user()->company_code,
                                'branch_id' => $branchId,
                                'payee_type' => 'supplier',
                                'payee_id' => $supplier->id,
                                'payee_name' => $supplier->supplier_name,
                                'amount' => $payAmount,
                                'currency_code' => 'IQD',
                                'exchange_rate' => 1,
                                'amount_in_default' => $payAmount,
                                'payment_method' => 'cash',
                                'description' => 'دفع نقدي لشحنة مادة كيميائية - إيصال ' . $payment->payment_number,
                                'related_type' => 'supplier_payment',
                                'related_id' => $payment->id,
                                'requires_approval' => false,
                                'status' => 'paid',
                                'paid_by' => auth()->id(),
                                'paid_at' => now(),
                                'created_by' => auth()->id(),
                            ]);

                            // تسجيل حركة خروج من الصندوق
                            \App\Models\CashRegister::addEntry(
                                $branchId,
                                'cash_out',
                                $payAmount,
                                [
                                    'company_code' => auth()->user()->company_code,
                                    'description' => 'دفع نقدي للمورد: ' . $supplier->supplier_name . ' - شحنة كيميائية',
                                    'payment_id' => $payment->id,
                                    'handled_by' => auth()->id(),
                                ]
                            );
                        }
                    }
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return back()->withInput()->with('error', 'تعذر حفظ الشحنة: ' . $e->getMessage());
            }

            if ($request->ReturnUrl == "companyadmin") {
                return redirect('warehouse/listchemicals')->with('success', 'تم إضافة الشحنة بنجاح.');
            }
            if ($request->ReturnUrl == "branch") {
                return redirect('warehouse/Branchlistchemicals')->with('success', 'تم إضافة الشحنة بنجاح.');
            }
        }

        if ($request->active == "ReportInventoryLoss") {
            $accessCheck = $this->ensureWarehouseAccess();
            if ($accessCheck) {
                return $accessCheck;
            }

            $request->validate([
                'loss_quantity' => 'required|numeric|min:0.0001',
                'note' => 'nullable|string|max:500',
            ]);

            $material = Inventory::where('code', $id)
                ->where('company_code', auth()->user()->company_code)
                ->where('branch_id', auth()->user()->branch_id)
                ->lockForUpdate()
                ->firstOrFail();

            $qtyDisplay = (float) $request->loss_quantity;
            $name = mb_strtolower((string) $material->name);
            $isCement = str_contains($name, 'اسمنت') || str_contains($name, 'إسمنت') || str_contains($name, 'cement');
            // للأسمنت: الإدخال بالأكياس والخصم بالأكياس (كما هو مخزن)
            $qtyBase = $isCement
                ? $qtyDisplay
                : ($material->unit === 'ton' ? ($qtyDisplay * 20) : $qtyDisplay);

            if ($qtyBase > (float) $material->quantity_total) {
                return back()->with('error', 'الكمية التالفة أكبر من الكمية المتوفرة')->withInput();
            }

            $unitCostBase = (float) ($material->unit_cost ?? 0);
            $unitPriceDisplay = $unitCostBase;
            $totalCost = $unitCostBase * $qtyBase;

            DB::beginTransaction();
            try {
                $loss = InventoryLoss::create([
                    'company_code' => auth()->user()->company_code,
                    'branch_id' => auth()->user()->branch_id,
                    'material_type' => 'inventory',
                    'material_code' => $material->code,
                    'material_id' => $material->id,
                    'material_name' => $material->name,
                    'unit' => $material->unit,
                    'quantity_lost' => $qtyDisplay,
                    'quantity_base' => $qtyBase,
                    'unit_cost' => $unitCostBase,
                    'unit_price_display' => $unitPriceDisplay,
                    'total_cost' => $totalCost,
                    'note' => $request->note,
                    'created_by' => auth()->id(),
                    'reported_at' => now(),
                ]);

                Inventory::where('id', $material->id)->update([
                    'quantity_total' => DB::raw('quantity_total - ' . (float) $qtyBase),
                ]);

                DB::commit();

                return redirect()->route('warehouse.losses.print', $loss->id)
                    ->with('success', 'تم تسجيل الإتلاف بنجاح');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
            }
        }

        if ($request->active == "ReportChemicalLoss") {
            $accessCheck = $this->ensureWarehouseAccess();
            if ($accessCheck) {
                return $accessCheck;
            }

            $request->validate([
                'loss_quantity' => 'required|numeric|min:0.0001',
                'note' => 'nullable|string|max:500',
            ]);

            $chemical = Chemical::where('id', $id)
                ->where('company_code', auth()->user()->company_code)
                ->where('branch_id', auth()->user()->branch_id)
                ->lockForUpdate()
                ->firstOrFail();

            $qtyDisplay = (float) $request->loss_quantity;
            $qtyBase = $qtyDisplay;

            if ($qtyBase > (float) $chemical->quantity_total) {
                return back()->with('error', 'الكمية التالفة أكبر من الكمية المتوفرة')->withInput();
            }

            $unitCostBase = (float) ($chemical->unit_cost ?? 0);
            $unitPriceDisplay = $unitCostBase;
            $totalCost = $unitCostBase * $qtyBase;

            DB::beginTransaction();
            try {
                $loss = InventoryLoss::create([
                    'company_code' => auth()->user()->company_code,
                    'branch_id' => auth()->user()->branch_id,
                    'material_type' => 'chemical',
                    'material_code' => null,
                    'material_id' => $chemical->id,
                    'material_name' => $chemical->name,
                    'unit' => $chemical->unit,
                    'quantity_lost' => $qtyDisplay,
                    'quantity_base' => $qtyBase,
                    'unit_cost' => $unitCostBase,
                    'unit_price_display' => $unitPriceDisplay,
                    'total_cost' => $totalCost,
                    'note' => $request->note,
                    'created_by' => auth()->id(),
                    'reported_at' => now(),
                ]);

                Chemical::where('id', $chemical->id)->update([
                    'quantity_total' => DB::raw('quantity_total - ' . (float) $qtyBase),
                ]);

                DB::commit();

                return redirect()->route('warehouse.losses.print', $loss->id)
                    ->with('success', 'تم تسجيل الإتلاف بنجاح');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
            }
        }

        if ($request->active == "UpdateChemical") {

            Chemical::where('id', $id)->update([
                'name' => $request->name,
                'unit' => $request->unit,
                'description' => $request->description
            ]);
            return redirect('warehouse/listchemicals')->with('success', 'تم تحديث معلومات المادة الكيميائية بنجاح');
        }

        if ($request->active == "EditQuantitiesConcreteMix") {

            $checkConcreteMix = ConcreteMix::where('company_code', auth()->user()->company_code)->where('classification', $request->classification)->exists();

            if ($checkConcreteMix) {

                $request->merge([
                    'costPrice' => str_replace(',', '', (string) $request->input('costPrice')),
                ]);

                // تنسيق حقول الأسعار حسب الفئات السعرية
                $categoryPricesInput = (array) $request->input('category_price', []);
                $normalizedCategoryPrices = [];
                foreach ($categoryPricesInput as $k => $v) {
                    $normalizedCategoryPrices[$k] = str_replace(',', '', (string) $v);
                }
                $request->merge([
                    'category_price' => $normalizedCategoryPrices,
                ]);

                $request->validate([
                    'cement' => 'nullable|numeric|min:0',
                    'sand' => 'nullable|numeric|min:0',
                    'gravel' => 'nullable|numeric|min:0',
                    'water' => 'nullable|numeric|min:0',
                    'costPrice' => 'nullable|numeric|min:0',
                    'notes' => 'nullable|string|max:1000',
                    'category_price' => 'array',
                    'category_price.*' => 'nullable|numeric|min:0',
                ]);

                ConcreteMix::where('id', $id)->update([
                    'sand' => $request->sand,
                    'cement' => $request->cement,
                    'gravel' => $request->gravel,
                    'water' => $request->water,
                    'costPrice' => $request->costPrice === '' ? 0 : $request->costPrice,
                    'notes' => $request->notes,
                ]);
                // حفظ المواد الكيميائية المرتبطة

                foreach ($request->all() as $key => $value) {
                    if (strpos($key, 'chemical_') === 0) {
                        $chemical_id = str_replace('chemical_', '', $key);

                        if ($value === null || $value === '' || $value == 0) {
                            // إذا كانت القيمة صفرية، يمكن حذف السجل القديم
                            ConcreteMixChemical::where('concrete_mix_id', $id)
                                ->where('chemical_id', $chemical_id)
                                ->delete();
                            continue;
                        }

                        // تحديث إذا موجود، أو إنشاء جديد إذا غير موجود
                        ConcreteMixChemical::updateOrCreate(
                            [
                                'concrete_mix_id' => $id,
                                'chemical_id' => $chemical_id,
                            ],
                            [
                                'quantity' => $value,
                            ]
                        );
                    }
                }

                // حفظ أسعار الخلطة حسب الفئات السعرية (سعر فقط)
                $companyCode = auth()->user()->company_code;
                $categoryPriceMap = (array) $request->input('category_price', []);

                foreach ($categoryPriceMap as $categoryId => $priceStr) {
                    $priceStr = (string) $priceStr;
                    $priceStr = trim($priceStr);

                    // إذا فاضي/صفر: نحذف السجل (ما نريد نخزن سجلات فارغة)
                    if ($priceStr === '' || (float) $priceStr <= 0) {
                        ConcreteMixCategoryPrice::where('company_code', $companyCode)
                            ->where('concrete_mix_id', $id)
                            ->where('pricing_category_id', $categoryId)
                            ->delete();
                        continue;
                    }

                    $price = (float) $priceStr;

                    ConcreteMixCategoryPrice::updateOrCreate(
                        [
                            'company_code' => $companyCode,
                            'concrete_mix_id' => $id,
                            'pricing_category_id' => (int) $categoryId,
                        ],
                        [
                            'price_per_meter' => $price,
                            'cost_per_meter' => null,
                            'notes' => null,
                            'is_active' => true,
                        ]
                    );
                }


                return redirect('warehouse/CompanyListConcreteMix')->with('success', 'تم تحديث الكميات ي المادة الخرسانية');
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
     * عرض تفاصيل المورد مع الدفعات
     */
    public function supplierDetails($id)
    {
        $supplier = Supplier::with(['payments' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'payments.createdBy', 'payments.paymentCard', 'branchName'])
            ->where('company_code', auth()->user()->company_code)
            ->findOrFail($id);

        $branches = Branch::where('company_code', auth()->user()->company_code)->get();

        return view('warehouse.supplierDetails', compact('supplier', 'branches'));
    }

    /**
     * تسجيل دفعة جديدة للمورد
     */
    public function storePayment(Request $request, $id)
    {
        $supplier = Supplier::where('company_code', auth()->user()->company_code)
            ->findOrFail($id);

        // إزالة الفواصل من المبلغ
        $amount = str_replace(',', '', $request->amount);

        // التحقق من البيانات
        $request->merge(['amount' => $amount]);
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check,online',
            'company_payment_card_id' => 'required_if:payment_method,online|nullable|exists:company_payment_cards,id',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $remainingBalance = $supplier->remaining_balance;

        // التحقق من أن الرصيد ليس صفر
        if ($remainingBalance <= 0) {
            return back()->with('error', 'لا يمكن التسديد، الرصيد المستحق صفر.');
        }

        // التحقق من أن المبلغ لا يتجاوز الرصيد المتبقي
        if ($amount > $remainingBalance) {
            return back()->with('error', 'مبلغ التسديد (' . number_format($amount, 2) . ') أكبر من الرصيد المتبقي (' . number_format($remainingBalance, 2) . ').');
        }

        // حساب الرصيد بعد الدفع
        $balanceAfter = $remainingBalance - $amount;

        // إنشاء الدفعة
        $payment = \App\Models\SupplierPayment::create([
            'payment_number' => \App\Models\SupplierPayment::generatePaymentNumber(auth()->user()->company_code),
            'supplier_id' => $supplier->id,
            'company_code' => auth()->user()->company_code,
            'branch_id' => $supplier->branch_id,
            'amount' => $amount,
            'balance_before' => $remainingBalance,
            'balance_after' => $balanceAfter,
            'payment_method' => $request->payment_method,
            'company_payment_card_id' => $request->company_payment_card_id,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        if ($request->payment_method === 'online' && $request->company_payment_card_id) {
            $card = \App\Models\CompanyPaymentCard::find($request->company_payment_card_id);
            if ($card) {
                $card->withdraw($amount, 'دفعة مورد: ' . $supplier->name . ' - إيصال ' . $payment->payment_number, 'supplier_payment', $payment->id, $supplier->branch_id);
            }
        }

        return redirect()->route('suppliers.details', $id)
            ->with('success', 'تم تسجيل الدفعة بنجاح. رقم الإيصال: ' . $payment->payment_number)
            ->with('print_payment_id', $payment->id);
    }

    /**
     * طباعة إيصال الدفعة
     */
    public function printPayment($id)
    {
        $payment = \App\Models\SupplierPayment::with(['supplier', 'supplier.branchName', 'createdBy'])
            ->where('company_code', auth()->user()->company_code)
            ->findOrFail($id);

        $company = Company::where('code', auth()->user()->company_code)->first();

        return view('warehouse.paymentReceipt', compact('payment', 'company'));
    }

    /**
     * التحقق من صلاحية قسم المستودع (مع السماح للإدارات العليا).
     */
    private function ensureWarehouseAccess()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin() || $user->isCompanyManager() || $user->isBranchManager()) {
            return null;
        }

        if ($user->emp_type_code === EmployeeType::CODE_WAREHOUSE) {
            return null;
        }

        return redirect()->back()->with('error', 'هذا القسم ضمن صلاحيات مسؤول المستودع فقط.');
    }

    /**
     * بعد إضافة مورد: العودة لصفحة الموردين (نفس الرابط قدر الإمكان عبر Referer الآمن).
     */
    private function redirectAfterAddSupplier(Request $request, string $flashKey, string $message, bool $withInput = false): \Illuminate\Http\RedirectResponse
    {
        $default = route('warehouse.show', 'addSupplier');
        $target = $default;
        $referer = $request->headers->get('referer');
        if (is_string($referer) && $referer !== '') {
            $refHost = parse_url($referer, PHP_URL_HOST);
            $refPath = (string) (parse_url($referer, PHP_URL_PATH) ?? '');
            if ($refHost === $request->getHost() && str_contains($refPath, 'warehouse/addSupplier')) {
                $target = $referer;
            }
        }

        $response = redirect()->to($target)->with($flashKey, $message);

        return $withInput ? $response->withInput() : $response;
    }
}
