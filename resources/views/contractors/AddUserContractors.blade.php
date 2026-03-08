@extends('layouts.app')

@section('page-title', $Contractor->user_id ? 'تعديل حساب المقاول' : 'إضافة حساب مقاول')

@section('content')
    <div class="space-y-6">
        {{-- مسار التنقل --}}
        <ul class="flex flex-wrap gap-2 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ url('/contractors/List') }}" class="text-primary hover:underline">المقاولين</a></li>
            <li class="before:content-['/'] before:ltr:mr-2 before:rtl:ml-2">
                <span class="text-gray-700 dark:text-gray-300">{{ $Contractor->contract_name }}</span>
            </li>
            <li class="before:content-['/'] before:ltr:mr-2 before:rtl:ml-2">
                <span>{{ $Contractor->user_id ? 'تعديل الحساب' : 'إضافة حساب' }}</span>
            </li>
        </ul>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (is_null($Contractor->user_id))
            {{-- إضافة حساب جديد --}}
            <div class="panel">
                <h5 class="text-lg font-bold dark:text-white-light mb-6 flex items-center gap-2">
                    👤 إضافة حساب مقاول
                </h5>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    شركة المقاول: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $Contractor->contract_name }}</span>
                </p>

                {!! Form::open([
                    'route' => ['contractors.update', $Contractor->id],
                    'method' => 'put',
                    'autocomplete' => 'off',
                    'files' => true,
                ]) !!}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
                    <div class="space-y-2">
                        <label for="Name" class="block font-medium text-gray-700 dark:text-gray-300">الاسم الثلاثي <span class="text-danger">*</span></label>
                        <input id="Name" type="text" required name="fullname" placeholder="الاسم الثلاثي"
                            value="{{ old('fullname') }}" class="form-input w-full">
                    </div>
                    <div class="space-y-2">
                        <label for="Username" class="block font-medium text-gray-700 dark:text-gray-300">اسم المستخدم <span class="text-danger">*</span></label>
                        <input id="Username" type="text" required name="username" placeholder="اسم المستخدم"
                            value="{{ old('username') }}" class="form-input w-full" pattern="[a-zA-Z0-9_\-.]+" title="أحرف إنجليزية وأرقام فقط">
                    </div>
                    <div class="space-y-2">
                        <label for="Password" class="block font-medium text-gray-700 dark:text-gray-300">كلمة المرور</label>
                        <input id="Password" type="text" name="password" placeholder="كلمة المرور (اختياري)"
                            class="form-input w-full">
                    </div>
                    <div class="space-y-2">
                        <label for="branchId" class="block font-medium text-gray-700 dark:text-gray-300">الفرع <span class="text-danger">*</span></label>
                        <select id="branchId" required name="branchId" class="form-select w-full">
                            @foreach ($branches as $item)
                                <option value="{{ $item->id }}" {{ old('branchId', $Contractor->branch_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" name="active" value="AddNewUserContractors" class="btn btn-primary">
                        حفظ وإضافة الحساب
                    </button>
                    <a href="{{ url('/contractors/List') }}" class="btn btn-outline-secondary">
                        إلغاء والعودة للقائمة
                    </a>
                </div>
                {!! Form::close() !!}
            </div>
        @else
            {{-- تعديل حساب موجود --}}
            <div class="panel">
                <h5 class="text-lg font-bold dark:text-white-light mb-6 flex items-center gap-2">
                    ✏️ تعديل حساب المقاول
                </h5>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    شركة المقاول: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $Contractor->contract_name }}</span>
                </p>

                {!! Form::open([
                    'route' => ['contractors.update', $Contractor->user_id],
                    'method' => 'put',
                    'autocomplete' => 'off',
                    'files' => true,
                ]) !!}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
                    <div class="space-y-2">
                        <label for="Name" class="block font-medium text-gray-700 dark:text-gray-300">الاسم الثلاثي <span class="text-danger">*</span></label>
                        <input id="Name" type="text" required name="fullname" placeholder="الاسم الثلاثي"
                            value="{{ old('fullname', $Contractor->user->fullname ?? '') }}" class="form-input w-full">
                    </div>
                    <div class="space-y-2">
                        <label for="Username" class="block font-medium text-gray-700 dark:text-gray-300">اسم المستخدم <span class="text-danger">*</span></label>
                        <input id="Username" type="text" required name="username" placeholder="اسم المستخدم"
                            value="{{ old('username', $Contractor->user->username ?? '') }}" class="form-input w-full" pattern="[a-zA-Z0-9_\-.]+" title="أحرف إنجليزية وأرقام فقط">
                    </div>
                    <div class="space-y-2">
                        <label for="is_active" class="block font-medium text-gray-700 dark:text-gray-300">حالة الحساب</label>
                        <select id="is_active" name="is_active" class="form-select w-full">
                            <option value="1" {{ old('is_active', $Contractor->user->is_active ?? 1) == 1 ? 'selected' : '' }}>فعال</option>
                            <option value="0" {{ old('is_active', $Contractor->user->is_active ?? 1) == 0 ? 'selected' : '' }}>غير فعال</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="branchId" class="block font-medium text-gray-700 dark:text-gray-300">الفرع</label>
                        <select id="branchId" required name="branchId" class="form-select w-full">
                            <option value="" disabled>اختر الفرع</option>
                            @foreach ($branches as $item)
                                <option value="{{ $item->id }}" {{ old('branchId', $Contractor->branch_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" name="active" value="UpdateUserContractors" class="btn btn-primary">
                        حفظ التعديلات
                    </button>
                    <a href="{{ url('/contractors/List') }}" class="btn btn-outline-secondary">
                        إلغاء والعودة للقائمة
                    </a>
                </div>
                {!! Form::close() !!}
            </div>
        @endif
    </div>
@endsection
