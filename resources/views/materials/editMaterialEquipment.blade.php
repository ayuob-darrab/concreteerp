@extends('layouts.app')

@section('page-title', 'تعديل المادة : ' . $editMaterialEquipment->name)

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">تعديل معلومات المادة :
                    {{ $editMaterialEquipment->name }}</h5>
            </div>


            {!! Form::open([
                'route' => ['materials.update', $editMaterialEquipment->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <!-- اسم المادة -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم المادة <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="name" id="name" placeholder="أدخل اسم المادة"
                        value="{{ $editMaterialEquipment->name }}" class="form-input" required>
                    @error('name')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- السعة -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">السعة <span class="text-danger">*</span></span>
                    </label>

                    <input type="text" name="capacity" id="capacity" placeholder="أدخل السعة"
                        value="{{ $editMaterialEquipment->code === 'ton' ? $editMaterialEquipment->capacity / 20 : $editMaterialEquipment->capacity }}"
                        class="form-input" required pattern="^\d+(\.\d+)?$" title="أدخل رقم صحيح أو عشري">

                    @error('capacity')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>


                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">وحدة القياس</span>
                    </label>

                    <select name="code" id="code" class="form-input" required>
                        <option value="">اختر وحدة القياس</option>
                        @foreach ($MeasurementUnit as $unit)
                            <option value="{{ $unit->code }}"
                                {{ $editMaterialEquipment->code == $unit->code ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->code }})
                            </option>
                        @endforeach
                    </select>

                    @error('code')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- نوع المادة (رمل/حصو/أسمنت...) -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">نوع المادة (اختياري)</span>
                    </label>

                    <select name="material_type" id="material_type" class="form-input">
                        <option value="">جميع المواد (عام)</option>
                        @if (isset($materials))
                            @foreach ($materials as $mat)
                                <option value="{{ $mat->name }}"
                                    {{ $editMaterialEquipment->material_type == $mat->name ? 'selected' : '' }}>
                                    {{ $mat->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <small class="text-gray-500">اختر نوع المادة لتخصيص هذه المعدة لمادة معينة فقط</small>

                    @error('material_type')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>


                <!-- ملاحظات -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات</span>
                    </label>
                    <textarea name="note" id="note" rows="2" placeholder="أدخل أي ملاحظات..." class="form-input">{{ $editMaterialEquipment->note }}</textarea>
                    @error('note')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الأزرار -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                    <button type="reset" @click="openModal = false"
                        class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-times-circle"></i>
                        <span>إلغاء</span>
                    </button>

                    <button type="submit" name="active" value="UpdateMaterialEquipment"
                        class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-check-circle"></i>
                        <span>تحديث تفاصيل المادة</span>
                    </button>
                </div>

            </div>

            {!! Form::close() !!}

        </div>

    </div>
@endsection
