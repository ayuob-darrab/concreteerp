@extends('layouts.app')

@section('page-title', (isset($mode) && $mode === 'contractor-orders') ? 'تقارير طلباتي' : 'فواتير المقاولين')

@section('content')
    <div id="contractor-invoices-page" class="max-w-6xl mx-auto">
        <!-- الإحصائيات -->
        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="panel" style="background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); color:#fff;">
                <div class="flex justify-between">
                    <div class="text-white">
                        <p class="text-sm font-semibold opacity-75">
                            {{ (isset($mode) && $mode === 'contractor-orders') ? 'عدد الطلبات' : 'عدد الفواتير' }}
                        </p>
                        <h4 class="mt-2 text-3xl font-bold">
                            {{ (isset($mode) && $mode === 'contractor-orders') ? ($stats['total_orders'] ?? 0) : ($stats['total_invoices'] ?? 0) }}
                        </h4>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6M9 8h3m-8 8V8a2 2 0 012-2h10l4 4v8a2 2 0 01-2 2H7a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="panel" style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); color:#fff;">
                <div class="flex justify-between">
                    <div class="text-white">
                        <p class="text-sm font-semibold opacity-75">إجمالي الفواتير</p>
                        <h4 class="mt-2 text-2xl font-bold">{{ number_format($stats['total_amount'] ?? 0, 0) }}</h4>
                        <p class="text-xs opacity-75">دينار</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="panel" style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%); color:#fff;">
                <div class="flex justify-between">
                    <div class="text-white">
                        <p class="text-sm font-semibold opacity-75">المدفوع</p>
                        <h4 class="mt-2 text-2xl font-bold">{{ number_format($stats['total_paid'] ?? 0, 0) }}</h4>
                        <p class="text-xs opacity-75">دينار</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="panel" style="background: linear-gradient(135deg, #dc2626 0%, #f97316 100%); color:#fff;">
                <div class="flex justify-between">
                    <div class="text-white">
                        <p class="text-sm font-semibold opacity-75">المتبقي</p>
                        <h4 class="mt-2 text-2xl font-bold">{{ number_format($stats['total_remaining'] ?? 0, 0) }}</h4>
                        <p class="text-xs opacity-75">دينار</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        @if (!isset($mode) || $mode !== 'contractor-orders')
        <div class="panel mb-5">
            <form method="GET" action="{{ route('contractor-invoices.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                @if (Auth::user()->account_code !== 'cont')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">المقاول</label>
                        <select name="contractor_id" class="form-select w-full">
                            <option value="">كل المقاولين</option>
                            @foreach (($contractors ?? []) as $contractor)
                                <option value="{{ $contractor->id }}" {{ (string) request('contractor_id') === (string) $contractor->id ? 'selected' : '' }}>
                                    {{ $contractor->contract_name ?? $contractor->name ?? ('#' . $contractor->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الفرع</label>
                        <select name="branch_id" class="form-select w-full">
                            <option value="">كل الفروع</option>
                            @foreach (($branches ?? []) as $branch)
                                <option value="{{ $branch->id }}" {{ (string) request('branch_id') === (string) $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name ?? ('فرع #' . $branch->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                    <select name="status" class="form-select w-full">
                        <option value="">كل الحالات</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>صادرة</option>
                        <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>مسددة جزئياً</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>مسددة</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>متأخرة</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">من تاريخ</label>
                    <input type="date" name="date_from" class="form-input w-full" value="{{ request('date_from') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-input w-full" value="{{ request('date_to') }}">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary w-full">بحث</button>
                </div>
            </form>
        </div>
        @endif

        <div class="panel">
            <div class="overflow-x-auto">
                @if (isset($mode) && $mode === 'contractor-orders')
                    <table class="table-striped w-full">
                        <thead>
                            <tr class="text-sm">
                                <th class="text-right">رقم الطلب</th>
                                <th class="text-right">التاريخ</th>
                                <th class="text-right">الكمية</th>
                                <th class="text-right">السعر الكلي</th>
                                <th class="text-right">المدفوع</th>
                                <th class="text-right">المتبقي</th>
                                <th class="text-right">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($orders ?? []) as $order)
                                @php
                                    $total = (float) ($order->price ?? 0);
                                    $paid = (float) ($order->paid_amount ?? 0);
                                    $remaining = max($total - $paid, 0);
                                @endphp
                                <tr class="text-sm">
                                    <td class="font-semibold">#{{ $order->id }}</td>
                                    <td>{{ optional($order->request_date ?? $order->created_at)->format('Y-m-d') }}</td>
                                    <td>{{ number_format((float) $order->quantity, 2) }} م³</td>
                                    <td>{{ number_format($total, 0) }}</td>
                                    <td class="text-green-600">{{ number_format($paid, 0) }}</td>
                                    <td class="text-red-600">{{ number_format($remaining, 0) }}</td>
                                    <td>{{ $order->status_code }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-6 text-gray-500">لا توجد طلبات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <table class="table-striped w-full">
                        <thead>
                            <tr class="text-sm">
                                <th class="text-right">رقم الفاتورة</th>
                                <th class="text-right">التاريخ</th>
                                <th class="text-right">الاستحقاق</th>
                                <th class="text-right">الإجمالي</th>
                                <th class="text-right">المدفوع</th>
                                <th class="text-right">المتبقي</th>
                                <th class="text-right">الحالة</th>
                                <th class="text-right">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr class="text-sm">
                                    <td class="font-semibold">
                                        <a class="text-primary hover:underline" href="{{ route('contractor-invoices.show', $invoice) }}">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td>{{ optional($invoice->invoice_date)->format('Y-m-d') }}</td>
                                    <td>{{ optional($invoice->due_date)->format('Y-m-d') }}</td>
                                    <td>{{ number_format((float) $invoice->total, 2) }}</td>
                                    <td class="text-green-600">{{ number_format((float) $invoice->paid_amount, 2) }}</td>
                                    <td class="text-red-600">{{ number_format((float) $invoice->remaining_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->status_badge }}">{{ $invoice->status_text }}</span>
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('contractor-invoices.show', $invoice) }}">عرض</a>
                                        <a class="btn btn-sm btn-outline-info" target="_blank" href="{{ route('contractor-invoices.print', $invoice) }}">طباعة</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-6 text-gray-500">لا توجد فواتير</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>

            @if (!isset($mode) || $mode !== 'contractor-orders')
                <div class="mt-4">
                    {{ $invoices instanceof \Illuminate\Pagination\AbstractPaginator ? $invoices->withQueryString()->links() : '' }}
                </div>
            @endif
        </div>
    </div>
@endsection

