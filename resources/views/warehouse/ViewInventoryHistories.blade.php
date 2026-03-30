@extends('layouts.app')

@section('page-title', 'عرض و اضافة المواد الرئيسية في المستودع')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">




            </h3>

            <!-- جدول المواد -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    الشحنات الكلية وتفاصيلها لمادة : {{ $ViewInventoryHistories->first()?->inventory?->name ?? $ViewInventoryHistories->first()?->Chemical?->name ?? 'غير محدد' }}
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
        document.addEventListener('alpine:init', () => {
            Alpine.data('multipleTable', () => ({
                datatable2: null,

                init() {
                    // بيانات الجدول
                    const tableData = {!! json_encode(
                        $ViewInventoryHistories->map(function ($b) {
                            // $quantity = ;
                            // تحويل الرقم إلى نص مع إزالة الأصفار بعد النقطة العشرية
                            $quantity = rtrim(rtrim($b->quantity_added, '0'), '.');
                            return [
                                'supplier_id' => $b->supplier->supplier_name,
                                'MaterialEquipment_id' =>
                                    $b->MaterialEquipment->capacity * $b->countUnit . '   -   ' . $b->MaterialEquipment->code,
                                'total_cost' =>  number_format($b->total_cost, 0, '.', ','),
                                'shipment_date' => $b->shipment_date,
                                'user_id' => $b->user->fullname,
                                'note' => $b->note,
                            ];
                        }),
                    ) !!};

                    // تحويل البيانات إلى صفوف
                    const rows = tableData.map(b => [
                        b.supplier_id,
                        b.MaterialEquipment_id,
                        b.total_cost,
                        b.shipment_date,
                        b.user_id,
                        b.note,

                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'اسم المورد',
                                'الكمية',
                                'سعر الكمية',
                                'تاريخ استلام الشحنة',
                                'مستلم الشحنة',
                                'ملاحظات',

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
                            {
                                select: 5,
                                className: 'text-center'
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
