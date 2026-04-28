@extends('layouts.app')

@section('page-title', 'عرض خلطات الخرسانة')

@section('content')
    @php
        $mixesDataArray = $ConcreteMix->map(function($mix) {
            return [
                'id' => $mix->id,
                'classification' => $mix->classification,
                'cement' => $mix->cement,
                'cement_name' => $mix->cementInventory->name ?? 'سمنت',
                'sand' => $mix->sand,
                'sand_name' => $mix->sandInventory->name ?? 'رمل',
                'gravel' => $mix->gravel,
                'gravel_name' => $mix->gravelInventory->name ?? 'حصى',
                'water' => $mix->water,
                'water_name' => $mix->waterInventory->name ?? 'ماء',
                'chemicals' => $mix->chemicals->map(function($chem) {
                    return [
                        'name' => $chem->name,
                        'quantity' => $chem->pivot->quantity,
                        'unit' => $chem->unit ?? 'كغم',
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        $categoriesArray = $categories->map(function ($cat) {
            return ['id' => $cat->id, 'name' => $cat->name];
        })->values()->toArray();

        $tableDataArray = $ConcreteMix->map(function ($b) use ($categories) {
            $prices = [];
            foreach ($categories as $cat) {
                $categoryPrice = $b->categoryPrices->where('pricing_category_id', $cat->id)->first();
                $prices['cat_' . $cat->id . '_price'] = $categoryPrice
                    ? number_format($categoryPrice->price_per_meter, 0, '.', ',')
                    : '-';
            }

            $componentsHtml = '<div class="text-xs space-y-1">';
            $componentsHtml .= '<span class="component-badge bg-blue-100 text-blue-800">سمنت: ' . ($b->cement ?? 0) . '</span>';
            $componentsHtml .= '<span class="component-badge bg-amber-100 text-amber-800">رمل: ' . ($b->sand ?? 0) . '</span>';
            $componentsHtml .= '<span class="component-badge bg-gray-100 text-gray-800">حصى: ' . ($b->gravel ?? 0) . '</span>';
            $componentsHtml .= '<span class="component-badge bg-cyan-100 text-cyan-800">ماء: ' . ($b->water ?? 0) . '</span>';
            if ($b->chemicals->count() > 0) {
                $componentsHtml .= '<br><span class="component-badge bg-purple-100 text-purple-800">+' . $b->chemicals->count() . ' كيميائيات</span>';
            }
            $componentsHtml .= '</div>';

            return array_merge(
                [
                    'id' => $b->id,
                    'classification' => $b->classification,
                    'components' => $componentsHtml,
                    'branchName' => $b->branchName->branch_name ?? 'الاستندر العام',
                    'notes' => preg_replace('/\s*[,،]\s*/u', '<br>', str_replace('•', '<br>•', $b->notes ?? '')),
                ],
                $prices,
            );
        })->values()->toArray();
    @endphp

    <div x-data="multipleTable">
        <div class="panel mt-6">
            <h3 class="mb-5 text-lg font-semibold dark:text-white-light">
                خلطات الخرسانة (عرض فقط)
            </h3>

            <table id="myTable2" class="whitespace-nowrap w-full border border-gray-200">
                <caption class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">
                    خلطات الخرسانة المسجلة - المكونات والأسعار لكل متر مكعب واحد (1 م³)
                </caption>
            </table>
        </div>

        {{-- Modal تفاصيل المكونات --}}
        <div id="componentsModal" class="fixed inset-0 z-[1050] hidden overflow-y-auto bg-black/50">
            <div class="flex min-h-screen w-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl rounded-xl bg-white p-6 dark:bg-gray-800">
                    <h3 class="text-lg font-semibold mb-4">📦 مكونات الخلطة: <span id="modalMixName" class="text-primary"></span></h3>
                    <div id="componentsContent" class="space-y-4"></div>
                    <div class="flex justify-end mt-6">
                        <button type="button" onclick="closeComponentsModal()" class="btn btn-outline-secondary">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        .component-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin: 1px;
        }
    </style>

    <script>
        const mixesData = @json($mixesDataArray);

        function showComponents(mixId) {
            const mix = mixesData.find(m => m.id == mixId);
            if (!mix) return;

            document.getElementById('modalMixName').textContent = mix.classification;

            let html = `
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                    <h4 class="font-semibold mb-3 text-gray-700 dark:text-gray-300">المواد الأساسية (لكل 1 م³)</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700 text-center">
                            <div class="text-2xl mb-1">🧱</div>
                            <div class="text-xs text-gray-500">${mix.cement_name}</div>
                            <div class="font-bold text-lg text-primary">${mix.cement || 0}</div>
                            <div class="text-xs text-gray-400">كغم</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700 text-center">
                            <div class="text-2xl mb-1">🏖️</div>
                            <div class="text-xs text-gray-500">${mix.sand_name}</div>
                            <div class="font-bold text-lg text-amber-600">${mix.sand || 0}</div>
                            <div class="text-xs text-gray-400">كغم</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700 text-center">
                            <div class="text-2xl mb-1">🪨</div>
                            <div class="text-xs text-gray-500">${mix.gravel_name}</div>
                            <div class="font-bold text-lg text-gray-600">${mix.gravel || 0}</div>
                            <div class="text-xs text-gray-400">كغم</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700 text-center">
                            <div class="text-2xl mb-1">💧</div>
                            <div class="text-xs text-gray-500">${mix.water_name}</div>
                            <div class="font-bold text-lg text-blue-500">${mix.water || 0}</div>
                            <div class="text-xs text-gray-400">لتر</div>
                        </div>
                    </div>
                </div>
            `;

            if (mix.chemicals && mix.chemicals.length > 0) {
                html += `
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 mt-4">
                        <h4 class="font-semibold mb-3 text-purple-700 dark:text-purple-300">المواد الكيميائية (لكل 1 م³)</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                `;
                mix.chemicals.forEach(chem => {
                    html += `
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-purple-200 dark:border-purple-700 text-center">
                            <div class="text-xl mb-1">🧪</div>
                            <div class="text-xs text-gray-500">${chem.name}</div>
                            <div class="font-bold text-lg text-purple-600">${chem.quantity || 0}</div>
                            <div class="text-xs text-gray-400">${chem.unit}</div>
                        </div>
                    `;
                });
                html += `</div></div>`;
            }

            document.getElementById('componentsContent').innerHTML = html;
            document.getElementById('componentsModal').classList.remove('hidden');
        }

        function closeComponentsModal() {
            document.getElementById('componentsModal').classList.add('hidden');
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('multipleTable', () => ({
                datatable2: null,

                init() {
                    const categories = @json($categoriesArray);
                    const tableData = @json($tableDataArray);

                    const headings = ['التصنيف', 'المكونات (1 م³)'];
                    categories.forEach(cat => {
                        headings.push('سعر ' + cat.name);
                    });
                    headings.push('الفرع', 'الملاحظات', 'تفاصيل');

                    const rows = tableData.map(b => {
                        const row = [b.classification, b.components];
                        categories.forEach(cat => {
                            row.push(b['cat_' + cat.id + '_price']);
                        });
                        row.push(
                            b.branchName,
                            b.notes,
                            `<button onclick="showComponents(${b.id})" class="btn btn-xs btn-outline-info">📦 التفاصيل</button>`
                        );
                        return row;
                    });

                    const priceColumns = [];
                    for (let i = 2; i <= categories.length + 1; i++) {
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
                        columns: [...priceColumns],
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
