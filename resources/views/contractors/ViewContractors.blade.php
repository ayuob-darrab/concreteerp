@extends('layouts.app')

@section('page-title', 'عرض معلومات المقاول: ' . $Contractor->contract_name)

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    معلومات المقاول : {{ $Contractor->contract_name }}
                </h5>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <!-- اسم الشركة / المقاول -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم الشركة / المقاول</span>
                    </label>
                    <input type="text" class="form-input" value="{{ $Contractor->contract_name }}" readonly>
                </div>


                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الفرع</span>
                    </label>
                    <input type="text" class="form-input" value="{{ $Contractor->branch->branch_name }}" readonly>
                </div>

                <!-- اسم المدير المسؤول -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم المدير المسؤول</span>
                    </label>
                    <input type="text" class="form-input" value="{{ $Contractor->contract_adminstarter }}" readonly>
                </div>

                <!-- الهاتف 1 -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الهاتف 1</span>
                    </label>
                    <input type="text" class="form-input" value="{{ $Contractor->phone1 }}" readonly>
                </div>

                <!-- الهاتف 2 -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الهاتف 2</span>
                    </label>
                    <input type="text" class="form-input" value="{{ $Contractor->phone2 }}" readonly>
                </div>

                <!-- الرصيد الافتتاحي -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الرصيد الافتتاحي</span>
                    </label>
                    <input type="text" class="form-input" value="{{ $Contractor->opening_balance }}" readonly>
                </div>

                <!-- العنوان -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">العنوان</span>
                    </label>
                    <input type="text" class="form-input" value="{{ $Contractor->address }}" readonly>
                </div>

                <!-- الملاحظات -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات</span>
                    </label>
                    <textarea class="form-input" readonly>{{ $Contractor->note }}</textarea>
                </div>


                               <!-- الشعار -->
<div class="space-y-3">
    <label class="inline-flex cursor-pointer">
        <span class="text-white-dark">شعار الشركة</span>
    </label>
    @if ($Contractor->logo)
        <img src="{{ asset('uploads/contractors_logo/' . $Contractor->logo) }}"
             class="h-32 w-auto mt-2 rounded shadow">
    @else
        <span class="text-gray-500">لا يوجد شعار</span>
    @endif
</div>

            </div>
        </div>
    </div>
@endsection
