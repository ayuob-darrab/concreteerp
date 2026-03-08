@extends('layouts.app')

@section('page-title', 'ادارة شفتات العمل')


@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                <div x-data="carTypeModal()" class="relative">
                    <!-- زر فتح المودال -->
                    <button type="button" class="btn btn-primary flex items-center gap-2" @click="openModal = true">
                        <i class="fas fa-car"></i>
                        <span>اضافة شفت عمل</span>
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
                                    اضافة شفت جديد</h5>

                            </div>

                            <!-- محتوى المودال -->
                            <div class="p-6">
                                {!! Form::open([
                                    'route' => 'companies.store',
                                    'method' => 'POST',
                                    'autocomplete' => 'off',
                                    'files' => true,
                                ]) !!}

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم الشفت <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="shift_name" id="shift_name" placeholder="أدخل اسم الشفت"
                                            value="{{ old('shift_name') }}" class="form-input" required>
                                        @error('shift_name')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- بداية الشفت -->
                                    <div class="space-y-3 cursor-pointer"
                                        onclick="document.getElementById('start_time').showPicker()">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">بداية الشفت <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="time" name="start_time" id="start_time"
                                            value="{{ old('start_time') }}" class="form-input cursor-pointer" required>
                                        @error('start_time')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- نهاية الشفت -->
                                    <div class="space-y-3 cursor-pointer"
                                        onclick="document.getElementById('end_time').showPicker()">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">نهاية الشفت <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}"
                                            class="form-input cursor-pointer" required>
                                        @error('end_time')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <!-- ملاحظات -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">ملاحظات</span>
                                        </label>
                                        <textarea name="note" id="note" rows="2" placeholder="أدخل أي ملاحظات..." class="form-input">{{ old('note') }}</textarea>
                                        @error('note')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- الأزرار -->
                                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                                        <button type="reset" @click="openModal = false"
                                            class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-times-circle"></i>
                                            <span>إلغاء</span>
                                        </button>

                                        <button type="submit" name="active" value="NewShift"
                                            class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-check-circle"></i>
                                            <span>اضافة شفت جديد</span>
                                        </button>
                                    </div>

                                </div>

                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('alpine:init', () => {
                        Alpine.data('carTypeModal', () => ({
                            openModal: false
                        }));
                    });
                </script>

            </h3>

            <!-- جدول المواد -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    شفتات العمل في الشركة
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
                        //  'id','company_code','name','start_time','end_time','notes'
                        $shifttimes->map(function ($b) {
                            return [
                                'id' => $b->id,
                                'name' => $b->name,
                                'company_code' => $b->company_code,
                                'start_time' => $b->start_time, // عدد السيارات حسب العلاقة
                                'end_time' => $b->end_time, // عدد السيارات حسب العلاقة
                                'notes' => $b->notes, // عدد السيارات حسب العلاقة
                            ];
                        }),
                    ) !!};

                    // تحويل البيانات إلى صفوف
                    const rows = tableData.map(b => [
                        b.name,
                        b.company_code,
                        b.start_time,
                        b.end_time,
                        b.notes,
                        b.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'اسم الشفت',
                                'كود الشركة',
                                'بداية الشفت',
                                'نهاية الشفت',
                                'الملاحظات',
                                'تعديل',
                            ],
                            data: rows,
                        },

                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],

                        columns: [{
                                select: 0,
                                className: 'text-center'
                            }, // الاسم
                            {
                                select: 1,
                                className: 'text-center'
                            }, // ملاحظات
                            {
                                select: 2,
                                className: 'text-center'
                            }, // عدد السيارات
                            {
                                select: 5, // زر التعديل
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const url =
                                        `${baseUrl}/companies/${id}&EditShiftTime/edit`;
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
