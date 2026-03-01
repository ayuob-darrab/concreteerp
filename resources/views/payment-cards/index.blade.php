@extends('layouts.app')

@section('page-title', 'حسابات الدفع الإلكتروني')

@section('content')
    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-lg shadow p-4 flex justify-between" style="background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%); color: #fff;">
            <div>
                <p class="text-sm font-semibold" style="color: rgba(255,255,255,0.95);">إجمالي البطاقات</p>
                <h4 class="mt-2 text-3xl font-bold" style="color: #fff;">{{ $stats['total_cards'] }}</h4>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                <svg class="h-7 w-7" style="color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
        </div>

        <div class="rounded-lg shadow p-4 flex justify-between" style="background: linear-gradient(135deg, #22c55e 0%, #4ade80 100%); color: #fff;">
            <div>
                <p class="text-sm font-semibold" style="color: rgba(255,255,255,0.95);">البطاقات النشطة</p>
                <h4 class="mt-2 text-3xl font-bold" style="color: #fff;">{{ $stats['active_cards'] }}</h4>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                <svg class="h-7 w-7" style="color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>

        <div class="rounded-lg shadow p-4 flex justify-between" style="background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); color: #fff;">
            <div>
                <p class="text-sm font-semibold" style="color: rgba(255,255,255,0.95);">إجمالي الرصيد</p>
                <h4 class="mt-2 text-2xl font-bold" style="color: #fff;">{{ number_format($stats['total_balance'], 0) }}</h4>
                <p class="text-xs" style="color: rgba(255,255,255,0.9);">دينار</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                <svg class="h-7 w-7" style="color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- قائمة البطاقات -->
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-white-light">
                <svg class="inline-block h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                حسابات الدفع الإلكتروني
            </h5>
            <div class="flex gap-2">
                <a href="{{ route('payment-cards.transactions') }}" class="btn btn-outline-info btn-sm">
                    <svg class="h-4 w-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    تقرير المعاملات
                </a>
                <a href="{{ route('payment-cards.create') }}" class="btn btn-primary btn-sm">
                    <svg class="h-4 w-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    إضافة بطاقة جديدة
                </a>
            </div>
        </div>

        @if ($cards->count() > 0)
            <div class="table-responsive">
                <table class="table-hover">
                    <thead>
                        <tr>
                            <th class="text-gray-800 dark:text-gray-200">#</th>
                            <th class="text-gray-800 dark:text-gray-200">نوع البطاقة</th>
                            <th class="text-gray-800 dark:text-gray-200">اسم البطاقة</th>
                            <th class="text-gray-800 dark:text-gray-200">صاحب البطاقة</th>
                            <th class="text-gray-800 dark:text-gray-200">رقم البطاقة</th>
                            <th class="text-gray-800 dark:text-gray-200">الرصيد الافتتاحي</th>
                            <th class="text-gray-800 dark:text-gray-200">الرصيد الحالي</th>
                            <th class="text-gray-800 dark:text-gray-200">تاريخ الانتهاء</th>
                            <th class="text-gray-800 dark:text-gray-200">الحالة</th>
                            <th class="text-center text-gray-800 dark:text-gray-200">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cards as $index => $card)
                            <tr>
                                <td class="text-gray-800 dark:text-gray-200">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge badge-outline-primary">{{ $card->card_type_name }}</span>
                                </td>
                                <td class="font-semibold text-gray-800 dark:text-gray-200">{{ $card->card_name }}</td>
                                <td class="text-gray-800 dark:text-gray-200">{{ $card->holder_name }}</td>
                                <td class="font-mono text-gray-700 dark:text-gray-400" dir="ltr">
                                    {{ $card->card_number_masked }}
                                </td>
                                <td class="text-gray-800 dark:text-gray-200">{{ number_format($card->opening_balance, 0) }} دينار</td>
                                <td
                                    class="font-semibold {{ $card->current_balance > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($card->current_balance, 0) }} دينار
                                </td>
                                <td class="text-gray-800 dark:text-gray-200">
                                    @if ($card->expiry_date)
                                        {{ $card->expiry_date->format('Y/m/d') }}
                                        @if ($card->expiry_date < now())
                                            <span class="badge badge-outline-danger text-xs">منتهية</span>
                                        @endif
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-outline-{{ $card->status_color }}">
                                        {{ $card->status_text }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('payment-cards.show', $card->id) }}"
                                            class="btn btn-outline-info btn-sm" title="التفاصيل">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('payment-cards.edit', $card->id) }}"
                                            class="btn btn-outline-warning btn-sm" title="تعديل">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        @if ($card->current_balance > 0)
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="سحب"
                                                onclick="openWithdrawModal({{ $card->id }}, {{ $card->current_balance }}, '{{ addslashes($card->card_name) }}', '{{ route('payment-cards.withdraw', $card->id) }}')">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        <form action="{{ route('payment-cards.toggle-status', $card->id) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('{{ $card->is_active ? 'هل أنت متأكد من تعطيل هذه البطاقة؟' : 'هل أنت متأكد من تفعيل هذه البطاقة؟' }}')">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-outline-{{ $card->is_active ? 'secondary' : 'success' }} btn-sm"
                                                title="{{ $card->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                @if ($card->is_active)
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                        </path>
                                                    </svg>
                                                @else
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                        <form action="{{ route('payment-cards.destroy', $card->id) }}" method="POST"
                                            class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه البطاقة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="حذف">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
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
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">لا توجد بطاقات مسجلة</p>
                <a href="{{ route('payment-cards.create') }}" class="btn btn-primary mt-4">
                    <svg class="h-4 w-4 ltr:mr-1 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    إضافة أول بطاقة
                </a>
            </div>
        @endif
    </div>

    <!-- Modal السحب من البطاقة -->
    <div id="withdrawModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeWithdrawModal()"></div>
            <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800 dark:text-gray-200">
                <h5 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">سحب من البطاقة</h5>
                <p id="withdrawModalCardName" class="mb-4 text-sm text-gray-600 dark:text-gray-400"></p>
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">الرصيد المتاح: <strong id="withdrawModalBalance">0</strong> دينار</p>
                <form id="withdrawForm" method="POST" action="">
                    @csrf
                    <div class="mb-4">
                        <label class="mb-2 block font-semibold text-gray-800 dark:text-gray-200">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="withdrawAmount" class="form-input" min="0.01" step="0.01" required placeholder="أدخل المبلغ">
                    </div>
                    <div class="mb-4">
                        <label class="mb-2 block font-semibold text-gray-800 dark:text-gray-200">الوصف (يُسجّل في سجل المعاملات)</label>
                        <input type="text" name="description" class="form-input" placeholder="مثال: سحب لصرف مصروفات">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="closeWithdrawModal()">إلغاء</button>
                        <button type="submit" class="btn btn-danger">سحب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openWithdrawModal(cardId, balance, cardName, formAction) {
            document.getElementById('withdrawForm').action = formAction;
            document.getElementById('withdrawAmount').setAttribute('max', balance);
            document.getElementById('withdrawModalBalance').textContent = new Intl.NumberFormat('ar-EG').format(balance);
            document.getElementById('withdrawModalCardName').textContent = 'البطاقة: ' + (cardName || '');
            document.getElementById('withdrawModal').classList.remove('hidden');
        }
        function closeWithdrawModal() {
            document.getElementById('withdrawModal').classList.add('hidden');
        }
    </script>
@endsection
