@extends('layouts.app')

@section('page-title', 'الطلبات المعتمدة - قيد العمل 🚧')

@section('content')
    <div x-data="approvedOrdersTable">
        <div class="panel mt-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                <h3 class="text-lg font-semibold dark:text-white-light">
                    <span class="text-2xl">🚧</span> الطلبات المعتمدة نهائياً - قيد العمل
                </h3>
                <div class="flex items-center gap-2">
                    <span class="badge bg-primary/20 text-primary px-3 py-1.5 rounded-full text-sm font-medium">
                        {{ $WorkOrder->count() }} طلب قيد العمل
                    </span>
                </div>
            </div>

            <!-- جدول الطلبات -->
            <table id="approvedOrdersTable" class="whitespace-nowrap w-full border border-gray-200"></table>

            @if ($WorkOrder->count() == 0)
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <p class="text-gray-500 text-lg">لا توجد طلبات قيد العمل حالياً</p>
                    <p class="text-gray-400 text-sm mt-2">ستظهر الطلبات هنا بعد الموافقة النهائية من الفرع</p>
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

        .status-in-progress {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('approvedOrdersTable', () => ({
                datatable: null,
                init() {
                    const tableData = {!! json_encode(
                        $WorkOrder->map(function ($order) {
                            return [
                                'id' => $order->id,
                                'classification' => $order->ConcreteMix->classification ?? '-',
                                'quantity' => $order->quantity,
                                'price' => $order->price ?? ($order->final_price ?? 0),
                                'execution_date' => $order->execution_date
                                    ? \Carbon\Carbon::parse($order->execution_date)->format('Y-m-d')
                                    : '-',
                                'execution_time' => $order->execution_time ?? '-',
                                'location' => $order->location ?? '-',
                                'accept_date' => $order->accept_date
                                    ? \Carbon\Carbon::parse($order->accept_date)->format('Y-m-d H:i')
                                    : '-',
                                'note' => $order->accept_note ?? ($order->note ?? '-'),
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(o => {
                        const priceDisplay = o.price ?
                            new Intl.NumberFormat('ar-IQ', {
                                maximumFractionDigits: 0
                            }).format(Number(o.price)) + ' د.ع' : '-';

                        return [
                            o.id,
                            o.classification,
                            o.quantity + ' م³',
                            priceDisplay,
                            o.execution_date,
                            o.execution_time,
                            o.location,
                            o.accept_date,
                            o.note,
                            o.id
                        ];
                    });

                    this.datatable = new simpleDatatables.DataTable('#approvedOrdersTable', {
                        data: {
                            headings: [
                                'رقم الطلب',
                                'نوع الخلطة',
                                'الكمية',
                                'السعر النهائي',
                                'تاريخ التنفيذ',
                                'وقت التنفيذ',
                                'الموقع',
                                'تاريخ الموافقة',
                                'ملاحظات',
                                'تفاصيل'
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                                select: 0, // رقم الطلب
                                render: (data) =>
                                    `<span class="font-bold text-primary">#${data}</span>`
                            },
                            {
                                select: 3, // السعر
                                render: (data) => `<span class="price-badge">${data}</span>`
                            },
                            {
                                select: 9, // تفاصيل
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const viewUrl =
                                        `/ConcreteERP/contractors/${id}&ViewApprovedOrder/edit`;
                                    return `
                            <div class="flex items-center justify-center gap-2">
                                <a href="${viewUrl}" class="btn btn-outline-primary btn-sm gap-1" x-tooltip="عرض التفاصيل">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    عرض
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
