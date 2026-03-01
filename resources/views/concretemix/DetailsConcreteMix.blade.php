@extends('layouts.app')

@section('page-title', 'عرض تفاصيل خلطات الخرسانة : ' . $ConcreteMix->classification)

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    تعديل خلطات الخرسانة: {{ $ConcreteMix->classification ?? '' }}
                </h5>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- التصنيف -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">التصنيف</span>
                    </label>
                    <input type="text" name="classification" value="{{ $ConcreteMix->classification }}" class="form-input"
                        required>
                </div>

                <!-- سعر التكلفة -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">سعر التكلفة</span>
                    </label>
                    <input type="text" name="costPrice" value="{{ number_format($ConcreteMix->costPrice ?? 0, 0) }}"
                        class="form-input" step="0.01" required>
                </div>

                <!-- الفرع -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الفرع</span>
                    </label>
                    <input type="text" name="costPrice"
                        value="{{ $ConcreteMix->branchName->branch_name ?? 'الاستندر العام' }}" class="form-input"
                        step="0.01" required>
                </div>

                <!-- الأسعار -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">سعر التكلفة</span>
                    </label>
                    <input type="text" name="costPrice" value="{{ number_format($ConcreteMix->costPrice) }}"
                        class="form-input" step="0.01" required>
                </div>

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">سعر البيع</span>
                    </label>
                    <input type="text" name="salePrice" value="{{ number_format($ConcreteMix->salePrice) }}"
                        class="form-input" step="0.01" required>
                </div>

                <!-- المواد الأساسية -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الأسمنت (أكياس)</span>
                    </label>
                    <input type="text" name="cement" value="{{ $ConcreteMix->cement }}" class="form-input"
                        step="1" min="0">
                </div>

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الرمل (م³)</span>
                    </label>
                    <input type="text" name="sand" value="{{ $ConcreteMix->sand }}" class="form-input" step="0.01"
                        min="0">
                </div>

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الحصى (م³)</span>
                    </label>
                    <input type="text" name="gravel" value="{{ $ConcreteMix->gravel }}" class="form-input"
                        step="0.01" min="0">
                </div>

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الماء (لتر)</span>
                    </label>
                    <input type="text" name="water" value="{{ $ConcreteMix->water }}" class="form-input" step="1"
                        min="0">
                </div>

                <!-- الملاحظات -->
                <div class="space-y-3 lg:col-span-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات</span>
                    </label>
                    <textarea name="notes" class="form-input">{{ $ConcreteMix->notes }}</textarea>
                </div>
            </div>
        </div>

        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    المواد الكيميائية لخلطة الخرسانة: {{ $ConcreteMix->classification ?? '' }}
                </h5>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                @foreach ($ConcreteMixChemical as $item)
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">المادة : {{ $item->ChemicalQuantity->name }}</span>
                        </label>
                        <input type="text" name="classification" value="{{ $item->quantity }}" class="form-input"
                            required>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
