@extends('layouts.app')

@section('title', 'تفاصيل الصيانة #' . $maintenance->id)

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-wrench text-warning"></i>
                            تفاصيل الصيانة #{{ $maintenance->id }}
                        </h4>
                    </div>
                    <div>
                        <a href="{{ route('maintenance.print', $maintenance) }}" class="btn btn-secondary" target="_blank">
                            <i class="fas fa-print"></i> طباعة
                        </a>
                        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- معلومات الصيانة -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">معلومات الصيانة</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">نوع الصيانة:</th>
                                        <td>
                                            @php
                                                $typeColors = [
                                                    'scheduled' => 'info',
                                                    'preventive' => 'primary',
                                                    'corrective' => 'warning',
                                                    'emergency' => 'danger',
                                                ];
                                            @endphp
                                            <span
                                                class="badge bg-{{ $typeColors[$maintenance->maintenance_type] ?? 'secondary' }}">
                                                {{ $maintenance->type_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ البدء:</th>
                                        <td>{{ $maintenance->started_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>تاريخ الانتهاء:</th>
                                        <td>
                                            @if ($maintenance->completed_at)
                                                {{ $maintenance->completed_at->format('Y-m-d H:i') }}
                                            @else
                                                <span class="text-warning">جارية</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($maintenance->duration)
                                        <tr>
                                            <th>المدة:</th>
                                            <td>{{ $maintenance->duration }} ساعة</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">قراءة العداد (قبل):</th>
                                        <td>{{ number_format($maintenance->odometer_before ?? 0) }} كم</td>
                                    </tr>
                                    <tr>
                                        <th>قراءة العداد (بعد):</th>
                                        <td>{{ number_format($maintenance->odometer_after ?? 0) }} كم</td>
                                    </tr>
                                    <tr>
                                        <th>ساعات العمل (قبل):</th>
                                        <td>{{ number_format($maintenance->working_hours_before ?? 0) }}</td>
                                    </tr>
                                    <tr>
                                        <th>ساعات العمل (بعد):</th>
                                        <td>{{ number_format($maintenance->working_hours_after ?? 0) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>
                        <h6>الوصف:</h6>
                        <p>{{ $maintenance->description }}</p>

                        @if ($maintenance->notes)
                            <h6>ملاحظات:</h6>
                            <p>{{ $maintenance->notes }}</p>
                        @endif
                    </div>
                </div>

                <!-- التكاليف -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">التكاليف</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h6>تكلفة العمالة</h6>
                                <h4>{{ number_format($maintenance->labor_cost, 2) }}</h4>
                                <small class="text-muted">د.ع</small>
                            </div>
                            <div class="col-md-4">
                                <h6>تكلفة قطع الغيار</h6>
                                <h4>{{ number_format($maintenance->parts_cost, 2) }}</h4>
                                <small class="text-muted">د.ع</small>
                            </div>
                            <div class="col-md-4">
                                <h6>الإجمالي</h6>
                                <h4 class="text-success">{{ number_format($maintenance->total_cost, 2) }}</h4>
                                <small class="text-muted">د.ع</small>
                            </div>
                        </div>

                        @if ($maintenance->parts_used && count($maintenance->parts_used) > 0)
                            <hr>
                            <h6>قطع الغيار المستخدمة:</h6>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>القطعة</th>
                                        <th>الكمية</th>
                                        <th>سعر الوحدة</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($maintenance->parts_used as $part)
                                        <tr>
                                            <td>{{ $part['name'] ?? '-' }}</td>
                                            <td>{{ $part['quantity'] ?? 0 }}</td>
                                            <td>{{ number_format($part['unit_price'] ?? 0, 2) }}</td>
                                            <td>{{ number_format(($part['quantity'] ?? 0) * ($part['unit_price'] ?? 0), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- معلومات الآلية -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">معلومات الآلية</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>رقم اللوحة:</th>
                                <td>{{ $maintenance->vehicle->plate_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>النوع:</th>
                                <td>{{ $maintenance->vehicle->car_type ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>الموديل:</th>
                                <td>{{ $maintenance->vehicle->model ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- من قام بالصيانة -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">من قام بالصيانة</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th>الفني:</th>
                                <td>{{ $maintenance->performed_by ?? '-' }}</td>
                            </tr>
                            @if ($maintenance->external_workshop)
                                <tr>
                                    <th>الورشة:</th>
                                    <td>{{ $maintenance->workshop_name ?? 'ورشة خارجية' }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>أُنشئ بواسطة:</th>
                                <td>{{ $maintenance->creator->name ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- الإجراءات -->
                @if (!$maintenance->isCompleted())
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0">الإجراءات</h5>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal"
                                data-bs-target="#completeModal">
                                <i class="fas fa-check"></i> إكمال الصيانة
                            </button>
                            <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="fas fa-times"></i> إلغاء الصيانة
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (!$maintenance->isCompleted())
        <!-- Modal إكمال الصيانة -->
        <div class="modal fade" id="completeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('maintenance.complete', $maintenance) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">إكمال الصيانة</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">تاريخ الانتهاء</label>
                                <input type="datetime-local" name="completed_at" class="form-control"
                                    value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">قراءة العداد الحالية</label>
                                    <input type="number" name="odometer_after" class="form-control"
                                        value="{{ $maintenance->vehicle->odometer_reading ?? 0 }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ساعات العمل الحالية</label>
                                    <input type="number" name="working_hours_after" class="form-control"
                                        value="{{ $maintenance->vehicle->working_hours ?? 0 }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تكلفة العمالة النهائية</label>
                                    <input type="number" name="labor_cost" class="form-control" step="0.01"
                                        value="{{ $maintenance->labor_cost }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تكلفة قطع الغيار النهائية</label>
                                    <input type="number" name="parts_cost" class="form-control" step="0.01"
                                        value="{{ $maintenance->parts_cost }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الصيانة التالية بعد (أيام)</label>
                                <input type="number" name="next_maintenance_days" class="form-control"
                                    value="{{ $maintenance->vehicle->maintenance_interval_days ?? 30 }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2">{{ $maintenance->notes }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-success">إكمال الصيانة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal إلغاء الصيانة -->
        <div class="modal fade" id="cancelModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('maintenance.cancel', $maintenance) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">إلغاء الصيانة</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                سيتم إلغاء الصيانة وإعادة الآلية للخدمة
                            </div>
                            <div class="mb-3">
                                <label class="form-label">سبب الإلغاء</label>
                                <textarea name="reason" class="form-control" rows="3"></textarea>
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
    @endif
@endsection
