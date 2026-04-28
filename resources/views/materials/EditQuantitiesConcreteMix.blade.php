@extends('layouts.app')

@section('page-title', 'تعديل كميات مادة الكونكريت : ' . $editConcreteMix->classification)

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    البيانات العامة: الكميات القياسية للمواد الأساسية لكل متر مكعب واحد
                    في :
                    {{ $editConcreteMix->branchName->branch_name ?? 'الستاندرد العام لكل الفروع' }} :
                    للمادة : {{ $editConcreteMix->classification }}
                </h5>
            </div>

            <form action="{{ route('warehouse.update', $editConcreteMix->id) }}" method="POST" autocomplete="off">
                @csrf
                @method('PUT')

                <input type="hidden" name="classification" value="{{ $editConcreteMix->classification }}">

                <!-- المواد الأساسية -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="space-y-2">
                        <label class="inline-flex cursor-pointer flex-col">
                            <span class="text-white-dark">الأسمنت (أكياس ×50كجم)</span>
                        </label>
                        <input type="number" name="cement" value="{{ old('cement', $editConcreteMix->cement) }}"
                            class="form-input" step="0.01" min="0"
                            title="أدخل رقم صحيح أو عشري (مثال: 7 أو 7.5)">
                    </div>

                    <div class="space-y-2">
                        <label class="inline-flex cursor-pointer flex-col">
                            <span class="text-white-dark">الرمل (م³)</span>
                        </label>
                        <input type="number" name="sand" value="{{ old('sand', $editConcreteMix->sand) }}"
                            class="form-input" step="0.01" min="0"
                            title="أدخل رقم صحيح أو عشري (مثال: 1 أو 1.5)">
                    </div>

                    <div class="space-y-2">
                        <label class="inline-flex cursor-pointer flex-col">
                            <span class="text-white-dark">الحصى (م³)</span>
                        </label>
                        <input type="number" name="gravel" value="{{ old('gravel', $editConcreteMix->gravel) }}"
                            class="form-input" step="0.01" min="0"
                            title="أدخل رقم صحيح أو عشري (مثال: 1 أو 1.5)">
                    </div>

                    <div class="space-y-2 md:col-span-3">
                        <label class="inline-flex cursor-pointer flex-col">
                            <span class="text-white-dark">الماء (لتر)</span>
                        </label>
                        <input type="number" name="water" value="{{ old('water', $editConcreteMix->water) }}"
                            class="form-input" step="0.01" min="0"
                            title="أدخل رقم صحيح أو عشري (مثال: 175 أو 175.5)">
                    </div>
                </div>

                <!-- المواد الكيميائية -->
                <div class="mt-6 border-t pt-6">
                    <h5 class="text-lg font-semibold dark:text-white-light mb-4">المواد الكيميائية</h5>

                    @if (empty($chemicalList) || $chemicalList->isEmpty())
                        <div class="text-gray-500 dark:text-gray-400">لا توجد مواد كيميائية.</div>
                    @else
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                            @foreach ($chemicalList as $item)
                                @php
                                    $pivotQty =
                                        old('chemical_' . $item->id) ??
                                        ($item->concreteMixes->first()?->pivot?->quantity ?? '');
                                @endphp

                                <div
                                    class="space-y-2 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow">
                                    <p class="text-white-dark font-semibold">{{ $item->name }}</p>
                                    <input name="chemical_{{ $item->id }}" id="input_{{ $item->id }}"
                                        value="{{ $pivotQty }}" class="form-input w-full" type="number" step="0.01"
                                        min="0" placeholder="الكمية" title="أدخل رقم صحيح أو عشري (مثال: 1 أو 1.5)">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- الفئات السعرية -->
                <div class="mt-6 border-t pt-6">
                    <h5 class="text-lg font-semibold dark:text-white-light mb-4">الفئات السعرية</h5>

                    @if (!isset($categories) || $categories->isEmpty())
                        <div class="text-gray-500 dark:text-gray-400">لا توجد فئات سعرية حالياً.</div>
                    @else
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                            @foreach ($categories as $cat)
                                @php
                                    $existing = $categoryPrices[$cat->id] ?? null;
                                @endphp

                                <div
                                    class="space-y-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow">
                                    <div>
                                        <p class="text-white-dark font-semibold">{{ $cat->name }}</p>
                                        @if ($cat->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $cat->description }}</p>
                                        @endif
                                    </div>

                                    <div>
                                        <label class="text-xs text-gray-600 dark:text-gray-300">السعر (دينار/م³)</label>
                                        <input type="text" inputmode="numeric" name="category_price[{{ $cat->id }}]"
                                            value="{{ old('category_price.' . $cat->id, $existing?->price_per_meter ? number_format($existing->price_per_meter, 0, '.', ',') : '') }}"
                                            class="form-input w-full" placeholder="السعر" oninput="formatPrice(this)">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- الكلفة + ملاحظات -->
                <div class="mt-6 border-t pt-6">
                    <h5 class="text-lg font-semibold dark:text-white-light mb-4">الكلفة والملاحظات</h5>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <div class="space-y-2">
                            <label class="inline-flex cursor-pointer">
                                <span class="text-white-dark">الكلفة (دينار/م³)</span>
                            </label>
                            <input name="costPrice" class="form-input" inputmode="numeric" placeholder="الكلفة"
                                value="{{ old('costPrice', $editConcreteMix->costPrice ? number_format($editConcreteMix->costPrice, 0, '.', ',') : '') }}"
                                oninput="formatPrice(this)">
                        </div>

                        <div class="space-y-2 lg:col-span-3">
                            <label class="inline-flex cursor-pointer" for="notes">
                                <span class="text-white-dark">ملاحظات</span>
                            </label>
                            <textarea name="notes" id="notes" rows="3" class="form-input"
                                placeholder="ملاحظات...">{{ old('notes', $editConcreteMix->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- الأزرار -->
                <div class="mt-6 border-t pt-6">
                    <div class="flex flex-col sm:flex-row justify-start gap-4">
                        <button type="reset"
                            class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                            <i class="fas fa-times-circle"></i>
                            <span>إلغاء</span>
                        </button>

                        <button type="submit" name="active" value="EditQuantitiesConcreteMix"
                            class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                            <i class="fas fa-check-circle"></i>
                            <span>تحديث الخلطة</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function formatPrice(el) {
            const raw = (el.value || '').toString().replace(/,/g, '').replace(/[^\d.]/g, '');
            if (raw === '') {
                el.value = '';
                return;
            }
            const n = Number(raw);
            if (Number.isNaN(n)) return;
            el.value = Math.round(n).toLocaleString('en-US');
        }
    </script>
@endsection
