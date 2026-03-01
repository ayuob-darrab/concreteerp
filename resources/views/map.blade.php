@extends('layouts.app')
@if ($addresCompany == true)
@section('page-title', 'اضافة موقة شركة:  '.$Company->name)
@else
@section('page-title', 'اضافة موقة الفرع : '.$Branch->branch_name)
@endif
@section('content')

    {{-- استدعاء مكتبة Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">


            <div class="space-y-3" id="map" style="width: 75%; height: 400px; border-radius: 10px;"></div>

        </div>
    </div>

    {{-- معلومات الشركة والإحداثيات --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1 mt-6">
        <div class="panel h-full w-full">
                        <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">التفاصيل</h5>
            </div>

            @if ($addresCompany == true)
                {!! Form::open(['route' => ['companies.update', $Company->code], 'method' => 'PUT', 'autocomplete' => 'off']) !!}
            @else
                {!! Form::open(['route' => ['companyBranch.update', $Branch->id], 'method' => 'PUT', 'autocomplete' => 'off']) !!}
            @endif
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                {{-- معلومات الشركة --}}
                @if ($addresCompany == true)
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">اسم الشركة <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="companyName" readonly value="{{ $Company->name }}" class="form-input" required>

                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">كود الشركة <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="companyCode" readonly value="{{ $Company->code }}" class="form-input" required>
                    </div>
                @else
                    <div class="space-y-3">
                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">اسم الفرع <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="branch_name" readonly value="{{ $Branch->branch_name }}" class="form-input" required>

                        <label class="inline-flex cursor-pointer">
                            <span class="text-white-dark">معرف الفرع <span class="text-danger">*</span></span>
                        </label>
                        <input type="text" name="id" readonly value="{{ $Branch->id }}" class="form-input" required>
                    </div>
                @endif

                {{-- الإحداثيات --}}
                <div class="space-y-3">
                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">خط العرض:<span class="text-danger">*</span></span>
                    </label>
                    <input type="text" id="latitude" name="latitude" class="form-input" required readonly>

                    <label class="inline-flex cursor-pointer">
                        <span class="text-white-dark">خط الطول:<span class="text-danger">*</span></span>
                    </label>
                    <input type="text" id="longitude" name="longitude" class="form-input" required readonly>
                </div>

                {{-- الأزرار --}}
                <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8 border-t pt-4 col-span-2">

                    <button type="submit" id="submitBtn" name="active" value="AddaddresCompanyOnGoogle"
                        class="btn btn-primary flex items-center gap-2 px-6 py-2" disabled>
                        <i class="fas fa-check-circle"></i> <span>اضافة الموقع</span>
                    </button>
                </div>

            </div>

            {!! Form::close() !!}

        </div>
    </div>

    {{-- كود تفعيل الخريطة --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // دالة لفحص الإحداثيات وتفعيل الزر
            function checkFields() {
                const lat = document.getElementById("latitude").value.trim();
                const lng = document.getElementById("longitude").value.trim();
                const btn = document.getElementById("submitBtn");

                btn.disabled = (lat === "" || lng === "");
            }

            // الإحداثيات الافتراضية
            var defaultLat = {{ $Company->latitude ?? 33.244201 }};
            var defaultLng = {{ $Company->longitude ?? 44.396441 }};

            // إنشاء الخريطة
            var map = L.map('map').setView([defaultLat, defaultLng], 13);

            // طبقة الخريطة
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // ماركر قابل للسحب
            var marker = L.marker([defaultLat, defaultLng], {
                draggable: true
            }).addTo(map);

            // عند سحب الماركر
            marker.on('dragend', function(e) {
                var lat = marker.getLatLng().lat.toFixed(6);
                var lng = marker.getLatLng().lng.toFixed(6);

                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;
                checkFields();
            });

            // عند الضغط على الخريطة
            map.on('click', function(e) {
                var lat = e.latlng.lat.toFixed(6);
                var lng = e.latlng.lng.toFixed(6);

                marker.setLatLng([lat, lng]);

                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;
                checkFields();
            });

            checkFields();
        });
    </script>

@endsection
