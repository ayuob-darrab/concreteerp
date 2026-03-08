@extends('layouts.app')

@section('page-title', 'عرض وادارة خلطات الخرسانة')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">
                {{-- <div x-data="concreteMixModal()" class="relative">
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
                                    إضافة خلطه خرسانية جديدة
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

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">التصنيف <span class="text-danger">*</span></span>
                                        </label>
                                        <input type="text" name="classification" id="classification"
                                            placeholder="أدخل التصنيف" class="form-input" required>
                                    </div>

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الأسمنت (أكياس ×50كجم)</span>
                                        </label>
                                        <input type="text" name="cement" class="form-input">
                                    </div>

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الرمل (م³)</span>
                                        </label>
                                        <input type="text" name="sand" class="form-input">
                                    </div>

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الحصى (م³)</span>
                                        </label>
                                        <input type="text" name="gravel" class="form-input">
                                    </div>

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">الماء (لتر)</span>
                                        </label>
                                        <input type="text" name="water" class="form-input">
                                    </div>

                                    <div class="space-y-3">
                                        <label class="inline-flex cursor-pointer">
                                            <span class="text-white-dark">ملاحظات</span>
                                        </label>
                                        <textarea name="notes" rows="2" class="form-input"></textarea>
                                    </div>

                                    <!-- الأزرار -->
                                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                                        <button type="reset" @click="openModal = false"
                                            class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-times-circle"></i>
                                            <span>إلغاء</span>
                                        </button>

                                        <button type="submit"
                                            class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                                            <i class="fas fa-check-circle"></i>
                                            <span>إضافة خلطه جديدة</span>
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
                        Alpine.data('concreteMixModal', () => ({
                            openModal: false
                        }));
                    });
                </script> --}}
            </h3>

            <!-- جدول الخرسانة -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    خلطات الخرسانة المسجلة
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
                    const tableData = {!! json_encode(
                        $ConcreteMix->map(function ($b) {
                            return [
                                'id' => $b->id,
                                'classification' => $b->classification,
                                'price' => number_format($b->price, 0, '.', ','),
                                'branchName' => $b->branchName->branch_name ?? 'الاستندر العام',
                                'cement' => $b->cement,
                                'sand' => $b->sand,
                                'gravel' => $b->gravel,
                                'water' => $b->water,
                                'chemicals' => $b->chemicals->map(function ($c) {
                                        return $c->name . ' = ' . $c->pivot->quantity . '<br/>';
                                    })->implode(','),
                                'notes' => str_replace('•', '<br>•', $b->notes),
                            ];
                        }),
                    ) !!};

                    const rows = tableData.map(b => [
                        b.classification,
                        b.price,
                        b.branchName,
                        b.cement,
                        b.sand,
                        b.gravel,
                        b.water,
                        b.chemicals,

                        b.notes,
                        // b.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'التصنيف',
                                'سعر م³',
                                'الفرع',
                                'الأسمنت (أكياس)',
                                'الرمل (م³)',
                                'الحصى (م³)',
                                'الماء (لتر)',
                                'المادة الكيميائية',
                                'ملاحظات',
                                // 'تعديل',
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        // columns: [{
                        //     select: 9, // زر التعديل
                        //     sortable: false,
                        //     className: 'text-center',
                        //     render: (data) => {
                        //         const id = data;
                        //         const url =
                        //             `${baseUrl}/warehouse/${id}&EditQuantitiesConcreteMix/edit`;
                        //         return `
                    //             <div class="flex items-center justify-center">
                    //                 <a href="${url}" class="text-green-600 hover:text-green-800" x-tooltip="تعديل">
                    //                     <svg xmlns="http://www.w3.org/2000/svg" 
                    //                          class="w-6 h-6 transition-transform duration-200 hover:scale-110" 
                    //                          fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    //                         <path stroke-linecap="round" stroke-linejoin="round"
                    //                             d="M11 5h2l7 7-2 2-7-7V5zM4 20h16v2H4z"/>
                    //                     </svg>
                    //                 </a>
                    //             </div>
                    //         `;
                        //     },
                        // }, ],
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
