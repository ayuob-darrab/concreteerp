@extends('layouts.app')

@section('page-title', 'تحديث حساب الشركات')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="panel">
            <div class="mb-5">
                <h5 class="text-lg font-semibold dark:text-white-light">تحديث حساب الشركة</h5>
                <p class="text-white-dark mt-1">{{ $CompanyAccount->fullname }}</p>
            </div>

            {!! Form::open([
                'route' => ['companies.update', $CompanyAccount->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
                'files' => true,
            ]) !!}

            <div class="grid grid-cols-1 gap-5">
                <!-- اسم الحساب -->
                <div class="md:col-span-2">
                    <label for="Name" class="block text-sm font-medium mb-2">اسم الحساب الكامل</label>
                    <input id="Name" type="text" required name="fullname" value="{{ $CompanyAccount->fullname }}"
                        placeholder="أدخل الاسم الكامل" class="form-input">
                </div>

                <!-- اسم المستخدم -->
                <div class="md:col-span-2">
                    <label for="Username" class="block text-sm font-medium mb-2">اسم المستخدم</label>
                    <input id="Username" type="text" required name="username" value="{{ $CompanyAccount->username }}"
                        placeholder="أدخل اسم المستخدم" class="form-input" pattern="[a-zA-Z0-9_\-.]+" title="أحرف إنجليزية وأرقام فقط">
                </div>

                <!-- حالة التفعيل -->
                <div class="md:col-span-2">
                    <label for="is_active" class="block text-sm font-medium mb-2">حالة التفعيل</label>
                    <select id="is_active" required name="is_active" class="form-select">
                        <option value="" disabled>اختر الحالة</option>
                        <option value="1" {{ $CompanyAccount->is_active == 1 ? 'selected' : '' }}>مفعل</option>
                        <option value="0" {{ $CompanyAccount->is_active == 0 ? 'selected' : '' }}>معطل</option>
                    </select>
                </div>
            </div>

            <!-- الأزرار -->
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="window.history.back()" class="btn btn-outline-secondary">
                    رجوع
                </button>
                <button type="submit" name="active" value="updateCompanyAccount" class="btn btn-primary">
                    حفظ التغييرات
                </button>
            </div>

            {!! Form::close() !!}
        </div>
    </div>
@endsection
