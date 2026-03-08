@extends('layouts.app')

@section('page-title', 'عرض وادارة خلطات الخرسانة')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                خلطات الخرسانة
            </h3>

            @if ($ConcreteMix->isEmpty())
                <div
                    class="mt-16 mb-5 flex items-center justify-center p-3.5 text-center text-warning bg-warning-light dark:bg-warning-dark-light">
                    <span class="ltr:pr-2 rtl:pl-2">
                        <strong class="ltr:mr-1 rtl:ml-1">تنبيه!</strong>
                        لا توجد خلطات خرسانة متاحة في فرعك حالياً.
                    </span>
                </div>
            @else
                <!-- جدول الخرسانة -->
                <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                    <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                        خلطات الخرسانة المتاحة في فرع : {{ $ConcreteMix->first()?->branchName?->branch_name ?? 'غير محدد' }}
                    </caption>
                </table>
            @endif
        </div>
    </div>

    <!-- CSS لتوسيط النصوص داخل الجدول -->
    <style>
        #myTable2 td,
        #myTable2 th {
            text-align: center;
            vertical-align: middle;
        }
    </style>

    @if ($ConcreteMix->isNotEmpty())
        <script>
            const baseUrl = '{{ url('/') }}';
            document.addEventListener('alpine:init', () => {
                Alpine.data('multipleTable', () => ({
                    datatable2: null,

                    init() {

                        const tableData = {!! json_encode(
                            $ConcreteMix->map(function ($b) {
                                return [
                                    'id' => $b->id,
                                    'classification' => $b->classification,
                                    'orders_count' => $b->workOrders?->count() ?? 0,
                                    'total_quantity' => ($b->workOrders?->sum('quantity') ?? 0) . '  م³  ',
                                    'notes' => str_replace('•', '<br>•', $b->notes ?? ''),
                                ];
                            }),
                        ) !!};

                        const rows = tableData.map(b => [
                            b.classification,
                            b.orders_count,
                            b.total_quantity,
                            b.notes,
                            b.id, // لزر التقديم
                        ]);

                        this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                            data: {
                                headings: [
                                    'التصنيف',
                                    'اجمالي الطلبات',
                                    'اجمالي الطلبيات م³',
                                    'ملاحظات',
                                    'تقديم طلب  عمل',
                                ],
                                data: rows,
                            },

                            searchable: true,
                            perPage: 25,
                            perPageSelect: [10, 20, 30, 50, 100],

                            // ✅ هنا المشكلة: يجب دمج الأزرار داخل مصفوفة واحدة
                            columns: [{
                                    select: 4,
                                    sortable: false,
                                    className: 'text-center',
                                    render: (data) => {
                                        const id = data;
                                        const url =
                                            `${baseUrl}/contractors/${id}&SendNewRequest/edit`;
                                        return `
                                    <div class="flex items-center justify-center">
                                        <a href="${url}" class="text-green-600 hover:text-green-800" x-tooltip="تقديم طلب عمل">
                                            <svg xmlns="http://www.w3.org/2000/svg"
     class="w-6 h-6 transition-transform duration-200 hover:scale-110"
     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round"
        d="M12 4v16m8-8H4" />
</svg>

                                        </a>
                                    </div>
                                `;
                                    },
                                },


                            ],

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
    @endif

@endsection
