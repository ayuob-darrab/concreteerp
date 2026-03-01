@extends('layouts.app')

@section('page-title', 'أسعار الخلطات حسب الفئات')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="panel mt-6">
            <!-- العنوان -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
                <h5 class="font-semibold text-lg dark:text-white-light">
                    <svg class="inline-block w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    تحديد أسعار الخلطات حسب الفئات السعرية
                </h5>
            </div>

            <!-- رسائل -->
            @if (session('success'))
                <div class="alert alert-success mb-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger mb-4">{{ session('error') }}</div>
            @endif

            <!-- فلاتر البحث -->
            @if (!$categories->isEmpty() && !$mixes->isEmpty())
                <div class="flex flex-wrap items-end gap-4 mb-5 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            البحث بالخلطة
                        </label>
                        <input type="text" id="filterMix" class="form-input w-full"
                            placeholder="ابحث عن خلطة... (مثل C20, C25)" oninput="applyFilters()">
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
                        <select id="filterBranch" class="form-select w-full" onchange="applyFilters()">
                            <option value="">كل الفروع</option>
                            @foreach ($mixes->pluck('branchName')->unique('id')->filter() as $branch)
                                <option value="{{ $branch->branch_name }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="button" onclick="clearFilters()" class="btn btn-outline-secondary">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            مسح الفلاتر
                        </button>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span id="filteredCount">{{ $mixes->count() }}</span> / {{ $mixes->count() }} خلطة
                    </div>
                </div>
            @endif

            @if ($categories->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-lg">لا توجد فئات سعرية حالياً</p>
                    <p>يرجى التواصل مع السوبر أدمن لإضافة فئات سعرية</p>
                </div>
            @elseif($mixes->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p class="text-lg">لا توجد خلطات خرسانية لشركتك</p>
                </div>
            @else
                <!-- جدول الأسعار -->
                <form action="{{ route('pricing-categories.company-prices.save') }}" method="POST" id="pricesForm">
                    @csrf

                    <div class="table-responsive">
                        <table class="table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">الخلطة</th>
                                    <th class="text-center">الفرع</th>
                                    <th class="text-center bg-warning/20">التكلفة المحسوبة</th>
                                    @foreach ($categories as $category)
                                        <th class="text-center">
                                            {{ $category->name }}
                                            @if ($category->description)
                                                <br><small
                                                    class="text-gray-500 font-normal">{{ $category->description }}</small>
                                            @endif
                                        </th>
                                    @endforeach
                                    <th class="text-center">إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mixes as $mix)
                                    <tr data-mix-id="{{ $mix->id }}">
                                        <td class="font-semibold text-center">
                                            <span class="badge bg-primary">{{ $mix->classification }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $mix->branchName->branch_name ?? '-' }}</span>
                                        </td>
                                        <td class="text-center bg-warning/10">
                                            <button type="button" onclick="showCostDetails({{ $mix->id }})"
                                                class="font-bold text-warning hover:underline cursor-pointer">
                                                {{ number_format($mix->calculated_cost, 0, '.', ',') }}
                                            </button>
                                            <br><small class="text-gray-500">دينار/م³ (اضغط للتفاصيل)</small>
                                        </td>
                                        @foreach ($categories as $category)
                                            @php
                                                $existingPrice = $prices
                                                    ->get($mix->id)
                                                    ?->firstWhere('pricing_category_id', $category->id);
                                            @endphp
                                            <td class="text-center">
                                                <div class="flex flex-col gap-1">
                                                    <input type="text" data-mix="{{ $mix->id }}"
                                                        data-category="{{ $category->id }}"
                                                        data-price-input="price_{{ $mix->id }}_{{ $category->id }}"
                                                        value="{{ $existingPrice?->price_per_meter ? number_format($existingPrice->price_per_meter, 0, '.', ',') : '' }}"
                                                        class="form-input text-center w-32 mx-auto price-input {{ $existingPrice ? 'border-green-500' : '' }}"
                                                        placeholder="سعر البيع"
                                                        oninput="formatPrice(this, 'price_{{ $mix->id }}_{{ $category->id }}')">
                                                    <input type="hidden"
                                                        id="price_{{ $mix->id }}_{{ $category->id }}"
                                                        value="{{ $existingPrice?->price_per_meter ?? '' }}">

                                                    <span id="status_{{ $mix->id }}_{{ $category->id }}"
                                                        class="text-xs {{ $existingPrice ? 'text-green-600' : 'text-gray-400' }}">
                                                        {{ $existingPrice ? '✓ تحديث' : '+ جديد' }}
                                                    </span>
                                                </div>
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            <button type="button" onclick="saveRowPrices({{ $mix->id }})"
                                                class="btn btn-sm btn-success">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                حفظ
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            حفظ جميع الأسعار
                        </button>
                    </div>
                </form>
            @endif
        </div>

        <!-- معلومات إضافية -->
        <div class="panel mt-6">
            <h6 class="font-semibold mb-3">ملاحظات:</h6>
            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1">
                <li>الفئات السعرية تمكنك من تحديد أسعار مختلفة لنفس نوع الخرسانة</li>
                <li>يمكنك ترك حقل السعر فارغاً إذا لم تكن توفر هذه الفئة لهذا النوع</li>
                <li>يتم حساب التكلفة تلقائياً بناءً على شحنات المواد</li>
                <li>الأسعار بالدينار العراقي للمتر المكعب الواحد</li>
                <li>يتم عرض الخلطات الخاصة بالفروع فقط</li>
            </ul>
        </div>
    </div>

    <!-- Cost Details Modal -->
    <div id="costDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4">
            <div class="border-b px-4 py-3 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">تفاصيل تكلفة الخلطة</h3>
                <button onclick="closeCostModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div id="costDetailsContent" class="p-4">
                <!-- سيتم تعبئتها من JavaScript -->
            </div>
            <div class="border-t px-4 py-3 text-left">
                <button onclick="closeCostModal()" class="btn btn-secondary">إغلاق</button>
            </div>
        </div>
    </div>

    <style>
        .form-input::-webkit-inner-spin-button,
        .form-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .form-input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

    <script>
        // دوال الفلاتر
        function applyFilters() {
            const mixFilter = document.getElementById('filterMix').value.toLowerCase().trim();
            const branchFilter = document.getElementById('filterBranch').value;
            const rows = document.querySelectorAll('tbody tr[data-mix-id]');
            let visibleCount = 0;

            rows.forEach(row => {
                const mixName = row.querySelector('td:first-child .badge').textContent.toLowerCase();
                const branchName = row.querySelector('td:nth-child(2) .badge').textContent;

                const matchesMix = !mixFilter || mixName.includes(mixFilter);
                const matchesBranch = !branchFilter || branchName === branchFilter;

                if (matchesMix && matchesBranch) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('filteredCount').textContent = visibleCount;
        }

        function clearFilters() {
            document.getElementById('filterMix').value = '';
            document.getElementById('filterBranch').value = '';
            applyFilters();
        }

        function formatPrice(input, hiddenId) {
            // إزالة كل شيء ما عدا الأرقام
            let value = input.value.replace(/[^0-9]/g, '');

            // تحديث الحقل المخفي بالقيمة الفعلية
            document.getElementById(hiddenId).value = value;

            // تنسيق القيمة مع الفواصل
            if (value) {
                input.value = Number(value).toLocaleString('en-US');
            } else {
                input.value = '';
            }
        }

        // حفظ أسعار صف واحد
        async function saveRowPrices(mixId) {
            const row = document.querySelector(`tr[data-mix-id="${mixId}"]`);
            const inputs = row.querySelectorAll('input[data-mix]');
            const prices = [];

            inputs.forEach(input => {
                const categoryId = input.dataset.category;
                // قراءة القيمة من الحقل المرئي وإزالة الفواصل
                const displayValue = input.value.replace(/,/g, '');
                const numericPrice = parseInt(displayValue) || 0;

                console.log(
                    `Mix: ${mixId}, Category: ${categoryId}, Display: ${input.value}, Numeric: ${numericPrice}`
                );

                if (numericPrice > 0) {
                    prices.push({
                        mix_id: mixId,
                        category_id: categoryId,
                        price: numericPrice
                    });
                }
            });

            if (prices.length === 0) {
                alert('الرجاء إدخال سعر واحد على الأقل');
                return;
            }

            try {
                const response = await fetch('{{ route('pricing-categories.company-prices.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        prices: prices
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // تحديث حالة الحقول
                    prices.forEach(p => {
                        const statusSpan = document.getElementById(`status_${p.mix_id}_${p.category_id}`);
                        const input = document.querySelector(
                            `input[data-mix="${p.mix_id}"][data-category="${p.category_id}"]`);

                        if (statusSpan) {
                            statusSpan.textContent = '✓ تم الحفظ';
                            statusSpan.className = 'text-xs text-green-600';
                        }
                        if (input) {
                            input.classList.add('border-green-500');
                        }
                    });

                    // إظهار رسالة نجاح
                    showNotification('تم حفظ الأسعار بنجاح', 'success');
                } else {
                    showNotification(data.message || 'حدث خطأ أثناء الحفظ', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('حدث خطأ في الاتصال', 'error');
            }
        }

        function showNotification(message, type) {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const notification = document.createElement('div');
            notification.className =
                `fixed top-4 left-1/2 transform -translate-x-1/2 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // عرض تفاصيل التكلفة
        async function showCostDetails(mixId) {
            const modal = document.getElementById('costDetailsModal');
            const content = document.getElementById('costDetailsContent');

            content.innerHTML =
                '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl"></i><br>جاري التحميل...</div>';
            modal.classList.remove('hidden');

            try {
                const response = await fetch(`/company-prices/cost-details/${mixId}`);
                const data = await response.json();

                if (response.ok && data.success) {
                    let html = `
                        <div class="text-center mb-4">
                            <span class="badge bg-primary text-lg px-3 py-1">${data.mix}</span>
                        </div>
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border p-2 text-right">المادة</th>
                                    <th class="border p-2 text-center">الكمية</th>
                                    <th class="border p-2 text-center">سعر الوحدة</th>
                                    <th class="border p-2 text-center">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    data.details.forEach(item => {
                        const rowClass = item.total_raw > 0 ? '' : 'text-gray-400';
                        html += `
                            <tr class="${rowClass}">
                                <td class="border p-2 text-right">${item.material}</td>
                                <td class="border p-2 text-center">${item.quantity}</td>
                                <td class="border p-2 text-center">${item.unit_cost}</td>
                                <td class="border p-2 text-center font-semibold">${item.total}</td>
                            </tr>
                        `;
                    });

                    html += `
                            </tbody>
                            <tfoot>
                                <tr class="bg-warning/20 font-bold">
                                    <td colspan="3" class="border p-2 text-right">إجمالي التكلفة</td>
                                    <td class="border p-2 text-center text-lg text-warning">${data.total_cost} دينار</td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="mt-3 p-2 bg-gray-100 rounded text-sm text-gray-600">
                            <i class="fas fa-info-circle"></i>
                            هذه التكلفة محسوبة من أسعار المواد المسجلة في المخزون
                        </div>
                    `;

                    content.innerHTML = html;
                } else {
                    content.innerHTML = '<div class="text-center text-red-500 py-4">حدث خطأ في جلب البيانات</div>';
                }
            } catch (error) {
                console.error('Error:', error);
                content.innerHTML = '<div class="text-center text-red-500 py-4">حدث خطأ في الاتصال</div>';
            }
        }

        function closeCostModal() {
            document.getElementById('costDetailsModal').classList.add('hidden');
        }

        // إغلاق Modal عند الضغط خارجها
        document.getElementById('costDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCostModal();
            }
        });
    </script>
@endsection
