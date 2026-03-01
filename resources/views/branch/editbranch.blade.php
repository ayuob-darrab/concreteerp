@extends('layouts.app')

@section('page-title', 'اضافة فرع')

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <h3 class="text-center text-2xl font-bold dark:text-white-light">
            تعديل معلومات فرع : {{ $branch->branch_name }}
        </h3>
    </div>

    {!! Form::open([
        'route' => ['companyBranch.update', $branch->id],
        'method' => 'PUT',
        'autocomplete' => 'off',
        'files' => true,
    ]) !!}

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Registration -->


        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">اسم الفرع <span class="text-danger">*</span></span>
                </label>
                <input type="text" name="branch_name" id="branch_name" placeholder="أدخل اسم الفرع"
                    value="{{ $branch->branch_name }}" class="form-input" required>
                @error('branch_name')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- المحافظة -->
        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">المحافظة <span class="text-danger">*</span></span>
                </label>
                <select name="city_id" id="city_id" class="form-input" required>
                    <option value="">اختر المحافظة</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" {{ $branch->city_id == $city->id ? 'selected' : '' }}>
                            {{ $city->name_ar }}
                        </option>
                    @endforeach
                </select>
                @error('city_id')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- مدير الفرع -->
        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">مدير الفرع</span>
                </label>
                <input type="text" name="branch_admin" id="branch_admin" required value="{{ $branch->branch_admin }}"
                    placeholder="أدخل اسم مدير الفرع" class="form-input">
                @error('branch_admin')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- الهاتف -->
        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">رقم الهاتف</span>
                </label>

                <input type="text" name="phone" id="phone" required value="{{ $branch->phone }}"
                    placeholder="أدخل رقم الهاتف" class="form-input" pattern="\d{11}" maxlength="11" minlength="11"
                    title="يجب أن يحتوي رقم الهاتف على 11 رقمًا فقط"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                @error('phone')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <!-- البريد الإلكتروني -->
        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">البريد الإلكتروني</span>
                </label>
                <input type="email" name="email" id="email" required value="{{ $branch->email }}"
                    placeholder="example@email.com" class="form-input">
                @error('email')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- العنوان -->
        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">العنوان</span>
                </label>
                <input type="text" name="address" id="address" required value="{{ $branch->address }}"
                    placeholder="أدخل عنوان الفرع" class="form-input">
                @error('address')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- حالة الفرع (مفعل / معطل) -->
        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">الحالة</span>
                </label>

                <select name="is_active" id="is_active" class="form-select" required>
                    <option value="1" {{ $branch->is_active == 1 ? 'selected' : '' }}>مفعل</option>
                    <option value="0" {{ $branch->is_active == 0 ? 'selected' : '' }}>تعطيل</option>
                </select>

                @error('is_active')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="panel">
            <div class="space-y-3">

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
                    <button type="submit" name="active" value="updateInformationBranch"
                        class="btn btn-primary !mt-6 px-8">
                        <i class="fas fa-check-circle me-2"></i> تحديث
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
                    <button type="reset" class="btn btn-outline-secondary !mt-6 px-8">
                        <i class="fas fa-times-circle me-2"></i> إلغاء
                    </button>
                </div>
            </div>
        </div>


        <!-- الأزرار -->






    </div>
    {!! Form::close() !!}
@endsection
