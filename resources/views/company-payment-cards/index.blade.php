@extends('layouts.app')

@section('page-title', 'بطاقات الدفع - الشركة')

@section('content')
    {{-- أنماط ثابتة لظهور الكروت في الثيم الفاتح والداكن --}}
    <style>
        #company-payment-cards-page .stats-panel { border-radius: 0.5rem; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        #company-payment-cards-page .cards-list-panel { border-radius: 0.5rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: visible; }
        body:not(.dark) #company-payment-cards-page .cards-list-panel { background-color: #ffffff !important; color: #1f2937 !important; border: 1px solid #e5e7eb; }
        body.dark #company-payment-cards-page .cards-list-panel { background-color: #1f2937 !important; color: #e5e7eb !important; border: 1px solid #374151; }
        body:not(.dark) #company-payment-cards-page .cards-list-panel h5,
        body:not(.dark) #company-payment-cards-page .cards-list-panel th,
        body:not(.dark) #company-payment-cards-page .cards-list-panel td { color: #374151 !important; }
        body.dark #company-payment-cards-page .cards-list-panel h5,
        body.dark #company-payment-cards-page .cards-list-panel th,
        body.dark #company-payment-cards-page .cards-list-panel td { color: #e5e7eb !important; }
        body:not(.dark) #company-payment-cards-page .cards-list-panel thead th { background-color: #f3f4f6 !important; }
        body.dark #company-payment-cards-page .cards-list-panel thead th { background-color: #374151 !important; }
        #company-payment-cards-page .cards-list-panel td.text-success { color: #16a34a !important; }
        #company-payment-cards-page .cards-list-panel td.text-danger { color: #dc2626 !important; }
    </style>
    <div id="company-payment-cards-page">
    <!-- الإحصائيات (ألوان ثابتة لظهور الكروت في الثيمين) -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-5">
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%); color: #fff;">
            <div class="flex justify-between">
                <div class="text-white">
                    <p class="text-sm font-semibold opacity-75">إجمالي البطاقات</p>
                    <h4 class="mt-2 text-3xl font-bold">{{ $stats['total_cards'] }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #22c55e 0%, #4ade80 100%); color: #fff;">
            <div class="flex justify-between">
                <div class="text-white">
                    <p class="text-sm font-semibold opacity-75">البطاقات النشطة</p>
                    <h4 class="mt-2 text-3xl font-bold">{{ $stats['active_cards'] }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); color: #fff;">
            <div class="flex justify-between">
                <div class="text-white">
                    <p class="text-sm font-semibold opacity-75">إجمالي الرصيد</p>
                    <h4 class="mt-2 text-2xl font-bold">{{ number_format($stats['total_balance'], 0) }}</h4>
                    <p class="text-xs opacity-75">دينار</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: #fff;">
            <div class="flex justify-between">
                <div class="text-white">
                    <p class="text-sm font-semibold opacity-75">إجمالي الإيداعات</p>
                    <h4 class="mt-2 text-2xl font-bold">{{ number_format($stats['total_deposits'], 0) }}</h4>
                    <p class="text-xs opacity-75">دينار</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); color: #fff;">
            <div class="flex justify-between">
                <div class="text-white">
                    <p class="text-sm font-semibold opacity-75">إجمالي السحوبات</p>
                    <h4 class="mt-2 text-2xl font-bold">{{ number_format($stats['total_withdrawals'], 0) }}</h4>
                    <p class="text-xs opacity-75">دينار</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة البطاقات -->
    <div class="panel cards-list-panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-white-light">
                💳 بطاقات الدفع - الشركة
            </h5>
            <div class="flex gap-2">
                <a href="{{ route('company-payment-cards.transactions') }}" class="btn btn-outline-info btn-sm">
                    📊 تقرير المعاملات
                </a>
                <a href="{{ route('company-payment-cards.create') }}" class="btn btn-primary btn-sm">
                    ➕ إضافة بطاقة
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success flex items-center mb-4">
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger flex items-center mb-4">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table-striped table-hover w-full text-gray-700 dark:text-gray-300">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-600">
                        <th class="bg-gray-50 px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">#</th>
                        <th class="bg-gray-50 px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">نوع البطاقة</th>
                        <th class="bg-gray-50 px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">اسم البطاقة</th>
                        <th class="bg-gray-50 px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">صاحب البطاقة</th>
                        <th class="bg-gray-50 px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">الرقم</th>
                        <th class="bg-gray-50 px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">الفرع</th>
                        <th class="bg-gray-50 px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">الرصيد الحالي</th>
                        <th class="bg-gray-50 px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">الحالة</th>
                        <th class="bg-gray-50 px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-300">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cards as $index => $card)
                        <tr class="border-b border-gray-100 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $card->card_type_name }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $card->card_name }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $card->holder_name }}</td>
                            <td class="px-4 py-3 font-mono text-gray-700 dark:text-gray-300">{{ $card->card_number_masked }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $card->branch->branch_name ?? 'عام' }}</td>
                            <td class="px-4 py-3 font-bold {{ $card->current_balance > 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($card->current_balance, 0) }} دينار
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $card->status_color }}">{{ $card->status_text }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('company-payment-cards.show', $card->id) }}" class="btn btn-sm btn-outline-info" title="التفاصيل">👁</a>
                                    <a href="{{ route('company-payment-cards.edit', $card->id) }}" class="btn btn-sm btn-outline-warning" title="تعديل">✏️</a>
                                    @if($card->is_active && $card->current_balance > 0)
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="سحب"
                                            data-card-id="{{ $card->id }}"
                                            data-card-name="{{ $card->card_name }}"
                                            data-card-holder="{{ $card->holder_name }}"
                                            data-card-number="{{ $card->card_number_masked }}"
                                            data-card-balance="{{ $card->current_balance }}"
                                            data-withdraw-url="{{ route('company-payment-cards.withdraw', $card->id) }}"
                                            onclick="openWithdrawModal(this)">
                                            💸 سحب
                                        </button>
                                    @endif
                                    <form action="{{ route('company-payment-cards.toggle-status', $card->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $card->is_active ? 'danger' : 'success' }}" title="{{ $card->is_active ? 'تعطيل' : 'تفعيل' }}">
                                            {{ $card->is_active ? '🔒' : '🔓' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                لا توجد بطاقات دفع مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>{{-- نهاية #company-payment-cards-page --}}

    <!-- مودال السحب -->
    <div id="withdrawModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="withdrawModalTitle" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 dark:bg-black/70" onclick="closeWithdrawModal()"></div>
            <div class="panel relative z-10 w-full max-w-md shadow-xl dark:bg-gray-800 dark:text-gray-100">
                <div class="mb-4 flex items-center justify-between border-b border-gray-200 pb-3 dark:border-gray-600">
                    <h3 id="withdrawModalTitle" class="text-lg font-semibold text-gray-800 dark:text-gray-100">💸 سحب من البطاقة</h3>
                    <button type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" onclick="closeWithdrawModal()" aria-label="إغلاق">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form id="withdrawForm" method="POST" action="">
                    @csrf
                    <!-- تفاصيل البطاقة -->
                    <div class="mb-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50">
                        <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">اسم البطاقة</p>
                        <p id="modalCardName" class="font-semibold text-gray-800 dark:text-gray-100"></p>
                        <p class="mb-1 mt-2 text-sm text-gray-500 dark:text-gray-400">صاحب البطاقة</p>
                        <p id="modalCardHolder" class="font-semibold text-gray-800 dark:text-gray-100"></p>
                        <p class="mb-1 mt-2 text-sm text-gray-500 dark:text-gray-400">رقم البطاقة</p>
                        <p id="modalCardNumber" class="font-mono text-gray-800 dark:text-gray-100"></p>
                        <p class="mb-1 mt-2 text-sm text-gray-500 dark:text-gray-400">الرصيد الحالي</p>
                        <p id="modalCardBalance" class="text-lg font-bold text-success"></p>
                    </div>

                    <div class="mb-4">
                        <label for="withdrawAmountDisplay" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">المبلغ (دينار) <span class="text-danger">*</span></label>
                        <input type="text" id="withdrawAmountDisplay" class="form-input w-full text-left" placeholder="0.00" inputmode="decimal" maxlength="20" required
                            oninput="formatWithdrawPrice(this)">
                        <input type="hidden" name="amount" id="withdrawAmountRaw" required>
                        <p id="withdrawAmountError" class="mt-1 hidden text-sm text-danger"></p>
                    </div>

                    <div class="mb-4">
                        <label for="withdrawDescription" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">الوصف (اختياري)</label>
                        <input type="text" name="description" id="withdrawDescription" class="form-input w-full" placeholder="سحب يدوي">
                    </div>

                    <div class="flex gap-2 justify-end">
                        <button type="button" class="btn btn-outline-secondary" onclick="closeWithdrawModal()">إلغاء</button>
                        <button type="submit" class="btn btn-danger">تنفيذ السحب</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const withdrawModal = document.getElementById('withdrawModal');
        const withdrawForm = document.getElementById('withdrawForm');
        const withdrawAmountDisplay = document.getElementById('withdrawAmountDisplay');
        const withdrawAmountRaw = document.getElementById('withdrawAmountRaw');
        const withdrawAmountError = document.getElementById('withdrawAmountError');
        let modalMaxBalance = 0;

        function openWithdrawModal(btn) {
            const id = btn.dataset.cardId;
            const name = btn.dataset.cardName;
            const holder = btn.dataset.cardHolder;
            const number = btn.dataset.cardNumber;
            const balance = parseFloat(btn.dataset.cardBalance) || 0;
            const url = btn.dataset.withdrawUrl;

            modalMaxBalance = balance;
            withdrawForm.action = url;
            document.getElementById('modalCardName').textContent = name || '-';
            document.getElementById('modalCardHolder').textContent = holder || '-';
            document.getElementById('modalCardNumber').textContent = number || '-';
            document.getElementById('modalCardBalance').textContent = (balance.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 })) + ' دينار';

            withdrawAmountDisplay.value = '';
            withdrawAmountRaw.value = '';
            withdrawAmountError.classList.add('hidden');
            withdrawAmountError.textContent = '';

            withdrawModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(function() { withdrawAmountDisplay.focus(); }, 100);
        }

        function closeWithdrawModal() {
            withdrawModal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function formatWithdrawPrice(input) {
            let value = input.value.replace(/,/g, '');
            if (!/^\d*\.?\d*$/.test(value)) {
                input.value = input.value.slice(0, -1);
                syncWithdrawAmount();
                return;
            }
            const parts = value.split('.');
            let integerPart = parts[0];
            const decimalPart = parts[1] ? '.' + parts[1].slice(0, 2) : '';
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            input.value = integerPart + decimalPart;
            syncWithdrawAmount();
        }

        function syncWithdrawAmount() {
            const raw = withdrawAmountDisplay.value.replace(/,/g, '');
            withdrawAmountRaw.value = raw === '' ? '' : (parseFloat(raw) || 0);
            if (withdrawAmountError && modalMaxBalance > 0 && raw !== '') {
                const num = parseFloat(raw);
                if (num > modalMaxBalance) {
                    withdrawAmountError.textContent = 'المبلغ يتجاوز الرصيد الحالي (' + modalMaxBalance.toLocaleString() + ' دينار)';
                    withdrawAmountError.classList.remove('hidden');
                } else {
                    withdrawAmountError.classList.add('hidden');
                }
            }
        }

        withdrawForm.addEventListener('submit', function(e) {
            const raw = withdrawAmountDisplay.value.replace(/,/g, '');
            const num = parseFloat(raw);
            if (!raw || isNaN(num) || num < 0.01) {
                e.preventDefault();
                withdrawAmountError.textContent = 'أدخل مبلغاً صحيحاً';
                withdrawAmountError.classList.remove('hidden');
                return;
            }
            if (num > modalMaxBalance) {
                e.preventDefault();
                withdrawAmountError.textContent = 'المبلغ يتجاوز الرصيد الحالي';
                withdrawAmountError.classList.remove('hidden');
                return;
            }
            withdrawAmountRaw.value = num;
        });
    </script>
@endsection
