@extends('layouts.app')

@section('page-title', 'أوامر العمل المكتملة ✅')

@section('content')
    <div class="panel mt-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <h3 class="text-lg font-semibold dark:text-white-light">
                <span class="text-2xl">✅</span> أوامر العمل المكتملة
            </h3>
            <div class="flex items-center gap-2">
                <span class="badge bg-success/20 text-success px-3 py-1.5 rounded-full text-sm font-medium">
                    {{ $jobs->total() }} أمر عمل مكتمل
                </span>
            </div>
        </div>

        @if ($jobs->count() > 0)
            <div class="table-responsive">
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th>رقم الأمر</th>
                            <th>العميل</th>
                            <th>نوع الكونكريت</th>
                            <th>الكمية المنفذة</th>
                            <th>المبلغ الإجمالي</th>
                            <th>تاريخ البدء</th>
                            <th>تاريخ الإنتهاء</th>
                            <th>عدد الشحنات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobs as $job)
                            <tr>
                                <td>
                                    <a href="/ConcreteERP/companyBranch/workJob/{{ $job->id }}/view"
                                        class="text-primary font-semibold hover:underline">
                                        {{ $job->job_number }}
                                    </a>
                                </td>
                                <td>
                                    <div>
                                        <p class="font-semibold">{{ $job->customer_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $job->customer_phone }}</p>
                                    </div>
                                </td>
                                <td>{{ $job->concreteType->classification ?? '-' }}</td>
                                <td class="font-semibold">{{ $job->executed_quantity }} م³</td>
                                <td class="font-semibold text-success">{{ number_format($job->final_price) }} د.ع</td>
                                <td>{{ $job->actual_start_date ? \Carbon\Carbon::parse($job->actual_start_date)->format('Y-m-d') : '-' }}
                                </td>
                                <td>{{ $job->actual_end_date ? \Carbon\Carbon::parse($job->actual_end_date)->format('Y-m-d') : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $job->total_shipments }} شحنة</span>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="/ConcreteERP/companyBranch/workJob/{{ $job->id }}/view"
                                            class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        <a href="/ConcreteERP/companyBranch/workJob/{{ $job->id }}/invoice"
                                            class="btn btn-sm btn-outline-success" title="الفاتورة">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                        <button type="button" onclick="printJob({{ $job->id }})"
                                            class="btn btn-sm btn-outline-secondary" title="طباعة">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ترقيم الصفحات --}}
            <div class="mt-4">
                {{ $jobs->links() }}
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-500 text-lg">لا توجد أوامر عمل مكتملة</p>
            </div>
        @endif
    </div>

    <script>
        function printJob(id) {
            window.open(`/ConcreteERP/companyBranch/workJob/${id}/print`, '_blank');
        }
    </script>
@endsection
