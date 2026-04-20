@extends('layouts.app')

@section('page-title', 'التقرير اليومي - ' . $date->format('Y-m-d'))

@section('content')
    <div class="panel">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold dark:text-white-light">التقرير اليومي</h2>
            <form method="GET" action="{{ route('financial.reports.daily') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="form-input w-auto">
                <button type="submit" class="btn btn-primary">عرض</button>
            </form>
        </div>

        @php
            $receivedTotal = (float) ($report['payments']['in']['total'] ?? 0);
            $paidTotal = (float) ($report['payments']['out']['total'] ?? 0);
            $netCashFlow = $receivedTotal - $paidTotal;
        @endphp

        {{-- الملخص المالي اليومي (استلام/دفع) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="rounded-lg p-4 bg-primary/10 border border-primary/20">
                <div class="text-sm text-gray-500 dark:text-gray-400">الشركة استلمت</div>
                <div class="text-2xl font-bold text-primary mt-1">{{ number_format($receivedTotal, 2) }}</div>
                <div class="text-sm mt-1">عدد الدفعات الواردة: {{ $report['payments']['in']['count'] ?? 0 }}</div>
            </div>
            <div class="rounded-lg p-4 bg-success/10 border border-success/20">
                <div class="text-sm text-gray-500 dark:text-gray-400">الشركة دفعت</div>
                <div class="text-2xl font-bold text-success mt-1">{{ number_format($paidTotal, 2) }}</div>
                <div class="text-sm mt-1">عدد الدفعات الصادرة: {{ $report['payments']['out']['count'] ?? 0 }}</div>
            </div>
            <div class="rounded-lg p-4 {{ $netCashFlow >= 0 ? 'bg-success/10 border border-success/20' : 'bg-danger/10 border border-danger/20' }}">
                <div class="text-sm text-gray-500 dark:text-gray-400">صافي الحركة اليومية</div>
                <div class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-success' : 'text-danger' }} mt-1">
                    {{ number_format($netCashFlow, 2) }}
                </div>
                <div class="text-sm mt-1">صافي = المستلم - المدفوع</div>
            </div>
        </div>

        {{-- جدول المعاملات --}}
        <div class="mb-6">
            <h3 class="font-semibold text-lg dark:text-white-light mb-3">المعاملات ({{ $report['date'] }})</h3>
            @if (isset($report['transactions']['items']) && $report['transactions']['items']->count() > 0)
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-right font-semibold">النوع</th>
                                <th class="px-4 py-3 text-right font-semibold">الحساب</th>
                                <th class="px-4 py-3 text-left font-semibold">المبلغ</th>
                                <th class="px-4 py-3 text-right font-semibold">الوصف</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($report['transactions']['items'] as $trx)
                                <tr>
                                    <td class="px-4 py-2">{{ $trx->transaction_type_name ?? $trx->transaction_type }}</td>
                                    <td class="px-4 py-2">{{ $trx->account->account_name ?? $trx->account->name ?? '-' }}</td>
                                    <td class="px-4 py-2 font-semibold">{{ number_format($trx->amount, 2) }} د.ع</td>
                                    <td class="px-4 py-2">{{ Str::limit($trx->description ?? '-', 40) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 py-6 text-center">لا توجد معاملات لهذا اليوم</p>
            @endif
        </div>
    </div>
@endsection
