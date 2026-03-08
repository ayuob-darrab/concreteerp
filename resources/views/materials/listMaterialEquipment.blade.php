@extends('layouts.app')

@section('page-title', 'سعات المواد الإنشائية')

@section('content')




@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                <div x-data="material_equipment()" class="relative">
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
                                    إضافة سعة جديدة
                                </h5>
                            </div>

                            <!-- محتوى المودال -->
                            <div class="p-6">
                                {!! Form::open([
                                    'route' => 'materials.store',
                                    'method' => 'POST',
                                    'autocomplete' => 'off',
                                ]) !!}

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                                    <!-- اسم المادة -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">اسم المادة <span
                                                    class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="name" id="name" placeholder="أدخل اسم المادة"
                                            value="{{ old('name') }}" class="form-input" required>
                                        @error('name')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- السعة -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">
                                                السعة (م³ , ton , cc) <span class="text-danger">*</span>
                                            </span>
                                        </label>
                                        <input type="number" name="capacity" id="capacity" placeholder="أدخل السعة"
                                            value="{{ old('capacity') }}" class="form-input" required min="1"
                                            step="any" title="أدخل رقم أكبر من أو يساوي 1">
                                        @error('capacity')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">وحدة القياس</span>
                                        </label>

                                        <select name="code" id="code" class="form-input" required>
                                            <option value="">اختر وحدة القياس</option>
                                            @foreach ($MeasurementUnit as $unit)
                                                <option value="{{ $unit->code }}">
                                                    {{ $unit->name }} ({{ $unit->code }})
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('code')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- نوع المادة (رمل/حصو/أسمنت...) -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">نوع المادة (اختياري)</span>
                                        </label>

                                        <select name="material_type" id="material_type" class="form-input">
                                            <option value="">جميع المواد (عام)</option>
                                            @if (isset($materials))
                                                @foreach ($materials as $mat)
                                                    <option value="{{ $mat->name }}">{{ $mat->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <small class="text-gray-500">اختر نوع المادة لتخصيص هذه المعدة لمادة معينة
                                            فقط</small>

                                        @error('material_type')
                                            <div class="text-danger text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- ملاحظات -->
                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">ملاحظات</span>
                                        </label>
                                        <input name="note" id="note" placeholder="أدخل أي ملاحظات..."
                                            class="form-input">
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

                                        <button type="submit" name="active" value="NewMaterialEquipment"
                                            class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-check-circle"></i>
                                            <span>إضافة سعة جديدة</span>
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
                        Alpine.data('material_equipment', () => ({
                            openModal: false
                        }));
                    });
                </script>
            </h3>

            <!-- جدول المواد -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    سعات المواد الإنشائية
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
                        $listMaterialEquipment->map(function ($b) {
                            return [
                                'id' => $b->id,
                                'name' => $b->name,
                                'capacity' => $b->capacity,
                                'UnitName' => $b->UnitName->name,
                                // 'company_code' => $b->CompanyName->name ?? 'عام',
                                'note' => $b->note,
                            ];
                        }),
                    ) !!};

                    // تحويل البيانات إلى صفوف
                    const rows = tableData.map(b => [
                        b.name,
                        b.capacity,
                        b.UnitName,
                        // b.company_code,
                        b.note,
                        b.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'اسم المادة',
                                'الحجم ',
                                'وحدة القياس',
                                // 'كود الشركة',
                                'ملاحظات',
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
                                select: 4, // زر التعديل
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const url =
                                        `${baseUrl}/materials/${id}&editMaterialEquipment/edit`;
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
