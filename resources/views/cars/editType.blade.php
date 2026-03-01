@extends('layouts.app')

@section('page-title', 'تعديل نوع سيارة')

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <h3 class="text-center text-2xl font-bold dark:text-white-light">
            تعديل تفاصيل نوع السيارة : {{ $CarType->name }}
        </h3>
    </div>


    {!! Form::open([
        'route' => ['cars.update', $CarType->id],
        'method' => 'PUT',
        'autocomplete' => 'off',
        'files' => true,
    ]) !!}


    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- اسم نوع السيارة -->
        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">اسم النوع <span class="text-danger">*</span></span>
                </label>
                <input type="text" name="car_type_name" id="car_type_name" placeholder="أدخل اسم النوع"
                    value="{{ $CarType->name }}" class="form-input" required>
                @error('car_type_name')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- ملاحظات -->
        <div class="panel">
            <div class="space-y-3">
                <label class="inline-flex cursor-pointer">
                    <span class="text-white-dark">ملاحظات</span>
                </label>
                <textarea name="note" id="note" placeholder="أدخل أي ملاحظات" class="form-input">{{ $CarType->note }}</textarea>
                @error('note')
                    <div class="text-danger text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- الأزرار -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
            <button type="submit" name="active" value="editCarType" class="btn btn-primary !mt-6 px-8">
                <i class="fas fa-check-circle me-2"></i> تحديث النوع
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
            <button type="reset" class="btn btn-outline-secondary !mt-6 px-8">
                <i class="fas fa-times-circle me-2"></i> إلغاء
            </button>
        </div>

    </div>

    {!! Form::close() !!}
@endsection
