@extends('layouts.app')

@section('page-title', 'المواد الرئيسية في مستودع الفرع')

@section('content')


    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
            </h3>

            <!-- جدول المواد -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    المواد الرئيسية في مستودع الفرع
                </caption>
            </table>
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

    <script>
        const baseUrl = '{{ url('/') }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('multipleTable', () => ({
                datatable2: null,

                init() {

                    // بيانات الجدول
                    const tableData = {!! json_encode(
                        $allmaterials->map(function ($b) {
                            $quantity = $b->quantity_total;
                            // تحويل الرقم إلى نص مع إزالة الأصفار بعد النقطة العشرية
                            $quantity = rtrim(rtrim($quantity, '0'), '.');
                            return [
                                'id' => $b->id,
                                'name' => $b->name,
                                // 'branch_id' => $b->branchName->branch_name ?? '',
                                'code' => $b->code,
                                'unit' => $b->unit,
                                'quantity_total' =>
                                    ($b->unit == 'ton' ? $quantity / 20 : $quantity) .
                                    ' ' .
                                    ($b->MeasurementUnit ? $b->MeasurementUnit->name : ''),
                                'note' => $b->note,
                            ];
                        }),
                    ) !!};

                    // تحويل البيانات إلى صفوف
                    const rows = tableData.map(b => [
                        b.name,
                        // b.branch_id,
                        b.code,
                        b.unit,
                        b.quantity_total,
                        b.note,
                        // b.id, // تعديل
                        b.code, // إضافة شحنة
                        b.code
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'اسم المادة',
                                // 'الفرع',
                                'كود المادة',
                                'وحدة القياس',
                                'الكمية المتوفرة',
                                'ملاحظات',
                                // 'تعديل',
                                'إضافة شحنة',
                                'تفاصيل الشحنات',
                            ],
                            data: rows,
                        },

                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],

                        columns: [{
                                select: 0,
                                className: 'text-center'
                            },
                            {
                                select: 1,
                                className: 'text-center'
                            },
                            {
                                select: 2,
                                className: 'text-center'
                            },
                            {
                                select: 3,
                                className: 'text-center'
                            },
                            {
                                select: 4,
                                className: 'text-center'
                            },

                            // {
                            //     select: 6, // زر تعديل
                            //     sortable: false,
                            //     className: 'text-center',
                            //     render: (data) => {
                            //         const id = data;
                            //         const url =
                            //             `${baseUrl}/warehouse/${id}&edit_MainMaterials/edit`;

                            //         return `
                        //         <a href="${url}" class="text-green-600 hover:text-green-800" x-tooltip="تعديل">
                        //             <svg xmlns="http://www.w3.org/2000/svg" 
                        //                 class="w-6 h-6 transition-transform duration-200 hover:scale-110" 
                        //                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        //                 <path stroke-linecap="round" stroke-linejoin="round"
                        //                     d="M11 5h2l7 7-2 2-7-7V5zM4 20h16v2H4z"/>
                        //             </svg>
                        //         </a>
                        //     `;
                            //     },
                            // },
                            {
                                select: 5, // زر إضافة شحنة
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const code = data;
                                    const addShipmentUrl =
                                        `${baseUrl}/warehouse/${code}&addShipment&branch/edit`;

                                    return `
                                    <a href="${addShipmentUrl}" class="text-blue-600 hover:text-blue-800" x-tooltip="إضافة شحنة">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-6 h-6 transition-transform duration-200 hover:scale-110"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <!-- صندوق مع سهم للأسفل -->
                                            <path stroke-linecap="round" stroke-linejoin="round" 
                                                d="M3 7h18v10H3V7zm9 3v4m-2-2h4"/>
                                        </svg>

                                    </a>
                                `;
                                },
                            },
                            {
                                select: 6, // زر إضافة شحنة
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const code = data;
                                    const addShipmentUrl =
                                        `${baseUrl}/warehouse/${code}&ViewInventoryHistories/edit`;

                                    return `
                                    <a href="${addShipmentUrl}" class="text-blue-600 hover:text-blue-800" x-tooltip="عرض الشحنات">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-6 h-6 transition-transform duration-200 hover:scale-110"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="7" width="18" height="10" rx="2" ry="2"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 10h12M6 13h12M6 16h12"/>
                                    </svg>

                                    </a>
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


@endsection
