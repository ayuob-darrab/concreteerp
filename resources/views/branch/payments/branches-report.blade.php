@extends('layouts.app')

@section('page-title', 'تقرير الفروع - المبالغ والطلبات')

@section('content')
    <div class="mb-5 flex items-center gap-3">
        <a href="{{ url('/ConcreteERP/home') }}" class="btn btn-outline-secondary btn-sm">← لوحة التحكم</a>
        <h5 class="text-lg font-semibold dark:text-white-light">📊 تقرير الفروع - المبالغ والطلبات</h5>
    </div>

    <!-- بطاقات الفروع -->
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($branchStats as $stat)
            <div class="panel">
                <div class="mb-4 flex items-center justify-between">
                    <h6 class="font-semibold text-lg">
                        🏢 {{ $stat->branch->branch_name }}
                    </h6>
                    <span class="badge bg-{{ $stat->branch->is_active ? 'success' : 'danger' }}">
                        {{ $stat->branch->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </div>

                <!-- إحصائيات الطلبات -->
                <div class="mb-4">
                    <h6 class="text-sm text-gray-500 mb-2">📋 الطلبات</h6>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-center p-2 bg-blue-50 dark:bg-blue-900/20 rounded">
                            <p class="text-2xl font-bold text-blue-600">{{ $stat->total_orders }}</p>
                            <p class="text-xs text-gray-500">إجمالي</p>
                        </div>
                        <div class="text-center p-2 bg-amber-50 dark:bg-amber-900/20 rounded">
                            <p class="text-2xl font-bold text-amber-600">{{ $stat->in_progress_orders }}</p>
                            <p class="text-xs text-gray-500">قيد العمل</p>
                        </div>
                        <div class="text-center p-2 bg-green-50 dark:bg-green-900/20 rounded">
                            <p class="text-2xl font-bold text-green-600">{{ $stat->completed_orders }}</p>
                            <p class="text-xs text-gray-500">مكتملة</p>
                        </div>
                    </div>
                </div>

                <!-- إحصائيات المالية -->
                <div>
                    <h6 class="text-sm text-gray-500 mb-2">💰 المالية</h6>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">إجمالي المبالغ:</span>
                            <span class="font-bold">{{ number_format($stat->total_amount, 0) }} دينار</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">المدفوع:</span>
                            <span class="font-bold text-success">{{ number_format($stat->paid_amount, 0) }} دينار</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">المتبقي:</span>
                            <span class="font-bold text-danger">{{ number_format($stat->remaining_amount, 0) }} دينار</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">مدفوع بالكامل:</span>
                            <span class="badge bg-success">{{ $stat->paid_count }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">غير مكتمل:</span>
                            <span class="badge bg-danger">{{ $stat->unpaid_count }}</span>
                        </div>
                    </div>
                </div>

                <!-- شريط التقدم -->
                @php
                    $progress = $stat->total_amount > 0 ? round(($stat->paid_amount / $stat->total_amount) * 100) : 0;
                @endphp
                <div class="mt-4">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>نسبة التحصيل</span>
                        <span>{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="bg-primary h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if (empty($branchStats))
        <div class="panel text-center py-10 text-gray-500">
            <p class="text-lg">لا توجد فروع مسجلة</p>
        </div>
    @endif
@endsection
