@extends('layouts.app')

@section('title', 'تتبع الشحنة')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: calc(100vh - 200px);
            min-height: 500px;
        }

        .tracking-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            min-width: 250px;
        }

        .status-badge {
            font-size: 1.1em;
            padding: 8px 15px;
        }

        .refresh-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-0 position-relative">
        <!-- رأس الصفحة -->
        <div class="bg-dark text-white p-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-satellite-dish me-2"></i>
                    تتبع الشحنة #{{ $shipment->shipment_number }}
                </h5>
                <small class="text-muted">{{ $shipment->job->job_number }} - {{ $shipment->job->customer_name }}</small>
            </div>
            <div>
                <span class="badge status-badge bg-{{ $shipment->status_badge }}">
                    {{ $shipment->status_label }}
                </span>
                <a href="{{ route('shipments.show', $shipment) }}" class="btn btn-outline-light btn-sm ms-2">
                    <i class="fas fa-info-circle"></i> التفاصيل
                </a>
            </div>
        </div>

        <!-- الخريطة -->
        <div id="map"></div>

        <!-- زر التحديث -->
        <button class="btn btn-primary refresh-btn" onclick="refreshLocation()">
            <i class="fas fa-sync-alt"></i> تحديث
        </button>

        <!-- معلومات التتبع -->
        <div class="tracking-info">
            <h6 class="mb-3">
                <i class="fas fa-info-circle text-primary me-2"></i>
                معلومات الموقع
            </h6>

            <div id="locationInfo">
                @if ($currentLocation)
                    <div class="mb-2">
                        <small class="text-muted">آخر تحديث</small>
                        <br>
                        <strong id="lastUpdate">{{ $currentLocation['time'] ?? 'غير متوفر' }}</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">السرعة</small>
                        <br>
                        <strong id="speed">{{ $currentLocation['speed'] ?? 0 }} كم/س</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">الإحداثيات</small>
                        <br>
                        <small id="coords">{{ $currentLocation['lat'] }}, {{ $currentLocation['lng'] }}</small>
                    </div>
                @else
                    <p class="text-muted mb-0">
                        <i class="fas fa-exclamation-circle"></i>
                        لا تتوفر بيانات الموقع حالياً
                    </p>
                @endif
            </div>

            <hr>

            <div class="d-flex justify-content-between">
                <div class="text-center">
                    <i class="fas fa-road fa-lg text-info"></i>
                    <br>
                    <small id="distance">{{ number_format($tripReport['total_distance'] ?? 0, 1) }} كم</small>
                </div>
                <div class="text-center">
                    <i class="fas fa-clock fa-lg text-warning"></i>
                    <br>
                    <small id="duration">{{ $tripReport['duration'] ?? '-' }}</small>
                </div>
                <div class="text-center">
                    <i class="fas fa-map-pin fa-lg text-success"></i>
                    <br>
                    <small id="points">{{ $tripReport['location_points'] ?? 0 }} نقطة</small>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // إعدادات الخريطة
        var map = L.map('map');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var marker = null;
        var trackLine = null;

        // إحداثيات نقطة البداية (المصنع)
        var factoryLat = {{ $factory['lat'] ?? 24.7136 }};
        var factoryLng = {{ $factory['lng'] ?? 46.6753 }};

        // إحداثيات الوجهة
        var destLat = {{ $shipment->job->location_latitude ?? ($factory['lat'] ?? 24.7136) }};
        var destLng = {{ $shipment->job->location_longitude ?? ($factory['lng'] ?? 46.6753) }};

        // علامة المصنع
        var factoryIcon = L.divIcon({
            html: '<i class="fas fa-industry fa-2x text-secondary"></i>',
            className: 'factory-marker',
            iconSize: [30, 30]
        });
        L.marker([factoryLat, factoryLng], {
            icon: factoryIcon
        }).addTo(map).bindPopup('المصنع');

        // علامة الوجهة
        var destIcon = L.divIcon({
            html: '<i class="fas fa-flag-checkered fa-2x text-danger"></i>',
            className: 'dest-marker',
            iconSize: [30, 30]
        });
        L.marker([destLat, destLng], {
            icon: destIcon
        }).addTo(map).bindPopup('الوجهة: ' + '{{ $shipment->job->location_address ?? '' }}');

        @if ($currentLocation)
            // الموقع الحالي
            var currentLat = {{ $currentLocation['lat'] }};
            var currentLng = {{ $currentLocation['lng'] }};

            var truckIcon = L.divIcon({
                html: '<i class="fas fa-truck fa-2x text-primary"></i>',
                className: 'truck-marker',
                iconSize: [40, 40]
            });
            marker = L.marker([currentLat, currentLng], {
                icon: truckIcon
            }).addTo(map);
            marker.bindPopup('الشحنة #{{ $shipment->shipment_number }}').openPopup();

            map.setView([currentLat, currentLng], 14);
        @else
            map.setView([factoryLat, factoryLng], 12);
        @endif

        // رسم مسار الرحلة
        @if (isset($track) && count($track) > 0)
            var trackPoints = [
                @foreach ($track as $point)
                    [{{ $point->latitude }}, {{ $point->longitude }}],
                @endforeach
            ];
            trackLine = L.polyline(trackPoints, {
                color: 'blue',
                weight: 3,
                opacity: 0.7
            }).addTo(map);
            map.fitBounds(trackLine.getBounds().pad(0.1));
        @endif

        // تحديث الموقع
        function refreshLocation() {
            fetch('{{ route('shipments.location', $shipment) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.location) {
                        var lat = data.location.lat;
                        var lng = data.location.lng;

                        if (marker) {
                            marker.setLatLng([lat, lng]);
                        } else {
                            var truckIcon = L.divIcon({
                                html: '<i class="fas fa-truck fa-2x text-primary"></i>',
                                className: 'truck-marker',
                                iconSize: [40, 40]
                            });
                            marker = L.marker([lat, lng], {
                                icon: truckIcon
                            }).addTo(map);
                        }

                        map.panTo([lat, lng]);

                        // تحديث المعلومات
                        document.getElementById('lastUpdate').textContent = data.location.time;
                        document.getElementById('speed').textContent = data.location.speed + ' كم/س';
                        document.getElementById('coords').textContent = lat + ', ' + lng;
                    }
                });
        }

        // تحديث تلقائي كل 30 ثانية
        @if ($shipment->is_active)
            setInterval(refreshLocation, 30000);
        @endif
    </script>
@endpush
