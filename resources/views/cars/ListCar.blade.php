@extends('layouts.app')

@section('page-title', 'عرض او اضافة سيارة جديدة')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                {{-- تفاصيل السيارات المسجلة في النظام --}}
                <div x-data="carModal()" class="relative">
    <!-- زر فتح المودال -->
    <button type="button" class="btn btn-primary flex items-center gap-2" @click="openModal = true">
        <i class="fas fa-car"></i>
        <span>إضافة سيارة جديدة</span>
    </button>

    <!-- المودال -->
    <div x-show="openModal" x-cloak
        class="fixed inset-0 z-50 flex items-start justify-center pt-10 bg-black/50 overflow-y-auto">
        <div x-show="openModal" x-transition
            class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-5xl shadow-2xl border border-gray-200 dark:border-gray-700 m-4">

            <!-- رأس المودال -->
            <div class="flex justify-between items-center p-4 border-b bg-indigo-100 dark:bg-indigo-900">
                <h5
                    class="font-bold text-lg text-center w-full text-gray-50 dark:text-white bg-gray-700 dark:bg-gray-900 py-3 rounded-lg shadow-md">
                    إضافة سيارة جديدة</h5>
            </div>

            <!-- محتوى المودال -->
            <div class="p-6">
                {!! Form::open([
                    'route' => 'cars.store',
                    'method' => 'POST',
                    'autocomplete' => 'off',
                    'files' => true,
                ]) !!}

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                    <!-- فرع السيارة -->
                    <div class="space-y-3">
                        <label for="branch_id" class="inline-flex cursor-pointer">
                            <span class="text-white-dark">الفرع <span class="text-danger">*</span></span>
                        </label>
                        <select name="branch_id" id="branch_id" class="form-select" required>
                            <option value="" disabled selected>اختر الفرع</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- نوع السيارة -->
                    <div class="space-y-3">
                        <label for="car_type_id" class="inline-flex cursor-pointer">
                            <span class="text-white-dark">نوع السيارة <span class="text-danger">*</span></span>
                        </label>
                        <select name="car_type_id" id="car_type_id" class="form-select" required>
                            <option value="" disabled selected>اختر نوع السيارة</option>
                            @foreach ($carstype as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('car_type_id')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- اسم السيارة -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">اسم السيارة <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="car_name" id="car_name" placeholder="مثال: مارسيدس"
                            value="{{ old('car_name') }}" class="form-input" required>
                        @error('car_name')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- رقم السيارة -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">رقم السيارة <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="car_number" id="car_number" placeholder="أدخل رقم السيارة"
                            value="{{ old('car_number') }}" class="form-input" required>
                        @error('car_number')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- موديل السيارة -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">موديل السيارة <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="car_model" id="car_model" placeholder="أدخل موديل السيارة"
                            value="{{ old('car_model') }}" class="form-input" required>
                        @error('car_model')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ملاحظات -->
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">ملاحظات</span>
                        </label>
                        <textarea name="note" id="note" placeholder="أدخل أي ملاحظات" class="form-input">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="text-danger text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- الأزرار -->
                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                        <button type="reset" @click="openModal = false" class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                            <i class="fas fa-times-circle"></i>
                            <span>إلغاء</span>
                        </button>

                        <button type="submit" name="active" value="AddnewCar" class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                            <i class="fas fa-check-circle"></i>
                            <span>حفظ السيارة</span>
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
    Alpine.data('carModal', () => ({
        openModal: false
    }));
});
</script>

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
        #myTable2 td, #myTable2 th {
            text-align: center;
            vertical-align: middle;
        }
    </style>

    <script>
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
                        })
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
                        columns: [
                            { select: 0, className: 'text-center' },
                            { select: 1, className: 'text-center' },
                            { select: 2, className: 'text-center' },
                            { select: 3, className: 'text-center' },
                            { select: 4, className: 'text-center' },
                            { select: 5, className: 'text-center' },
                            { select: 6, className: 'text-center' },
                            { select: 7, className: 'text-center' },
                            {
                                select: 8,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const url = `/ConcreteERP/cars/${id}&EditCarInformation/edit`;
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
