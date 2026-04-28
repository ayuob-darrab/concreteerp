@extends('layouts.app')

@section('page-title', 'الطلبات المرفوضة')

@section('content')
    <div x-data="rejectedOrdersTable">
        <div class="panel mt-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <h3 class="text-lg font-semibold dark:text-white-light">❌ الطلبات المرفوضة</h3>
                <span class="badge bg-danger/20 text-danger px-3 py-1.5 rounded-full">{{ $WorkOrder->count() }} طلب</span>
            </div>

            @if ($WorkOrder->count() > 0)
                <table id="rejectedOrdersTable" class="whitespace-nowrap w-full border border-gray-200"></table>
            @else
                <div class="text-center py-10 text-gray-500">
                    لا توجد طلبات مرفوضة حالياً.
                </div>
            @endif
        </div>
    </div>

    <style>
        #rejectedOrdersTable td,
        #rejectedOrdersTable th {
            text-align: center;
            vertical-align: middle;
        }
    </style>

    @if ($WorkOrder->count() > 0)
        <script>
            const baseUrl = '{{ url('/') }}';
            document.addEventListener('alpine:init', () => {
                Alpine.data('rejectedOrdersTable', () => ({
                    datatable: null,
                    init() {
                        const tableData = {!! json_encode(
                            $WorkOrder->map(function ($o) {
                                $rejectedDate = $o->rejected_date
                                    ? \Carbon\Carbon::parse($o->rejected_date)->format('Y-m-d H:i')
                                    : ($o->updated_at ? $o->updated_at->format('Y-m-d H:i') : '-');
                                return [
                                    'id' => $o->id,
                                    'classification' => $o->concreteMix->classification ?? '-',
                                    'branch' => $o->branch->branch_name ?? '-',
                                    'quantity' => $o->quantity ?? 0,
                                    'location' => $o->location ?? '-',
                                    'rejected_date' => $rejectedDate,
                                    'rejected_note' => $o->rejected_note ?? $o->review_note ?? $o->accept_note ?? $o->note ?? '-',
                                ];
                            }),
                        ) !!};

                        const rows = tableData.map(o => [
                            o.id,
                            o.classification,
                            o.branch,
                            o.quantity + ' م³',
                            o.location,
                            o.rejected_date,
                            o.rejected_note,
                            o.id,
                        ]);

                        this.datatable = new simpleDatatables.DataTable('#rejectedOrdersTable', {
                            data: {
                                headings: [
                                    'رقم الطلب',
                                    'الخلطة',
                                    'الفرع',
                                    'الكمية',
                                    'الموقع',
                                    'تاريخ الرفض',
                                    'سبب الرفض',
                                    'تفاصيل',
                                ],
                                data: rows,
                            },
                            searchable: true,
                            perPage: 25,
                            perPageSelect: [10, 20, 30, 50, 100],
                            columns: [{
                                select: 7,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    return `
                                        <a href="${baseUrl}/contractors/${id}&ViewRequest/edit" class="btn btn-outline-primary btn-sm">
                                            عرض
                                        </a>
                                    `;
                                },
                            }],
                            labels: {
                                perPage: '{select}',
                            },
                            layout: {
                                top: '{search}',
                                bottom: '{info}{select}{pager}',
                            },
                        });
                    },
                }));
            });
        </script>
    @endif
@endsection
