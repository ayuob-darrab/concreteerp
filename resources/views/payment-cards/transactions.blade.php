@extends('layouts.app')

@section('page-title', 'تقرير معاملات البطاقات')

@section('content')
    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-3">
        <div class="rounded-lg shadow p-4 flex justify-between" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: #fff;">
            <div>
                <p class="text-sm font-semibold" style="color: rgba(255,255,255,0.95);">إجمالي الإيداعات</p>
                <h4 class="mt-2 text-2xl font-bold" style="color: #fff;">{{ number_format($stats['total_deposits'], 0) }}</h4>
                <p class="text-xs" style="color: rgba(255,255,255,0.9);">دينار</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                <svg class="h-7 w-7" style="color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                </svg>
            </div>
        </div>

        <div class="rounded-lg shadow p-4 flex justify-between" style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); color: #fff;">
            <div>
                <p class="text-sm font-semibold" style="color: rgba(255,255,255,0.95);">إجمالي السحوبات</p>
                <h4 class="mt-2 text-2xl font-bold" style="color: #fff;">{{ number_format($stats['total_withdrawals'], 0) }}</h4>
                <p class="text-xs" style="color: rgba(255,255,255,0.9);">دينار</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                <svg class="h-7 w-7" style="color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                </svg>
            </div>
        </div>

        <div class="rounded-lg shadow p-4 flex justify-between" style="background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); color: #fff;">
            <div>
                <p class="text-sm font-semibold" style="color: rgba(255,255,255,0.95);">عدد المعاملات</p>
                <h4 class="mt-2 text-2xl font-bold" style="color: #fff;">{{ $stats['transactions_count'] }}</h4>
                <p class="text-xs" style="color: rgba(255,255,255,0.9);">معاملة</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                <svg class="h-7 w-7" style="color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-white-light">
                <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                تقرير معاملات البطاقات
            </h5>
            <a href="{{ route('payment-cards.index') }}" class="btn btn-outline-secondary btn-sm">
                <svg class="h-4 w-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 18L9 12L15 6"></path>
                </svg>
                رجوع للبطاقات
            </a>
        </div>

        <!-- فلاتر البحث: صف واحد (تمرير أفقي على الشاشات الضيقة) — ترتيب RTL: من ← إلى ← بطاقة ← نوع ← أزرار -->
        <form method="GET" class="mb-6">
            <div
                class="rounded-lg border border-[#e0e6ed] bg-[#f8f9fa] p-3 dark:border-[#1b2e4b] dark:bg-[#121e32] sm:p-4">
                <p class="mb-2 text-xs font-semibold text-gray-700 dark:text-gray-300">تصفية النتائج</p>

                <div class="-mx-1 overflow-x-auto pb-1 [scrollbar-width:thin]">
                    <div class="flex min-w-max flex-nowrap items-end gap-2 px-1 sm:gap-3">
                        <div class="w-[10rem] shrink-0 sm:w-[10.5rem]">
                            <label class="mb-1 block text-xs font-medium text-gray-800 dark:text-gray-200">من تاريخ</label>
                            <input type="date" name="date_from"
                                class="form-input w-full py-1.5 text-sm leading-tight"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="w-[10rem] shrink-0 sm:w-[10.5rem]">
                            <label class="mb-1 block text-xs font-medium text-gray-800 dark:text-gray-200">إلى تاريخ</label>
                            <input type="date" name="date_to"
                                class="form-input w-full py-1.5 text-sm leading-tight"
                                value="{{ request('date_to') }}">
                        </div>
                        <div class="w-[12rem] shrink-0 sm:w-[14rem]">
                            <label class="mb-1 block text-xs font-medium text-gray-800 dark:text-gray-200">البطاقة</label>
                            <select name="card_id" class="form-select w-full py-1.5 text-sm leading-tight">
                                <option value="">جميع البطاقات</option>
                                @foreach ($cards as $card)
                                    <option value="{{ $card->id }}" {{ request('card_id') == $card->id ? 'selected' : '' }}>
                                        {{ $card->card_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-[8.5rem] shrink-0 sm:w-[9rem]">
                            <label class="mb-1 block text-xs font-medium text-gray-800 dark:text-gray-200">نوع المعاملة</label>
                            <select name="type" class="form-select w-full py-1.5 text-sm leading-tight">
                                <option value="">الكل</option>
                                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>إيداع</option>
                                <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>سحب</option>
                            </select>
                        </div>
                        <div class="flex shrink-0 items-end gap-2 pb-px">
                            <button type="submit" class="btn btn-primary btn-sm whitespace-nowrap px-3">
                                <svg class="h-3.5 w-3.5 inline-block ltr:mr-1 rtl:ml-1" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                بحث
                            </button>
                            <a href="{{ route('payment-cards.transactions') }}"
                                class="btn btn-outline-secondary btn-sm whitespace-nowrap px-3 text-center">
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @if ($transactions->count() > 0)
            <div class="table-responsive">
                <table class="table-hover">
                    <thead>
                        <tr>
                            <th class="text-gray-800 dark:text-gray-200">رقم المعاملة</th>
                            <th class="text-gray-800 dark:text-gray-200">البطاقة</th>
                            <th class="text-gray-800 dark:text-gray-200">النوع</th>
                            <th class="text-gray-800 dark:text-gray-200">المبلغ</th>
                            <th class="text-gray-800 dark:text-gray-200">الرصيد قبل</th>
                            <th class="text-gray-800 dark:text-gray-200">الرصيد بعد</th>
                            <th class="text-gray-800 dark:text-gray-200">المرجع</th>
                            <th class="text-gray-800 dark:text-gray-200">الشركة</th>
                            <th class="text-gray-800 dark:text-gray-200">الوصف</th>
                            <th class="text-gray-800 dark:text-gray-200">التاريخ</th>
                            <th class="text-gray-800 dark:text-gray-200">بواسطة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td class="font-mono text-sm text-gray-800 dark:text-gray-200">{{ $transaction->transaction_number }}</td>
                                <td>
                                    <a href="{{ route('payment-cards.show', $transaction->payment_card_id) }}"
                                        class="text-primary hover:underline">
                                        {{ $transaction->paymentCard->card_name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-outline-{{ $transaction->type_color }}">
                                        {{ $transaction->type_name }}
                                    </span>
                                </td>
                                <td
                                    class="font-semibold {{ $transaction->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 0) }}
                                </td>
                                <td class="text-gray-800 dark:text-gray-200">{{ number_format($transaction->balance_before, 0) }}</td>
                                <td class="font-semibold text-gray-800 dark:text-gray-200">{{ number_format($transaction->balance_after, 0) }}</td>
                                <td>
                                    @if ($transaction->reference_type)
                                        <span
                                            class="badge badge-outline-info">{{ $transaction->reference_type_name }}</span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="text-gray-800 dark:text-gray-200">
                                    @if ($transaction->company)
                                        {{ $transaction->company->name }}
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="text-gray-800 dark:text-gray-200">{{ Str::limit($transaction->description, 30) ?? '-' }}</td>
                                <td class="text-gray-800 dark:text-gray-200">{{ $transaction->created_at->format('Y/m/d H:i') }}</td>
                                <td class="text-gray-800 dark:text-gray-200">{{ $transaction->creator?->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">لا توجد معاملات</p>
            </div>
        @endif
    </div>
@endsection
