@extends('layouts.app')

@section('page-title', 'إضافة طلب مباشر')

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">

            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    ⚡ إضافة طلب مباشر (موافقة تلقائية)
                </h5>
                <span class="badge bg-success text-white px-3 py-1">
                    سيتم الموافقة تلقائياً
                </span>
            </div>

            <div class="mb-4 p-3 bg-blue-100 dark:bg-blue-900 rounded-lg text-blue-800 dark:text-blue-200">
                <strong>ملاحظة:</strong> هذا الطلب سيتم تحويله مباشرة إلى حالة "قيد العمل" بدون الحاجة لمراجعة.
            </div>

            {!! Form::open([
                'route' => ['companyBranch.store'],
                'method' => 'POST',
                'autocomplete' => 'off',
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- نوع الخلطة الخرسانية --}}
                <div class="space-y-3">
                    <label for="classification_id" class="inline-flex cursor-pointer">
                        <span class="text-white-dark">
                            نوع الخلطة الخرسانية <span class="text-danger">*</span>
                        </span>
                    </label>
                    <select id="classification_id" name="classification_id" class="form-select" required>
                        <option value="">-- اختر نوع الخلطة --</option>
                        @foreach ($ConcreteMixes as $mix)
                            <option value="{{ $mix->id }}">{{ $mix->classification }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- الكمية المطلوبة --}}
                <div class="space-y-3">
                    <label for="quantity" class="inline-flex cursor-pointer">
                        <span class="text-white-dark">
                            الكمية المطلوبة م³ <span class="text-danger">*</span>
                        </span>
                    </label>
                    <input id="quantity" type="number" name="quantity" class="form-input"
                        placeholder="أدخل الكمية المطلوبة" step="1" min="1" required>
                </div>

                {{-- الفئات السعرية (للعرض فقط كمرجع) --}}
                <div class="space-y-3 col-span-2" id="pricing-categories-container" style="display: none;">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">
                            📋 الفئات السعرية المتاحة (للاطلاع)
                        </span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3" id="pricing-categories-cards">
                        {{-- يتم ملؤه ديناميكياً عبر JavaScript --}}
                    </div>
                </div>

                {{-- سعر المتر المكعب (قابل للتعديل) --}}
                <div class="space-y-3" id="unit-price-container" style="display: none;">
                    <label for="unit_price" class="inline-flex cursor-pointer">
                        <span class="text-white-dark">
                            💰 سعر المتر المكعب <span class="text-danger">*</span>
                        </span>
                    </label>
                    <div class="relative">
                        <input id="unit_price_display" type="text" class="form-input text-lg font-bold" placeholder="0"
                            autocomplete="off" required>
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none">د.ع</span>
                    </div>
                    <input type="hidden" id="unit_price" name="unit_price" value="0">
                    <small class="text-gray-500">يتم تعبئته تلقائياً ويمكنك تعديله حسب الحاجة</small>
                </div>

                {{-- الإجمالي --}}
                <div class="space-y-3" id="total-price-container" style="display: none;">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">📊 الإجمالي</span>
                    </label>
                    <div
                        class="p-4 bg-green-50 dark:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-700">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">الإجمالي المتوقع:</span>
                            <span id="total-price-display" class="text-2xl font-bold text-success">0</span>
                        </div>
                        <div class="flex items-center justify-between mt-2 text-sm text-gray-400">
                            <span>سعر المتر: <span id="summary-unit-price">0</span></span>
                            <span>× الكمية: <span id="summary-quantity">0</span> م³</span>
                        </div>
                    </div>
                </div>

                {{-- اسم العميل --}}
                <div class="space-y-3">
                    <label for="customer_name" class="inline-flex cursor-pointer">
                        <span class="text-white-dark">
                            اسم العميل <span class="text-danger">*</span>
                        </span>
                    </label>
                    <input id="customer_name" type="text" name="customer_name" class="form-input"
                        placeholder="أدخل اسم العميل" required>
                </div>

                {{-- رقم هاتف العميل --}}
                <div class="space-y-3">
                    <label for="customer_phone" class="inline-flex cursor-pointer">
                        <span class="text-white-dark">
                            رقم هاتف العميل <span class="text-danger">*</span>
                        </span>
                    </label>
                    <input id="customer_phone" type="tel" name="customer_phone" class="form-input"
                        placeholder="07xxxxxxxxx" pattern="\d{11}" maxlength="11" minlength="11" required>
                    <small class="text-gray-500">يجب أن يكون 11 رقم</small>
                </div>

                {{-- موقع الصب --}}
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">موقع الصب <span class="text-danger">*</span></span>
                    </label>
                    <input type="text" name="location" class="form-input"
                        placeholder="أدخل موقع الصب (مثال: حي الكرادة - شارع أبو نؤاس)" required>
                </div>

                {{-- رابط الموقع على خرائط Google --}}
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">
                            <svg class="inline w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                            </svg>
                            رابط خرائط Google (اختياري)
                        </span>
                    </label>
                    <div class="flex gap-2">
                        <input type="url" name="location_map_url" id="location_map_url" class="form-input flex-1"
                            placeholder="https://www.google.com/maps/@33.3218825,44.4197323,17z"
                            pattern="https?://.*google.*maps.*|https?://maps\.app\.goo\.gl/.*|https?://goo\.gl/maps/.*">
                        <button type="button" onclick="getCurrentLocation()" class="btn btn-outline-info"
                            title="تحديد موقعي الحالي">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                    <small class="text-gray-500">
                        يمكنك لصق رابط من خرائط Google أو الضغط على زر تحديد الموقع
                    </small>
                </div>

                {{-- حقول الإحداثيات المخفية --}}
                <input type="hidden" name="location_lat" id="location_lat">
                <input type="hidden" name="location_lng" id="location_lng">

                {{-- خريطة المعاينة --}}
                <div class="space-y-3 col-span-2" id="map-preview-container" style="display: none;">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">معاينة الموقع</span>
                    </label>
                    <div id="map-preview" class="w-full h-64 rounded-lg border border-gray-300 overflow-hidden"></div>
                </div>

                {{-- تاريخ التنفيذ --}}
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">تاريخ التنفيذ <span class="text-danger">*</span></span>
                    </label>
                    <input type="date" name="execution_date" class="form-input" value="{{ date('Y-m-d') }}"
                        min="{{ date('Y-m-d') }}" required>
                </div>

                {{-- وقت التنفيذ --}}
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">وقت التنفيذ <span class="text-danger">*</span></span>
                    </label>
                    <input type="time" name="execution_time" class="form-input" required>
                </div>

                {{-- ملاحظات --}}
                <div class="space-y-3 col-span-2">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات إضافية</span>
                    </label>
                    <textarea name="note" class="form-input" rows="3" placeholder="اكتب ملاحظاتك هنا"></textarea>
                </div>

                {{-- زر الإرسال --}}
                <div class="flex items-center justify-end gap-2 mt-8 border-t pt-4 col-span-2">
                    <a href="/ConcreteERP/companyBranch/ordersInProgress"
                        class="btn btn-outline-danger btn-sm px-4 py-1.5">
                        إلغاء
                    </a>
                    <button type="submit" name="active" value="DirectRequest"
                        class="btn btn-success btn-sm px-4 py-1.5">
                        ⚡ إضافة الطلب المباشر
                    </button>
                </div>

            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <script>
        // بيانات الفئات السعرية لكل خلطة
        const mixPricingData = {!! json_encode(
            $ConcreteMixes->mapWithKeys(function ($mix) {
                return [
                    $mix->id => [
                        'classification' => $mix->classification,
                        'salePrice' => $mix->salePrice,
                        'categoryPrices' => $mix->categoryPrices->map(function ($cp) {
                                return [
                                    'pricing_category_id' => $cp->pricing_category_id,
                                    'category_name' => $cp->pricingCategory ? $cp->pricingCategory->name : 'غير محدد',
                                    'category_description' => $cp->pricingCategory ? $cp->pricingCategory->description : '',
                                    'price_per_meter' => $cp->price_per_meter,
                                ];
                            })->values(),
                    ],
                ];
            }),
        ) !!};

        // عند تغيير نوع الخلطة
        document.getElementById('classification_id').addEventListener('change', function() {
            const mixId = this.value;
            const container = document.getElementById('pricing-categories-container');
            const cardsDiv = document.getElementById('pricing-categories-cards');
            const priceContainer = document.getElementById('unit-price-container');
            const totalContainer = document.getElementById('total-price-container');
            const priceInput = document.getElementById('unit_price');

            if (!mixId || !mixPricingData[mixId]) {
                container.style.display = 'none';
                priceContainer.style.display = 'none';
                totalContainer.style.display = 'none';
                priceInput.value = '';
                return;
            }

            const data = mixPricingData[mixId];
            priceContainer.style.display = 'block';
            totalContainer.style.display = 'block';

            // عرض بطاقات الفئات السعرية كمرجع
            cardsDiv.innerHTML = '';
            if (data.categoryPrices.length > 0) {
                container.style.display = 'block';
                data.categoryPrices.forEach(function(cp) {
                    const card = document.createElement('div');
                    card.className =
                        'p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 cursor-pointer hover:border-primary hover:shadow-md transition-all';
                    card.innerHTML = `
                        <div class="text-sm font-bold text-primary mb-1">${cp.category_name}</div>
                        <div class="text-lg font-bold text-success">${parseFloat(cp.price_per_meter).toLocaleString()}</div>
                        <div class="text-xs text-gray-400">${cp.category_description || 'سعر المتر المكعب'}</div>
                    `;
                    // عند النقر على البطاقة يتم نسخ السعر لحقل السعر
                    card.addEventListener('click', function() {
                        setPriceValue(cp.price_per_meter);
                        // تمييز البطاقة المختارة
                        cardsDiv.querySelectorAll('div.border-primary').forEach(c => {
                            c.classList.remove('border-primary', 'shadow-md',
                                'bg-primary/5');
                            c.classList.add('border-gray-200', 'dark:border-gray-700');
                        });
                        this.classList.remove('border-gray-200', 'dark:border-gray-700');
                        this.classList.add('border-primary', 'shadow-md', 'bg-primary/5');
                    });
                    cardsDiv.appendChild(card);
                });

                // تعيين سعر أول فئة كقيمة افتراضية
                setPriceValue(data.categoryPrices[0].price_per_meter);
            } else {
                container.style.display = 'none';
                // استخدام سعر البيع الافتراضي
                setPriceValue(data.salePrice || 0);
            }

            updateTotal();
        });

        // تنسيق الأرقام
        function formatNumber(num) {
            if (!num && num !== 0) return '';
            return parseFloat(num).toLocaleString('en-US');
        }

        function unformatNumber(str) {
            if (!str) return 0;
            return parseFloat(str.replace(/,/g, '')) || 0;
        }

        // تعيين قيمة السعر (من الكود)
        function setPriceValue(value) {
            const displayInput = document.getElementById('unit_price_display');
            const hiddenInput = document.getElementById('unit_price');
            const numVal = parseFloat(value) || 0;
            hiddenInput.value = numVal;
            displayInput.value = numVal > 0 ? formatNumber(numVal) : '';
            updateTotal();
        }

        // عند الكتابة في حقل السعر
        document.getElementById('unit_price_display').addEventListener('input', function() {
            const cursorPos = this.selectionStart;
            const oldLen = this.value.length;
            const rawValue = unformatNumber(this.value);
            document.getElementById('unit_price').value = rawValue;
            if (rawValue > 0) {
                this.value = formatNumber(rawValue);
                const newLen = this.value.length;
                this.setSelectionRange(cursorPos + (newLen - oldLen), cursorPos + (newLen - oldLen));
            }
            updateTotal();
        });

        document.getElementById('quantity').addEventListener('input', updateTotal);

        function updateTotal() {
            const price = parseFloat(document.getElementById('unit_price').value) || 0;
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const total = price * quantity;

            document.getElementById('total-price-display').textContent = formatNumber(total.toFixed(2)) + ' د.ع';
            document.getElementById('summary-unit-price').textContent = formatNumber(price);
            document.getElementById('summary-quantity').textContent = quantity;
        }

        // استخراج الإحداثيات من رابط Google Maps
        function extractCoordinates(url) {
            // Pattern 1: @lat,lng,zoom
            let match = url.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*)/);
            if (match) {
                return {
                    lat: parseFloat(match[1]),
                    lng: parseFloat(match[2])
                };
            }

            // Pattern 2: ?q=lat,lng
            match = url.match(/[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
            if (match) {
                return {
                    lat: parseFloat(match[1]),
                    lng: parseFloat(match[2])
                };
            }

            // Pattern 3: /place/lat,lng
            match = url.match(/place\/(-?\d+\.?\d*),(-?\d+\.?\d*)/);
            if (match) {
                return {
                    lat: parseFloat(match[1]),
                    lng: parseFloat(match[2])
                };
            }

            return null;
        }

        // تحديث معاينة الخريطة
        function updateMapPreview(lat, lng) {
            const container = document.getElementById('map-preview-container');
            const mapDiv = document.getElementById('map-preview');

            if (lat && lng) {
                container.style.display = 'block';
                // استخدام iframe لعرض الخريطة
                mapDiv.innerHTML = `
                    <iframe 
                        width="100%" 
                        height="100%" 
                        frameborder="0" 
                        style="border:0" 
                        src="https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed"
                        allowfullscreen>
                    </iframe>
                `;
            } else {
                container.style.display = 'none';
            }
        }

        // مراقبة تغيير حقل الرابط
        document.getElementById('location_map_url').addEventListener('input', function() {
            const url = this.value;
            const coords = extractCoordinates(url);

            if (coords) {
                document.getElementById('location_lat').value = coords.lat;
                document.getElementById('location_lng').value = coords.lng;
                updateMapPreview(coords.lat, coords.lng);
            } else {
                document.getElementById('location_lat').value = '';
                document.getElementById('location_lng').value = '';
                updateMapPreview(null, null);
            }
        });

        // تحديد الموقع الحالي
        function getCurrentLocation() {
            if (!navigator.geolocation) {
                alert('المتصفح لا يدعم تحديد الموقع');
                return;
            }

            // إظهار رسالة انتظار
            const btn = event.target.closest('button');
            const originalContent = btn.innerHTML;
            btn.innerHTML =
                '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            btn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // إنشاء رابط Google Maps
                    const mapUrl = `https://www.google.com/maps/@${lat},${lng},17z`;

                    document.getElementById('location_map_url').value = mapUrl;
                    document.getElementById('location_lat').value = lat;
                    document.getElementById('location_lng').value = lng;

                    updateMapPreview(lat, lng);

                    // إعادة الزر لحالته الأصلية
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                },
                function(error) {
                    let message = 'حدث خطأ في تحديد الموقع';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            message = 'تم رفض إذن تحديد الموقع';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message = 'معلومات الموقع غير متاحة';
                            break;
                        case error.TIMEOUT:
                            message = 'انتهت مهلة طلب الموقع';
                            break;
                    }
                    alert(message);

                    // إعادة الزر لحالته الأصلية
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }
    </script>

@endsection
