@extends('layouts.app')

@section('page-title', 'المواد الكيميائية الخاصة بالشركة')

@section('content')


    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                <div x-data="concreteMixModal()" class="relative">
                    <!-- زر فتح المودال -->
                    <button type="button" class="btn btn-primary flex items-center gap-2" @click="openModal = true">
                        <i class="fas fa-cubes"></i>
                        <span>إضافة جديد</span>
                    </button>

                    <!-- المودال -->
                    <div x-show="openModal" x-cloak
                        class="fixed inset-0 z-50 flex items-start justify-center pt-10 bg-black/50 overflow-y-auto">
                        <div x-show="openModal" x-transition
                            class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-3xl shadow-2xl border border-gray-200 dark:border-gray-700 m-4">

                            <!-- رأس المودال -->
                            <div class="flex justify-between items-center p-4 border-b bg-indigo-100 dark:bg-indigo-900">
                                <h5
                                    class="font-bold text-lg text-center w-full text-gray-50 dark:text-white bg-gray-700 dark:bg-gray-900 py-3 rounded-lg shadow-md">
                                    إضافة مادة كيميائية جديدة
                                </h5>
                            </div>

                            <!-- محتوى المودال -->
                            <div class="p-6">
                                {!! Form::open([
                                    'route' => 'warehouse.store',
                                    'method' => 'POST',
                                    'autocomplete' => 'off',
                                ]) !!}

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم المادة <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="name" id="name" placeholder="أدخل اسم المادة"
                                            class="form-input" required>
                                    </div>

                                    <!-- وحدة القياس -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">وحدة القياس <span
                                                    class="text-danger">*</span></span>
                                        </label>

                                        <select name="unit" id="unit" class="form-select" required>
                                            <option value="">اختر وحدة القياس</option>
                                            @foreach ($MeasurementUnit as $unit)
                                                <option value="{{ $unit->code }}">{{ $unit->name }} </option>
                                            @endforeach
                                        </select>

                                        @error('unit')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark"> اختيار الفرع<span
                                                    class="text-danger">*</span></span>
                                        </label>

                                        <select name="branches_id" id="branches_id" class="form-select" required>
                                            <option value="allbranches">اضافة لكل الافرع</option>
                                            @foreach ($Branches as $item)
                                                <option value="{{ $item->id }}">{{ $item->branch_name }} </option>
                                            @endforeach
                                        </select>

                                        @error('branches_id')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">وصف المادة </span>
                                        </label>
                                        <input type="text" placeholder="وصف المادة" name="description"
                                            class="form-input">
                                    </div>

                                    <!-- الأزرار -->


                                    <div class="space-y-3 ">
                                        <div class="flex flex-col sm:flex-row justify-end gap-2 mt-8  pt-4 col-span-2">
                                            <button type="reset" @click="openModal = false"
                                                class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                                <i class="fas fa-times-circle"></i>
                                                <span>إلغاء</span>
                                            </button>

                                            <button type="submit" name="active" value="AddNewChemical"
                                                class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                                <i class="fas fa-check-circle"></i>
                                                <span>إضافة مادة جديدة</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>

                                {!! Form::close() !!}
                            </div>

                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('concreteMixModal', () => ({
                            openModal: false
                        }));
                    });
                </script>



            </h3>
            {{ auth()->user()->company_code }}
            {{ Auth::user()->company_code }}

            <!-- جدول الخرسانة -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    المواد الكيميائية في الشركة

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
                    const tableData = {!! json_encode(
                        $listChemical->map(function ($b) {
                            return [
                                'id' => $b->id,
                                'name' => $b->name,
                                'branch_id' => $b->branchName->branch_name,
                                'quantity_total' => number_format($b->quantity_total) . '   ' . $b->MeasurementUnit->name, // الكمية الإجمالية
                                'unit_cost' => number_format($b->unit_cost) . ' لكل لتر  ', // الكمية الإجمالية
                                'description' => $b->description,
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(b => [
                        b.name,
                        b.branch_id,
                        b.quantity_total, // عرض الكمية هنا
                        b.unit_cost,
                        b.description,
                        b.id,
                        b.id,
                        b.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'اسم المادة',
                                'الفرع',
                                'الكمية في المخزن', // العمود الجديد
                                'سعر الوحدة', // العمود الجديد
                                'وصف المادة',
                                'تعديل',
                                'إضافة شحنة',
                                'تفاصيل الشحنات',
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [

                            {
                                select: 5, // زر التعديل (تغير ترتيبه بعد إضافة الكمية)
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const url =
                                        `${baseUrl}/warehouse/${id}&EditChemical/edit`;
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
                            {
                                select: 6, // زر إضافة شحنة
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const addShipmentUrl =
                                        `${baseUrl}/warehouse/${id}&AddChemicalShipment&companyadmin/edit`;

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
                                select: 7, // زر إضافة شحنة
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const addShipmentUrl =
                                        `${baseUrl}/warehouse/${id}&ViewChemicalInventoryHistories/edit`;

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
