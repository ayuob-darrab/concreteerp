{{-- @extends('layouts.app')

@section('page-title', 'تعديل كميات مادة الكونكريت : ' . $editConcreteMix->classification)

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    البيانات العامة: الكميات القياسية للمواد الأساسية لكل متر مكعب واحد
                    في فرع : {{ $editConcreteMix->branchName->branch_name }} :
                    للمادة : {{ $editConcreteMix->classification }}
                </h5>

            </div>

            {!! Form::open([
                'route' => ['warehouse.update', $editConcreteMix->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {!! Form::hidden('classification', $editConcreteMix->classification) !!}

                <!-- الأسمنت -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer flex-col">
                        <span class="text-white-dark">الأسمنت (أكياس ×50كجم)</span>
                        <span>سعر الكيس: {{ number_format($editConcreteMix->cementInventory->unit_cost, 2) }}</span>
                        <span>المتوفر في المخزن:
                            {{ number_format($editConcreteMix->cementInventory->quantity_total) . ' ' . $editConcreteMix->cementInventory->MeasurementUnit->name }}</span>
                    </label>
                    <input type="number" name="cement" value="{{ $editConcreteMix->cement }}" class="form-input"
                        step="1" min="0" pattern="^[0-9]+$"
                        title="يُسمح بالأرقام الصحيحة فقط (بدون فاصلة عشرية)"
                        oninput="checkMaxQuantity(this, {{ number_format($editConcreteMix->cementInventory->quantity_total) . ' ' . $editConcreteMix->cementInventory->MeasurementUnit->name }})">
                </div>

                <!-- الرمل -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer flex-col">
                        <span class="text-white-dark">الرمل (م³)</span>
                        <span>سعر المتر المكعب: {{ number_format($editConcreteMix->sandInventory->unit_cost, 2) }}</span>
                        <span>المتوفر في المخزن:
                            {{ number_format($editConcreteMix->sandInventory->quantity_total) . ' ' . $editConcreteMix->sandInventory->MeasurementUnit->name }}</span>
                    </label>
                    <input type="number" name="sand" value="{{ $editConcreteMix->sand }}" class="form-input"
                        step="0.01" min="0" pattern="^\d+(\.\d+)?$" title="أدخل رقم صحيح أو عشري (مثال: 1 أو 1.5)"
                        oninput="checkMaxQuantity(this, {{ $editConcreteMix->sandInventory->quantity_total }})">
                </div>

                <!-- الحصى -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer flex-col">
                        <span class="text-white-dark">الحصى (م³)</span>
                        <span>سعر المتر المكعب: {{ number_format($editConcreteMix->gravelInventory->unit_cost, 2) }}</span>
                        <span>المتوفر في المخزن:
                            {{ number_format($editConcreteMix->gravelInventory->quantity_total) . ' ' . $editConcreteMix->gravelInventory->MeasurementUnit->name }}</span>
                    </label>
                    <input type="number" name="gravel" value="{{ $editConcreteMix->gravel }}" class="form-input"
                        step="0.01" min="0" pattern="^\d+(\.\d+)?$" title="أدخل رقم صحيح أو عشري (مثال: 1 أو 1.5)"
                        oninput="checkMaxQuantity(this, {{ $editConcreteMix->gravelInventory->quantity_total }})">
                </div>

                <!-- الماء -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer flex-col">
                        <span class="text-white-dark">الماء (لتر)</span>
                        <span>سعر اللتر: {{ number_format($editConcreteMix->waterInventory->unit_cost, 2) }}</span>
                        <span>المتوفر في المخزن:
                            {{ number_format($editConcreteMix->waterInventory->quantity_total) . ' ' . $editConcreteMix->waterInventory->MeasurementUnit->name }}</span>
                    </label>
                    <input type="number" name="water" value="{{ $editConcreteMix->water }}" class="form-input"
                        step="1" min="0" pattern="^[0-9]+$"
                        title="يُسمح بالأرقام الصحيحة فقط (بدون فاصلة عشرية)"
                        oninput="checkMaxQuantity(this, {{ $editConcreteMix->waterInventory->quantity_total }})">
                </div>

            </div>

            <script>
                function checkMaxQuantity(input, max) {
                    if (parseFloat(input.value) > max) {
                        alert("الكمية لا تكفي! الحد الأقصى المتاح: " + max);
                        input.value = max;
                    }
                }
            </script>


            <br>
            <hr>



            <div class="panel h-full w-full">
                <div class="mb-5 flex items-center justify-between">
                    <h5 class="text-lg font-semibold dark:text-white-light">المواد الكيميائية</h5>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    @foreach ($chemicalList as $item)
                        @php
                            $available = $item->quantity_total;
                            $pivotQty = $item->concreteMixes->first()->pivot->quantity ?? '';
                        @endphp

                        <div
                            class="space-y-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow">

                            <!-- معلومات المادة -->
                            <div>
                                <p class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $item->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    سعر اللتر:
                                    <span class="font-semibold">{{ number_format($item->unit_cost) }}</span>
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    الباقي في المخزن:
                                    <span class="font-bold {{ $available == 0 ? 'text-red-500' : 'text-green-600' }}">
                                        {{ number_format($available) . ' ' . $item->MeasurementUnit->name }}
                                    </span>
                                </p>
                            </div>

                            <!-- الإدخال -->
                            <div>
                                <input name="chemical_{{ $item->id }}" id="input_{{ $item->id }}"
                                    value="{{ $pivotQty }}" class="form-input w-full" type="number" step="0.1"
                                    min="0" max="{{ $available }}" placeholder="الكمية (لتر)"
                                    {{ $available == 0 ? 'disabled' : '' }}
                                    oninput="validateQuantity(this, {{ $available }})">

                                <!-- رسالة الخطأ -->
                                <p class="text-red-600 text-sm mt-1 hidden">❌ الكمية لا تكفي</p>
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>
            <script>
                function validateQuantity(input, available) {
                    const errorMsg = input.parentElement.querySelector("p");

                    if (parseFloat(input.value) > available) {

                        // إظهار الرسالة تحت الحقل
                        errorMsg.classList.remove("hidden");

                        // تحديد القيمة بالحد الأقصى
                        input.value = available;

                        // تلوين الحقل
                        input.classList.add("border-red-500");

                        // تنبيه (Alert)
                        alert("الكمية لا تكفي. الحد الأقصى هو: " + available);

                    } else {
                        // إخفاء الرسالة
                        errorMsg.classList.add("hidden");

                        // إزالة اللون الأحمر من الحقل
                        input.classList.remove("border-red-500");
                    }
                }
            </script>



            <div class="panel h-full w-full">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">ادخل سعر البيع للمتر المكعب الواحد</span>
                        </label>

                        <input name="salePrice" class="form-input" inputmode="numeric" minlength="4" maxlength="8"
                            required placeholder="سعر البيع للمتر المكعب الواحد"
                            value="{{ number_format($editConcreteMix->salePrice) }}" oninput="formatPrice(this)">
                    </div>

                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">سعر التكلفة</span>
                        </label>

                        <input name="costPrice" class="form-input" inputmode="numeric" minlength="4" maxlength="8"
                            required placeholder="سعر التكلفة" value="{{ number_format($editConcreteMix->costPrice) }}"
                            oninput="formatPrice(this)">
                    </div>

                    <!-- الأزرار -->
                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
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
            </div>


            {!! Form::close() !!}

        </div>

    </div>
@endsection --}}



@extends('layouts.app')

@section('page-title', 'تعديل كميات مادة الكونكريت : ' . $editConcreteMix->classification)

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    البيانات العامة: الكميات القياسية للمواد الأساسية لكل متر مكعب واحد
                    في فرع : {{ $editConcreteMix->branchName->branch_name }} :
                    للمادة : {{ $editConcreteMix->classification }}
                </h5>

            </div>

            {!! Form::open([
                'route' => ['warehouse.update', $editConcreteMix->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {!! Form::hidden('classification', $editConcreteMix->classification) !!}

                <!-- الأسمنت -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer flex-col">
                        <span class="text-white-dark">الأسمنت (أكياس ×50كجم)</span>
                        <span>سعر الكيس: {{ number_format($editConcreteMix->cementInventory->unit_cost, 2) }}</span>
                        <span>المتوفر في المخزن:
                            {{ number_format($editConcreteMix->cementInventory->quantity_total) . ' ' . $editConcreteMix->cementInventory->MeasurementUnit->name }}</span>
                    </label>
                    <input type="number" name="cement" value="{{ $editConcreteMix->cement }}" class="form-input"
                        step="1" min="0" pattern="^[0-9]+$"
                        title="يُسمح بالأرقام الصحيحة فقط (بدون فاصلة عشرية)"
                        oninput="checkMaxQuantity(this, {{ $editConcreteMix->cementInventory->quantity_total }})">
                </div>

                <!-- الرمل -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer flex-col">
                        <span class="text-white-dark">الرمل (م³)</span>
                        <span>سعر المتر المكعب: {{ number_format($editConcreteMix->sandInventory->unit_cost, 2) }}</span>
                        <span>المتوفر في المخزن:
                            {{ number_format($editConcreteMix->sandInventory->quantity_total) . ' ' . $editConcreteMix->sandInventory->MeasurementUnit->name }}</span>
                    </label>
                    <input type="number" name="sand" value="{{ $editConcreteMix->sand }}" class="form-input"
                        step="0.01" min="0" pattern="^\d+(\.\d+)?$" title="أدخل رقم صحيح أو عشري (مثال: 1 أو 1.5)"
                        oninput="checkMaxQuantity(this, {{ $editConcreteMix->sandInventory->quantity_total }})">
                </div>

                <!-- الحصى -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer flex-col">
                        <span class="text-white-dark">الحصى (م³)</span>
                        <span>سعر المتر المكعب: {{ number_format($editConcreteMix->gravelInventory->unit_cost, 2) }}</span>
                        <span>المتوفر في المخزن:
                            {{ number_format($editConcreteMix->gravelInventory->quantity_total) . ' ' . $editConcreteMix->gravelInventory->MeasurementUnit->name }}</span>
                    </label>
                    <input type="number" name="gravel" value="{{ $editConcreteMix->gravel }}" class="form-input"
                        step="0.01" min="0" pattern="^\d+(\.\d+)?$" title="أدخل رقم صحيح أو عشري (مثال: 1 أو 1.5)"
                        oninput="checkMaxQuantity(this, {{ $editConcreteMix->gravelInventory->quantity_total }})">
                </div>

                <!-- الماء -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer flex-col">
                        <span class="text-white-dark">الماء (لتر)</span>
                        <span>سعر اللتر: {{ number_format($editConcreteMix->waterInventory->unit_cost, 2) }}</span>
                        <span>المتوفر في المخزن:
                            {{ number_format($editConcreteMix->waterInventory->quantity_total) . ' ' . $editConcreteMix->waterInventory->MeasurementUnit->name }}</span>
                    </label>
                    <input type="number" name="water" value="{{ $editConcreteMix->water }}" class="form-input"
                        step="1" min="0" pattern="^[0-9]+$"
                        title="يُسمح بالأرقام الصحيحة فقط (بدون فاصلة عشرية)"
                        oninput="checkMaxQuantity(this, {{ $editConcreteMix->waterInventory->quantity_total }})">
                </div>

            </div>

            <script>
                function checkMaxQuantity(input, max) {
                    if (parseFloat(input.value) > max) {
                        alert("الكمية لا تكفي! الحد الأقصى المتاح: " + max);
                        input.value = max;
                    }
                }
            </script>


            <br>
            <hr>



            <div class="panel h-full w-full">
                <div class="mb-5 flex items-center justify-between">
                    <h5 class="text-lg font-semibold dark:text-white-light">المواد الكيميائية</h5>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    @foreach ($chemicalList as $item)
                        @php
                            $available = $item->quantity_total;
                            $pivotQty = $item->concreteMixes->first()->pivot->quantity ?? '';
                        @endphp

                        <div
                            class="space-y-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow">

                            <!-- معلومات المادة -->
                            <div>
                                <p class="text-white-dark">{{ $item->name }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    سعر اللتر:
                                    <span class="font-semibold">{{ number_format($item->unit_cost) }}</span>
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    الباقي في المخزن:
                                    <span class="font-bold {{ $available == 0 ? 'text-red-500' : 'text-green-600' }}">
                                        {{ number_format($available) . ' ' . $item->MeasurementUnit->name }}
                                    </span>
                                </p>
                            </div>

                            <!-- الإدخال -->
                            <div>
                                <input name="chemical_{{ $item->id }}" id="input_{{ $item->id }}"
                                    value="{{ $pivotQty }}" class="form-input w-full" type="number" step="0.1"
                                    min="0" max="{{ $available }}" placeholder="الكمية (لتر)"
                                    {{ $available == 0 ? 'disabled' : '' }}
                                    oninput="validateQuantity(this, {{ $available }})">

                                <!-- رسالة الخطأ -->
                                <p class="text-red-600 text-sm mt-1 hidden">❌ الكمية لا تكفي</p>
                            </div>

                        </div>
                    @endforeach

                </div>
            </div>
            <script>
                function validateQuantity(input, available) {
                    const errorMsg = input.parentElement.querySelector("p");

                    if (parseFloat(input.value) > available) {

                        // إظهار الرسالة تحت الحقل
                        errorMsg.classList.remove("hidden");

                        // تحديد القيمة بالحد الأقصى
                        input.value = available;

                        // تلوين الحقل
                        input.classList.add("border-red-500");

                        // تنبيه (Alert)
                        alert("الكمية لا تكفي. الحد الأقصى هو: " + available);

                    } else {
                        // إخفاء الرسالة
                        errorMsg.classList.add("hidden");

                        // إزالة اللون الأحمر من الحقل
                        input.classList.remove("border-red-500");
                    }
                }
            </script>



            <div class="panel h-full w-full">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    <!-- الأزرار -->
                    <div class="flex flex-col sm:flex-row justify-start gap-4 mt-4 col-span-3">
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
            </div>


            {!! Form::close() !!}

        </div>

    </div>

@endsection
