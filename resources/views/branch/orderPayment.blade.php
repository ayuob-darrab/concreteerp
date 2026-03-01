@extends('layouts.app')

@section('page-title', 'تسديد الطلب #' . $order->id)

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- تفاصيل الطلب --}}
        <div class="panel lg:col-span-2">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    💳 تسديد الطلب #{{ $order->id }}
                </h5>
                @if ($order->request_type === 'direct')
                    <span class="badge bg-info/20 text-info px-3 py-1">⚡ طلب مباشر</span>
                @else
                    <span class="badge bg-primary/20 text-primary px-3 py-1">طلب عادي (مقاول)</span>
                @endif
            </div>

            {{-- معلومات الطلب --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-xs text-gray-400 mb-1">العميل</div>
                    <div class="font-bold">{{ $order->customer_name ?? 'غير محدد' }}</div>
                    <div class="text-sm text-gray-500">{{ $order->customer_phone ?? '-' }}</div>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-xs text-gray-400 mb-1">نوع الخلطة</div>
                    <div class="font-bold">{{ $order->concreteMix->classification ?? '-' }}</div>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-xs text-gray-400 mb-1">الكمية</div>
                    <div class="font-bold">{{ $order->quantity }} م³</div>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-xs text-gray-400 mb-1">سعر المتر</div>
                    <div class="font-bold text-primary">{{ number_format($order->price ?? 0) }} د.ع</div>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-xs text-gray-400 mb-1">الموقع</div>
                    <div class="font-bold text-sm">{{ $order->location ?? '-' }}</div>
                </div>
                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="text-xs text-gray-400 mb-1">تاريخ التنفيذ</div>
                    <div class="font-bold">{{ $order->execution_date ? $order->execution_date->format('Y-m-d') : '-' }}
                    </div>
                </div>
            </div>

            {{-- ملخص مالي --}}
            <div
                class="p-4 rounded-lg border mb-6
                @if ($order->payment_status === 'paid') border-green-300 bg-green-50 dark:bg-green-900/20
                @elseif($order->payment_status === 'partial') border-yellow-300 bg-yellow-50 dark:bg-yellow-900/20
                @else border-red-300 bg-red-50 dark:bg-red-900/20 @endif">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-xs text-gray-400">إجمالي المبلغ</div>
                        <div class="text-xl font-bold text-dark dark:text-white">{{ number_format($totalAmount) }} د.ع</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">المدفوع</div>
                        <div class="text-xl font-bold text-success">{{ number_format($order->paid_amount ?? 0) }} د.ع</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">المتبقي</div>
                        <div class="text-xl font-bold text-danger">{{ number_format($remainingAmount) }} د.ع</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">حالة الدفع</div>
                        @if ($order->payment_status === 'paid')
                            <span class="badge bg-success text-white text-sm px-3 py-1">✅ مدفوع بالكامل</span>
                        @elseif($order->payment_status === 'partial')
                            <span class="badge bg-warning text-dark text-sm px-3 py-1">⏳ مدفوع جزئياً</span>
                        @else
                            <span class="badge bg-danger text-white text-sm px-3 py-1">❌ غير مدفوع</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- نموذج الدفع --}}
            @if ($remainingAmount > 0)
                {!! Form::open([
                    'route' => ['companyBranch.update', $order->id],
                    'method' => 'PUT',
                    'autocomplete' => 'off',
                    'id' => 'paymentForm',
                ]) !!}
                <input type="hidden" name="active" value="recordPayment">

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    {{-- المبلغ --}}
                    <div class="space-y-2">
                        <label class="text-white-dark">
                            المبلغ المراد تسديده <span class="text-danger">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="payment_amount_display" class="form-input text-lg font-bold"
                                placeholder="0" autocomplete="off" required>
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">د.ع</span>
                        </div>
                        <input type="hidden" name="payment_amount" id="payment_amount" value="0">
                        <div class="flex gap-2 mt-1">
                            <button type="button" onclick="setPaymentAmount({{ $remainingAmount }})"
                                class="btn btn-outline-success btn-sm">كامل المبلغ</button>
                            <button type="button" onclick="setPaymentAmount({{ $remainingAmount / 2 }})"
                                class="btn btn-outline-warning btn-sm">النصف</button>
                        </div>
                    </div>

                    {{-- طريقة الدفع --}}
                    <div class="space-y-2">
                        <label class="text-white-dark">
                            طريقة الدفع <span class="text-danger">*</span>
                        </label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">-- اختر طريقة الدفع --</option>
                            <option value="cash">💵 نقداً</option>
                            <option value="bank_transfer">🏦 حوالة بنكية</option>
                            <option value="check">📄 شيك</option>
                            <option value="card">💳 بطاقة إلكترونية</option>
                        </select>
                    </div>

                    {{-- ملاحظة --}}
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-white-dark">ملاحظة الدفع</label>
                        <textarea name="payment_note" class="form-input" rows="2" placeholder="ملاحظة اختيارية..."></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-6 border-t pt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-danger btn-sm px-4">إلغاء</a>
                    <button type="submit" class="btn btn-success px-6" id="submitPayment">
                        💰 تأكيد التسديد
                    </button>
                </div>

                {!! Form::close() !!}
            @else
                <div class="p-4 bg-green-100 dark:bg-green-900/30 rounded-lg text-center">
                    <div class="text-4xl mb-2">✅</div>
                    <div class="text-lg font-bold text-success">تم تسديد كامل المبلغ</div>
                    @if ($order->paid_at)
                        <div class="text-sm text-gray-500 mt-1">بتاريخ {{ $order->paid_at->format('Y-m-d H:i') }}</div>
                    @endif
                </div>
            @endif
        </div>

        {{-- الشريط الجانبي - سجل المدفوعات --}}
        <div class="panel">
            <h5 class="text-lg font-semibold dark:text-white-light mb-4">📋 سجل المدفوعات</h5>

            @if ($paymentReceipts->count() > 0)
                <div class="space-y-3">
                    @foreach ($paymentReceipts as $receipt)
                        <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-success">{{ number_format($receipt->amount) }} د.ع</span>
                                <span
                                    class="badge bg-{{ $receipt->status === 'confirmed' ? 'success' : ($receipt->status === 'pending' ? 'warning' : 'danger') }}/20
                                    text-{{ $receipt->status === 'confirmed' ? 'success' : ($receipt->status === 'pending' ? 'warning' : 'danger') }} text-xs">
                                    {{ $receipt->status === 'confirmed' ? 'مؤكد' : ($receipt->status === 'pending' ? 'معلق' : 'ملغي') }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $receipt->created_at->format('Y-m-d H:i') }}
                                •
                                @switch($receipt->payment_method)
                                    @case('cash')
                                        نقداً
                                    @break

                                    @case('bank_transfer')
                                        حوالة بنكية
                                    @break

                                    @case('check')
                                        شيك
                                    @break

                                    @case('card')
                                        بطاقة
                                    @break

                                    @default
                                        {{ $receipt->payment_method }}
                                @endswitch
                            </div>
                            @if ($receipt->description)
                                <div class="text-xs text-gray-500 mt-1">{{ $receipt->description }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-400">
                    <div class="text-3xl mb-2">📭</div>
                    <p>لا توجد مدفوعات مسجلة</p>
                </div>
            @endif

            <div class="mt-4 pt-4 border-t">
                <a href="{{ url('companyBranch/ordersInProgress') }}" class="btn btn-outline-primary w-full">
                    ← العودة للطلبات
                </a>
            </div>
        </div>
    </div>

    <script>
        function formatNumber(num) {
            if (!num && num !== 0) return '';
            return parseFloat(num).toLocaleString('en-US');
        }

        function unformatNumber(str) {
            if (!str) return 0;
            return parseFloat(str.replace(/,/g, '')) || 0;
        }

        function setPaymentAmount(amount) {
            amount = Math.ceil(amount);
            document.getElementById('payment_amount').value = amount;
            document.getElementById('payment_amount_display').value = formatNumber(amount);
        }

        document.getElementById('payment_amount_display').addEventListener('input', function() {
            const cursorPos = this.selectionStart;
            const oldLen = this.value.length;
            const rawValue = unformatNumber(this.value);
            document.getElementById('payment_amount').value = rawValue;
            if (rawValue > 0) {
                this.value = formatNumber(rawValue);
                const newLen = this.value.length;
                this.setSelectionRange(cursorPos + (newLen - oldLen), cursorPos + (newLen - oldLen));
            }
        });

        document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
            const amount = parseFloat(document.getElementById('payment_amount').value) || 0;
            const remaining = {{ $remainingAmount }};
            if (amount <= 0) {
                e.preventDefault();
                alert('يرجى إدخال مبلغ صحيح');
                return;
            }
            if (amount > remaining) {
                e.preventDefault();
                alert('المبلغ المدخل أكبر من المتبقي (' + formatNumber(remaining) + ' د.ع)');
                return;
            }
        });
    </script>
@endsection
