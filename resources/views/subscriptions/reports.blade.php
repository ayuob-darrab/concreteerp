@extends('layouts.app')

@section('page-title', 'تقارير الاشتراكات')

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="panel h-full w-full">
            <h5 class="mb-3 text-lg font-semibold dark:text-white-light">ملخص الحالات</h5>
            <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                <li>نشطة: {{ $stats['total_active'] }}</li>
                <li>منتهية: {{ $stats['total_expired'] }}</li>
                <li>معلقة: {{ $stats['total_suspended'] }}</li>
            </ul>
        </div>
        <div class="panel h-full w-full col-span-2">
            <h5 class="mb-3 text-lg font-semibold dark:text-white-light">حسب نوع الخطة</h5>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <th class="pb-2">الخطة</th>
                            <th class="pb-2">العدد</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($stats['by_plan'] as $row)
                            <tr>
                                <td class="py-2">{{ $row->plan_type }}</td>
                                <td class="py-2">{{ $row->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6 panel h-full w-full">
        <div class="mb-3 flex items-center justify-between">
            <h5 class="text-lg font-semibold dark:text-white-light">آخر 50 اشتراك</h5>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <th class="pb-2">الشركة</th>
                        <th class="pb-2">الخطة</th>
                        <th class="pb-2">البداية</th>
                        <th class="pb-2">النهاية</th>
                        <th class="pb-2">الرسوم</th>
                        <th class="pb-2">النسبة</th>
                        <th class="pb-2">الحالة</th>
                        <th class="pb-2">فاتورة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($subscriptions as $sub)
                        <tr>
                            <td class="py-2">{{ $sub->company_code }}</td>
                            <td class="py-2">{{ $sub->plan_type }}</td>
                            <td class="py-2">{{ $sub->start_date }}</td>
                            <td class="py-2">{{ $sub->end_date ?? '-' }}</td>
                            <td class="py-2">{{ $sub->base_fee ?? 0 }}</td>
                            <td class="py-2">{{ $sub->percentage_rate ?? 0 }}%</td>
                            <td class="py-2">{{ $sub->status }}</td>
                            <td class="py-2">
                                <a class="text-primary" href="{{ route('subscriptions.invoice', $sub->id) }}">عرض فاتورة</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection



