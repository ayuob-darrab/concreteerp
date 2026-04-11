{{--
    حقول طرق الدفع لبطاقات الفرع (CompanyPaymentCard)
    المتغيرات: $paymentMethods, $paymentCards، واختياريًا $maintenance، $requirePayment (bool)
--}}
@php
    $m = $maintenance ?? null;
    $defaultPm = old('payment_method', $m->payment_method ?? 'cash');
    $requirePayment = filter_var($requirePayment ?? true, FILTER_VALIDATE_BOOLEAN);
@endphp
<div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600"
    x-data="{ paymentMethod: @js($defaultPm) }">
    <h5 class="font-bold mb-4 flex items-center gap-2 text-gray-900 dark:text-gray-100">
        <span>💳</span>
        <span>طريقة الدفع</span>
    </h5>
    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">تظهر بطاقات الدفع المفعّلة لهذا الفرع فقط عند اختيار «دفع إلكتروني».</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label for="payment_method" class="block font-medium mb-2">
                طريقة الدفع
                @if ($requirePayment)
                    <span class="text-red-500">*</span>
                @endif
            </label>
            <select name="payment_method" id="payment_method" x-model="paymentMethod"
                class="form-select w-full @error('payment_method') border-red-500 @enderror"
                @if ($requirePayment) required @endif>
                @foreach ($paymentMethods as $key => $label)
                    <option value="{{ $key }}" @selected(old('payment_method', $m->payment_method ?? 'cash') == $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('payment_method')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2" x-show="paymentMethod === 'online'" x-cloak x-transition>
            <label for="company_payment_card_id" class="block font-medium mb-2">
                بطاقة الدفع
                @if ($requirePayment)
                    <span class="text-red-500">*</span>
                @endif
            </label>
            @if ($paymentCards->isEmpty())
                <p class="text-sm text-amber-600 dark:text-amber-400 rounded-lg bg-amber-500/10 p-3">
                    لا توجد بطاقات دفع مفعّلة لهذا الفرع. أضف بطاقة من إعدادات الدفع أو اختر طريقة أخرى.
                </p>
            @else
                <select name="company_payment_card_id" id="company_payment_card_id"
                    class="form-select w-full @error('company_payment_card_id') border-red-500 @enderror"
                    :required="paymentMethod === 'online' && @js($requirePayment)">
                    <option value="">— اختر البطاقة —</option>
                    @foreach ($paymentCards as $card)
                        <option value="{{ $card->id }}"
                            @selected((string) old('company_payment_card_id', $m->company_payment_card_id ?? '') === (string) $card->id)>
                            {{ $card->card_name }}
                            @if ($card->card_number_masked)
                                ({{ $card->card_number_masked }})
                            @endif
                            — {{ \App\Models\CompanyPaymentCard::$cardTypes[$card->card_type] ?? $card->card_type }}
                        </option>
                    @endforeach
                </select>
            @endif
            @error('company_payment_card_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2" x-show="paymentMethod === 'bank_transfer' || paymentMethod === 'check'" x-cloak
            x-transition>
            <label for="payment_reference" class="block font-medium mb-2">مرجع الدفع / رقم الشيك / الحوالة</label>
            <input type="text" name="payment_reference" id="payment_reference" maxlength="120"
                value="{{ old('payment_reference', $m->payment_reference ?? '') }}" class="form-input w-full"
                placeholder="اختياري">
            @error('payment_reference')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
