@extends('layouts.app')

@section('page-title', 'اضافة موظف جديد')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <div class="flex items-center justify-between mb-5 md:absolute md:top-[25px] md:w-full md:pr-4">


                <a href="{{ route('Employees.create') }}" class="btn btn-primary flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>إضافة موظف</span>
                </a>




            </div>
            <table id="myTable2" class="table-striped whitespace-nowrap w-full">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    قائمة الموظفين : {{ $branches->first()?->Companyname?->name ?? 'الشركة' }}
                </caption>
            </table>
        </div>
    </div>


    <script>
        const baseUrl = '{{ url('/') }}';
        document.addEventListener('alpine:init', () => {
            Alpine.data('multipleTable', () => ({
                datatable2: null,

                init() {
                    const tableData = {!! json_encode(
                        $employees->map(function ($emp) {
                            // جمع الشفتات من الجدول الجديد
                            $shifts = $emp->activeShifts->map(function ($es) {
                                    $name = $es->shift ? $es->shift->name : 'غير محدد';
                                    return $es->is_primary ? "⭐ {$name}" : $name;
                                })->toArray();
                    
                            // fallback للنظام القديم
                            if (empty($shifts) && $emp->shift) {
                                $shifts = [$emp->shift->name];
                            }
                    
                            return [
                                'id' => $emp->id,
                                'fullname' => $emp->fullname,
                                'branch' => $emp->Branchesname ? $emp->Branchesname->branch_name : '-',
                                'employee_type' => $emp->employee_type_code
                                    ? ($emp->employee_type_code . ($emp->employeeType ? ' — ' . $emp->employeeType->name : ''))
                                    : ($emp->employeeType ? $emp->employeeType->name : 'لا يوجد'),
                                'shifts' => $shifts,
                                'shift' => implode(' ، ', $shifts) ?: 'لا يوجد',
                                'phone' => $emp->phone ?? 'لا يوجد',
                                'createdate' => $emp->createdate ?? 'لا يوجد',
                                'isactive' => $emp->isactive ? 'مفعل' : 'معطل',
                            ];
                        }),
                    ) !!};

                    // بناء الصفوف: إضافة عمودي عرض + تعديل
                    const rows = tableData.map(emp => [
                        emp.fullname,
                        emp.branch,
                        emp.employee_type,
                        emp.shift,
                        emp.phone,
                        emp.createdate,
                        emp.isactive,
                        emp.id, // زر عرض التفاصيل
                        emp.id // زر التعديل
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'الاسم الكامل',
                                'الفرع',
                                'نوع الموظف',
                                'الشفت',
                                'رقم الهاتف',
                                'تاريخ التعيين',
                                'نشط',
                                'عرض',
                                'تعديل'
                            ],
                            data: rows,
                        },

                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],

                        columns: [
                            // زر العرض
                            {
                                select: 7,
                                sortable: false,
                                render: (id) => {
                                    const viewUrl =
                                        `${baseUrl}/Employees/${id}&ViewEmployeeDetails/edit`;
                                    return `
                                    <a href="${viewUrl}" class="text-green-600 hover:text-green-800" x-tooltip="عرض التفاصيل">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mx-auto">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                            <circle cx="12" cy="12" r="3" fill="currentColor" />
                                        </svg>
                                    </a>
                                `;
                                }
                            },

                            // زر التعديل
                            {
                                select: 8,
                                sortable: false,
                                render: (id) => {
                                    const editUrl =
                                        `${baseUrl}/Employees/${id}&EditEmployee/edit`;
                                    return `
                                    <a href="${editUrl}" class="text-blue-600 hover:text-blue-800" x-tooltip="تعديل">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mx-auto">
                                            <path d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999C5.26166 13.1771 
                                                     4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 
                                                     3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208
                                                     L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 
                                                     2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456
                                                     L4.19792 21.6782L7.47918 20.5844C8.25353 20.3263 8.6407 20.1973 
                                                     9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751
                                                     C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L19.9213 9.63993
                                                     L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178
                                                 C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z"
                                                  stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </a>
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
                },
            }));
        });
    </script>

@endsection
