<?php

namespace App\Http\Controllers;

use App\Models\Chemical;
use App\Models\Company;
use App\Models\ConcreteMix;
use App\Models\ConcreteMixChemical;
use App\Models\Inventory;
use App\Models\Material;
use App\Models\MaterialComponent;
use App\Models\MaterialEquipment;
use App\Models\MeasurementUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialsController extends Controller
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
        if ($request->active == "NewMaterials") {
            $addNewMaterial = new Material();
            $addNewMaterial->name = $request->material_name;
            $addNewMaterial->company_code =  Auth::user()->company_code;
            $addNewMaterial->price = $request->price;
            $addNewMaterial->save();
            return back()->with('success', 'تم اضافة المادة بنجاح');
        }
        if ($request->active == "NewMaterialComponent") {

            $MaterialComponent = new MaterialComponent();
            $MaterialComponent->company_code = Auth::user()->company_code; // رمز الشركة من المستخدم الحالي
            $MaterialComponent->material_name = $request->material_name;
            $MaterialComponent->material_type = $request->material_type;
            $MaterialComponent->unit_price = $request->unit_price;
            $MaterialComponent->notes = $request->notes;
            // حفظ السجل في قاعدة البيانات
            $MaterialComponent->save();

            // إعادة المستخدم مع رسالة نجاح
            return back()->with('success', 'تم إضافة مكونات المواد بنجاح');
        }
        if ($request->active == "NewMaterialEquipment") {

            // تحقق أولاً من وجود المادة بنفس الاسم والكود والكمية
            $exists = MaterialEquipment::where('name', $request->name)
                ->where('code', $request->code)
                ->where('company_code', auth()->user()->company_code)
                ->where('capacity', $request->capacity)
                ->exists();

            if ($exists) {
                return back()->with('error', 'هذه المادة موجودة مسبقاً.');
            }

            // إنشاء سجل جديد
            $newMaterialEquipment = new MaterialEquipment();
            $newMaterialEquipment->name = $request->name;
            $newMaterialEquipment->company_code = auth()->user()->company_code;
            $newMaterialEquipment->code = $request->code;
            $newMaterialEquipment->material_type = $request->material_type; // نوع المادة (رمل/حصو/أسمنت...)
            $newMaterialEquipment->capacity = $request->capacity;

            $newMaterialEquipment->note = $request->note;
            $newMaterialEquipment->save();

            return back()->with('success', 'تم إضافة المكونات بنجاح');
        }
        if ($request->active == "Newmeasurement_units") {

            $Newmeasurement_units = new MeasurementUnit();
            $Newmeasurement_units->name = $request->name;

            $Newmeasurement_units->code = $request->code;

            $Newmeasurement_units->note = $request->note;

            // حفظ السجل في قاعدة البيانات
            $Newmeasurement_units->save();

            // إعادة المستخدم مع رسالة نجاح
            return back()->with('success', 'تم إضافة وحدة القياس بنجاح');
        }



        if ($request->active == "AddNewGeneralConcreteMix") {


            // التحقق إذا كانت المادة موجودة مسبقًا
            $exists = ConcreteMix::where('classification', $request->classification)
                ->where('company_code', 'general')
                ->exists();

            if ($exists) {
                // إذا كانت موجودة بالفعل
                return back()->with('error', 'المادة الخرسانية موجودة مسبقًا.');
            }

            // 'id','','','','','','','',

            // إذا لم تكن موجودة، قم بالإضافة
            $NewConcreteMix = new ConcreteMix();
            $NewConcreteMix->company_code = 'general';
            $NewConcreteMix->classification = $request->classification;
            $NewConcreteMix->sand = $request->sand;
            $NewConcreteMix->cement = $request->cement;
            $NewConcreteMix->gravel = $request->gravel;
            $NewConcreteMix->water = $request->water;
            $NewConcreteMix->notes = $request->notes;

            $NewConcreteMix->save();

            return back()->with('success', 'تمت إضافة المادة الخرسانية الجديدة بنجاح.');
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

        if ($id == "list") {
            $material = Material::where('company_code', Auth::user()->company_code)->get();
            return view('materials.list', compact('material'));
        }
        if ($id == 'MaterialComponents') {
            // dd('dfghfhjfghfgh');
            $MaterialComponents = MaterialComponent::where('company_code', auth()->user()->company_code)->get();

            return view('companies.materialcomponents', compact('MaterialComponents'));
        }

        if ($id == 'listmeasurement_units') {
            $listmeasurement_units = MeasurementUnit::get();

            return view('materials.listmeasurement_units', compact('listmeasurement_units'));
        }

        if ($id == 'ConcreteMix') {
            $ConcreteMix = ConcreteMix::get();
            $companies = Company::where('code', '!=', 'SA')->get();

            return view('concretemix.generalListConcreteMix', compact('ConcreteMix'));
        }





        if ($id == 'listMaterialEquipment') {

            $listMaterialEquipment = MaterialEquipment::where('company_code', auth()->user()->company_code)->get();
            $MeasurementUnit = MeasurementUnit::get();
            // قائمة المواد الموجودة في مخازن الشركة
            $materials = Inventory::where('company_code', auth()->user()->company_code)
                ->select('name')
                ->distinct()
                ->get();

            return view('materials.listMaterialEquipment', compact('listMaterialEquipment', 'MeasurementUnit', 'materials'));
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

        if ($explode[1] == "edit_material") {
            $material = Material::where('id', $explode[0])->first();
            return view('materials.edit_materials', compact('material'));
        }
        if ($explode[1] == "EditMaterialComponent") {
            // dd('EditMaterialComponent');
            $EditMaterialComponent = MaterialComponent::where('id', $explode[0])->first();
            return view('materials.EditMaterialComponent', compact('EditMaterialComponent'));
        }
        if ($explode[1] == "editMaterialEquipment") {
            // dd('EditMaterialComponent');
            $editMaterialEquipment = MaterialEquipment::where('id', $explode[0])->first();
            // $companies = Company::where('code', '!=', 'SA')->get();
            $MeasurementUnit = MeasurementUnit::get();
            // قائمة المواد الموجودة في مخازن الشركة
            $materials = Inventory::where('company_code', auth()->user()->company_code)
                ->select('name')
                ->distinct()
                ->get();
            return view('materials.editMaterialEquipment', compact('editMaterialEquipment', 'MeasurementUnit', 'materials'));
        }
        if ($explode[1] == "EditMeasurement_Units") {
            // dd('EditMaterialComponent');
            $EditMeasurement_Units = MeasurementUnit::where('id', $explode[0])->first();
            return view('materials.EditMeasurement_Units', compact('EditMeasurement_Units'));
        }







        if ($explode[1] == "EditGeneralConcreteMix") {

            $EditGeneralConcreteMix = ConcreteMix::where('id', $explode[0])->first();

            if ($EditGeneralConcreteMix->company_code != "general") {
                return back()->with('error', 'لا يمكن تعديل تفاصيل المادة الخرسانية لانها خاصة لشركة ');
            } elseif ($EditGeneralConcreteMix->company_code == "general") {
                $EditGeneralConcreteMix = ConcreteMix::where('id', $explode[0])->where('company_code', 'general')->first();

                return view('concretemix.EditGeneralConcreteMix', compact('EditGeneralConcreteMix'));
            }
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
        if ($request->active == "UpdateMaterials") {
            Material::where('id', $id)->update([
                'name'  => $request->material_name,
                'price'  => $request->price,
            ]);
            return redirect('materials/list')->with('success', 'تم تحديث معلومات المادة بنجاح');
        }
        if ($request->active == "EditMaterialComponentinformation") {
            MaterialComponent::where('id', $id)->update([
                'material_name' => $request->material_name,
                'material_type' => $request->material_type,
                'unit_price' => $request->unit_price,
                'notes' => $request->notes,
            ]);
            return redirect('materials/MaterialComponents')->with('success', 'تم تحديث معلومات المادة بنجاح');
        }
        if ($request->active == "UpdateMaterialEquipment") {
            MaterialEquipment::where('id', $id)->update([

                'name' => $request->name,
                // السعة تعتمد على نوع الوحدة
                'capacity' => $request->code === 'ton'
                    ? $request->capacity * 20   // إذا كانت طن نحولها إلى م³ تقريباً
                    : $request->capacity,        // إذا كانت متر مكعب تبقى كما هي

                'code' => $request->code,
                'material_type' => $request->material_type, // نوع المادة (رمل/حصو/أسمنت...)
                'note' => $request->note,
            ]);
            return redirect('materials/listMaterialEquipment')->with('success', 'تم تحديث معلومات المادة بنجاح');
        }
        if ($request->active == "updatemeasurement_units") {
            MeasurementUnit::where('id', $id)->update([
                'name' => $request->name,
                'note' => $request->note,
            ]);
            return redirect('materials/listmeasurement_units')->with('success', 'تم تحديث معلومات وحدة القياس بنجاح');
        }



        if ($request->active == "EditInformationGeneralConcreteMix") {
            ConcreteMix::where('id', $id)->update(
                [
                    'sand' => $request->sand,
                    'cement' => $request->cement,
                    'gravel' => $request->gravel,
                    'water' => $request->water,
                    'notes' => $request->notes,
                ]
            );

            return redirect('materials/ConcreteMix')->with('success', 'تم تحديث معلومات المادة بنجاح');
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
}
