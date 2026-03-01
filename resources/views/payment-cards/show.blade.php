@extends('layouts.app')

@section('page-title', 'تفاصيل البطاقة - ' . $card->card_name)

@section('content')
    <!-- معلومات البطاقة -->
    <div class="panel mb-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('payment-cards.index') }}" class="btn btn-outline-secondary btn-sm">
                    <svg class="h-5 w-5 ltr:mr-2 rtl:ml-2" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    رجوع
                </a>
                <h5 class="text-lg font-semibold dark:text-white-light">
                    <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                    {{ $card->card_name }}
                </h5>
            </div>
            <div class="flex gap-2">
                <span class="badge badge-outline-primary">{{ $card->card_type_name }}</span>
                <span class="badge badge-outline-{{ $card->status_color }}">{{ $card->status_text }}</span>
            </div>
        </div>

        <!-- بطاقات المعلومات -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="rounded-lg bg-primary-light p-4 dark:bg-primary/10">
                <div class="mb-1 text-xs text-gray-600 dark:text-gray-300">صاحب البطاقة</div>
                <div class="text-lg font-semibold text-primary">{{ $card->holder_name }}</div>
            </div>
            <div class="rounded-lg bg-info-light p-4 dark:bg-info/10">
                <div class="mb-1 text-xs text-gray-600 dark:text-gray-300">رقم البطاقة</div>
                <div class="text-lg font-mono font-semibold text-info" dir="ltr">{{ $card->card_number_masked }}</div>
            </div>
            <div class="rounded-lg bg-success-light p-4 dark:bg-success/10">
                <div class="mb-1 text-xs text-gray-600 dark:text-gray-300">الرصيد الحالي</div>
                <div class="text-2xl font-bold text-success">{{ number_format($card->current_balance, 0) }}</div>
                <div class="text-xs text-gray-500">دينار</div>
            </div>
            <div class="rounded-lg bg-warning-light p-4 dark:bg-warning/10">
                <div class="mb-1 text-xs text-gray-600 dark:text-gray-300">الرصيد الافتتاحي</div>
                <div class="text-lg font-semibold text-warning">{{ number_format($card->opening_balance, 0) }}</div>
                <div class="text-xs text-gray-500">دينار</div>
            </div>
        </div>

        <!-- إحصائيات المعاملات -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
            <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b] text-center">
                <div class="text-2xl font-bold text-success">{{ number_format($stats['total_deposits'], 0) }}</div>
                <div class="text-sm text-gray-500">إجمالي الإيداعات</div>
            </div>
            <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b] text-center">
                <div class="text-2xl font-bold text-danger">{{ number_format($stats['total_withdrawals'], 0) }}</div>
                <div class="text-sm text-gray-500">إجمالي السحوبات</div>
            </div>
            <div class="rounded-lg border border-[#e0e6ed] p-4 dark:border-[#1b2e4b] text-center">
                <div class="text-2xl font-bold text-primary">{{ $stats['transactions_count'] }}</div>
                <div class="text-sm text-gray-500">عدد المعاملات</div>
            </div>
        </div>

        <!-- أزرار الإجراءات -->
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('payment-cards.edit', $card->id) }}" class="btn btn-warning">
                <svg class="h-4 w-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
                تعديل البطاقة
            </a>
            <button type="button" class="btn btn-success" onclick="openDepositModal()">
                <svg class="h-4 w-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12">
                    </path>
                </svg>
                إيداع
            </button>
            <button type="button" class="btn btn-danger" onclick="openWithdrawModal()">
                <svg class="h-4 w-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6">
                    </path>
                </svg>
                سحب
            </button>
        </div>
    </div>

    <!-- جدول المعاملات -->
    <div class="panel">
        <div class="mb-5 flex items-center justify-between">
            <h6 class="text-base font-semibold">
                <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                سجل المعاملات
            </h6>
        </div>

        @if ($card->transactions->count() > 0)
            <div class="table-responsive">
                <table class="table-hover">
                    <thead>
                        <tr>
                            <th>رقم المعاملة</th>
                            <th>النوع</th>
                            <th>المبلغ</th>
                            <th>الرصيد قبل</th>
                            <th>الرصيد بعد</th>
                            <th>المرجع</th>
                            <th>الشركة</th>
                            <th>الوصف</th>
                            <th>التاريخ</th>
                            <th>بواسطة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($card->transactions as $transaction)
                            <tr>
                                <td class="font-mono text-sm">{{ $transaction->transaction_number }}</td>
                                <td>
                                    <span class="badge badge-outline-{{ $transaction->type_color }}">
                                        {{ $transaction->type_name }}
                                    </span>
                                </td>
                                <td
                                    class="font-semibold {{ $transaction->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 0) }}
                                </td>
                                <td>{{ number_format($transaction->balance_before, 0) }}</td>
                                <td class="font-semibold">{{ number_format($transaction->balance_after, 0) }}</td>
                                <td>
                                    @if ($transaction->reference_type)
                                        <span
                                            class="badge badge-outline-info">{{ $transaction->reference_type_name }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($transaction->company)
                                        {{ $transaction->company->name }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->description ?? '-' }}</td>
                                <td>{{ $transaction->created_at->format('Y/m/d H:i') }}</td>
                                <td>{{ $transaction->creator?->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">لا توجد معاملات مسجلة</p>
            </div>
        @endif
    </div>

    <!-- Modal الإيداع -->
    <div id="depositModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeDepositModal()"></div>
            <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-[#1b2e4b]">
                <h5 class="mb-4 text-lg font-semibold">إيداع في البطاقة</h5>
                <form action="{{ route('payment-cards.deposit', $card->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="mb-2 block font-semibold">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-input" min="1" required
                            placeholder="أدخل المبلغ">
                    </div>
                    <div class="mb-4">
                        <label class="mb-2 block font-semibold">الوصف</label>
                        <input type="text" name="description" class="form-input" placeholder="وصف اختياري">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="closeDepositModal()">إلغاء</button>
                        <button type="submit" class="btn btn-success">إيداع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal السحب -->
    <div id="withdrawModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeWithdrawModal()"></div>
            <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-[#1b2e4b]">
                <h5 class="mb-4 text-lg font-semibold">سحب من البطاقة</h5>
                <p class="mb-4 text-sm text-gray-500">الرصيد المتاح:
                    <strong>{{ number_format($card->current_balance, 0) }}</strong> دينار
                </p>
                <form action="{{ route('payment-cards.withdraw', $card->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="mb-2 block font-semibold">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-input" min="1"
                            max="{{ $card->current_balance }}" required placeholder="أدخل المبلغ">
                    </div>
                    <div class="mb-4">
                        <label class="mb-2 block font-semibold">الوصف</label>
                        <input type="text" name="description" class="form-input" placeholder="وصف اختياري">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="closeWithdrawModal()">إلغاء</button>
                        <button type="submit" class="btn btn-danger">سحب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDepositModal() {
            document.getElementById('depositModal').classList.remove('hidden');
        }

        function closeDepositModal() {
            document.getElementById('depositModal').classList.add('hidden');
        }

        function openWithdrawModal() {
            document.getElementById('withdrawModal').classList.remove('hidden');
        }

        function closeWithdrawModal() {
            document.getElementById('withdrawModal').classList.add('hidden');
        }
    </script>
@endsection
