@extends('layouts.app')

@section('page-title', 'عرض وادارة خلطات الخرسانة')

@section('content')
    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light">
                خلطات الخرسانة
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

        .price-cell {
            font-weight: 600;
            color: #059669;
        }
    </style>
    <script>
        const baseUrl = '{{ url('/') }}';
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
                                    'costPrice' => $b->costPrice ? number_format($b->costPrice, 0, '.', ',') : '-',
                                    'branchName' => $b->branchName->branch_name ?? 'الاستندر العام',
                                ],
                                $prices,
                            );
                        }),
                    ) !!};

                    // بناء رؤوس الأعمدة
                    const headings = ['التصنيف', 'سعر التكلفة'];

                    // إضافة أعمدة الفئات
                    categories.forEach(cat => {
                        headings.push('سعر ' + cat.name);
                    });

                    headings.push('الفرع', 'تعديل', 'عرض تفاصيل');

                    // بناء الصفوف
                    const rows = tableData.map(b => {
                        const row = [b.classification, b.costPrice];

                        // إضافة أسعار الفئات
                        categories.forEach(cat => {
                            row.push(b['cat_' + cat.id + '_price']);
                        });

                        row.push(b.branchName, b.id, b.id);
                        return row;
                    });

                    // حساب مواقع أعمدة التعديل والتفاصيل
                    const editColumnIndex = headings.length - 2;
                    const detailsColumnIndex = headings.length - 1;

                    // إعداد أعمدة الأسعار للتنسيق
                    const priceColumns = [];
                    for (let i = 1; i <= categories.length + 1; i++) {
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
                        perPage: 15,
                        perPageSelect: [15, 20, 30, 50, 100],

                        columns: [
                            ...priceColumns,
                            {
                                select: editColumnIndex,
                                sortable: false,
                                className: 'text-center',
                                render: (data) => {
                                    const id = data;
                                    const url =
                                        `${baseUrl}/warehouse/${id}&EditQuantitiesConcreteMix/edit`;
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
                                        `${baseUrl}/warehouse/${id}&ViewQuantitiesConcreteMix/edit`;
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

        // تم حذف الفلاتر المخصصة (بحث الجدول الافتراضي يكفي)
    </script>


@endsection
