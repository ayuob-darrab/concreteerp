@extends('layouts.app')

@section('page-title', 'تفاصيل الفاتورة')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="panel mb-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🧾 تفاصيل الفاتورة</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">رقم: {{ $invoice->invoice_number }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('contractor-invoices.index') }}" class="btn btn-outline-secondary">رجوع</a>
                    <a href="{{ route('contractor-invoices.print', $invoice) }}" target="_blank" class="btn btn-outline-info">طباعة</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
            <div class="panel">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">معلومات الفاتورة</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">الحالة</span>
                        <span class="badge bg-{{ $invoice->status_badge }}">{{ $invoice->status_text }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">تاريخ الفاتورة</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ optional($invoice->invoice_date)->format('Y-m-d') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">تاريخ الاستحقاق</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ optional($invoice->due_date)->format('Y-m-d') }}</span>
                    </div>
                    @if ($invoice->work_order_id)
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600 dark:text-gray-400">رقم الطلب</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200">#{{ $invoice->work_order_id }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="panel">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">المبالغ</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">المجموع الفرعي</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ number_format((float) $invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">الضريبة</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ number_format((float) $invoice->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">الخصم</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ number_format((float) $invoice->discount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">الإجمالي</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ number_format((float) $invoice->total, 2) }} د.ع</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">المدفوع</span>
                        <span class="font-medium text-green-600">{{ number_format((float) $invoice->paid_amount, 2) }} د.ع</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600 dark:text-gray-400">المتبقي</span>
                        <span class="font-medium text-red-600">{{ number_format((float) $invoice->remaining_amount, 2) }} د.ع</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">بنود الفاتورة</h3>
            <div class="overflow-x-auto">
                <table class="table-striped w-full">
                    <thead>
                        <tr class="text-sm">
                            <th class="text-right">الوصف</th>
                            <th class="text-right">الكمية</th>
                            <th class="text-right">سعر الوحدة</th>
                            <th class="text-right">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (($invoice->items ?? []) as $item)
                            @php
                                $qty = (float) ($item['quantity'] ?? 0);
                                $price = (float) ($item['unit_price'] ?? 0);
                                $rowTotal = $qty * $price;
                            @endphp
                            <tr class="text-sm">
                                <td class="font-medium">{{ $item['description'] ?? '-' }}</td>
                                <td>{{ number_format($qty, 2) }}</td>
                                <td>{{ number_format($price, 2) }}</td>
                                <td>{{ number_format($rowTotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($invoice->description)
            <div class="panel">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">الوصف / الملاحظات</h3>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $invoice->description }}</p>
            </div>
        @endif
    </div>
@endsection

