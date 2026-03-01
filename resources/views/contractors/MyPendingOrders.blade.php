@extends('layouts.app')

@section('page-title', 'طلباتي الجديدة')

@section('content')
    <div x-data="pendingOrdersTable">
        <div class="panel mt-6">
            @if ($WorkOrder->count() > 0)
                <!-- جدول الطلبات -->
                <table id="pendingOrdersTable" class="whitespace-nowrap w-full border border-gray-200">
                    <caption class="text-lg font-semibold dark:text-white-light text-right mb-4 p-3">
                        📋 الطلبات الجديدة - بانتظار موافقة الفرع
                        <span class="badge bg-warning text-dark rounded-full px-3 py-1 mr-2">{{ $WorkOrder->count() }}
                            طلب</span>
                    </caption>
                </table>
            @else
                <div class="flex flex-col items-center justify-center py-10">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">لا توجد طلبات معلقة حالياً</p>
                    <a href="/ConcreteERP/contractors/SendRequestsContractor" class="btn btn-primary mt-4">
                        ➕ تقديم طلب جديد
                    </a>
                </div>
            @endif
        </div>
    </div>

    <style>
        #pendingOrdersTable td,
        #pendingOrdersTable th {
            text-align: center;
            vertical-align: middle;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
    </style>

    @if ($WorkOrder->count() > 0)
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('pendingOrdersTable', () => ({
                    datatable: null,

                    init() {
                        const tableData = {!! json_encode(
                            $WorkOrder->map(function ($o) {
                                $requestDate = $o->request_date
                                    ? \Carbon\Carbon::parse($o->request_date)->format('Y-m-d')
                                    : ($o->created_at
                                        ? $o->created_at->format('Y-m-d')
                                        : '-');
                                return [
                                    'id' => $o->id,
                                    'order_number' => $o->order_number ?? $o->id,
                                    'classification' => $o->ConcreteMix->classification ?? '-',
                                    'mix_type' => $o->ConcreteMix->mix_type ?? '-',
                                    'branch' => $o->branch->branch_name ?? '-',
                                    'quantity' => $o->quantity ?? '-',
                                    'location' => $o->location ?? '-',
                                    'request_date' => $requestDate,
                                    'note' => $o->note ?? '-',
                                    'status' => 'بانتظار الموافقة',
                                ];
                            }),
                        ) !!};

                        const rows = tableData.map(o => {
                            return [
                                o.order_number,
                                o.classification,
                                o.mix_type,
                                o.branch,
                                o.quantity + (o.quantity ? ' م³' : ''),
                                o.location,
                                o.request_date,
                                `<span class="status-pending">⏳ ${o.status}</span>`,
                                o.note,
                            ];
                        });

                        this.datatable = new simpleDatatables.DataTable('#pendingOrdersTable', {
                            data: {
                                headings: [
                                    'رقم الطلب',
                                    'الخلطة',
                                    'النوع',
                                    'الفرع',
                                    'الكمية',
                                    'الموقع',
                                    'تاريخ الطلب',
                                    'الحالة',
                                    'ملاحظات',
                                ],
                                data: rows,
                            },

                            searchable: true,
                            perPage: 25,
                            perPageSelect: [10, 20, 30, 50, 100],

                            labels: {
                                placeholder: 'بحث...',
                                // perPage: 'عدد الصفوف لكل صفحة',
                                noRows: 'لا توجد نتائج',
                                info: 'عرض {start} إلى {end} من {rows} طلب',
                            },
                        });
                    },
                }));
            });
        </script>
    @endif
@endsection
