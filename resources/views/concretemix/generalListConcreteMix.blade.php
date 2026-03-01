@extends('layouts.app')

@section('page-title', 'عرض وادارة خلطات الخرسانة')

@section('content')
    <div class="max-w-6xl mx-auto" x-data="multipleTable">
        <div class="panel mt-6">
            <div class="mb-5 space-y-4">
                <!-- أزرار الإضافة والفلاتر -->
                <div class="flex flex-wrap items-center gap-3">
                    <div x-data="concreteMixModal()">
                        <button type="button" class="btn btn-primary"
                            @click="openModal = true; $nextTick(() => $refs.classificationInput?.focus())">
                            إضافة خلطة جديدة
                        </button>

                        <!-- المودال - يُغلق فقط عند الضغط على إلغاء أو X -->
                        <div x-show="openModal" x-cloak
                            class="fixed inset-0 z-50 flex items-start justify-center pt-6 sm:pt-10 pb-10 px-4 bg-black/50 overflow-y-auto">
                            <div x-show="openModal" x-transition
                                class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-2xl shadow-xl border border-gray-200 dark:border-gray-700 mt-0">

                                <!-- رأس المودال -->
                                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">إضافة خلطة خرسانية جديدة</h3>
                                    <button type="button" @click="openModal = false"
                                        class="p-1.5 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- محتوى المودال -->
                                <div class="p-6">
                                    {!! Form::open([
                                        'route' => 'materials.store',
                                        'method' => 'POST',
                                        'autocomplete' => 'off',
                                    ]) !!}
                                    <input type="hidden" name="active" value="AddNewGeneralConcreteMix">

                                    <div class="space-y-5">
                                        <!-- التصنيف -->
                                        <div>
                                            <label for="classification" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                                التصنيف <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" name="classification" id="classification" required
                                                x-ref="classificationInput"
                                                placeholder="مثال: خلطة C25"
                                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                                autofocus>
                                        </div>

                                        <!-- مكونات الخلطة -->
                                        <div>
                                            <p class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">مكونات الخلطة</p>
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                                <div>
                                                    <label for="cement" class="block text-xs text-gray-600 dark:text-gray-400 mb-1">الأسمنت (أكياس)</label>
                                                    <input type="text" name="cement" id="cement" placeholder="0"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                </div>
                                                <div>
                                                    <label for="sand" class="block text-xs text-gray-600 dark:text-gray-400 mb-1">الرمل (م³)</label>
                                                    <input type="text" name="sand" id="sand" placeholder="0"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                </div>
                                                <div>
                                                    <label for="gravel" class="block text-xs text-gray-600 dark:text-gray-400 mb-1">الحصى (م³)</label>
                                                    <input type="text" name="gravel" id="gravel" placeholder="0"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                </div>
                                                <div>
                                                    <label for="water" class="block text-xs text-gray-600 dark:text-gray-400 mb-1">الماء (لتر)</label>
                                                    <input type="text" name="water" id="water" placeholder="0"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ملاحظات -->
                                        <div>
                                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">ملاحظات</label>
                                            <textarea name="notes" id="notes" rows="3" placeholder="أي ملاحظات إضافية..."
                                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 resize-none"></textarea>
                                        </div>

                                        <!-- أزرار الإجراءات -->
                                        <div class="flex flex-wrap justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <button type="button" @click="openModal = false"
                                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500 rounded-lg transition-colors">
                                                إلغاء
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                                style="background-color:#2563eb;color:#fff;">
                                                إضافة خلطة جديدة
                                            </button>
                                        </div>
                                    </div>

                                    {!! Form::close() !!}
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
                    </div>

                    <!-- فلاتر الشركة -->
                    <select x-model="companyFilter" @change="filterTable()" class="form-select w-48">
                        <option value="">كل الشركات</option>
                        <template x-for="company in companies" :key="company">
                            <option :value="company" x-text="company"></option>
                        </template>
                    </select>

                    <!-- زر إعادة التعيين -->
                    <button type="button" @click="resetFilters()" class="btn btn-outline-secondary">
                        إعادة تعيين الفلاتر
                    </button>
                </div>
            </div>

            <!-- جدول الخرسانة -->
            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3">
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
                allData: [],
                companies: [],
                companyFilter: '',

                init() {
                    const tableData = {!! json_encode(
                        $ConcreteMix->map(function ($b) {
                            return [
                                'id' => $b->id,
                                'classification' => $b->classification,
                                'company_code' => $b->CompanyName->name ?? 'عام',
                                'salePrice' => $b->salePrice
                                    ? rtrim(rtrim(number_format($b->salePrice, 2), '0'), '.') . ' دينار عراقي'
                                    : '-',
                                'cement' => $b->cement,
                                'sand' => $b->sand,
                                'gravel' => $b->gravel,
                                'water' => $b->water,
                                'chemicals' => $b->chemicals->map(function ($c) {
                                        return $c->name . ' = ' . $c->pivot->quantity . '<br/>';
                                    })->implode(','),
                                'notes' => str_replace('•', '<br>•', $b->notes ?? ''),
                            ];
                        }),
                    ) !!};

                    // حفظ البيانات واستخراج الشركات
                    this.allData = tableData;
                    this.companies = [...new Set(tableData.map(b => b.company_code))].filter(c => c);

                    const rows = tableData.map(b => [
                        b.classification,
                        b.company_code,
                        b.salePrice,
                        b.cement,
                        b.sand,
                        b.gravel,
                        b.water,
                        b.chemicals,
                        b.notes,
                        b.id
                    ]);

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'التصنيف',
                                'الشركة',
                                'سعر المتر',
                                'الأسمنت (أكياس)',
                                'الرمل (م³)',
                                'الحصى (م³)',
                                'الماء (لتر)',
                                'المادة الكيميائية',
                                'ملاحظات',
                                'تعديل',
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                            select: 9,
                            sortable: false,
                            className: 'text-center',
                            render: (data) => {
                                const id = data;
                                const url =
                                    `/ConcreteERP/materials/${id}&EditGeneralConcreteMix/edit`;
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
                        }],
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

                filterTable() {
                    let filtered = this.allData;

                    // فلتر الشركة
                    if (this.companyFilter) {
                        filtered = filtered.filter(b => b.company_code === this.companyFilter);
                    }

                    // تحديث الجدول
                    const rows = filtered.map(b => [
                        b.classification,
                        b.company_code,
                        b.salePrice,
                        b.cement,
                        b.sand,
                        b.gravel,
                        b.water,
                        b.chemicals,
                        b.notes,
                        b.id
                    ]);

                    this.datatable2.destroy();
                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: [
                                'التصنيف',
                                'الشركة',
                                'سعر المتر',
                                'الأسمنت (أكياس)',
                                'الرمل (م³)',
                                'الحصى (م³)',
                                'الماء (لتر)',
                                'المادة الكيميائية',
                                'ملاحظات',
                                'تعديل',
                            ],
                            data: rows,
                        },
                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],
                        columns: [{
                            select: 9,
                            sortable: false,
                            className: 'text-center',
                            render: (data) => {
                                const id = data;
                                const url =
                                    `/ConcreteERP/materials/${id}&EditGeneralConcreteMix/edit`;
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
                        }],
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

                resetFilters() {
                    this.companyFilter = '';
                    this.filterTable();
                },
            }));
        });
    </script>

@endsection
