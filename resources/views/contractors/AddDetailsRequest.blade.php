@extends('layouts.app')

@section('page-title', 'طلب للمادة : ' . $ConcreteMix->classification)

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">

            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    تسجيل طلب للمادة : {{ $ConcreteMix->classification }}
                </h5>
            </div>

            {!! Form::open([
                'route' => ['contractors.store'],
                'method' => 'POST',
                'autocomplete' => 'off',
            ]) !!}

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {!! Form::hidden('classification_id', $ConcreteMix->id) !!}
                {{-- الكمية المطلوبة --}}
                <div class="space-y-3">
                    <label for="quantity" class="inline-flex cursor-pointer">
                        <span class="text-white-dark">
                            الكمية المطلوبة م³ <span class="text-danger">*</span>
                        </span>
                    </label>

                    <input id="quantity" type="number" name="quantity" class="form-input"
                        placeholder="أدخل الكمية المطلوبة" step="1" min="0" required>
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

                {{-- وقت وتاريخ التسليم --}}
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">تاريخ ووقت التسليم <span class="text-danger">*</span></span>
                    </label>
                    <input type="datetime-local" name="delivery_datetime" class="form-input" required>
                </div>

                {{-- ملاحظات --}}
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">ملاحظات إضافية</span>
                    </label>
                    <textarea name="note" class="form-input" rows="3" placeholder="اكتب ملاحظاتك هنا"></textarea>
                </div>

                {{-- زر الإرسال --}}
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">
                    <button type="reset"
                        class="btn btn-outline-secondary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-times-circle"></i>
                        <span>إلغاء</span>
                    </button>

                    <button type="submit" name="active" value="AddDetailsRequest"
                        class="btn btn-primary flex items-center justify-center gap-2 px-6 py-2 w-full sm:w-auto">
                        <i class="fas fa-check-circle"></i>
                        <span>تسجيل الطلب</span>
                    </button>
                </div>

            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <script>
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
