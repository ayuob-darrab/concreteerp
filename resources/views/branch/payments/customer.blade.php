@extends('layouts.app')

@section('page-title', 'دفعات الزبون - ' . $customerName)

@section('content')
    <div class="mb-5 flex items-center gap-3">
        <a href="{{ route('branch.payments.index') }}" class="btn btn-outline-secondary btn-sm">← رجوع</a>
        <h5 class="text-lg font-semibold dark:text-white-light">
            💰 دفعات الزبون: <span class="text-primary">{{ $customerName }}</span>
            <span class="text-sm text-gray-500">({{ $phone }})</span>
        </h5>
    </div>

    @if (session('success'))
        <div class="alert alert-success flex items-center mb-4"><span>{{ session('success') }}</span></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger flex items-center mb-4"><span>{{ session('error') }}</span></div>
    @endif

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <!-- قائمة الطلبات غير المدفوعة -->
        <div class="lg:col-span-2">
            <div class="panel">
                <h6 class="mb-4 font-semibold">📋 الطلبات المستحقة</h6>

                <div class="table-responsive">
                    <table class="table-striped table-hover">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>نوع الخلطة</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>الإجمالي</th>
                                <th>المدفوع</th>
                                <th>المتبقي</th>
                                <th>الحالة</th>
                                <th>الدفع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                @php
                                    $totalAmount = $order->price ?? 0;
                                    $unitPrice = ($order->quantity > 0) ? $totalAmount / $order->quantity : $totalAmount;
                                    $paidAmount = $order->paid_amount ?? 0;
                                    $remainingAmount = $totalAmount - $paidAmount;
                                @endphp
                                <tr>
                                    <td class="font-mono">#{{ $order->id }}</td>
                                    <td>{{ $order->concreteMix->name ?? '-' }}</td>
                                    <td>{{ $order->quantity }} م³</td>
                                    <td>{{ number_format($unitPrice, 0) }}</td>
                                    <td>{{ number_format($totalAmount, 0) }}</td>
                                    <td class="text-success">{{ number_format($paidAmount, 0) }}</td>
                                    <td class="font-bold text-danger">{{ number_format($remainingAmount, 0) }}</td>
                                    <td>
                                        @if ($remainingAmount <= 0)
                                            <span class="badge bg-success">مدفوع</span>
                                        @elseif ($paidAmount > 0)
                                            <span class="badge bg-warning">جزئي</span>
                                        @else
                                            <span class="badge bg-danger">غير مدفوع</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($remainingAmount <= 0)
                                            <span class="text-success">✅ مدفوع</span>
                                        @else
                                            <span class="text-danger">{{ number_format($remainingAmount, 0) }} د.ع</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                                <td colspan="4" class="text-right">الإجمالي</td>
                                <td>{{ number_format($grandTotal, 0) }}</td>
                                <td class="text-success">{{ number_format($grandPaid, 0) }}</td>
                                <td class="font-bold text-danger">{{ number_format($grandRemaining, 0) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- سجل المدفوعات السابقة -->
            @if ($paymentHistory->count() > 0)
                <div class="panel mt-5">
                    <h6 class="mb-4 font-semibold">📜 سجل المدفوعات السابقة</h6>
                    <div class="table-responsive">
                        <table class="table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الدفعة</th>
                                    <th>رقم الطلب</th>
                                    <th>نوع الدفع</th>
                                    <th>الإجمالي</th>
                                    <th>المدفوع</th>
                                    <th>المتبقي</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>طباعة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paymentHistory as $payment)
                                    <tr>
                                        <td class="font-mono text-sm">{{ $payment->payment_number }}</td>
                                        <td class="font-mono">#{{ $payment->work_order_id }}</td>
                                        <td>{{ $payment->payment_type_text }}</td>
                                        <td>{{ number_format($payment->total_amount, 0) }}</td>
                                        <td class="text-success">{{ number_format($payment->paid_amount, 0) }}</td>
                                        <td class="text-danger">{{ number_format($payment->remaining_amount, 0) }}</td>
                                        <td><span
                                                class="badge bg-{{ $payment->status_color }}">{{ $payment->status_text }}</span>
                                        </td>
                                        <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('branch.payments.invoice', $payment->id) }}" target="_blank"
                                                class="btn btn-sm btn-outline-info">🖨</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- نموذج الدفع -->
        <div class="lg:col-span-1">
            <div class="panel sticky top-5" id="paymentFormPanel">
                <h6 class="mb-4 font-semibold">💳 تسجيل دفعة</h6>

                @if ($grandRemaining > 0)
                    <form action="{{ route('branch.payments.store') }}" method="POST" id="paymentForm"
                        x-data="{
                            paymentType: 'cash',
                            paymentMethod: '',
                            amount: {{ $grandRemaining }},
                            maxAmount: {{ $grandRemaining }},
                        }">
                        @csrf
                        <input type="hidden" name="customer_phone" value="{{ $phone }}">
                        <input type="hidden" name="total_debt" value="{{ $grandRemaining }}">

                        <!-- معلومات إجمالي المديونية -->
                        <div
                            class="mb-4 p-4 rounded-lg bg-gradient-to-r from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-700 border border-blue-200 dark:border-gray-600">
                            <h6 class="font-bold text-blue-800 dark:text-blue-300 mb-2">💰 ملخص المديونية</h6>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">الإجمالي</p>
                                    <p class="font-bold text-lg">{{ number_format($grandTotal, 0) }} د.ع</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">المدفوع</p>
                                    <p class="font-bold text-lg text-success">{{ number_format($grandPaid, 0) }} د.ع</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">المتبقي</p>
                                    <p class="font-bold text-xl text-danger">{{ number_format($grandRemaining, 0) }} د.ع
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- نوع الدفع -->
                        <div class="mb-4">
                            <label class="mb-2 block font-semibold">نوع الدفع <span class="text-danger">*</span></label>
                            <select name="payment_type" class="form-select" x-model="paymentType" required>
                                <option value="cash">💵 كاش (دفع فوري)</option>
                                <option value="deferred">📋 آجل (دفع لاحقاً)</option>
                            </select>
                        </div>

                        <!-- طريقة الدفع -->
                        <div class="mb-4" x-show="paymentType === 'cash'">
                            <label class="mb-2 block font-semibold">طريقة الدفع <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" x-model="paymentMethod"
                                x-bind:required="paymentType === 'cash'">
                                <option value="">اختر طريقة الدفع</option>
                                <option value="cash">نقدي</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                                <option value="check">شيك</option>
                                <option value="online">دفع إلكتروني</option>
                            </select>
                        </div>

                        <!-- اختيار البطاقة -->
                        <div class="mb-4" x-show="paymentType === 'cash' && paymentMethod === 'online'">
                            <label class="mb-2 block font-semibold">اختر البطاقة <span class="text-danger">*</span></label>
                            <select name="company_payment_card_id" class="form-select"
                                x-bind:required="paymentType === 'cash' && paymentMethod === 'online'">
                                <option value="">-- اختر بطاقة الدفع --</option>
                                @foreach ($paymentCards as $card)
                                    <option value="{{ $card->id }}">
                                        {{ $card->card_name }} ({{ $card->card_number_masked }}) -
                                        {{ number_format($card->current_balance, 0) }} دينار
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-gray-500">سيتم إيداع المبلغ في البطاقة المختارة</small>
                        </div>

                        <!-- المبلغ المدفوع -->
                        <div class="mb-4" x-show="paymentType === 'cash'">
                            <label class="mb-2 block font-semibold">المبلغ المدفوع (دينار) <span
                                    class="text-danger">*</span></label>
                            <div class="relative">
                                <input type="number" name="amount" class="form-input pr-12" x-model="amount"
                                    :max="maxAmount" min="0.01" step="0.01"
                                    x-bind:required="paymentType === 'cash'"
                                    :placeholder="'الحد الأقصى: ' + maxAmount.toLocaleString() + ' د.ع'">
                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-sm">د.ع</span>
                                </div>
                            </div>
                            <small class="text-gray-500">
                                <span x-show="amount > 0 && amount < maxAmount" class="text-warning">⚠️ دفع جزئي - سيتم
                                    توزيع المبلغ على الطلبات</span>
                                <span x-show="amount >= maxAmount && amount > 0" class="text-success">✅ دفع كامل - سداد
                                    جميع المديونية</span>
                            </small>
                        </div>

                        <!-- رسالة الدفع الآجل -->
                        <div class="mb-4" x-show="paymentType === 'deferred'">
                            <div class="bg-warning/10 border border-warning rounded-lg p-4">
                                <div class="flex items-center gap-2 text-warning">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-semibold">دفع آجل</span>
                                </div>
                                <p class="text-sm mt-2 text-gray-600">سيتم تسجيل المبلغ كدين على الزبون.</p>
                            </div>
                        </div>

                        <!-- رقم المرجع -->
                        <div class="mb-4" x-show="paymentType === 'cash'">
                            <label class="mb-2 block font-semibold">رقم الإيصال/المرجع</label>
                            <input type="text" name="reference_number" class="form-input" placeholder="رقم الإيصال">
                        </div>

                        <!-- ملاحظات -->
                        <div class="mb-4">
                            <label class="mb-2 block font-semibold">ملاحظات</label>
                            <textarea name="notes" class="form-textarea" rows="2" placeholder="ملاحظات إضافية"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">
                            💰 تسجيل الدفعة
                        </button>
                    </form>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <h6 class="text-xl font-semibold text-success mb-2">✅ مبروك!</h6>
                        <p>جميع طلبات هذا العميل مدفوعة بالكامل</p>
                        <a href="{{ route('branch.payments.index') }}" class="btn btn-primary mt-3">← رجوع للقائمة</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // تحديد المبلغ تلقائياً عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            const maxAmount = {{ $grandRemaining ?? 0 }};
            console.log('إجمالي المديونية المستحقة: ' + maxAmount.toLocaleString() + ' دينار');
        });
    </script>
@endsection
