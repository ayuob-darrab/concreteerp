@extends('layouts.app')

@section('page-title', 'اضافة حساب موظف')

@section('content')

    <div class="max-w-4xl mx-auto">
        <div class="panel p-6">
            <div class="mb-6 border-b pb-4">
                <h1 class="text-xl font-bold text-primary">إضافة حساب جديد</h1>
                <p class="text-sm text-gray-500 mt-1">أضف مستخدم جديد للنظام</p>
            </div>

            {{-- رسائل الخطأ --}}
            @if (session('error'))
                <div class="alert alert-danger mb-4">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {!! Form::open([
                'route' => 'accounts.store',
                'method' => 'POST',
                'autocomplete' => 'off',
                'id' => 'addUserForm',
            ]) !!}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- الاسم الثلاثي --}}
                <div>
                    <label for="Name" class="block mb-1 font-medium text-sm">الاسم الثلاثي <span
                            class="text-danger">*</span></label>
                    <input id="Name" type="text" required name="fullname" placeholder="الاسم الثلاثي"
                        value="{{ old('fullname') }}" minlength="3" class="form-input w-full">
                </div>

                {{-- اسم المستخدم --}}
                <div>
                    <label for="Username" class="block mb-1 font-medium text-sm">اسم المستخدم <span
                            class="text-danger">*</span></label>
                    <input id="Username" type="text" required placeholder="أدخل اسم المستخدم" name="username"
                        value="{{ old('username') }}" class="form-input w-full" pattern="[a-zA-Z0-9_\-.]+" title="أحرف إنجليزية وأرقام فقط">
                </div>

                {{-- كلمة المرور --}}
                <div>
                    <label for="Password" class="block mb-1 font-medium text-sm">كلمة المرور <span
                            class="text-danger">*</span></label>
                    <input id="Password" type="text" required name="password" placeholder="أدخل كلمة المرور"
                        minlength="6" class="form-input w-full">
                    <p class="text-xs text-gray-500 mt-1">6 أحرف على الأقل</p>
                </div>

                {{-- الفرع --}}
                <div>
                    <label for="branchId" class="block mb-1 font-medium text-sm">الفرع <span
                            class="text-danger">*</span></label>
                    <select id="branchId" required name="branchId" class="form-select w-full">
                        <option value="" selected disabled>اختر الفرع</option>
                        @foreach ($baranches as $item)
                            <option value="{{ $item->id }}" {{ old('branchId') == $item->id ? 'selected' : '' }}>
                                {{ $item->branch_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- صلاحيات المستخدم --}}
                <div>
                    <label for="user_type" class="block mb-1 font-medium text-sm">صلاحيات المستخدم <span
                            class="text-danger">*</span></label>
                    <select id="user_type" required name="user_type" class="form-select w-full">
                        <option value="" selected disabled>اختر الصلاحيات</option>
                        @foreach ($typeUser as $type)
                            <option value="{{ $type->code }}" {{ old('user_type') == $type->code ? 'selected' : '' }}>
                                {{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- نوع الموظف --}}
                <div >
                    <label for="employee_type" class="block mb-1 font-medium text-sm">نوع الموظف <span
                            class="text-danger">*</span></label>
                    <select id="employee_type" required name="employee_type" class="form-select w-full md:w-1/2">
                        <option value="" selected disabled>اختر نوع الموظف</option>
                        @foreach ($employeeType as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_type') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- الأزرار --}}
            <div class="flex gap-3 mt-6 pt-4 border-t">
                <button type="submit" name="active" value="AddNewUser" class="btn btn-primary flex items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    إضافة الحساب
                </button>
                <a href="{{ url('accounts/listaccount') }}" class="btn btn-outline-secondary">
                    إلغاء
                </a>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
@endsection
