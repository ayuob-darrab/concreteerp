@extends('layouts.app')

@section('page-title', 'عرض وادارة خلطات الخرسانة')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light">
                خلطات الخرسانة
            </h3>

            <!-- فلاتر البحث -->
            <div class="flex flex-wrap items-end gap-4 mb-5 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        البحث بالنوع (التصنيف)
                    </label>
                    <input type="text" id="filterClassification" class="form-input w-full"
                        placeholder="ابحث عن خلطة... (مثل C20, C25)" oninput="applyCustomFilters()">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        تصفية بالفرع
                    </label>
                    <select id="filterBranch" class="form-select w-full" onchange="applyCustomFilters()">
                        <option value="">كل الفروع</option>
                        @foreach ($ConcreteMix->pluck('branchName')->unique('id')->filter() as $branch)
                            <option value="{{ $branch->branch_name }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="button" onclick="clearCustomFilters()" class="btn btn-outline-secondary">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        مسح الفلاتر
                    </button>
                </div>
                <div class="text-sm text-gray-500">
                    <span id="filteredCount">{{ $ConcreteMix->count() }}</span> / {{ $ConcreteMix->count() }} خلطة
                </div>
            </div>

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

        .price-cell {
            font-weight: 600;
            color: #059669;
        }
    </style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('multipleTable', () => ({
                datatable2: null,

                init() {
                    // الفئات السعرية
                    const categories = {!! json_encode(
                        $categories->map(function ($cat) {
                            return ['id' => $cat->id, 'name' => $cat->name];
                        }),
                    ) !!};

                    const tableData = {!! json_encode(
                        $ConcreteMix->map(function ($b) use ($categories) {
                            $prices = [];
                            foreach ($categories as $cat) {
                                $categoryPrice = $b->categoryPrices->where('pricing_category_id', $cat->id)->first();
                                $prices['cat_' . $cat->id . '_price'] = $categoryPrice
                                    ? number_format($categoryPrice->price_per_meter, 0, '.', ',')
                                    : '-';
                            }
                            return array_merge(
                                [
                                    'id' => $b->id,
                                    'classification' => $b->classification,
                                    'branchName' => $b->branchName->branch_name ?? 'الاستندر العام',
                                    'notes' => $b->notes,
                                ],
                                $prices,
                            );
                        }),
                    ) !!};

                    // بناء رؤوس الأعمدة
                    const headings = ['التصنيف'];

                    // إضافة أعمدة الفئات
                    categories.forEach(cat => {
                        headings.push('سعر ' + cat.name);
                    });

                    headings.push('الفرع', 'ملاحظات', 'تعديل', 'عرض تفاصيل');

                    // بناء الصفوف
                    const rows = tableData.map(b => {
                        const row = [b.classification];

                        // إضافة أسعار الفئات
                        categories.forEach(cat => {
                            row.push(b['cat_' + cat.id + '_price']);
                        });

                        row.push(b.branchName, b.notes, b.id, b.id);
                        return row;
                    });

                    // حساب مواقع أعمدة التعديل والتفاصيل
                    const editColumnIndex = headings.length - 2;
                    const detailsColumnIndex = headings.length - 1;

                    // إعداد أعمدة الأسعار للتنسيق
                    const priceColumns = [];
                    for (let i = 1; i <= categories.length; i++) {
                        priceColumns.push({
                            select: i,
                            className: 'price-cell',
                        });
                    }

                    this.datatable2 = new simpleDatatables.DataTable('#myTable2', {
                        data: {
                            headings: headings,
                            data: rows,
                        },

                        searchable: true,
                        perPage: 10,
                        perPageSelect: [10, 20, 30, 50, 100],

                        columns: [
                            ...priceColumns,
                            {
                                select: editColumnIndex,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const url =
                                        `/ConcreteERP/warehouse/${id}&EditQuantitiesConcreteMix/edit`;
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
                                select: detailsColumnIndex,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const url =
                                        `/ConcreteERP/warehouse/${id}&ViewQuantitiesConcreteMix/edit`;
                                    return `
                                    <div class="flex items-center justify-center">
                                        <a href="${url}" class="text-blue-600 hover:text-blue-800" x-tooltip="عرض تفاصيل">
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                 class="w-6 h-6 transition-transform duration-200 hover:scale-110" 
                                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
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

                    // حفظ البيانات الأصلية للفلترة
                    window.originalTableData = tableData;
                    window.datatableInstance = this.datatable2;
                    window.tableCategories = categories;
                },
            }));
        });

        // دوال الفلترة المخصصة
        function applyCustomFilters() {
            const classificationFilter = document.getElementById('filterClassification').value.toLowerCase().trim();
            const branchFilter = document.getElementById('filterBranch').value;

            const categories = window.tableCategories;
            const originalData = window.originalTableData;

            // فلترة البيانات
            const filteredData = originalData.filter(item => {
                const matchesClassification = !classificationFilter || item.classification.toLowerCase().includes(
                    classificationFilter);
                const matchesBranch = !branchFilter || item.branchName === branchFilter;
                return matchesClassification && matchesBranch;
            });

            // إعادة بناء الصفوف
            const rows = filteredData.map(b => {
                const row = [b.classification];
                categories.forEach(cat => {
                    row.push(b['cat_' + cat.id + '_price']);
                });
                row.push(b.branchName, b.notes, b.id, b.id);
                return row;
            });

            // بناء رؤوس الأعمدة
            const headings = ['التصنيف'];
            categories.forEach(cat => {
                headings.push('سعر ' + cat.name);
            });
            headings.push('الفرع', 'ملاحظات', 'تعديل', 'عرض تفاصيل');

            const editColumnIndex = headings.length - 2;
            const detailsColumnIndex = headings.length - 1;

            const priceColumns = [];
            for (let i = 1; i <= categories.length; i++) {
                priceColumns.push({
                    select: i,
                    className: 'price-cell',
                });
            }

            // تدمير الجدول القديم وإنشاء جديد
            if (window.datatableInstance) {
                window.datatableInstance.destroy();
            }

            // إعادة إنشاء عنصر الجدول
            const tableContainer = document.querySelector('.panel');
            const oldTable = document.getElementById('myTable2');
            if (oldTable) {
                const newTable = document.createElement('table');
                newTable.id = 'myTable2';
                newTable.className = 'whitespace-nowrap w-full border border-gray-200';
                oldTable.parentNode.replaceChild(newTable, oldTable);
            }

            window.datatableInstance = new simpleDatatables.DataTable('#myTable2', {
                data: {
                    headings: headings,
                    data: rows,
                },
                searchable: true,
                perPage: 10,
                perPageSelect: [10, 20, 30, 50, 100],
                columns: [
                    ...priceColumns,
                    {
                        select: editColumnIndex,
                        sortable: false,
                        className: 'text-center',
                        render: (data) => {
                            const id = data;
                            const url = `/ConcreteERP/warehouse/${id}&EditQuantitiesConcreteMix/edit`;
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
                        select: detailsColumnIndex,
                        sortable: false,
                        className: 'text-center',
                        render: (data) => {
                            const id = data;
                            const url = `/ConcreteERP/warehouse/${id}&ViewQuantitiesConcreteMix/edit`;
                            return `
                            <div class="flex items-center justify-center">
                                <a href="${url}" class="text-blue-600 hover:text-blue-800" x-tooltip="عرض تفاصيل">
                                    <svg xmlns="http://www.w3.org/2000/svg" 
                                         class="w-6 h-6 transition-transform duration-200 hover:scale-110" 
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
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

            // تحديث العداد
            document.getElementById('filteredCount').textContent = filteredData.length;
        }

        function clearCustomFilters() {
            document.getElementById('filterClassification').value = '';
            document.getElementById('filterBranch').value = '';
            applyCustomFilters();
        }
    </script>


@endsection
