@extends('layouts.app')

@section('title', 'تفاصيل الشحنة')

@section('content')
    <div class="container-fluid">
        <!-- رأس الصفحة -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-truck me-2"></i>
                    الشحنة #{{ $shipment->shipment_number }}
                </h4>
                <p class="text-muted mb-0">
                    أمر العمل: <a href="{{ route('work-jobs.show', $shipment->job) }}">{{ $shipment->job->job_number }}</a>
                </p>
            </div>
            <div>
                <span class="badge bg-{{ $shipment->status_badge }} fs-6 me-2">
                    <i class="fas {{ $shipment->status_icon }}"></i>
                    {{ $shipment->status_label }}
                </span>
                @if ($shipment->is_active)
                    <a href="{{ route('shipments.tracking', $shipment) }}" class="btn btn-info">
                        <i class="fas fa-map-marker-alt"></i> تتبع مباشر
                    </a>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- المعلومات الأساسية -->
            <div class="col-lg-4">
                <!-- بطاقة الكميات -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-cubes me-2"></i>الكميات
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="text-primary">{{ number_format($shipment->planned_quantity, 1) }}</h4>
                                <small class="text-muted">مخطط (م³)</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">
                                    {{ $shipment->actual_quantity ? number_format($shipment->actual_quantity, 1) : '-' }}
                                </h4>
                                <small class="text-muted">فعلي (م³)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الآليات -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-truck-loading me-2"></i>الآليات والسائقين
                    </div>
                    <div class="card-body">
                        @if ($shipment->mixer)
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-blender text-primary me-2"></i>الخلاطة</span>
                                <span>{{ $shipment->mixer->plate_number }}</span>
                            </div>
                            @if ($shipment->mixerDriver)
                                <div class="d-flex justify-content-between mb-3 ps-4">
                                    <small class="text-muted">السائق</small>
                                    <small>{{ $shipment->mixerDriver->name }}</small>
                                </div>
                            @endif
                        @endif

                        @if ($shipment->truck)
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-truck text-secondary me-2"></i>اللوري</span>
                                <span>{{ $shipment->truck->plate_number }}</span>
                            </div>
                            @if ($shipment->truckDriver)
                                <div class="d-flex justify-content-between mb-3 ps-4">
                                    <small class="text-muted">السائق</small>
                                    <small>{{ $shipment->truckDriver->name }}</small>
                                </div>
                            @endif
                        @endif

                        @if ($shipment->pump)
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="fas fa-water text-info me-2"></i>المضخة</span>
                                <span>{{ $shipment->pump->plate_number }}</span>
                            </div>
                            @if ($shipment->pumpDriver)
                                <div class="d-flex justify-content-between ps-4">
                                    <small class="text-muted">السائق</small>
                                    <small>{{ $shipment->pumpDriver->name }}</small>
                                </div>
                            @endif
                        @endif

                        @if (!$shipment->mixer && !$shipment->truck && !$shipment->pump)
                            <p class="text-muted text-center mb-0">لم يتم تعيين آليات</p>
                        @endif
                    </div>
                </div>

                <!-- الأوقات -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-clock me-2"></i>الأوقات
                    </div>
                    <div class="card-body">
                        <div class="timeline-simple">
                            <div class="d-flex justify-content-between mb-2">
                                <span>الانطلاق</span>
                                <span>{{ $shipment->departure_time ? $shipment->departure_time->format('H:i') : '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>الوصول</span>
                                <span>{{ $shipment->arrival_time ? $shipment->arrival_time->format('H:i') : '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>بدء العمل</span>
                                <span>{{ $shipment->work_start_time ? $shipment->work_start_time->format('H:i') : '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>انتهاء العمل</span>
                                <span>{{ $shipment->work_end_time ? $shipment->work_end_time->format('H:i') : '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>العودة</span>
                                <span>{{ $shipment->return_time ? $shipment->return_time->format('H:i') : '-' }}</span>
                            </div>
                        </div>

                        @if ($shipment->work_duration)
                            <hr>
                            <div class="text-center">
                                <strong>مدة العمل:</strong> {{ $shipment->work_duration }} دقيقة
                            </div>
                        @endif
                    </div>
                </div>

                <!-- تقرير الرحلة -->
                @if ($tripReport)
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-road me-2"></i>تقرير الرحلة
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>المسافة الكلية</span>
                                <span>{{ $tripReport['total_distance'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>متوسط السرعة</span>
                                <span>{{ $tripReport['average_speed'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>نقاط التتبع</span>
                                <span>{{ $tripReport['location_points'] }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- الخط الزمني والخريطة -->
            <div class="col-lg-8">
                <!-- أزرار الإجراءات -->
                @if ($shipment->status != 'cancelled' && $shipment->status != 'returned')
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                @if ($shipment->can_depart)
                                    <form action="{{ route('shipments.depart', $shipment) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-truck"></i> تسجيل الانطلاق
                                        </button>
                                    </form>
                                @endif

                                @if ($shipment->can_arrive)
                                    <form action="{{ route('shipments.arrive', $shipment) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-map-marker-alt"></i> تسجيل الوصول
                                        </button>
                                    </form>
                                @endif

                                @if ($shipment->can_start_work)
                                    <form action="{{ route('shipments.start-work', $shipment) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-play"></i> بدء العمل
                                        </button>
                                    </form>
                                @endif

                                @if ($shipment->can_end_work)
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#endWorkModal">
                                        <i class="fas fa-stop"></i> انتهاء العمل
                                    </button>
                                @endif

                                @if ($shipment->can_return)
                                    <form action="{{ route('shipments.return', $shipment) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-dark">
                                            <i class="fas fa-home"></i> تسجيل العودة
                                        </button>
                                    </form>
                                @endif

                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#cancelModal">
                                    <i class="fas fa-times"></i> إلغاء
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- سجل الأحداث -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-history me-2"></i>سجل الأحداث
                    </div>
                    <div class="card-body">
                        @if ($shipment->events->count() > 0)
                            <div class="timeline">
                                @foreach ($shipment->events->sortByDesc('recorded_at') as $event)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-{{ $event->type_color }}">
                                            <i class="fas {{ $event->type_icon }}"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <strong>{{ $event->type_label }}</strong>
                                                <small
                                                    class="text-muted">{{ $event->recorded_at->format('H:i:s') }}</small>
                                            </div>
                                            @if ($event->description)
                                                <p class="mb-0 text-muted">{{ $event->description }}</p>
                                            @endif
                                            @if ($event->has_location)
                                                <small class="text-info">
                                                    <i class="fas fa-map-pin"></i>
                                                    {{ $event->latitude }}, {{ $event->longitude }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">لا توجد أحداث مسجلة</p>
                        @endif
                    </div>
                </div>

                <!-- الموقع الحالي -->
                @if ($currentLocation)
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-map-marker-alt me-2"></i>الموقع الحالي
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 text-center">
                                    <h5>{{ $currentLocation['lat'] }}, {{ $currentLocation['lng'] }}</h5>
                                    <small class="text-muted">الإحداثيات</small>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h5>{{ $currentLocation['speed'] ?? '-' }} كم/س</h5>
                                    <small class="text-muted">السرعة</small>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h5>{{ $currentLocation['minutes_ago'] }} دقيقة</h5>
                                    <small class="text-muted">منذ آخر تحديث</small>
                                </div>
                            </div>
                            <div id="map" style="height: 300px; border-radius: 10px;"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal انتهاء العمل -->
    <div class="modal fade" id="endWorkModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('shipments.end-work', $shipment) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">تسجيل انتهاء العمل</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الكمية الفعلية المنفذة (م³) <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="actual_quantity" class="form-control" step="0.1"
                                value="{{ $shipment->planned_quantity }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات السائق</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تأكيد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal الإلغاء -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('shipments.cancel', $shipment) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إلغاء الشحنة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required minlength="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                        <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($currentLocation)
        @push('scripts')
            <script>
                // إعداد الخريطة
                var map = L.map('map').setView([{{ $currentLocation['lat'] }}, {{ $currentLocation['lng'] }}], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                L.marker([{{ $currentLocation['lat'] }}, {{ $currentLocation['lng'] }}]).addTo(map);
            </script>
        @endpush
    @endif

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
            border-left: 2px solid #dee2e6;
            padding-left: 20px;
            margin-left: 10px;
        }

        .timeline-marker {
            position: absolute;
            left: -31px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
        }
    </style>
@endsection
