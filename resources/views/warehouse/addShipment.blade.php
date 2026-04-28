@extends('layouts.app')

@section('page-title', 'اضافة شحنة جديدة لمادة : ' . $material->name)

@section('content')

    {{-- <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">     cols-1 يمثل عد الاعمده --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light text-center">
                    <span>إضافة شحنة جديدة للمادة:</span>
                    <span class="text-primary font-bold"> {{ $material->name }} </span>
                    <span>— التابعة لفرع:</span>
                    <span class="text-primary font-bold"> {{ $material->branchName->branch_name }} </span>
                </h5>

            </div>

            <form action="{{ route('warehouse.update', $material->code) }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">


                <input type="hidden" name="branch_id" value="{{ $material->branch_id }}">
                <input type="hidden" name="material_unit" value="{{ $material->unit }}">

                <input type="hidden" name="ReturnUrl" value="{{ $ReturnUrl }}">
                <!-- اختيار المورد -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">اسم المورد <span class="text-danger">*</span></span>
                    </label>
                    <select name="supplier_id" id="supplier_id" class="form-select" required>
                        <option value="">اختر المورد</option>
                        @foreach ($Supplier as $sup)
                            <option value="{{ $sup->id }}"
                                {{ old('supplier_id', $material->supplier_id ?? '') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-3 ">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">الكمية ({{ $material->unit === 'ton' ? 'كيس' : ($material->MeasurementUnit->name ?? $material->unit) }}) <span class="text-danger">*</span></span>
                    </label>
                    <input name="quantity" type="number" min="0.0001" step="any" required id="quantity" placeholder="أدخل الكمية"
                        class="form-input">
                    @error('quantity')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- السعر -->
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">السعر الكلي <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="price" id="price" placeholder="أدخل السعر" class="form-input"
                        inputmode="decimal" inputmode="numeric" maxlength="8" required oninput="formatPrice(this); syncPaidAmountLimit();">
                    @error('price')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">نوع الدفع <span class="text-danger">*</span></span>
                    </label>
                    <select name="payment_term" id="payment_term" class="form-select" required onchange="togglePaymentOptions()">
                        <option value="deferred">آجل</option>
                        <option value="immediate">دفع فوري</option>
                    </select>
                    @error('payment_term')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div id="immediate_payment_section" class="space-y-3 hidden">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">طريقة الدفع الفوري <span class="text-danger">*</span></span>
                    </label>
                    <select name="payment_method" id="payment_method" class="form-select" onchange="toggleCardSection()">
                        <option value="">اختر طريقة الدفع</option>
                        <option value="cash">نقدي</option>
                        <option value="online">إلكتروني</option>
                    </select>
                    @error('payment_method')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror

                    <label class="inline-flex cursor-pointer mt-3">
                        <span class="text-white-dark">المبلغ المدفوع الآن <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="paid_amount" id="paid_amount" class="form-input"
                        inputmode="decimal" maxlength="12"
                        placeholder="أدخل المبلغ المدفوع فوراً" oninput="formatPrice(this); syncPaidAmountLimit();">
                    <small class="text-gray-500">يمكنك إدخال جزء من قيمة الشحنة، والباقي يبقى آجل على المورد.</small>
                    @error('paid_amount')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <div id="card_section" class="space-y-3 hidden">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">بطاقة الشركة <span class="text-danger">*</span></span>
                    </label>
                    <select name="company_payment_card_id" id="company_payment_card_id" class="form-select">
                        <option value="">اختر البطاقة</option>
                        @foreach ($companyCards as $card)
                            <option value="{{ $card->id }}">
                                {{ $card->card_name }} - الرصيد: {{ number_format($card->current_balance, 0) }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_payment_card_id')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <script>
                    function formatPrice(input) {
                        let value = input.value.replace(/,/g, '');
                        if (!/^\d*\.?\d*$/.test(value)) {
                            input.value = input.value.slice(0, -1);
                            return;
                        }
                        const parts = value.split('.');
                        let integerPart = parts[0];
                        const decimalPart = parts[1] ? '.' + parts[1].slice(0, 2) : '';
                        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        input.value = integerPart + decimalPart;
                    }

                    function togglePaymentOptions() {
                        const term = document.getElementById('payment_term').value;
                        const immediateSection = document.getElementById('immediate_payment_section');
                        const paymentMethod = document.getElementById('payment_method');
                        const paidAmount = document.getElementById('paid_amount');

                        immediateSection.classList.toggle('hidden', term !== 'immediate');
                        paymentMethod.required = term === 'immediate';
                        paidAmount.required = term === 'immediate';
                        syncPaidAmountLimit();

                        if (term !== 'immediate') {
                            paymentMethod.value = '';
                            paidAmount.value = '';
                            toggleCardSection();
                        }
                    }

                    function toggleCardSection() {
                        const method = document.getElementById('payment_method').value;
                        const cardSection = document.getElementById('card_section');
                        const cardSelect = document.getElementById('company_payment_card_id');

                        const showCards = method === 'online';
                        cardSection.classList.toggle('hidden', !showCards);
                        cardSelect.required = showCards;

                        if (!showCards) {
                            cardSelect.value = '';
                        }
                    }

                    function parseMoney(raw) {
                        const n = parseFloat(String(raw || '').replace(/,/g, ''));
                        return isNaN(n) ? 0 : n;
                    }

                    function syncPaidAmountLimit() {
                        const totalPrice = parseMoney(document.getElementById('price').value);
                        const paidAmountInput = document.getElementById('paid_amount');
                        if (!paidAmountInput) return;
                        if (totalPrice > 0) {
                            paidAmountInput.max = totalPrice.toFixed(2);
                        } else {
                            paidAmountInput.removeAttribute('max');
                        }
                    }
                </script>


                <!-- الملاحظات -->
                <div class="space-y-3 ">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات الشحنة</span>
                    </label>
                    <input name="note" id="note" placeholder="أدخل الملاحظات إن وجدت" class="form-input">
                    @error('note')
                        <div class="text-danger text-sm">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الأزرار -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                    <button type="submit" name="active" value="AddNewShipment"
                        class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-check-circle"></i>
                        <span> حفظ معلومات الشحنة</span>
                    </button>

                    <button type="reset"
                        class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-times-circle"></i>
                        <span>إلغاء</span>
                    </button>
                </div>
            </div>


            </form>

        </div>

    </div>
@endsection
