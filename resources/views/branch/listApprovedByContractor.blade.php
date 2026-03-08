@extends('layouts.app')

@section('page-title', 'الطلبات المعتمدة من المقاول - بانتظار الموافقة النهائية ✅')

@section('content')
    <div x-data="approvedOrdersTable">
        <div class="panel mt-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                <h3 class="text-lg font-semibold dark:text-white-light">
                    <span class="text-2xl">✅</span> الطلبات المعتمدة من المقاول - بانتظار الموافقة النهائية
                </h3>
                <div class="flex items-center gap-2">
                    <span class="badge bg-success/20 text-success px-3 py-1.5 rounded-full text-sm font-medium">
                        {{ $orders->count() }} طلب بانتظار الموافقة النهائية
                    </span>
                </div>
            </div>

            <!-- جدول الطلبات -->
            <table id="approvedOrdersTable" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    قائمة الطلبات المعتمدة من المقاول للفرع :
                    @if ($orders && count($orders) > 0)
                        {{ $orders[0]->branch->branch_name ?? 'الفرع' }}
                    @else
                        لا يوجد طلبات حالياً
                    @endif
                </caption>
            </table>

            @if ($orders->count() == 0)
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <p class="text-gray-500 text-lg">لا توجد طلبات معتمدة من المقاول حالياً</p>
                    <p class="text-gray-400 text-sm mt-2">ستظهر الطلبات هنا بعد موافقة المقاول على السعر المقترح</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        #approvedOrdersTable td,
        #approvedOrdersTable th {
            text-align: center;
            vertical-align: middle;
        }

        .price-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: bold;
            display: inline-block;
        }
    </style>

    <script>
        const baseUrl = '{{ url('/') }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('approvedOrdersTable', () => ({
                datatable: null,
                init() {
                    const tableData = {!! json_encode(
                        $orders->map(function ($order) {
                            return [
                                'id' => $order->id,
                                'sender_type' => $order->sendertype->typename ?? 'مقاول',
                                'request_type' => $order->request_type,
                                'sender_name' => $order->sender->fullname ?? 'غير محدد',
                                'classification' => $order->ConcreteMix->classification ?? '-',
                                'quantity' => $order->quantity,
                                'price' => number_format($order->price ?? 0, 2),
                                'requester_approval_date' => $order->requester_approval_date
                                    ? \Carbon\Carbon::parse($order->requester_approval_date)->format('Y-m-d H:i')
                                    : '-',
                                'requester_approval_note' => $order->requester_approval_note ?? '-',
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(o => [
                        o.sender_type,
                        o.request_type === 'direct' ?
                        '<span class="badge bg-info/20 text-info">⚡ طلب مباشر</span>' :
                        '<span class="badge bg-primary/20 text-primary">طلب عادي</span>',
                        o.sender_name,
                        o.classification,
                        o.quantity,
                        o.price,
                        o.requester_approval_date,
                        o.requester_approval_note,
                        o.id
                    ]);

                    this.datatable = new simpleDatatables.DataTable('#approvedOrdersTable', {
                        data: {
                            headings: [
                                'نوع المرسل',
                                'نوع الطلب',
                                'اسم المقاول',
                                'التصنيف',
                                'الكمية',
                                'السعر المتفق عليه',
                                'تاريخ موافقة المقاول',
                                'ملاحظة المقاول',
                                'إجراءات'
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                                select: 5, // السعر
                                render: (data) =>
                                    `<span class="price-badge">${data} د.ع</span>`
                            },
                            {
                                select: 8, // الإجراءات
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const viewUrl =
                                        `${baseUrl}/companyBranch/${id}&FinalApproval/edit`;
                                    return `
                            <div class="flex items-center justify-center gap-2">
                                <a href="${viewUrl}" class="btn btn-success btn-sm gap-1" x-tooltip="الموافقة النهائية وتحويل للعمل">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    موافقة نهائية
                                </a>
                            </div>
                            `;
                                }
                            }
                        ],
                        firstLast: true,
                        labels: {
                            perPage: '{select}'
                        },
                        layout: {
                            top: '{search}',
                            bottom: '{info}{select}{pager}'
                        },
                    });
                }
            }));
        });
    </script>
@endsection
