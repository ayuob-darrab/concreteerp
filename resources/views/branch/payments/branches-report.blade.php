@extends('layouts.app')

@section('page-title', isset($isCompanyWide) && $isCompanyWide ? 'تقرير الفروع — اختيار الفرع' : 'تقرير الفرع')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ url('/home') }}" class="btn btn-outline-secondary btn-sm">← لوحة التحكم</a>
            <h5 class="text-lg font-semibold dark:text-white-light">
                {{ isset($isCompanyWide) && $isCompanyWide ? 'تقرير الفروع' : 'تقرير الفرع' }}
            </h5>
        </div>
        <a href="{{ route('branch.payments.report') }}" class="btn btn-outline-primary btn-sm">تقرير المقبوضات (كل الفروع)</a>
    </div>

    @if (session('info'))
        <div class="alert alert-info mb-4">{{ session('info') }}</div>
    @endif

    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
        @if (isset($isCompanyWide) && $isCompanyWide)
            اعرض الفروع التابعة لشركتك، ثم افتح <strong>التقرير المالي التفصيلي</strong> لأي فرع.
        @else
            هذا التقرير يعرض <strong>بيانات فرعك فقط</strong> للمحاسب.
        @endif
    </p>

    @if (empty($branchStats))
        <div class="panel text-center py-10 text-gray-500">
            <p class="text-lg">لا توجد فروع مسجلة</p>
        </div>
    @else
        <div class="panel overflow-x-auto">
            <table class="table-striped table-hover w-full min-w-[720px] text-sm">
                <thead>
                    <tr>
                        <th class="text-start">الفرع</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">الطلبات</th>
                        <th class="text-center">قيد العمل</th>
                        <th class="text-center">مكتملة</th>
                        <th class="text-end">إجمالي المبالغ</th>
                        <th class="text-end">المدفوع</th>
                        <th class="text-end">المتبقي</th>
                        <th class="text-center">نسبة التحصيل</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($branchStats as $stat)
                        @php
                            $progress =
                                $stat->total_amount > 0
                                    ? round(($stat->paid_amount / $stat->total_amount) * 100)
                                    : 0;
                        @endphp
                        <tr>
                            <td class="font-semibold">{{ $stat->branch->branch_name }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $stat->branch->is_active ? 'success' : 'danger' }}">
                                    {{ $stat->branch->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td class="text-center">{{ $stat->total_orders }}</td>
                            <td class="text-center">{{ $stat->in_progress_orders }}</td>
                            <td class="text-center">{{ $stat->completed_orders }}</td>
                            <td class="text-end font-mono">{{ number_format($stat->total_amount, 0) }}</td>
                            <td class="text-end font-mono text-success">{{ number_format($stat->paid_amount, 0) }}</td>
                            <td class="text-end font-mono text-danger">{{ number_format($stat->remaining_amount, 0) }}</td>
                            <td class="text-center">
                                <span class="font-semibold">{{ $progress }}%</span>
                            </td>
                            <td class="text-center whitespace-nowrap">
                                <a href="{{ route('companyBranch.financial-report', ['branch_id' => $stat->branch->id]) }}"
                                    class="btn btn-primary btn-sm">
                                    تقرير مالي تفصيلي
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">المبالغ بالدينار العراقي حيث ينطبق ذلك على بيانات المقبوضات.</p>
    @endif
@endsection
