@extends('layouts.app')

@section('page-title', 'تحديث معلومات المادة : ' . $EditMaterialComponent->material_name)

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">تحديث معلومات المادة :
                    {{ $EditMaterialComponent->material_name }}</h5>
            </div>


            {!! Form::open([
                'route' => ['materials.update', $EditMaterialComponent->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <!-- اسم المادة -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم المادة <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="material_name" id="material_name" placeholder="أدخل اسم المادة"
                        value="{{ $EditMaterialComponent->material_name }}" class="form-input" required>
                    @error('material_name')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- نوع المادة -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">نوع المادة <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="material_type" id="material_type"
                        placeholder="أدخل نوع المادة (مثلاً: حصى ناعم، رمل خشن، سمنت)"
                        value="{{ $EditMaterialComponent->material_type }}" class="form-input" required>
                    @error('material_type')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- سعر الوحدة -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">سعر الوحدة 'م³'<span class="text-danger">*</span></span>
                    </label>
                    <input type="number" step="0.01" name="unit_price" id="unit_price" placeholder="أدخل سعر الوحدة"
                        value="{{ $EditMaterialComponent->unit_price }}" class="form-input" required min="0"
                        max="999999" oninput="if(this.value.length > 6) this.value = this.value.slice(0,6)">
                    @error('unit_price')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>


                <!-- ملاحظات -->
                <div class="space-y-3 ">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات</span>
                    </label>
                    <textarea name="notes" id="notes" rows="2" placeholder="أدخل أي ملاحظات..." class="form-input">{{ $EditMaterialComponent->notes }}</textarea>
                    @error('notes')
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

                    <button type="submit" name="active" value="EditMaterialComponentinformation"
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
