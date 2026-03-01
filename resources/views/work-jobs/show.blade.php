@extends('layouts.app')

@section('title', 'تفاصيل أمر العمل')

@section('content')
    <div class="container-fluid">
        <!-- رأس الصفحة -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-hard-hat me-2"></i>
                    أمر العمل: {{ $workJob->job_number }}
                </h4>
                <p class="text-muted mb-0">
                    {{ $workJob->branch->name ?? '' }} |
                    {{ $workJob->scheduled_date->format('Y-m-d') }}
                    @if ($workJob->scheduled_time)
                        {{ $workJob->scheduled_time->format('H:i') }}
                    @endif
                </p>
            </div>
            <div>
                <span class="badge bg-{{ $workJob->status_badge }} fs-6 me-2">{{ $workJob->status_label }}</span>
                <div class="btn-group">
                    <a href="{{ route('work-jobs.print', $workJob) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="fas fa-print"></i>
                    </a>
                    <a href="{{ route('work-jobs.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> القائمة
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- المعلومات الأساسية -->
            <div class="col-lg-4">
                <!-- بطاقة التقدم -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <h2 class="display-4 fw-bold text-success">{{ number_format($workJob->completion_percentage, 0) }}%
                        </h2>
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar bg-success progress-bar-striped"
                                style="width: {{ $workJob->completion_percentage }}%"></div>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <h5>{{ number_format($workJob->executed_quantity, 1) }}</h5>
                                <small class="text-muted">منفذ م³</small>
                            </div>
                            <div class="col-6">
                                <h5>{{ number_format($workJob->remaining_quantity, 1) }}</h5>
                                <small class="text-muted">متبقي م³</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات العميل -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user me-2"></i>معلومات العميل
                    </div>
                    <div class="card-body">
                        <p><strong>الاسم:</strong> {{ $workJob->customer_name ?? 'غير محدد' }}</p>
                        <p><strong>الهاتف:</strong> {{ $workJob->customer_phone ?? '-' }}</p>
                        <p><strong>النوع:</strong> {{ $workJob->customer_type_label }}</p>
                        <p class="mb-0"><strong>العنوان:</strong><br>{{ $workJob->location_address }}</p>
                        @if ($workJob->location_map_url)
                            <a href="{{ $workJob->location_map_url }}" target="_blank"
                                class="btn btn-sm btn-outline-info mt-2">
                                <i class="fas fa-map"></i> فتح الخريطة
                            </a>
                        @endif
                    </div>
                </div>

                <!-- معلومات الطلب -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i>تفاصيل الطلب
                    </div>
                    <div class="card-body">
                        <p><strong>نوع الخرسانة:</strong> {{ $workJob->concreteType->name ?? '-' }}</p>
                        <p><strong>الكمية:</strong> {{ number_format($workJob->total_quantity, 2) }} م³</p>
                        <p><strong>سعر المتر:</strong> {{ number_format($workJob->unit_price, 2) }}</p>
                        <p><strong>الإجمالي:</strong> {{ number_format($workJob->total_price, 2) }}</p>
                        @if ($workJob->discount_amount > 0)
                            <p><strong>الخصم:</strong> {{ number_format($workJob->discount_amount, 2) }}</p>
                        @endif
                        <p class="mb-0"><strong>الصافي:</strong> <span
                                class="fw-bold text-success">{{ number_format($workJob->final_price, 2) }}</span></p>
                    </div>
                </div>

                <!-- إجراءات -->
                @if ($workJob->can_add_shipment)
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-cogs me-2"></i>الإجراءات
                        </div>
                        <div class="card-body">
                            @if ($workJob->status == 'pending')
                                <form action="{{ route('work-jobs.reserve-materials', $workJob) }}" method="POST"
                                    class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-info w-100">
                                        <i class="fas fa-box"></i> حجز المواد
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('shipments.create', $workJob) }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-plus"></i> إضافة شحنة
                            </a>

                            @if ($workJob->status != 'on_hold')
                                <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal"
                                    data-bs-target="#holdModal">
                                    <i class="fas fa-pause"></i> تعليق
                                </button>
                            @else
                                <form action="{{ route('work-jobs.resume', $workJob) }}" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-play"></i> استئناف
                                    </button>
                                </form>
                            @endif

                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal"
                                data-bs-target="#cancelModal">
                                <i class="fas fa-times"></i> إلغاء
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- الشحنات -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-truck me-2"></i>الشحنات ({{ $workJob->shipments->count() }})</span>
                        @if ($workJob->can_add_shipment)
                            <a href="{{ route('shipments.create', $workJob) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> إضافة شحنة
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @forelse($workJob->shipments as $shipment)
                            <div class="card mb-3 border-{{ $shipment->status_badge }}">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>الشحنة #{{ $shipment->shipment_number }}</strong>
                                        <span
                                            class="badge bg-{{ $shipment->status_badge }} ms-2">{{ $shipment->status_label }}</span>
                                    </div>
                                    <a href="{{ route('shipments.show', $shipment) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> تفاصيل
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <small class="text-muted">الكمية</small>
                                            <p class="mb-0">
                                                <strong>{{ number_format($shipment->planned_quantity, 1) }}</strong> م³
                                                @if ($shipment->actual_quantity)
                                                    <br><span class="text-success">فعلي:
                                                        {{ number_format($shipment->actual_quantity, 1) }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">الخلاطة</small>
                                            <p class="mb-0">{{ $shipment->mixer->plate_number ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">السائق</small>
                                            <p class="mb-0">{{ $shipment->mixerDriver->name ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">الأوقات</small>
                                            <p class="mb-0">
                                                @if ($shipment->departure_time)
                                                    انطلاق: {{ $shipment->departure_time->format('H:i') }}<br>
                                                @endif
                                                @if ($shipment->arrival_time)
                                                    وصول: {{ $shipment->arrival_time->format('H:i') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    @if ($shipment->is_active)
                                        <div class="mt-3">
                                            <a href="{{ route('shipments.tracking', $shipment) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-map-marker-alt"></i> تتبع مباشر
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد شحنات بعد</p>
                                @if ($workJob->can_add_shipment)
                                    <a href="{{ route('shipments.create', $workJob) }}" class="btn btn-success">
                                        <i class="fas fa-plus"></i> إضافة أول شحنة
                                    </a>
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- حجوزات المواد -->
                @if ($workJob->materialReservations->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-boxes me-2"></i>المواد المحجوزة
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>المادة</th>
                                        <th>الكمية المحجوزة</th>
                                        <th>المستخدم</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($workJob->materialReservations as $reservation)
                                        <tr>
                                            <td>{{ $reservation->material->name ?? '-' }}</td>
                                            <td>{{ number_format($reservation->quantity_reserved, 2) }}</td>
                                            <td>{{ number_format($reservation->quantity_used, 2) }}</td>
                                            <td><span
                                                    class="badge bg-{{ $reservation->status_badge }}">{{ $reservation->status_label }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- الملاحظات -->
                @if ($workJob->notes || $workJob->internal_notes)
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-sticky-note me-2"></i>الملاحظات
                        </div>
                        <div class="card-body">
                            @if ($workJob->notes)
                                <p><strong>ملاحظات:</strong><br>{{ $workJob->notes }}</p>
                            @endif
                            @if ($workJob->internal_notes)
                                <p class="mb-0"><strong>ملاحظات داخلية:</strong><br>{{ $workJob->internal_notes }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal تعليق -->
    <div class="modal fade" id="holdModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('work-jobs.hold', $workJob) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">تعليق أمر العمل</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">سبب التعليق <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3" required minlength="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">تعليق</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal إلغاء -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('work-jobs.cancel', $workJob) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إلغاء أمر العمل</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            تحذير: هذا الإجراء لا يمكن التراجع عنه!
                        </div>
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
@endsection
