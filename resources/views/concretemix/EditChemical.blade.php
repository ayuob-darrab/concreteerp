@extends('layouts.app')

@section('page-title', 'تعديل المادة الكيميائية : ' . $EditChemical->name)

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">معلومات المادة : {{ $EditChemical->name }}</h5>
            </div>
            {!! Form::open([
                'route' => ['warehouse.update', $EditChemical->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم المادة <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="name" value="{{ $EditChemical->name }}" id="name"
                        placeholder="أدخل اسم المادة" class="form-input" required>
                </div>

                    <!-- وحدة القياس -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">وحدة القياس <span
                                                    class="text-danger">*</span></span>
                                        </label>

                                        <select name="unit" id="unit" class="form-select" required>
                                            <option value="">اختر وحدة القياس</option>
                                            @foreach ($MeasurementUnit as $unit)
                                                <option value="{{ $unit->code }}"  {{ $EditChemical->unit == $unit->code ? 'selected' : '' }}>{{ $unit->name }} </option>
                                            @endforeach
                                        </select>

                                        @error('unit')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">وصف المادة </span>
                    </label>
                    <input type="text" placeholder="وصف المادة" value="{{ $EditChemical->description }}"
                        name="description" class="form-input">
                </div>

                <!-- الأزرار -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                    <button type="reset" @click="openModal = false"
                        class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-times-circle"></i>
                        <span>إلغاء</span>
                    </button>

                    <button type="submit" name="active" value="UpdateChemical"
                        class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-check-circle"></i>
                        <span>تحديث معلومات المادة</span>
                    </button>
                </div>

            </div>

            {!! Form::close() !!}

        </div>

    </div>
@endsection
