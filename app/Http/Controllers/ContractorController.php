<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ConcreteMix;
use App\Models\Contractor;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ContractorController extends Controller
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
        // name="active" value=""
        if ($request->active == "AddNewContractor") {
            // تحقق من وجود مقاول بنفس البيانات
            $exists = Contractor::where('branch_id', $request->branch_id)
                ->where('contract_name', $request->contract_name)
                ->where('contract_adminstarter', $request->contract_adminstarter)
                ->where('phone1', $request->phone1)
                ->exists();

            if ($exists) {
                return back()->with('error', 'المقاول موجود بالفعل في هذا الفرع بنفس البيانات.');
            }

            // إذا لم يكن موجودًا، قم بإنشاء المقاول
            $NewContractor = new Contractor();
            $NewContractor->branch_id = $request->branch_id;
            $NewContractor->company_code = auth()->user()->company_code;
            $NewContractor->contract_name = $request->contract_name;
            $NewContractor->contract_adminstarter = $request->contract_adminstarter;
            $NewContractor->phone1 = $request->phone1;
            $NewContractor->phone2 = $request->phone2;
            $NewContractor->opening_balance = $request->opening_balance ?? 0;
            $NewContractor->isactive = $request->isactive ?? 1;
            $NewContractor->address = $request->address;
            $NewContractor->createdate =  now();
            $NewContractor->note = $request->note;

            // حفظ شعار الشركة إذا تم رفعه (بشكل آمن)
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $folderPath = public_path('uploads/contractors_logo');
                $uploadResult = \App\Helpers\FileUploadHelper::uploadSecurely(
                    $file,
                    $folderPath,
                    \App\Helpers\FileUploadHelper::IMAGE_EXTENSIONS
                );

                if ($uploadResult['success']) {
                    $NewContractor->logo = $uploadResult['filename'];
                }
            }

            $NewContractor->save();

            return back()->with('success', 'تم إضافة المقاول بنجاح');
        }

        if ($request->active == "AddDetailsRequest") {
            // التحقق من وجود طلب مكرر
            $exists = WorkOrder::where([
                'sender_type'       => 'cont',
                'sender_id'         => auth()->user()->id,
                'company_code'      => auth()->user()->company_code,
                'branch_id'         => auth()->user()->branch_id,
                'customer_phone'    => auth()->user()->contractor->phone1 ?? auth()->user()->contractor->phone2,
                'quantity'          => $request->quantity,
                'location'          => $request->location,
                'delivery_datetime' => $request->delivery_datetime,
                'note'              => $request->note,
            ])->exists();

            if ($exists) {
                return back()->with('error', '⚠️ يوجد طلب مطابق مسجل مسبقاً، لا يمكن تكرار الطلب.');
            }

            WorkOrder::create([
                'sender_type' => 'cont',
                'sender_id' => auth()->user()->id,

                'company_code' => auth()->user()->company_code,
                'branch_id' => auth()->user()->branch_id,

                'customer_phone' => auth()->user()->contractor->phone1 ?? auth()->user()->contractor->phone2,

                'status_code' => 'new',
                'classification' => $request->classification_id,

                'quantity' => $request->quantity,
                'location' => $request->location,
                'location_map_url' => $request->location_map_url,
                'location_lat' => $request->location_lat,
                'location_lng' => $request->location_lng,
                'delivery_datetime' => $request->delivery_datetime,
                'note' => $request->note,

                'request_date' => now(),
                'created_by' => auth()->id(),
            ]);

            return redirect('contractors/SendRequestsContractor')
                ->with('success', 'تم تسجيل الطلب بنجاح');
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
        if ($id == "List") {
            $Contractor = Contractor::where('company_code', auth()->user()->company_code)->get();
            $branches = Branch::where('is_active', true)->where('company_code', auth()->user()->company_code)->get();
            return view('contractors.list', compact('Contractor', 'branches'));
        }


        if ($id == "SendRequestsContractor") {

            // إذا كان المستخدم مقاول، نجلب البيانات بناءً على بيانات المقاول
            $user = Auth::user();

            if ($user->account_code == 'cont' && $user->contractor) {
                $contractor = $user->contractor;
                $companyCode = $contractor->company_code;
                $branchId = $contractor->branch_id;
            } else {
                $companyCode = $user->company_code;
                $branchId = $user->branch_id;
            }

            $ConcreteMix = ConcreteMix::with(['branchName', 'workOrders'])
                ->where('company_code', $companyCode)
                ->where('branch_id', $branchId)
                ->orderBy('branch_id', 'desc')
                ->get();

            return view('contractors.listConcreteMix', compact('ConcreteMix'));
        }

        if ($id == "MyPendingOrders") {
            // الطلبات الجديدة التي أرسلها المقاول وتنتظر موافقة الفرع
            $user = Auth::user();

            if ($user->account_code == 'cont' && $user->contractor) {
                $contractor = $user->contractor;

                $WorkOrder = WorkOrder::with(['concreteMix', 'branch'])
                    ->where('company_code', $contractor->company_code)
                    ->where('branch_id', $contractor->branch_id)
                    ->where('sender_type', 'cont')
                    ->where('sender_id', $user->id)
                    ->where('status_code', 'new')
                    ->whereNull('branch_approval_status')
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $WorkOrder = WorkOrder::with(['concreteMix', 'branch'])
                    ->where('company_code', $user->company_code)
                    ->where('branch_id', $user->branch_id)
                    ->where('status_code', 'new')
                    ->whereNull('branch_approval_status')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            return view('contractors.MyPendingOrders', compact('WorkOrder'));
        }

        if ($id == "CheckRequestsContractor") {

            // إذا كان المستخدم مقاول، نجلب البيانات بناءً على بيانات المقاول
            $user = Auth::user();

            if ($user->account_code == 'cont' && $user->contractor) {
                $contractor = $user->contractor;
                $companyCode = $contractor->company_code;
                $branchId = $contractor->branch_id;

                // جلب طلبات هذا المقاول فقط باستخدام sender_type و sender_id
                // sender_id يخزن User.id وليس Contractor.id
                $WorkOrder = WorkOrder::where('company_code', $companyCode)
                    ->where('branch_id', $branchId)
                    ->where('sender_type', 'cont')
                    ->where('sender_id', $user->id)
                    ->where('branch_approval_status', 'approved')
                    ->where('requester_approval_status', null)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $WorkOrder = WorkOrder::where('company_code', $user->company_code)
                    ->where('branch_id', $user->branch_id)
                    ->where('branch_approval_status', 'approved')
                    ->where('requester_approval_status', null)
                    ->orderBy('branch_id', 'desc')
                    ->get();
            }

            return view('contractors.CheckRequestsContractor', compact('WorkOrder'));
        }

        // صفحة الطلبات المعتمدة نهائياً - قيد العمل
        if ($id == "ApprovedOrders") {
            $user = Auth::user();

            if ($user->account_code == 'cont' && $user->contractor) {
                $contractor = $user->contractor;

                // جلب الطلبات المعتمدة نهائياً (status_code = in_progress)
                $WorkOrder = WorkOrder::with(['concreteMix', 'branch'])
                    ->where('company_code', $contractor->company_code)
                    ->where('branch_id', $contractor->branch_id)
                    ->where('sender_type', 'cont')
                    ->where('sender_id', $user->id)
                    ->where('status_code', 'in_progress')
                    ->orderBy('accept_date', 'desc')
                    ->get();
            } else {
                $WorkOrder = WorkOrder::with(['concreteMix', 'branch'])
                    ->where('company_code', $user->company_code)
                    ->where('branch_id', $user->branch_id)
                    ->where('status_code', 'in_progress')
                    ->orderBy('accept_date', 'desc')
                    ->get();
            }

            return view('contractors.ApprovedOrders', compact('WorkOrder'));
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

        if ($explode[1] == "EditContractors") {
            $Contractor = Contractor::where('id', $explode[0])->first();
            $branches = Branch::where('is_active', true)->where('company_code', auth()->user()->company_code)->get();

            return view('contractors.EditContractors', compact('Contractor', 'branches'));
        }
        if ($explode[1] == "ViewContractors") {
            $Contractor = Contractor::where('id', $explode[0])->first();

            return view('contractors.ViewContractors', compact('Contractor'));
        }

        if ($explode[1] == "AddUserContractors") {
            $Contractor = Contractor::where('id', $explode[0])->first();
            // إضافة حساب: فرع المقاول فقط. تعديل حساب: كل فروع الشركة
            $branchesQuery = Branch::where('is_active', true)->where('company_code', auth()->user()->company_code);
            if (is_null($Contractor->user_id)) {
                $branchesQuery->where('id', $Contractor->branch_id);
            }
            $branches = $branchesQuery->get();
            return view('contractors.AddUserContractors', compact('Contractor', 'branches'));
        }

        if ($explode[1] == "SendNewRequest") {

            $ConcreteMix = ConcreteMix::where('id', $explode[0])->first();
            $listWorkOrder = WorkOrder::where('company_code', auth()->user()->company_code)
                ->where('status_code', 'new')
                ->get();

            return view('contractors.AddDetailsRequest', compact('ConcreteMix', 'listWorkOrder'));
        }

        if ($explode[1] == "ViewRequest") {
            $WorkOrder = WorkOrder::where('id', $explode[0])->with(['ConcreteMix', 'branch', 'sender'])->first();
            if (!$WorkOrder) {
                return redirect('contractors/CheckRequestsContractor')->with('error', 'لم يتم العثور على الطلب.');
            }

            return view('contractors.ViewRequest', compact('WorkOrder'));
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
        if ($request->active == "UpdateContractor") {
            $data = [
                'branch_id'            => $request->branch_id,
                'company_code'         => auth()->user()->company_code,
                'contract_name'        => $request->contract_name,
                'contract_adminstarter' => $request->contract_adminstarter,
                'phone1'               => $request->phone1,
                'phone2'               => $request->phone2,
                'opening_balance'      => $request->opening_balance ?? 0,
                'isactive'             => $request->isactive ?? 1,
                'address'              => $request->address,
                'note'                 => $request->note,
            ];

            // إذا تم رفع صورة جديدة → احفظها (بشكل آمن)
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $folderPath = public_path('uploads/contractors_logo');
                $uploadResult = \App\Helpers\FileUploadHelper::uploadSecurely(
                    $file,
                    $folderPath,
                    \App\Helpers\FileUploadHelper::IMAGE_EXTENSIONS
                );

                if ($uploadResult['success']) {
                    $data['logo'] = $uploadResult['filename']; // أضفها إلى البيانات
                }
            }

            // تنفيذ التحديث
            Contractor::where('id', $id)->update($data);

            // إعادة التوجيه مع رسالة نجاح
            return redirect('contractors/List')->with('success', 'تم تحديث بيانات المقاول بنجاح.');
        }

        if ($request->active == "ApproveRequest") {
            // approve by requester
            $order = WorkOrder::where('id', $id)->first();
            if (!$order) {
                return redirect('contractors/CheckRequestsContractor')->with('error', 'الطلب غير موجود');
            }

            $order->requester_approval_status = 'approved';
            $order->requester_approval_user_id = auth()->user()->id;
            $order->requester_approval_date = now();
            $order->requester_approval_note = $request->accept_note ?? null;
            $order->save();

            return redirect('contractors/CheckRequestsContractor')->with('success', 'تمت الموافقة على الطلب بنجاح');
        }

        if ($request->active == "RejectRequest") {
            // reject by requester
            $order = WorkOrder::where('id', $id)->first();
            if (!$order) {
                return redirect('contractors/CheckRequestsContractor')->with('error', 'الطلب غير موجود');
            }

            $order->requester_approval_status = 'rejected';
            $order->requester_approval_user_id = auth()->user()->id;
            $order->requester_approval_date = now();
            $order->requester_approval_note = $request->reject_note ?? null;
            $order->save();

            return redirect('contractors/CheckRequestsContractor')->with('success', 'تم رفض الطلب بنجاح');
        }

        if ($request->active == "AddNewUserContractors") {
            $checkUser = User::where('username', strtolower(trim($request->username)))
                ->first();

            if ($checkUser) {
                return redirect()->back()->with('error', 'اسم المستخدم مستخدم مسبقاً!');
            }

            $addNewUser = new User();
            $addNewUser->fullname = $request->fullname;
            $addNewUser->company_code = Auth::user()->company_code;
            $addNewUser->username = strtolower(trim($request->username));
            $addNewUser->email = $request->username . '@system.local';
            $addNewUser->password = Hash::make($request->password);
            $addNewUser->usertype_id = 'CM';
            $addNewUser->emp_type_id = $request->employee_type;
            $addNewUser->branch_id = $request->branchId;
            $addNewUser->account_code = 'cont';
            $addNewUser->is_active = true;
            $addNewUser->save();


            Contractor::where('id', $id)->update(['user_id' => $addNewUser->id]);
            return redirect('contractors/List')->with('success', 'تم اضافة المستخدم بنجاح');
        }

        if ($request->active == "UpdateUserContractors") {

            user::where('id', $id)->update([
                'fullname'      => $request->fullname,
                'username'      => strtolower(trim($request->username)),
                'is_active'   => $request->is_active,
                'branch_id'     => $request->branchId,
            ]);

            return redirect('contractors/List')->with('success', 'تم تحديث معلومات المستخدم بنجاح');
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
