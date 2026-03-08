@extends('layouts.app')

@section('page-title', 'الطلبات المكتملة 📦')

@section('content')
    <div x-data="completedOrdersTable">
        <div class="panel mt-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                <h3 class="text-lg font-semibold dark:text-white-light">
                    <span class="text-2xl">📦</span> الطلبات المكتملة
                </h3>
                <div class="flex items-center gap-2">
                    <span class="badge bg-primary/20 text-primary px-3 py-1.5 rounded-full text-sm font-medium">
                        {{ $orders->count() }} طلب مكتمل
                    </span>
                </div>
            </div>

            @if ($orders->count() > 0)
                <!-- جدول الطلبات -->
                <table id="completedOrdersTable" class="whitespace-nowrap w-full border border-gray-200">
                    <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                        قائمة الطلبات المكتملة للفرع: {{ $orders[0]->branch->branch_name ?? 'الفرع' }}
                    </caption>
                </table>
            @else
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-gray-500 text-lg">لا توجد طلبات مكتملة حالياً</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        #completedOrdersTable td,
        #completedOrdersTable th {
            text-align: center;
            vertical-align: middle;
        }

        .status-completed {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
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
                Alpine.data('completedOrdersTable', () => ({
                    datatable: null,
                    init() {
                        const tableData = {!! json_encode(
                            $orders->map(function ($order) {
                                $completedDate = $order->updated_at ? \Carbon\Carbon::parse($order->updated_at)->format('Y-m-d') : '-';
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
                                    'completed_date' => $completedDate,
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
                                o.completed_date,
                                paymentBadge,
                                `<span class="status-completed">✅ مكتمل</span>`,
                                o.id
                            ];
                        });

                        this.datatable = new simpleDatatables.DataTable('#completedOrdersTable', {
                            data: {
                                headings: [
                                    'نوع المرسل',
                                    'نوع الطلب',
                                    'اسم المقاول',
                                    'التصنيف',
                                    'الكمية',
                                    'السعر',
                                    'الموقع',
                                    'تاريخ الإكمال',
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
                                select: 10,
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
                                                    onclick="printOrder(${id})"
                                                    class="btn btn-sm btn-outline-secondary" title="طباعة">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
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

            function printOrder(id) {
                window.open(`${baseUrl}/companyBranch/${id}&printOrder/edit`, '_blank');
            }
        </script>
    @endif
@endsection
