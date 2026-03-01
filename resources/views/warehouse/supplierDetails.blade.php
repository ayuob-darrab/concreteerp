@extends('layouts.app')

@section('page-title', 'تفاصيل المورد: ' . $supplier->supplier_name)

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="panel">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-truck text-primary ml-2"></i>
                        {{ $supplier->supplier_name }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $supplier->company_name ?? 'بدون شركة' }}</p>
                </div>
                <a href="{{ route('warehouse.show', 'addSupplier') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                @if (session('print_payment_id'))
                    <a href="{{ route('suppliers.payment.print', session('print_payment_id')) }}" target="_blank"
                        class="btn btn-sm btn-success mr-auto">
                        <i class="fas fa-print ml-1"></i>
                        طباعة الإيصال
                    </a>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- معلومات المورد --}}
            <div class="panel">
                <h5 class="text-lg font-semibold mb-4 border-b pb-2">
                    <i class="fas fa-info-circle text-info ml-2"></i>
                    معلومات المورد
                </h5>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">الفرع:</span>
                        <span class="font-medium">{{ $supplier->branchName->branch_name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">الهاتف:</span>
                        <span class="font-medium" dir="ltr">{{ $supplier->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">العنوان:</span>
                        <span class="font-medium">{{ $supplier->address ?? '-' }}</span>
                    </div>
                    @if ($supplier->note)
                        <div class="pt-2 border-t">
                            <span class="text-gray-500 block mb-1">ملاحظات:</span>
                            <p class="text-sm">{{ $supplier->note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ملخص مالي --}}
            <div class="panel">
                <h5 class="text-lg font-semibold mb-4 border-b pb-2">
                    <i class="fas fa-chart-pie text-warning ml-2"></i>
                    الملخص المالي
                </h5>
                <div class="space-y-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <p class="text-sm text-blue-600 dark:text-blue-400">الرصيد الافتتاحي</p>
                        <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                            {{ number_format($supplier->opening_balance, 0) }}
                        </p>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <p class="text-sm text-green-600 dark:text-green-400">إجمالي المدفوعات</p>
                        <p class="text-2xl font-bold text-green-700 dark:text-green-300">
                            {{ number_format($supplier->total_paid, 0) }}
                        </p>
                    </div>
                    <div
                        class="{{ $supplier->remaining_balance > 0 ? 'bg-red-50 dark:bg-red-900/20' : 'bg-gray-50 dark:bg-gray-800' }} rounded-lg p-4">
                        <p
                            class="text-sm {{ $supplier->remaining_balance > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                            الرصيد المتبقي</p>
                        <p
                            class="text-2xl font-bold {{ $supplier->remaining_balance > 0 ? 'text-red-700 dark:text-red-300' : 'text-gray-700 dark:text-gray-300' }}">
                            {{ number_format($supplier->remaining_balance, 0) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- نموذج التسديد --}}
            <div class="panel">
                <h5 class="text-lg font-semibold mb-4 border-b pb-2 flex items-center gap-2">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-success/20">
                        <svg class="w-5 h-5 text-success" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                    </div>
                    تسديد دفعة
                </h5>

                @if ($supplier->remaining_balance > 0)
                    <form action="{{ route('suppliers.payment.store', $supplier->id) }}" method="POST" id="paymentForm">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">المبلغ <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="amount" id="paymentAmount" class="form-input" required
                                    placeholder="أدخل مبلغ التسديد" oninput="formatPrice(this); validateAmount();">
                                <p class="text-xs text-gray-500 mt-1">الحد الأقصى:
                                    {{ number_format($supplier->remaining_balance, 0) }}</p>
                                <p id="amountError" class="text-xs text-danger mt-1 hidden"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">طريقة الدفع <span
                                        class="text-danger">*</span></label>
                                <select name="payment_method" id="paymentMethod" class="form-select" required
                                    onchange="toggleReferenceField()">
                                    <option value="cash">نقدي</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="check">شيك</option>
                                </select>
                            </div>

                            <div id="referenceField" class="hidden">
                                <label class="block text-sm font-medium mb-1">رقم المرجع</label>
                                <input type="text" name="reference_number" class="form-input"
                                    placeholder="رقم الشيك أو رقم التحويل">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">ملاحظات</label>
                                <textarea name="notes" class="form-input" rows="2" placeholder="ملاحظات إضافية (اختياري)"></textarea>
                            </div>

                            <button type="submit" id="submitBtn"
                                class="btn btn-success w-full flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S15.33 8 14.5 8 13 8.67 13 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S8.33 8 7.5 8 6 8.67 6 9.5 6.67 11 7.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
                                </svg>
                                تأكيد التسديد
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-8">
                        <div
                            class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-double text-3xl text-green-500"></i>
                        </div>
                        <p class="text-gray-500">تم تسديد كامل المبلغ</p>
                        <p class="text-sm text-gray-400 mt-1">لا يوجد رصيد مستحق</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- سجل الدفعات --}}
        <div class="panel">
            <h5 class="text-lg font-semibold mb-4 border-b pb-2">
                <i class="fas fa-history text-primary ml-2"></i>
                سجل الدفعات
                <span class="badge badge-primary mr-2">{{ $supplier->payments->count() }}</span>
            </h5>

            @if ($supplier->payments->count() > 0)
                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">رقم الإيصال</th>
                                <th class="text-center">التاريخ</th>
                                <th class="text-center">المبلغ</th>
                                <th class="text-center">الرصيد قبل</th>
                                <th class="text-center">الرصيد بعد</th>
                                <th class="text-center">طريقة الدفع</th>
                                <th class="text-center">بواسطة</th>
                                <th class="text-center">طباعة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($supplier->payments as $payment)
                                <tr>
                                    <td class="text-center">
                                        <span class="font-mono text-primary">{{ $payment->payment_number }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div>{{ $payment->created_at->format('Y/m/d') }}</div>
                                        <div class="text-xs text-gray-500">{{ $payment->created_at->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="font-bold text-success">{{ number_format($payment->amount, 0) }}</span>
                                    </td>
                                    <td class="text-center text-gray-500">
                                        {{ number_format($payment->balance_before, 0) }}
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($payment->balance_after, 0) }}
                                    </td>
                                    <td class="text-center">
                                        @if ($payment->payment_method == 'cash')
                                            <span class="badge badge-outline-success">نقدي</span>
                                        @elseif($payment->payment_method == 'bank_transfer')
                                            <span class="badge badge-outline-info">تحويل بنكي</span>
                                        @elseif($payment->payment_method == 'online')
                                            <span class="badge badge-outline-primary">إلكتروني</span>
                                            @if ($payment->paymentCard)
                                                <div class="text-xs text-gray-500 mt-1">{{ $payment->paymentCard->card_name }}</div>
                                            @endif
                                        @else
                                            <span class="badge badge-outline-warning">شيك</span>
                                        @endif
                                        @if ($payment->reference_number)
                                            <div class="text-xs text-gray-500 mt-1">{{ $payment->reference_number }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center text-sm">
                                        {{ $payment->createdBy->fullname ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('suppliers.payment.print', $payment->id) }}" target="_blank"
                                            class="inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all duration-200"
                                            title="طباعة الإيصال">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z" />
                                            </svg>
                                            طباعة
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-receipt text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">لا توجد دفعات مسجلة</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        const maxAmount = {{ $supplier->remaining_balance }};

        function togglePaymentFields() {
            const method = document.getElementById('paymentMethod').value;
            const refField = document.getElementById('referenceField');
            const cardField = document.getElementById('cardField');
            const cardSelect = document.getElementById('cardSelect');

            refField.classList.add('hidden');
            cardField.classList.add('hidden');
            cardSelect.removeAttribute('required');

            if (method === 'bank_transfer' || method === 'check') {
                refField.classList.remove('hidden');
            } else if (method === 'online') {
                cardField.classList.remove('hidden');
                cardSelect.setAttribute('required', 'required');
            }
        }

        function validateAmount() {
            const input = document.getElementById('paymentAmount');
            const error = document.getElementById('amountError');
            const btn = document.getElementById('submitBtn');
            const value = parseFloat(input.value.replace(/,/g, '')) || 0;

            if (value > maxAmount) {
                error.textContent = 'المبلغ أكبر من الرصيد المتبقي!';
                error.classList.remove('hidden');
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            } else if (value <= 0) {
                error.textContent = 'يجب إدخال مبلغ صحيح';
                error.classList.remove('hidden');
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                error.classList.add('hidden');
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        // تأكيد قبل الإرسال
        document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
            const amount = document.getElementById('paymentAmount').value;
            if (!confirm('هل أنت متأكد من تسديد مبلغ ' + amount + '؟')) {
                e.preventDefault();
            }
        });
    </script>
@endsection
