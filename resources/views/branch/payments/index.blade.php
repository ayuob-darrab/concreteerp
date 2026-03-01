@extends('layouts.app')

@section('page-title', 'المدفوعات - الزبائن')

@section('content')
    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="panel bg-gradient-to-r from-blue-500 to-blue-400">
            <div class="flex justify-between">
                <div class="text-white">
                    <p class="text-sm font-semibold opacity-75">عدد الزبائن</p>
                    <h4 class="mt-2 text-3xl font-bold">{{ $stats['total_customers'] }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="panel bg-gradient-to-r from-amber-500 to-amber-400">
            <div class="flex justify-between">
                <div class="text-white">
                    <p class="text-sm font-semibold opacity-75">إجمالي المبالغ</p>
                    <h4 class="mt-2 text-2xl font-bold">{{ number_format($stats['total_amount'], 0) }}</h4>
                    <p class="text-xs opacity-75">دينار</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="panel bg-gradient-to-r from-green-500 to-green-400">
            <div class="flex justify-between">
                <div class="text-white">
                    <p class="text-sm font-semibold opacity-75">المدفوع</p>
                    <h4 class="mt-2 text-2xl font-bold">{{ number_format($stats['total_paid'], 0) }}</h4>
                    <p class="text-xs opacity-75">دينار</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="panel bg-gradient-to-r from-red-500 to-red-400">
            <div class="flex justify-between">
                <div class="text-white">
                    <p class="text-sm font-semibold opacity-75">المتبقي</p>
                    <h4 class="mt-2 text-2xl font-bold">{{ number_format($stats['total_remaining'], 0) }}</h4>
                    <p class="text-xs opacity-75">دينار</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white/20">
                    <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الزبائن -->
    <div class="panel">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <h5 class="text-lg font-semibold dark:text-white-light">
                💰 الزبائن الذين عليهم مبالغ مستحقة
            </h5>
            <a href="{{ route('branch.payments.report') }}" class="btn btn-outline-info btn-sm">
                📊 تقرير المقبوضات
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success flex items-center mb-4"><span>{{ session('success') }}</span></div>
        @endif
        @if (session('info'))
            <div class="alert alert-info flex items-center mb-4"><span>{{ session('info') }}</span></div>
        @endif

        <div class="table-responsive">
            <table class="table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الزبون</th>
                        <th>رقم الهاتف</th>
                        <th>عدد الطلبات</th>
                        <th>إجمالي المبلغ</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $index => $customer)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="font-semibold">{{ $customer->customer_name }}</td>
                            <td class="font-mono">{{ $customer->customer_phone }}</td>
                            <td>
                                <span class="badge bg-info">{{ $customer->orders_count }} طلب</span>
                            </td>
                            <td>{{ number_format($customer->total_amount, 0) }} دينار</td>
                            <td class="text-success">{{ number_format($customer->paid_amount, 0) }} دينار</td>
                            <td class="font-bold text-danger">{{ number_format($customer->remaining_amount, 0) }} دينار</td>
                            <td>
                                <a href="{{ route('branch.payments.customer', $customer->customer_phone) }}"
                                    class="btn btn-primary btn-sm">
                                    💳 الدفع
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-lg">لا توجد مبالغ مستحقة على الزبائن ✅</p>
                                    <p class="text-sm text-gray-400 mt-1">جميع المبالغ تمت تسويتها</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
