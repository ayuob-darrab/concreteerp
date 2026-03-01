@extends('layouts.app')

@section('page-title', 'تحديث تفاصيل المادة  : ' . $EditGeneralConcreteMix->classification)

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">تحديث تفاصيل المادة : {{$EditGeneralConcreteMix->classification}}</h5>
            </div>

            {!! Form::open([
                'route' => ['materials.update', $EditGeneralConcreteMix->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">التصنيف <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="classification" readonly id="classification"
                        value="{{ $EditGeneralConcreteMix->classification }}" placeholder="أدخل التصنيف" class="form-input"
                        required>
                </div>

                <!-- الأسمنت -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الأسمنت (أكياس ×50كجم)</span>
                    </label>
                    <input type="number" name="cement" value="{{ $EditGeneralConcreteMix->cement }}" class="form-input"
                        step="1" min="0" pattern="^[0-9]+$"
                        title="يُسمح بالأرقام الصحيحة فقط (بدون فاصلة عشرية)">
                </div>

                <!-- الرمل -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الرمل (م³)</span>
                    </label>
                    <input type="number" name="sand" value="{{ $EditGeneralConcreteMix->sand }}" class="form-input"
                        step="0.01" min="0" pattern="^\d+(\.\d+)?$"
                        title="أدخل رقم صحيح أو عشري (مثال: 1 أو 1.5)">
                </div>

                <!-- الحصى -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الحصى (م³)</span>
                    </label>
                    <input type="number" name="gravel" value="{{ $EditGeneralConcreteMix->gravel }}" class="form-input"
                        step="0.01" min="0" pattern="^\d+(\.\d+)?$"
                        title="أدخل رقم صحيح أو عشري (مثال: 1 أو 1.5)">
                </div>

                <!-- الماء -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الماء (لتر)</span>
                    </label>
                    <input type="number" name="water" value="{{ $EditGeneralConcreteMix->water }}" class="form-input"
                        step="1" min="0" pattern="^[0-9]+$"
                        title="يُسمح بالأرقام الصحيحة فقط (بدون فاصلة عشرية)">
                </div>

                <!-- الملاحظات -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات</span>
                    </label>
                    <input name="notes" value="{{ $EditGeneralConcreteMix->notes }}" class="form-input">
                </div>


            </div>





            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <!-- الأزرار -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                    <button type="reset" @click="openModal = false"
                        class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-times-circle"></i>
                        <span>إلغاء</span>
                    </button>

                    <button type="submit" name="active" value="EditInformationGeneralConcreteMix"
                        class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-check-circle"></i>
                        <span>تحديث الخلطة</span>
                    </button>
                </div>
            </div>


            {!! Form::close() !!}

        </div>

    </div>
@endsection
