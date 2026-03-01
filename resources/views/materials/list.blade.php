@extends('layouts.app')

@section('page-title', 'عرض مواد البناء')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            {{-- <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                
            </h3> --}}
            <div x-data="materialModal()" class="relative">
                <!-- زر فتح المودال -->
                <button type="button" class="btn btn-primary flex items-center gap-2" @click="openModal = true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>إضافة مادة</span>
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
                                إضافة مادة هيكلية جديدة</h5>
                            <button @click="openModal = false"
                                class="absolute right-4 text-gray-500 hover:text-gray-700 text-2xl">اغلاق</button>
                        </div>

                        <!-- محتوى المودال -->
                        <div class="p-6">
                            {!! Form::open([
                                'route' => 'materials.store',
                                'method' => 'POST',
                                'autocomplete' => 'off',
                                'files' => true,
                            ]) !!}

                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                                <!-- اسم المادة -->
                                <div class="space-y-3">
                                    <label class="inline-flex cursor-pointer">
                                        <span class="text-white-dark">اسم المادة <span class="text-danger">*</span></span>
                                    </label>
                                    <input type="text" name="material_name" id="material_name"
                                        placeholder="أدخل اسم المادة" value="{{ old('material_name') }}" class="form-input"
                                        required>
                                    @error('material_name')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- سعر المادة -->
                                <div class="space-y-3">
                                    <label class="inline-flex cursor-pointer">
                                        <span class="text-white-dark">
                                            سعر المادة في المتر المربع <span class="text-danger">*</span>
                                        </span>
                                    </label>
                                    <input type="text" name="price" id="price"
                                        placeholder="أدخل السعر بالمتر المربع" value="{{ old('price') }}"
                                        class="form-input" required>
                                    <div id="price-error" class="text-danger text-sm hidden"></div>
                                    @error('price')
                                        <div class="text-danger text-sm">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- الأزرار -->
                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
                                    <button type="submit" name="active" value="NewMaterials"
                                        class="btn btn-primary !mt-6 px-8">
                                        <i class="fas fa-check-circle me-2"></i> حفظ المادة
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
                                    <button type="reset" class="btn btn-outline-secondary !mt-6 px-8">
                                        <i class="fas fa-times-circle me-2"></i> إلغاء
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
                    Alpine.data('materialModal', () => ({
                        openModal: false
                    }));
                });

                // تنسيق السعر مع الفواصل والتحقق من الحد الأدنى والحد الأعلى
                const priceInput = document.getElementById('price');
                const priceError = document.getElementById('price-error');

                priceInput.addEventListener('input', function() {
                    let value = this.value.replace(/,/g, '');
                    value = value.replace(/[^\d]/g, ''); // أرقام فقط

                    // الحد الأعلى والأدنى
                    if (value.length > 0 && parseInt(value) < 1000) {
                        priceError.textContent = '⚠️ السعر لا يمكن أن يقل عن 1,000';
                        priceError.classList.remove('hidden');
                    } else if (parseInt(value) > 999999) {
                        value = '999999';
                        priceError.textContent = '⚠️ السعر لا يمكن أن يزيد عن 999,999';
                        priceError.classList.remove('hidden');
                    } else {
                        priceError.classList.add('hidden');
                    }

                    // تنسيق الرقم بالفواصل
                    this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                });

                // إزالة الفواصل قبل الإرسال
                document.querySelector('form')?.addEventListener('submit', function(e) {
                    priceInput.value = priceInput.value.replace(/,/g, '');
                });
            </script>

            <!-- جدول المواد -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    المواد المتوفرة في الشركة
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
                        $material->map(function ($b) {
                            return [
                                'id' => $b->id,
                                'name' => $b->name,
                                'price' => $b->price,
                            ];
                        }),
                    ) !!};

                    // تحويل البيانات إلى صفوف
                    const rows = tableData.map(b => [
                        b.name,
                        b.price,
                        b.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'اسم المادة',
                                'السعر في المتر المربع',
                                'تعديل تفاصيل'
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
                            }, // السعر
                            {
                                select: 2,
                                className: 'text-center'
                            }, // السعر
                            {
                                select: 2, // زر التعديل
                                sortable: true,
                                className: 'text-center',
                                render: (data, cell, row) => {
                                    const id = data;
                                    const url =
                                        `/ConcreteERP/materials/${id}&edit_material/edit`;
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
