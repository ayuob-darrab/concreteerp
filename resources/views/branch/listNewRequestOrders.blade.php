@extends('layouts.app')

@section('page-title', 'عرض الطلبات الجديدة 📋')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                الطلبات الجديدة
            </h3>

            <!-- جدول الطلبات -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    قائمة الطلبات الجديدة للفرع :
                    @if ($listNewRequestOrders && count($listNewRequestOrders) > 0)
                        {{ $listNewRequestOrders[0]->branch->branch_name }}
                    @else
                        لا يوجد طلبات حالياً
                    @endif
                </caption>

            </table>
        </div>
    </div>

    <style>
        #myTable2 td,
        #myTable2 th {
            text-align: center;
            vertical-align: middle;
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('multipleTable', () => ({
                datatable2: null,
                init() {

                    const tableData = {!! json_encode(
                        $listNewRequestOrders->map(function ($order) {
                            return [
                                'id' => $order->id,
                                'sender_type' => $order->sendertype->typename ?? 'غير محدد',
                                'request_type' => $order->request_type,
                                'sender_id' => $order->sender->fullname ?? 'غير محدد',
                                'classification' => $order->ConcreteMix->classification,
                                'quantity' => $order->quantity,
                                'status' => $order->status->name_code ?? 'جديد',
                                'note' => $order->note ?? '-',
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(o => [
                        o.sender_type,
                        o.request_type === 'direct' ?
                        '<span class="badge bg-info/20 text-info">⚡ طلب مباشر</span>' :
                        '<span class="badge bg-primary/20 text-primary">طلب عادي</span>',
                        o.sender_id,
                        o.classification,
                        o.quantity,
                        o.status,
                        o.note,
                        o.id // زر التفاصيل أو الإجراء
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'نوع المرسل',
                                'نوع الطلب',
                                'مرسل الطلب',
                                'التصنيف',
                                'الكمية المطلوبة',

                                'الحالة',
                                'ملاحظات',
                                'إجراءات'
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                            select: 7,
                            sortable: false,
                            className: 'text-center',
                            render: (data) => {
                                const id = data;
                                const url =
                                    `/ConcreteERP/companyBranch/${id}&ReviewRequest/edit`;
                                return `
                        <div class="flex items-center justify-center gap-2">
                            <a href="${url}" class="text-green-600 hover:text-green-800" x-tooltip="عرض / تعديل">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </div>
                        `;
                            }
                        }],
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
