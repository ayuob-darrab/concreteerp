@extends('layouts.app')

@section('title', 'تقرير الطلبات')

@section('content')
<div class="panel">
    {{-- عنوان التقرير --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                <i class="fas fa-file-alt text-primary ml-2"></i>
                تقرير الطلبات
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">عرض وبحث في جميع الطلبات</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('financial-report.print', request()->all()) }}" target="_blank" 
               class="btn btn-secondary">
                <i class="fas fa-print ml-1"></i> طباعة
            </a>
        </div>
    </div>

    {{-- فلاتر البحث --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الفرع</label>
                <select name="branch_id" class="form-select w-full">
                    <option value="">جميع الفروع</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                <select name="status" class="form-select w-full">
                    <option value="">جميع الحالات</option>
                    @foreach($statuses as $code => $label)
                        <option value="{{ $code }}" {{ request('status') == $code ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">من تاريخ</label>
                <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" 
                       class="form-input w-full">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" 
                       class="form-input w-full">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search ml-1"></i> بحث
                </button>
            </div>
        </form>
    </div>

    {{-- الإحصائيات --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-gray-500 dark:text-gray-400 text-sm">إجمالي الطلبات</div>
            <div class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_orders'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-gray-500 dark:text-gray-400 text-sm">المكتملة</div>
            <div class="text-2xl font-bold text-success mt-1">{{ $stats['completed'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-gray-500 dark:text-gray-400 text-sm">قيد التنفيذ</div>
            <div class="text-2xl font-bold text-warning mt-1">{{ $stats['in_progress'] }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-gray-500 dark:text-gray-400 text-sm">المبلغ الإجمالي</div>
            <div class="text-2xl font-bold text-primary mt-1">{{ number_format($stats['total_amount'], 2) }}</div>
        </div>
    </div>

    {{-- جدول الطلبات --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">رقم الطلب</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الفرع</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">نوع الخلطة</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">الكمية</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">التاريخ</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">المبلغ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($orders as $index => $order)
                    @php
                        $statusColors = [
                            'new' => 'bg-blue-100 text-blue-800',
                            'under_review' => 'bg-purple-100 text-purple-800',
                            'approved' => 'bg-teal-100 text-teal-800',
                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusClass = $statusColors[$order->status_code] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $order->order_number ?? $order->id }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $order->branch->branch_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $order->concreteMix->mix_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">
                            {{ number_format($order->quantity, 2) }} م³
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ $statuses[$order->status_code] ?? $order->status_code }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">
                            {{ $order->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-800 dark:text-white">
                            {{ number_format($order->final_price ?? $order->initial_price ?? 0, 2) }} د.ع
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            لا توجد طلبات في هذه الفترة
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($orders->count() > 0)
                <tfoot class="bg-gray-100 dark:bg-gray-700 font-bold">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-gray-800 dark:text-white">الإجمالي</td>
                        <td class="px-4 py-3 text-center text-gray-800 dark:text-white">
                            {{ number_format($stats['total_quantity'], 2) }} م³
                        </td>
                        <td class="px-4 py-3 text-center text-gray-800 dark:text-white">
                            {{ $stats['total_orders'] }} طلب
                        </td>
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3 text-center text-primary">
                            {{ number_format($stats['total_amount'], 2) }} د.ع
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
