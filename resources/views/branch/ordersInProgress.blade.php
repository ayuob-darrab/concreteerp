@extends('layouts.app')

@section('page-title', 'الطلبات قيد العمل 🚧')

@section('content')
    <div x-data="inProgressOrdersTable">
        <div class="panel mt-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                <h3 class="text-lg font-semibold dark:text-white-light">
                    <span class="text-2xl">🚧</span> الطلبات قيد العمل
                </h3>
                <div class="flex items-center gap-2">
                    <span class="badge bg-warning/20 text-warning px-3 py-1.5 rounded-full text-sm font-medium">
                        {{ $orders->count() }} طلب قيد العمل
                    </span>
                </div>
            </div>

            @if ($orders->count() > 0)
                <!-- جدول الطلبات -->
                <table id="inProgressOrdersTable" class="whitespace-nowrap w-full border border-gray-200">
                    <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                        قائمة الطلبات قيد العمل للفرع: {{ $orders->first()?->branch?->branch_name ?? 'الفرع' }}
                    </caption>
                </table>
            @else
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-gray-500 text-lg">لا توجد طلبات قيد العمل حالياً</p>
                    <p class="text-gray-400 text-sm mt-2">ستظهر الطلبات هنا بعد الموافقة النهائية</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        #inProgressOrdersTable td,
        #inProgressOrdersTable th {
            text-align: center;
            vertical-align: middle;
        }

        .status-in-progress {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>

    @if ($orders->count() > 0)
        <script>
            const baseUrl = '{{ url('/') }}';
            document.addEventListener('alpine:init', () => {
                Alpine.data('inProgressOrdersTable', () => ({
                    datatable: null,
                    init() {
                        const tableData = {!! json_encode(
                            $orders->map(function ($order) {
                                $acceptDate = $order->accept_date ? \Carbon\Carbon::parse($order->accept_date)->format('Y-m-d') : '-';
                                $executionDate = $order->execution_date ? \Carbon\Carbon::parse($order->execution_date)->format('Y-m-d') : '-';
                                $totalAmount = $order->price ?? 0;
                                return [
                                    'id' => $order->id,
                                    'sender_type' => $order->sendertype->typename ?? 'مقاول',
                                    'request_type' => $order->request_type,
                                    'sender_name' => $order->sender->fullname ?? ($order->customer_name ?? 'غير محدد'),
                                    'classification' => $order->ConcreteMix->classification ?? '-',
                                    'quantity' => $order->quantity,
                                    'price' => number_format($order->price ?? 0),
                                    'location' => $order->location ?? '-',
                                    'accept_date' => $acceptDate,
                                    'execution_date' => $executionDate,
                                    'payment_status' => $order->payment_status ?? 'unpaid',
                                    'paid_amount' => number_format($order->paid_amount ?? 0),
                                    'total_amount' => number_format($totalAmount),
                                ];
                            }),
                        ) !!};

                        const rows = tableData.map(o => {
                            let paymentBadge = '';
                            if (o.payment_status === 'paid') {
                                paymentBadge =
                                    `<span class="badge bg-success/20 text-success">✅ مدفوع</span>`;
                            } else if (o.payment_status === 'partial') {
                                paymentBadge =
                                    `<span class="badge bg-warning/20 text-warning">⏳ جزئي</span><br><small class="text-xs text-gray-400">${o.paid_amount} / ${o.total_amount}</small>`;
                            } else {
                                paymentBadge =
                                    `<span class="badge bg-danger/20 text-danger">❌ غير مدفوع</span>`;
                            }
                            return [
                                o.sender_type,
                                o.request_type === 'direct' ?
                                '<span class="badge bg-info/20 text-info">⚡ طلب مباشر</span>' :
                                '<span class="badge bg-primary/20 text-primary">طلب عادي</span>',
                                o.sender_name,
                                o.classification,
                                o.quantity + ' م³',
                                o.price + ' د.ع',
                                o.location,
                                o.accept_date,
                                o.execution_date,
                                paymentBadge,
                                `<span class="status-in-progress">🚧 قيد العمل</span>`,
                                o.id
                            ];
                        });

                        this.datatable = new simpleDatatables.DataTable('#inProgressOrdersTable', {
                            data: {
                                headings: [
                                    'نوع المرسل',
                                    'نوع الطلب',
                                    'اسم المقاول',
                                    'التصنيف',
                                    'الكمية',
                                    'السعر',
                                    'الموقع',
                                    'تاريخ الموافقة',
                                    'تاريخ التنفيذ',
                                    'الدفع',
                                    'الحالة',
                                    'إجراءات',
                                ],
                                data: rows,
                            },
                            searchable: true,
                            perPage: 25,
                            perPageSelect: [10, 20, 30, 50, 100],
                            columns: [{
                                select: 11,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    return `
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ url('companyBranch/${id}&viewOrder/edit') }}" 
                                               class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ url('companyBranch/${id}&orderPayment/edit') }}" 
                                               class="btn btn-sm btn-outline-success" title="تحصيل / تسديد">
                                                💰
                                            </a>
                                            <button type="button" 
                                                    onclick="markAsCompleted(${id})"
                                                    class="btn btn-sm btn-success" title="إكمال الطلب">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        </div>
                                    `;
                                },
                            }],
                            firstLast: true,
                            labels: {
                                placeholder: 'بحث...',
                                perPage: '{select}',
                                noRows: 'لا توجد نتائج',
                                info: 'عرض {start} إلى {end} من {rows} طلب',
                            },
                            layout: {
                                top: '{search}',
                                bottom: '{info}{select}{pager}',
                            },
                        });
                    },
                }));
            });

            function markAsCompleted(id) {
                if (confirm('هل تريد تأكيد إكمال هذا الطلب؟')) {
                    // إنشاء نموذج وإرساله
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `${baseUrl}/companyBranch/${id}`;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                    form.innerHTML = `
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="active" value="markCompleted">
                    `;

                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
    @endif
@endsection
