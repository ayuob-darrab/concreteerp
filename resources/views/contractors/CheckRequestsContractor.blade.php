@extends('layouts.app')

@section('page-title', 'طلبات بحاجة لموافقة صاحب الطلب')

@section('content')
    <div x-data="requestsTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                الطلبات التي تمت الموافقة عليها من الفرع وتنتظر موافقة صاحب الطلب
            </h3>

            <!-- جدول الطلبات -->
            <table id="requestsTable" class="whitespace-nowrap w-full border border-gray-200"></table>
        </div>
    </div>

    <style>
        #requestsTable td,
        #requestsTable th {
            text-align: center;
            vertical-align: middle;
        }
    </style>

    <script>
        const baseUrl = '{{ url('/') }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('requestsTable', () => ({
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
                                'classification' => $o->ConcreteMix->classification ?? '-',
                                'branch' => $o->branch->branch_name ?? '-',
                                'quantity' => $o->quantity ?? '-',
                                'price' => $o->price ?? '-',
                                'location' => $o->location ?? '-',
                                'request_date' => $requestDate,
                                'sender' => $o->sender->fullname ?? '-',
                                'note' => $o->note ?? '-',
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(o => {
                        const priceDisplay = (o.price !== null && o.price !== undefined && o
                                .price !== '' && o.price !== '-') ?
                            (new Intl.NumberFormat('ar-IQ', {
                                maximumFractionDigits: 0
                            }).format(Number(o.price)) + ' د.ع') :
                            '-';

                        return [
                            o.classification,
                            o.mix_type,
                            o.branch,
                            o.quantity + (o.quantity ? '  م³' : ''),
                            priceDisplay,
                            o.location,
                            o.request_date,
                            o.sender,
                            o.note,
                            o.id,
                        ];
                    });

                    this.datatable = new simpleDatatables.DataTable('#requestsTable', {
                        data: {
                            headings: [
                                'الخلطة',
                                'النوع',
                                'الفرع',
                                'الكمية',
                                'السعر',
                                'الموقع',
                                'تاريخ الطلب',
                                'المرسل',
                                'ملاحظات',
                                'تفاصيل',
                            ],
                            data: rows,
                        },

                        searchable: true,
                        perPage: 25,
                        perPageSelect: [10, 20, 30, 50, 100],

                        columns: [{
                            select: 9,
                            sortable: false,
                            className: 'text-center',
                            render: (data) => {
                                const id = data;
                                const url =
                                    `${baseUrl}/contractors/${id}&ViewRequest/edit`;
                                return `
									<div class="flex items-center justify-center">
										<a href="${url}" class="text-indigo-600 hover:text-indigo-800" x-tooltip="عرض التفاصيل">
											<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
												<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
												<path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
											</svg>
										</a>
									</div>
								`;
                            },
                        }, ],

                        firstLast: true,
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

@endsection
