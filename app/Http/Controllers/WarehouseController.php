<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Chemical;
use App\Models\Company;
use App\Models\ConcreteMix;
use App\Models\ConcreteMixChemical;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\MaterialEquipment;
use App\Models\MeasurementUnit;
use App\Models\PricingCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class WarehouseController extends Controller
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
        if ($request->active == "AddNewMainMaterials") {

            if ($request->branches_id === 'allbranches') {

                $branches = Branch::where('company_code', auth()->user()->company_code)->get();

                foreach ($branches as $branch) {

                    $exists = Inventory::where([
                        'company_code' => auth()->user()->company_code,
                        'name'         => $request->name,
                        'branch_id'    => $branch->id,
                    ])->exists();

                    if (!$exists) {
                        do {
                            $code = strtoupper(Str::random(5));
                        } while (Inventory::where('code', $code)->exists());

                        Inventory::create([
                            'company_code'    => auth()->user()->company_code,
                            'name'            => $request->name,
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
                    'name'         => $request->name,
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
                    'name'            => $request->name,
                    'branch_id'       => $request->branches_id,
                    'unit'            => $request->unit,
                    'quantity_total'  => 0,
                    'code'  => $code,
                    'note'            => $request->note,
                ]);

                return back()->with('success', 'تم إضافة المادة الرئيسية بنجاح إلى الفرع المحدد.');
            }
        }

        if ($request->active == "AddNewSupplier") {

            $price = str_replace(',', '', $request->opening_balance);

            // تحقق من وجود سجل مطابق
            $exists = Supplier::where('supplier_name', $request->supplier_name)
                ->where('company_code', auth()->user()->company_code)
                ->where('branch_id', $request->branch_id)
                ->where('company_name', $request->company_name)
                ->where('phone', $request->phone)
                ->exists();

            if ($exists) {
                return back()->with('error', 'المورد مضاف مسبقًا ولا يمكن تكراره.');
            }

            // إذا لم يوجد، أضف المورد الجديد
            Supplier::create([
                'supplier_name' => $request->supplier_name,
                'company_code' => auth()->user()->company_code,
                'branch_id' => $request->branch_id,
                'company_name' => $request->company_name,
                'opening_balance' => $price,
                'phone' => $request->phone,
                'address' => $request->address,
                'note' => $request->note,
            ]);

            return back()->with('success', 'تمت إضافة المورد بنجاح.');
        }

        if ($request->active == "AddNewChemical") {

            if ($request->branches_id === 'allbranches') {

                $branches = Branch::where('company_code', auth()->user()->company_code)->get();

                foreach ($branches as $branch) {

                    $exists = Chemical::where('name', $request->name)
                        ->where('branch_id', $request->branch_id)
                        ->where('company_code', auth()->user()->company_code)
                        ->exists();

                    if (!$exists) {

                        $NewChemical = new Chemical();
                        $NewChemical->company_code = auth()->user()->company_code;
                        $NewChemical->branch_id = $branch->id;
                        $NewChemical->name = $request->name;
                        $NewChemical->unit = $request->unit;
                        $NewChemical->description = $request->description;
                        $NewChemical->save();
                    }
                }

                return back()->with('success', 'تم إضافة المادة الكيميائية بنجاح إلى جميع الفروع.');
            } else {

                $exists = Chemical::where('name', $request->name)
                    ->where('branch_id', $request->branch_id)
                    ->where('company_code', auth()->user()->company_code)
                    ->exists();

                if (!$exists) {

                    $NewChemical = new Chemical();
                    $NewChemical->company_code = auth()->user()->company_code;
                    $NewChemical->branch_id = $request->branches_id;
                    $NewChemical->name = $request->name;
                    $NewChemical->unit = $request->unit;
                    $NewChemical->description = $request->description;
                    $NewChemical->save();
                }

                return back()->with('success', 'تم إضافة المادة الرئيسية بنجاح إلى الفرع المحدد.');
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
        if ($id == "addMainMaterials") {
            $allmaterials = Inventory::with('companyName')->where('company_code', auth()->user()->company_code)->get();
            $MeasurementUnit = MeasurementUnit::all();
            $Branches = Branch::where('company_code', auth()->user()->company_code)->get();
            return view('warehouse.allMainMaterials', compact('allmaterials', 'MeasurementUnit', 'Branches'));
        }
        if ($id == "addSupplier") {
            $allSuppliers = Supplier::where('company_code', auth()->user()->company_code)->get();
            $Branches = Branch::where('company_code', auth()->user()->company_code)->get();
            return view('warehouse.allSupplier', compact('allSuppliers', 'Branches'));
        }

        if ($id == 'listchemicals') {
            $listChemical = Chemical::where('company_code', auth()->user()->company_code)->get();
            $MeasurementUnit = MeasurementUnit::all();
            $Branches = Branch::where('company_code', auth()->user()->company_code)->get();
            return view('concretemix.listchemicals', compact('listChemical', 'Branches', 'MeasurementUnit'));
        }

        if ($id == 'CompanyListConcreteMix') {

            $ConcreteMix = ConcreteMix::where('company_code', Auth::user()->company_code)
                ->with(['categoryPrices.pricingCategory'])
                ->orderBy('branch_id', 'desc')
                ->get();

            $categories = PricingCategory::active()->ordered()->get();

            return view('concretemix.CompanyListConcreteMix', compact('ConcreteMix', 'categories'));
        }

        if ($id == 'BranchConcreteMix') {

            $ConcreteMix = ConcreteMix::where('company_code', Auth::user()->company_code)->where('branch_id', Auth::user()->branch_id)
                ->orderBy('branch_id', 'desc')
                ->get();
            return view('concretemix.BranchConcreteMix', compact('ConcreteMix'));
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

            // فلترة المعدات حسب نوع المادة (اسم المادة) + وحدة القياس
            $listMaterialEquipment = MaterialEquipment::where('company_code', auth()->user()->company_code)
                ->where('code', $material->unit)
                ->where(function ($query) use ($material) {
                    $query->where('material_type', $material->name)
                        ->orWhereNull('material_type')
                        ->orWhere('material_type', '');
                })
                ->get();

            if ($Supplier->isEmpty()) {
                return back()->with('warning', 'لا يوجد مورد مواد في الفرع');
            }
            $ReturnUrl = $explode[2];



            return view('warehouse.addShipment', compact('material', 'Supplier', 'listMaterialEquipment', 'ReturnUrl'));
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

            $listMaterialEquipment = MaterialEquipment::where('company_code', auth()->user()->company_code)->get();

            $Supplier = Supplier::where('company_code', auth()->user()->company_code)->where('branch_id', $Chemical->branch_id)->get();

            if ($Supplier->isEmpty()) {
                return back()->with('warning', 'لا يوجد مورد مواد في الفرع');
            }

            $ReturnUrl = $explode[2];

            return view('warehouse.AddChemicalShipment', compact('Chemical', 'Supplier', 'listMaterialEquipment', 'ReturnUrl'));
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
            if (is_null($editConcreteMix->branch_id)) {
                return back()->with('warning', 'هذا النوع قياسي (Standard) ولا يمكن تعديل الكميات الخاصة به.');
            }
            // $listChemical = Chemical::where('company_code', auth()->user()->company_code)->where('branch_id', $editConcreteMix->branch_id)->get();
            $chemicalList = Chemical::where('company_code', auth()->user()->company_code)
                ->where('branch_id', $editConcreteMix->branch_id)
                ->with(['concreteMixes' => function ($q) use ($editConcreteMix) {
                    $q->where('concrete_mix_id', $editConcreteMix->id);
                }])
                ->get();


            return view('materials.EditQuantitiesConcreteMix', compact('editConcreteMix', 'chemicalList'));
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

            $price = str_replace(',', '', $request->price);

            $MaterialEquipment_id = MaterialEquipment::where('id', $request->MaterialEquipment_id)->first();



            InventoryHistory::create([
                'material_code' => $id,
                'company_code' => auth()->user()->company_code,
                'branch_id' => $request->branch_id,
                'supplier_id' => $request->supplier_id,
                'MaterialEquipment_id' => $request->MaterialEquipment_id,
                'countUnit' => $request->countUnit,
                'total_cost' => $price,
                'shipment_date' => now(),
                'user_id' => auth()->user()->id,
                'note' => $request->note,
            ]);


            // dd($request->material_unit);
            $quantity_total = 0;
            $unit_cost = 0;
            if ($request->material_unit == 'ton') {
                $quantity_total =  ($MaterialEquipment_id->capacity * 20) * $request->countUnit;

                $unit_cost = (float)$price / (float)$quantity_total;
            } else {
                $quantity_total =  $MaterialEquipment_id->capacity * $request->countUnit;
                $unit_cost = (float)$price / (float)$quantity_total;
            }

            Supplier::where('id', $request->supplier_id)
                ->increment('opening_balance', (float) $price);





            $Inventory_qt = Inventory::where('code', $id)->first();

            if ($Inventory_qt->quantity_total != 0) {
                // dd('> 0' , $unit_cost);
                $avar_unit_cost = ($Inventory_qt->quantity_total * $Inventory_qt->unit_cost + $quantity_total * $unit_cost) / ($Inventory_qt->quantity_total + $quantity_total);
                Inventory::where('code', $id)->update([
                    'unit_cost' => $avar_unit_cost,
                ]);
            } else {
                // dd('< 0', $unit_cost);
                Inventory::where('code', $id)->update([
                    'unit_cost' => $unit_cost,
                ]);
            }
            Inventory::where('code', $id)->update([
                'quantity_total' => DB::raw('quantity_total + ' . (float) $quantity_total),
            ]);




            if ($request->ReturnUrl == "caompanyAdmin") {
                return redirect('warehouse/addMainMaterials')->with('success', 'تم اضافة تفاصيل الشحنة الجديدة.');
            }
            if ($request->ReturnUrl == "branch") {
                return redirect('warehouse/addMainMaterialsBranch')->with('success', 'تم اضافة تفاصيل الشحنة الجديدة.');
            }
        }

        if ($request->active == "AddNewChemicalShipment") {

            //  `id`, `company_code`, `branch_id`, `name`, `unit`, `quantity_total`, `unit_cost`, `description`, `created_at`, `updated_at` 

            $price = str_replace(',', '', $request->price);
            InventoryHistory::create([
                'material_code' => $id,
                'company_code' => auth()->user()->company_code,
                'branch_id' => $request->branch_id,
                'supplier_id' => $request->supplier_id,
                'MaterialEquipment_id' => $request->MaterialEquipment_id,
                'countUnit' => $request->countUnit,
                'total_cost' => $price,
                'shipment_date' => now(),
                'user_id' => auth()->user()->id,
                'note' => $request->note,
            ]);

            $MaterialEquipment_id = MaterialEquipment::where('id', $request->MaterialEquipment_id)->first();

            $quantity_total =  $MaterialEquipment_id->capacity * $request->countUnit;

            Supplier::where('id', $request->supplier_id)
                ->increment('opening_balance', (float) $price);






            $quantity_total =  $MaterialEquipment_id->capacity * $request->countUnit;
            $unit_cost = (float)$price / (float)$quantity_total;



            $Chemical_qt = Chemical::where('id', $id)->first();

            // dd($unit_cost);
            // لللللللل
            if ($Chemical_qt->quantity_total != 0) {
                // dd('> 0' , $unit_cost);
                $avar_unit_cost = ($Chemical_qt->quantity_total * $Chemical_qt->unit_cost + $quantity_total * $unit_cost) / ($Chemical_qt->quantity_total + $quantity_total);
                Chemical::where('id', $id)->update([
                    'unit_cost' => $avar_unit_cost,
                ]);
            } else {
                // dd('< 0', $unit_cost);
                Chemical::where('id', $id)->update([
                    'unit_cost' => $unit_cost,
                ]);
            }
            Chemical::where('id', $id)->update([
                'quantity_total' => DB::raw('quantity_total + ' . (float) $quantity_total),
            ]);






            if ($request->ReturnUrl == "companyadmin") {
                return redirect('warehouse/listchemicals')->with('success', 'تم اضافة تفاصيل الشحنة الجديدة.');
            }
            if ($request->ReturnUrl == "branch") {
                return redirect('warehouse/Branchlistchemicals')->with('success', 'تم اضافة تفاصيل الشحنة الجديدة.');
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

                ConcreteMix::where('id', $id)->update([
                    'sand' => $request->sand,
                    'cement' => $request->cement,
                    'gravel' => $request->gravel,
                    'water' => $request->water,
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
}
