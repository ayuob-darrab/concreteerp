@extends('layouts.app')

@section('page-title', 'عرض او اضافة سيارة جديدة')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                <a href="{{ route('cars.create', ['return_url' => url()->current()]) }}"
                    class="btn btn-primary flex items-center gap-2">
                    <i class="fas fa-car"></i>
                    <span>إضافة سيارة جديدة</span>
                </a>
            </h3>

            <!-- جدول السيارات -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    السيارات المتوفرة في الشركة
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
                    // تمرير بيانات السيارات من السيرفر إلى JavaScript
                    const tableData = {!! json_encode(
                        $listCars->map(function ($car) {
                            return [
                                'id' => $car->id,
                                'branch' => $car->BranchName->branch_name ?? 'غير محدد',
                                'car_type' => $car->carType->name ?? 'غير محدد',
                                'car_number' => $car->car_number ?? 'غير متوفر',
                                'car_model' => $car->car_model ?? 'غير متوفر',
                                'is_active' => $car->is_active ? 'فعالة' : 'غير فعالة',
                                'driver_name' => $car->driver_name ?? 'غير متوفر',
                                'add_date' => $car->add_date ?? 'غير محدد',
                                'note' => $car->note ?? 'لا يوجد',
                            ];
                        }),
                    ) !!};

                    // تحويل البيانات إلى صفوف الجدول
                    const rows = tableData.map(c => [
                        c.branch,
                        c.car_type,
                        c.car_number,
                        c.car_model,
                        c.is_active,
                        c.driver_name,
                        c.add_date,
                        c.note,
                        c.id
                    ]);

                    // إنشاء الجدول
                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'الفرع',
                                'نوع السيارة',
                                'رقم السيارة',
                                'الموديل',
                                'الحالة',
                                'اسم السائق',
                                'تاريخ الإضافة',
                                'ملاحظات',
                                'تعديل'
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
                            {
                                select: 6,
                                className: 'text-center'
                            },
                            {
                                select: 7,
                                className: 'text-center'
                            },
                            {
                                select: 8,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const url =
                                        `${baseUrl}/cars/${id}&EditCarInformation/edit`;
                                    return `
                                        <div class="flex items-center justify-center">
                                            <a href="${url}" class="text-green-600 hover:text-green-800" x-tooltip="تعديل">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="w-6 h-6 transition-transform duration-200 hover:scale-110"
                                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M11 5h2l7 7-2 2-7-7V5zM4 20h16v2H4z"/>
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
@endsection
