@extends('layouts.app')

@section('page-title', 'قائمة موظفين الفرع')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <!-- زر إضافة موظف جديد -->
                <a href="/ConcreteERP/Employees/addBranchEmployee" class="btn btn-primary flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>إضافة موظف جديد</span>
                </a>
            </div>

            <!-- رسائل النجاح -->
            @if (session('success'))
                <div class="alert alert-success flex items-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    قائمة موظفين الفرع
                </caption>
            </table>
        </div>
    </div>


    <script>
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
                                'email' => $emp->email,
                                'branch' => $emp->Branchesname ? $emp->Branchesname->branch_name : '-',
                                'employee_type' => $emp->employeeType ? $emp->employeeType->name : 'لا يوجد',
                                'shifts' => $shifts,
                                'shift' => implode(' ، ', $shifts) ?: 'لا يوجد',
                                'phone' => $emp->phone ?? 'لا يوجد',
                                'createdate' => $emp->createdate ?? 'لا يوجد',
                                'isactive' => $emp->isactive ? 'مفعل' : 'معطل',
                                'has_account' => $emp->user_id ? true : false,
                            ];
                        }),
                    ) !!};

                    // بناء الصفوف
                    const rows = tableData.map(emp => [
                        emp.fullname,
                        emp.employee_type,
                        emp.shift,
                        emp.phone,
                        emp.isactive,
                        emp.id + '|' + (emp.has_account ? '1' : '0'), // عمود إضافة حساب
                        emp.id // عمود عرض
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'الاسم الكامل',
                                'نوع الموظف',
                                'الشفت',
                                'رقم الهاتف',
                                'نشط',
                                'إضافة حساب',
                                'عرض'
                            ],
                            data: rows,
                        },

                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],

                        columns: [
                            // عمود إضافة حساب
                            {
                                select: 5,
                                sortable: false,
                                render: (data) => {
                                    const parts = String(data).split('|');
                                    const id = parts[0];
                                    const hasAccount = parts[1] === '1';
                                    if (hasAccount) {
                                        return '<span class="text-success" title="لديه حساب"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>';
                                    }
                                    const createAccountUrl = '/ConcreteERP/employee/' +
                                        id + '/create-account';
                                    return '<a href="' + createAccountUrl +
                                        '" class="text-success hover:text-success/80" title="إنشاء حساب"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></a>';
                                }
                            },
                            // عمود عرض
                            {
                                select: 6,
                                sortable: false,
                                render: (id) => {
                                    const viewUrl = '/ConcreteERP/Employees/' + id +
                                        '&ViewEmployeeDetails/edit';
                                    return '<a href="' + viewUrl +
                                        '" class="text-info hover:text-info/80" title="عرض التفاصيل"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>';
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
