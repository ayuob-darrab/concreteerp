@extends('layouts.app')

@section('page-title', 'تقرير المقبوضات')

@section('content')
    {{-- أنماط ثابتة لظهور الكروت والجدول في الثيم الفاتح والداكن --}}
    <style>
        #branch-payments-report-page .stats-panel { border-radius: 0.5rem; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); color: #fff !important; }
        #branch-payments-report-page .stats-panel * { color: #fff !important; }
        #branch-payments-report-page .report-list-panel { border-radius: 0.5rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: visible; }
        body:not(.dark) #branch-payments-report-page .report-list-panel { background-color: #ffffff !important; color: #1f2937 !important; border: 1px solid #e5e7eb; }
        body.dark #branch-payments-report-page .report-list-panel { background-color: #1f2937 !important; color: #e5e7eb !important; border: 1px solid #374151; }
        body:not(.dark) #branch-payments-report-page .report-list-panel h5,
        body:not(.dark) #branch-payments-report-page .report-list-panel label,
        body:not(.dark) #branch-payments-report-page .report-list-panel th,
        body:not(.dark) #branch-payments-report-page .report-list-panel td { color: #374151 !important; }
        body.dark #branch-payments-report-page .report-list-panel h5,
        body.dark #branch-payments-report-page .report-list-panel label,
        body.dark #branch-payments-report-page .report-list-panel th,
        body.dark #branch-payments-report-page .report-list-panel td { color: #e5e7eb !important; }
        body:not(.dark) #branch-payments-report-page .report-list-panel thead th { background-color: #f3f4f6 !important; }
        body.dark #branch-payments-report-page .report-list-panel thead th { background-color: #374151 !important; }
        #branch-payments-report-page .report-list-panel td.text-success { color: #16a34a !important; }
        #branch-payments-report-page .report-list-panel td.text-danger { color: #dc2626 !important; }
    </style>
    <div id="branch-payments-report-page">
    <!-- الإحصائيات (ألوان ثابتة لظهور الكروت في الثيمين) -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-3 lg:grid-cols-7">
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);">
            <div class="text-center">
                <p class="text-xs opacity-95">إجمالي الدفعات</p>
                <h4 class="text-2xl font-bold">{{ $stats['total_payments'] }}</h4>
            </div>
        </div>
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
            <div class="text-center">
                <p class="text-xs opacity-95">إجمالي المبالغ</p>
                <h4 class="text-xl font-bold">{{ number_format($stats['total_amount'], 0) }}</h4>
                <p class="text-xs opacity-95">دينار</p>
            </div>
        </div>
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #22c55e 0%, #4ade80 100%);">
            <div class="text-center">
                <p class="text-xs opacity-95">المدفوع</p>
                <h4 class="text-xl font-bold">{{ number_format($stats['total_paid'], 0) }}</h4>
                <p class="text-xs opacity-95">دينار</p>
            </div>
        </div>
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);">
            <div class="text-center">
                <p class="text-xs opacity-95">المتبقي</p>
                <h4 class="text-xl font-bold">{{ number_format($stats['total_remaining'], 0) }}</h4>
                <p class="text-xs opacity-95">دينار</p>
            </div>
        </div>
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #10b981 0%, #34d399 100%);">
            <div class="text-center">
                <p class="text-xs opacity-95">مدفوع بالكامل</p>
                <h4 class="text-2xl font-bold">{{ $stats['paid_count'] }}</h4>
            </div>
        </div>
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);">
            <div class="text-center">
                <p class="text-xs opacity-95">جزئي</p>
                <h4 class="text-2xl font-bold">{{ $stats['partial_count'] }}</h4>
            </div>
        </div>
        <div class="panel stats-panel" style="background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%);">
            <div class="text-center">
                <p class="text-xs opacity-95">غير مدفوع</p>
                <h4 class="text-2xl font-bold">{{ $stats['unpaid_count'] }}</h4>
            </div>
        </div>
    </div>

    <div class="panel report-list-panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <h5 class="text-lg font-semibold dark:text-white-light">📊 تقرير المقبوضات</h5>
            <a href="{{ route('branch.payments.index') }}" class="btn btn-outline-secondary btn-sm">← رجوع للمدفوعات</a>
        </div>

        <!-- فلاتر -->
        <form method="GET" class="mb-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                @if (Auth::user()->usertype_id === 'CM')
                    <div>
                        <label class="mb-1 block text-sm font-medium">الفرع</label>
                        <select name="branch_id" class="form-select form-select-sm w-full">
                            <option value="">الكل</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="{{ Auth::user()->usertype_id === 'CM' ? '' : 'sm:col-span-2' }}">
                    <label class="mb-1 block text-sm font-medium">الزبون</label>
                    <div class="relative" id="customer-select-wrap">
                        <input type="text" id="customer-search-input" class="form-input form-input-sm w-full" placeholder="ابحث أو اختر زبون..." autocomplete="off" value="{{ $selectedCustomerLabel ?? '' }}">
                        <input type="hidden" name="customer_phone" id="customer_phone" value="{{ request('customer_phone') }}">
                        <div id="customer-dropdown" class="absolute left-0 right-0 top-full z-20 mt-1 max-h-60 overflow-auto rounded border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800 hidden" style="min-width: 16rem;"></div>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">الحالة</label>
                    <select name="status" class="form-select form-select-sm w-full">
                        <option value="">الكل</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>جزئي</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>غير مدفوع</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">من تاريخ</label>
                    <input type="date" name="date_from" class="form-input form-input-sm w-full" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-input form-input-sm w-full" value="{{ request('date_to') }}">
                </div>
                <div class="sm:col-span-2 xl:col-span-1">
                    <label class="mb-1 block text-sm font-medium">بحث بالاسم أو الهاتف</label>
                    <input type="text" name="customer_search" class="form-input form-input-sm w-full" placeholder="اكتب الاسم أو رقم الهاتف..." value="{{ request('customer_search') }}">
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary btn-sm">🔍 بحث</button>
                <a href="{{ route('branch.payments.report') }}" class="btn btn-outline-secondary btn-sm">إعادة تعيين</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width:40px"></th>
                        <th>رقم الدفعة</th>
                        <th>الزبون</th>
                        <th>الهاتف</th>
                        <th>رقم الطلب</th>
                        @if (Auth::user()->usertype_id === 'CM')
                            <th>الفرع</th>
                        @endif
                        <th>الإجمالي</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>الدفعات</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th>طباعة</th>
                    </tr>
                </thead>
                <tbody>
                    @php $colSpan = Auth::user()->usertype_id === 'CM' ? 13 : 12; @endphp
                    @forelse ($payments as $payment)
                        <tr class="cursor-pointer hover:bg-primary/5 transition-colors payment-row" data-payment-id="{{ $payment->id }}">
                            <td class="text-center">
                                @if ($payment->records->count() > 0)
                                    <button type="button" class="toggle-records text-primary" data-id="{{ $payment->id }}">
                                        <svg class="w-5 h-5 transition-transform duration-200 arrow-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                @endif
                            </td>
                            <td class="font-mono text-sm">{{ $payment->payment_number }}</td>
                            <td class="font-semibold">{{ $payment->customer_name }}</td>
                            <td class="font-mono">{{ $payment->customer_phone }}</td>
                            <td class="font-mono">#{{ $payment->work_order_id }}</td>
                            @if (Auth::user()->usertype_id === 'CM')
                                <td>{{ $payment->branch->branch_name ?? '-' }}</td>
                            @endif
                            <td>{{ number_format($payment->total_amount, 0) }} <small class="text-gray-400">د.ع</small></td>
                            <td class="text-success">{{ number_format($payment->paid_amount, 0) }} <small class="text-gray-400">د.ع</small></td>
                            <td class="text-danger font-bold">{{ number_format($payment->remaining_amount, 0) }} <small class="text-gray-400">د.ع</small></td>
                            <td>
                                @if ($payment->records->count() > 0)
                                    <span class="badge bg-info/20 text-info">{{ $payment->records->count() }} دفعة</span>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $payment->status_color }}">{{ $payment->status_text }}</span></td>
                            <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('branch.payments.invoice', $payment->id) }}" target="_blank" class="btn btn-sm btn-outline-info">🖨</a>
                            </td>
                        </tr>
                        {{-- صفوف الدفعات التفصيلية --}}
                        @if ($payment->records->count() > 0)
                            <tr class="records-row hidden" id="records-{{ $payment->id }}">
                                <td colspan="{{ $colSpan }}" class="!p-0">
                                    <div class="bg-gray-50 dark:bg-gray-800/50 border-y border-primary/20 px-6 py-4">
                                        <h6 class="text-sm font-bold mb-3 text-primary">
                                            📝 سجل الدفعات لـ {{ $payment->customer_name }} (طلب #{{ $payment->work_order_id }})
                                        </h6>
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="text-gray-500 dark:text-gray-400">
                                                    <th class="py-2 px-3 text-right">#</th>
                                                    <th class="py-2 px-3 text-right">رقم السجل</th>
                                                    <th class="py-2 px-3 text-right">المبلغ</th>
                                                    <th class="py-2 px-3 text-right">طريقة الدفع</th>
                                                    <th class="py-2 px-3 text-right">الرصيد قبل</th>
                                                    <th class="py-2 px-3 text-right">الرصيد بعد</th>
                                                    <th class="py-2 px-3 text-right">رقم المرجع</th>
                                                    <th class="py-2 px-3 text-right">الملاحظات</th>
                                                    <th class="py-2 px-3 text-right">التاريخ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($payment->records->sortBy('created_at') as $idx => $record)
                                                    <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-white/50 dark:hover:bg-gray-700/50">
                                                        <td class="py-2 px-3 text-gray-400">{{ $idx + 1 }}</td>
                                                        <td class="py-2 px-3 font-mono text-xs">{{ $record->record_number }}</td>
                                                        <td class="py-2 px-3 font-bold text-success">{{ number_format($record->amount, 0) }} د.ع</td>
                                                        <td class="py-2 px-3">
                                                            @switch($record->payment_method)
                                                                @case('cash')
                                                                    <span class="badge bg-success/20 text-success">💵 نقدي</span>
                                                                    @break
                                                                @case('bank_transfer')
                                                                    <span class="badge bg-blue-500/20 text-blue-500">🏦 تحويل بنكي</span>
                                                                    @break
                                                                @case('check')
                                                                    <span class="badge bg-amber-500/20 text-amber-500">📄 شيك</span>
                                                                    @break
                                                                @case('online')
                                                                    <span class="badge bg-purple-500/20 text-purple-500">💳 إلكتروني</span>
                                                                    @break
                                                                @default
                                                                    <span class="text-gray-400">-</span>
                                                            @endswitch
                                                        </td>
                                                        <td class="py-2 px-3 text-gray-500">{{ number_format($record->balance_before, 0) }}</td>
                                                        <td class="py-2 px-3 text-gray-500">{{ number_format($record->balance_after, 0) }}</td>
                                                        <td class="py-2 px-3 font-mono text-xs">{{ $record->reference_number ?? '-' }}</td>
                                                        <td class="py-2 px-3 text-xs text-gray-500">{{ $record->notes ?? '-' }}</td>
                                                        <td class="py-2 px-3 text-xs">{{ $record->created_at->format('Y-m-d H:i') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="{{ $colSpan }}" class="text-center py-5 text-gray-500">لا توجد مدفوعات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $payments->appends(request()->query())->links() }}</div>

        <script>
            // قائمة الزبائن القابلة للبحث
            (function() {
                var customers = @json($customers->map(fn($c) => ['phone' => $c->customer_phone, 'label' => ($c->customer_name ?: 'غير محدد') . ' — ' . $c->customer_phone]));
                var wrap = document.getElementById('customer-select-wrap');
                var input = document.getElementById('customer-search-input');
                var hidden = document.getElementById('customer_phone');
                var dropdown = document.getElementById('customer-dropdown');

                function showDropdown(filter) {
                    var term = (filter || '').toLowerCase();
                    dropdown.innerHTML = '';
                    var empty = document.createElement('div');
                    empty.className = 'px-3 py-2 text-sm text-gray-500 dark:text-gray-400';
                    empty.setAttribute('data-value', '');
                    empty.textContent = 'الكل';
                    empty.style.cursor = 'pointer';
                    dropdown.appendChild(empty);
                    var count = 0;
                    customers.forEach(function(c) {
                        if (term && c.label.toLowerCase().indexOf(term) < 0) return;
                        count++;
                        var div = document.createElement('div');
                        div.className = 'cursor-pointer px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border-t border-gray-100 dark:border-gray-600';
                        div.setAttribute('data-value', c.phone);
                        div.setAttribute('data-label', c.label);
                        div.textContent = c.label;
                        dropdown.appendChild(div);
                    });
                    if (count === 0 && term) {
                        var no = document.createElement('div');
                        no.className = 'px-3 py-2 text-sm text-gray-500 dark:text-gray-400';
                        no.textContent = 'لا توجد نتائج';
                        dropdown.appendChild(no);
                    }
                    dropdown.classList.remove('hidden');
                }

                function hideDropdown() {
                    dropdown.classList.add('hidden');
                }

                function selectItem(value, label) {
                    hidden.value = value || '';
                    input.value = label || '';
                    hideDropdown();
                }

                input.addEventListener('focus', function() { showDropdown(input.value.trim()); });
                input.addEventListener('input', function() {
                    if (input.value.trim() === '') hidden.value = '';
                    showDropdown(input.value.trim());
                });
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') { hideDropdown(); input.blur(); }
                });

                dropdown.addEventListener('click', function(e) {
                    var el = e.target.closest('[data-value]');
                    if (!el) return;
                    selectItem(el.getAttribute('data-value'), el.getAttribute('data-label') || el.textContent);
                });

                document.addEventListener('click', function(e) {
                    if (wrap && !wrap.contains(e.target)) hideDropdown();
                });
            })();

            document.querySelectorAll('.toggle-records').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const id = this.dataset.id;
                    const row = document.getElementById('records-' + id);
                    const arrow = this.querySelector('.arrow-icon');
                    if (row) {
                        row.classList.toggle('hidden');
                        arrow.style.transform = row.classList.contains('hidden') ? '' : 'rotate(90deg)';
                    }
                });
            });
            document.querySelectorAll('.payment-row').forEach(row => {
                row.addEventListener('click', function() {
                    const btn = this.querySelector('.toggle-records');
                    if (btn) btn.click();
                });
            });
        </script>
    </div>
    </div>{{-- نهاية #branch-payments-report-page --}}
@endsection
