{{-- @extends('layouts.app')

@section('page-title', 'تعديل كميات مادة الكونكريت : ' . $editConcreteMix->classification)

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    البيانات العامة: الكميات القياسية للمواد الأساسية لكل متر مكعب واحد
                    في فرع : {{ $editConcreteMix->branchName->branch_name }}
                </h5>

            </div>

            {!! Form::open([
                'route' => ['warehouse.update', $editConcreteMix->id],
                'method' => 'PUT',
                'autocomplete' => 'off',
            ]) !!}



            @php
                function formatNumber($number, $decimals = 2)
                {
                    if (round($number, $decimals) == round($number)) {
                        return number_format($number, 0);
                    } else {
                        return number_format($number, $decimals);
                    }
                }

                $materials = [
                    [
                        'name' => 'الأسمنت (أكياس × 50 كجم)',
                        'value' => $editConcreteMix->cement,
                        'code' => $editConcreteMix->cement_code,
                        'step' => 1,
                    ],
                    [
                        'name' => 'الرمل (م³)',
                        'value' => $editConcreteMix->sand,
                        'code' => $editConcreteMix->sand_code,
                        'step' => 0.01,
                    ],
                    [
                        'name' => 'الحصى (م³)',
                        'value' => $editConcreteMix->gravel,
                        'code' => $editConcreteMix->gravel_code,
                        'step' => 0.01,
                    ],
                    [
                        'name' => 'الماء (لتر)',
                        'value' => $editConcreteMix->water,
                        'code' => $editConcreteMix->water_code,
                        'step' => 1,
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                @foreach ($materials as $material)
                    @php
                        $inventory = \App\Models\Inventory::where('code', $material['code'])->first();
                        $available = $inventory->quantity_total ?? 0;
                        $unit = $inventory->MeasurementUnit->name ?? '';
                        $unitCost = $inventory->unit_cost ?? 0;
                    @endphp

                    <div
                        class="space-y-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow">

                        <!-- اسم المادة -->
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">{{ $material['name'] }}</span>
                        </label>

                        <!-- عرض المخزون وسعر الوحدة -->
                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            <div>
                                الكمية الكلية بالمخزن:
                                <strong class="{{ $available == 0 ? 'text-red-500' : 'text-green-600' }}">
                                    {{ formatNumber($available) }}
                                </strong>
                                {{ $unit }}
                            </div>
                            <div>
                                سعر الوحدة:
                                <strong>{{ formatNumber($unitCost) }}</strong>
                            </div>
                        </div>

                        <!-- حقل الإدخال -->
                        <div>
                            <input type="number"
                                name="{{ strtolower(str_replace([' ', '(', ')', '×', '-'], ['_', '', '', '', ''], $material['name'])) }}"
                                value="{{ $material['value'] }}" step="{{ $material['step'] }}" min="0"
                                max="{{ $available }}" placeholder="أدخل الكمية" class="form-input w-full"
                                {{ $available == 0 ? 'disabled' : '' }}
                                oninput="checkMaterialQuantity(this, {{ $available }})">

                            <!-- رسالة الخطأ -->
                            <p class="text-red-600 text-sm mt-1 hidden">❌ الكمية لا تكفي</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <script>
                function checkMaterialQuantity(input, available) {
                    const errorMsg = input.parentElement.querySelector("p");

                    if (parseFloat(input.value) > available) {
                        input.value = available;
                        errorMsg.classList.remove("hidden");
                        input.classList.add("border-red-500");
                        alert("الكمية لا تكفي. الحد الأقصى المتوفر: " + available);
                    } else {
                        errorMsg.classList.add("hidden");
                        input.classList.remove("border-red-500");
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
                            <span class="text-white-dark">ادخل السعر للمتر المكعب الواحد</span>
                        </label>

                        <input name="price" class="form-input" inputmode="numeric" minlength="4" maxlength="8" required
                            placeholder="السعر للمتر المكعب الواحد" value="{{ $editConcreteMix->price }}"
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




            $table->string('sender_type');        // company - contractor
            $table->unsignedBigInteger('sender_id'); // id للشركة أو المقاول
         $table->string('company_code');
            $table->unsignedBigInteger('branch_id'); // الفرع التابع له الطلب
            $table->unsignedBigInteger('status_code'); // حالة الطلب

            
            $table->date('workdate	');
            $table->text('note')->nullable();

         