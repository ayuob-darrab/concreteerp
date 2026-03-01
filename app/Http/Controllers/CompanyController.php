<?php

namespace App\Http\Controllers;

use App\Helpers\RandomCodeGenerator;
use App\Models\City;
use App\Models\Company;
use App\Models\ConcreteMix;
use App\Models\MaterialComponent;
use App\Models\MaterialEquipment;
use App\Models\MeasurementUnit;
use App\Models\ShiftTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class CompanyController extends Controller
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
        $cities = City::get();
        return view('companies.create', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->active == 'AddNewCompany') {
            // ✅ التحقق من القيم المدخلة
            $validated = $request->validate([

                'note' => 'nullable|string',
                // 'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $exists = Company::where('name', $request->name)
                ->where('email', $request->email)
                ->where('phone', $request->phone)
                ->exists();

            if ($exists) {
                return back()->with('error', '⚠️ لا يمكن إضافة الشركة، البيانات الثلاثة (الاسم + الإيميل + الهاتف) موجودة مسبقاً.');
            }


            // 3️⃣ توليد كود الشركة
            // ==============================
            $company_code = RandomCodeGenerator::generateCompanycode();
            $filename = "";
            // 5️⃣ رفع شعار الشركة إن وجد (بشكل آمن)
            // ==============================
            if ($request->hasFile('logo')) {

                // مسار مجلد الشركة حسب الكود
                $folderPath = public_path('uploads/' . $company_code . '/companies_logo');

                $file = $request->file('logo');
                $uploadResult = \App\Helpers\FileUploadHelper::uploadSecurely(
                    $file,
                    $folderPath,
                    \App\Helpers\FileUploadHelper::IMAGE_EXTENSIONS
                );

                if (!$uploadResult['success']) {
                    return back()->with('error', $uploadResult['error'])->withInput();
                }

                $filename = 'uploads/' . $company_code . '/companies_logo/' . $uploadResult['filename'];
            }
            // dd($filename);

            // ==============================
            // 4️⃣ إنشاء الشركة باستخدام create()
            // ==============================
            $creationPrice = $request->creation_price ? floatval($request->creation_price) : 0;

            $NewCompany = Company::create([
                'code'           => $company_code,
                'name'           => $request->name,
                'managername'    => $request->managername,
                'city_id'        => $request->city_id,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'address'        => $request->address,
                'logo'           => $filename,
                'note'           => $request->note,
                'creation_price' => $creationPrice,
                'is_active'      => true,
                'created_at'     => now(),
            ]);

            // ==============================
            // 5️⃣ جلب الخلطات الأصلية ونسخها للشركة
            // ==============================
            $ConcreteMix = ConcreteMix::where('company_code', 'general')->get();

            // 6️⃣ إنشاء خلطة واحدة لكل تصنيف
            foreach ($ConcreteMix as $item) {
                // تحقق قبل إنشاء سجل جديد (منع التكرار)
                $exists = ConcreteMix::where('classification', $item->classification)
                    ->where('company_code', $company_code)
                    ->exists();

                if ($exists) {
                    continue; // تخطي السجل
                }

                // إنشاء الخلطة الجديدة
                ConcreteMix::create([
                    'classification' => $item->classification,
                    'company_code'    => $company_code,
                    'cement'          => $item->cement,
                    'sand'            => $item->sand,
                    'gravel'          => $item->gravel,
                    'water'           => $item->water,
                    'notes'           => $item->notes
                ]);
            }


            // ==============================
            // 7️⃣ رسالة نجاح
            // ==============================
            // إذا كان سعر الإنشاء أكبر من صفر، توجيه لطباعة الفاتورة
            if ($creationPrice > 0) {
                return redirect()->route('companies.print-creation-invoice', $NewCompany->id)
                    ->with('success', 'تمت إضافة الشركة بنجاح ✅ - جاري طباعة الفاتورة...');
            }

            return redirect()->route('companies.show', 'ListCompanies')->with('success', 'تمت إضافة الشركة بنجاح ✅');
        }


        if ($request->active == 'AddNewTocompany') {

            $existingUser = User::where('username', $request->username)->first();
            if ($existingUser) {
                return back()->withInput()->with('error', 'اسم المستخدم مستخدم مسبقاً ❌');
            }

            $accountCompany = new User();
            $accountCompany->fullname = $request->fullname;
            $accountCompany->username = strtolower(trim($request->username));
            $accountCompany->email = $request->username . '@system.local';
            $accountCompany->password = Hash::make($request->password);
            $accountCompany->usertype_id = 'CM';
            $accountCompany->account_code = 'emp';
            $accountCompany->company_code = $request->company_code;
            $accountCompany->is_active = true;
            $accountCompany->save();

            return redirect('companies/listAccountsCompanies')->with('success', 'تم إضافة الحساب للشركة بنجاح ✅');
        }

        if ($request->active == 'AddaddressGoogle') {

            $codeCompany = $request->companyCode;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            // dd($codeCompany);

            Company::where('code', $codeCompany)->update([
                'latitude'  =>  $request->latitude,
                'longitude'  =>  $request->longitude,
            ]);

            return back()->with('success', 'تم إضافة موقع الشركة على خرائط كوكل بنجاح ✅');
        }

        if ($request->active == 'NewShift') {

            //  dd($request->all());

            // إنشاء سجل جديد
            $NewShiftTime = new ShiftTime();
            $NewShiftTime->name = $request->shift_name;
            $NewShiftTime->company_code = auth()->user()->company_code;
            $NewShiftTime->start_time = $request->start_time;
            $NewShiftTime->end_time = $request->end_time;
            $NewShiftTime->notes = $request->note;
            $NewShiftTime->save();

            // رسالة النجاح
            return back()->with('success', 'تم حفظ الشفت بنجاح ✅');
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
        if ($id == 'ListCompanies') {
            $cities = City::get();
            $companies = Company::where('code', '!=', 'SA')->get();

            return view('companies.List', compact('cities', 'companies'));
        }
        if ($id == 'NewAccountsCompany') {
            $companies = Company::where('code', '!=', 'SA')->get();

            return view('companies.NewAccountsCompany', compact('companies'));
        }
        if ($id == 'listAccountsCompanies') {
            // ✅ إحصائيات سريعة من قاعدة البيانات
            $baseQuery = User::where('usertype_id', 'CM')
                ->whereNotIn('account_code', ['cont', 'delegate'])
                ->where('company_code', '!=', 'SA');

            $stats = [
                'total' => $baseQuery->count(),
                'active' => (clone $baseQuery)->where('is_active', 1)->count(),
                'inactive' => (clone $baseQuery)->where('is_active', 0)->count(),
            ];

            // ✅ استخدام Pagination بدلاً من get() لتحسين الأداء
            $users = User::with('CompanyName')
                ->where('usertype_id', 'CM')
                ->whereNotIn('account_code', ['cont', 'delegate'])
                ->where('company_code', '!=', 'SA')
                ->orderBy('created_at', 'desc')
                ->paginate(50);

            return view('companies.listAccountsCompanies', compact('users', 'stats'));
        }
        if ($id == 'ShiftTimes') {
            $shifttimes = ShiftTime::where('company_code', auth()->user()->company_code)->get();

            return view('companies.shifttimes', compact('shifttimes'));
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
        if ($explode[1] == 'edit_company') {
            // dd($explode);
            $company = Company::where('id', $explode[0])->first();
            $cities = City::get();
            return view('companies.edit', compact('cities', 'company'));
        }
        if ($explode[1] == 'editCompanyAccount') {
            $CompanyAccount = User::where('id', $explode[0])->first();

            $cities = City::get();
            return view('companies.editCompanyAccount', compact('cities', 'CompanyAccount'));
        }
        if ($explode[1] == 'EditShiftTime') {
            $EditShiftTime = ShiftTime::where('id', $explode[0])->first();
            return view('companies.editshifttime', compact('EditShiftTime'));
        }

        if ($explode[1] == 'Location') {

            $Company = Company::where('id', $explode[0])->first();
            $addresCompany = true;
            return view('map', compact('Company', 'addresCompany'));
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
        if ($request->active == 'edit_informationCompany') {

            $updateData = [
                'name'       => $request->name,
                'managername'       => $request->managername,
                'city_id'    => $request->city_id,
                'phone'      => $request->phone,
                'email'      => $request->email,
                'address'    => $request->address,
                'note'       => $request->note,
                'is_active'  => $request->is_active,
            ];

            // إذا كان هناك ملف لوغو مرفوع (بشكل آمن)
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $folderPath = public_path('uploads/companies_logo');
                $uploadResult = \App\Helpers\FileUploadHelper::uploadSecurely(
                    $file,
                    $folderPath,
                    \App\Helpers\FileUploadHelper::IMAGE_EXTENSIONS
                );

                if (!$uploadResult['success']) {
                    return back()->with('error', $uploadResult['error'])->withInput();
                }

                $updateData['logo'] = 'uploads/companies_logo/' . $uploadResult['filename'];
            }

            // تنفيذ التحديث
            Company::where('id', $id)->update($updateData);
            return redirect('companies/ListCompanies')->with('success', 'تم تحديث تفاصيل الشركة بنجاح');
        }

        if ($request->active  == 'UpdatePassword') {
            User::where('id', $id)->update([
                'password'  =>  Hash::make($request->newPassword),
            ]);
            return back()->with('success', 'تم تحديث كلمة المرور بنجاح');
        }

        if ($request->active  == 'updateCompanyAccount') {
            User::where('id', $id)->update([
                'fullname'  =>  $request->fullname,
                'username'  =>  strtolower(trim($request->username)),
                'is_active'  =>  $request->is_active,
            ]);
            return redirect('companies/listAccountsCompanies')->with('success', 'تم تحديث  معلومات حساب الشركة بنجاح');
        }
        if ($request->active  == 'updateShiftTime') {
            ShiftTime::where('id', $id)->update([
                'name' => $request->shift_name,
                'company_code' => auth()->user()->company_code,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->note,
            ]);



            return redirect('companies/ShiftTimes')->with('success', 'تم تحديث  معلومات الشفت بنجاح');
        }

        if ($request->active == 'AddaddresCompanyOnGoogle') {

            Company::where('code', $id)->update([
                'latitude'  =>  $request->latitude,
                'longitude'  =>  $request->longitude,
            ]);

            return redirect('companies/ListCompanies')->with('success', 'تم إضافة موقع الشركة على خرائط كوكل بنجاح ✅');
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
     * طباعة فاتورة إنشاء الشركة
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printCreationInvoice($id)
    {
        $company = Company::with('city')->findOrFail($id);

        // التأكد من أن سعر الإنشاء أكبر من صفر
        if ($company->creation_price <= 0) {
            return redirect()->route('companies.show', 'ListCompanies')
                ->with('error', 'لا يمكن طباعة فاتورة - سعر الإنشاء صفر');
        }

        return view('companies.print-creation-invoice', compact('company'));
    }
}
