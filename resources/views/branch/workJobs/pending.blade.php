@extends('layouts.app')

@section('page-title', 'أوامر العمل بانتظار التنفيذ ⏳')

@section('content')
    {{-- قسم الطلبات المعتمدة --}}
    @if (isset($approvedOrders) && $approvedOrders->count() > 0)
        <div class="panel mt-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                <h3 class="text-lg font-semibold dark:text-white-light">
                    <span class="text-2xl">✅</span> الطلبات المعتمدة (بانتظار إنشاء أمر عمل)
                </h3>
                <div class="flex items-center gap-2">
                    <span class="badge bg-success/20 text-success px-3 py-1.5 rounded-full text-sm font-medium">
                        {{ $approvedOrders->count() }} طلب معتمد
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th>نوع المرسل</th>
                            <th>اسم المقاول</th>
                            <th>التصنيف</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>الموقع</th>
                            <th>تاريخ الموافقة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($approvedOrders as $order)
                            <tr>
                                <td>{{ $order->senderType->typename ?? 'مقاول' }}</td>
                                <td>
                                    <div>
                                        <p class="font-semibold">{{ $order->sender->fullname ?? 'غير محدد' }}</p>
                                    </div>
                                </td>
                                <td>{{ $order->concreteMix->classification ?? '-' }}</td>
                                <td class="font-semibold">{{ $order->quantity ?? 0 }} م³</td>
                                <td class="font-semibold text-success">{{ number_format($order->price ?? 0) }} د.ع</td>
                                <td>
                                    <span class="text-xs">{{ $order->location ?? '-' }}</span>
                                </td>
                                <td>
                                    <div>
                                        <p class="font-semibold">
                                            {{ $order->accept_date ? \Carbon\Carbon::parse($order->accept_date)->format('Y-m-d') : '-' }}
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="/ConcreteERP/companyBranch/order/{{ $order->id }}"
                                            class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="/ConcreteERP/companyBranch/workOrder/{{ $order->id }}/createJob"
                                            class="btn btn-sm btn-success" title="إنشاء أمر عمل">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- قسم أوامر العمل المعلقة --}}
    <div class="panel mt-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
            <h3 class="text-lg font-semibold dark:text-white-light">
                <span class="text-2xl">⏳</span> أوامر العمل بانتظار التنفيذ
            </h3>
            <div class="flex items-center gap-2">
                <span class="badge bg-warning/20 text-warning px-3 py-1.5 rounded-full text-sm font-medium">
                    {{ $jobs->count() }} أمر عمل
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
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>تاريخ التنفيذ</th>
                            <th>الحالة</th>
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
                                <td class="font-semibold">{{ $job->total_quantity }} م³</td>
                                <td class="font-semibold text-success">{{ number_format($job->final_price) }} د.ع</td>
                                <td>
                                    <div>
                                        <p class="font-semibold">
                                            {{ $job->scheduled_date ? \Carbon\Carbon::parse($job->scheduled_date)->format('Y-m-d') : '-' }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $job->scheduled_time ? \Carbon\Carbon::parse($job->scheduled_time)->format('H:i') : '-' }}
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    @if ($job->status == 'pending')
                                        <span class="badge bg-secondary">بانتظار التنفيذ</span>
                                    @elseif ($job->status == 'materials_reserved')
                                        <span class="badge bg-info">تم حجز المواد</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <a href="/ConcreteERP/companyBranch/workJob/{{ $job->id }}/view"
                                            class="btn btn-sm btn-outline-primary flex items-center gap-1" title="عرض التفاصيل">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>عرض</span>
                                        </a>
                                        <a href="/ConcreteERP/companyBranch/workJob/{{ $job->id }}/assign"
                                            class="btn btn-sm btn-outline-info flex items-center gap-1" title="تخصيص الآليات">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                            <span>تخصيص</span>
                                        </a>
                                        <button type="button" onclick="startJob({{ $job->id }})"
                                            class="btn btn-sm btn-success flex items-center gap-1" title="بدء العمل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            </svg>
                                            <span>بدء</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-gray-500 text-lg">لا توجد أوامر عمل بانتظار التنفيذ</p>
            </div>
        @endif
    </div>

    <script>
        function startJob(id) {
            if (confirm('هل تريد بدء العمل على هذا الأمر؟')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/ConcreteERP/companyBranch/workJob/${id}/start`;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                form.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
