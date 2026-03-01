@extends('layouts.app')

@section('title', 'جدول الصيانة')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-calendar-alt text-info"></i>
                            جدول الصيانة
                        </h4>
                    </div>
                    <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- آليات تحتاج صيانة -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            آليات تحتاج صيانة قريباً
                            <span class="badge bg-danger">{{ $dueVehicles->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($dueVehicles->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($dueVehicles as $vehicle)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $vehicle->plate_number }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $vehicle->model }} -
                                                {{ $vehicle->car_type }}</small>
                                            <br>
                                            @if ($vehicle->next_maintenance_date)
                                                @php
                                                    $daysUntil = now()->diffInDays(
                                                        \Carbon\Carbon::parse($vehicle->next_maintenance_date),
                                                        false,
                                                    );
                                                @endphp
                                                <small
                                                    class="text-{{ $daysUntil < 0 ? 'danger' : ($daysUntil <= 3 ? 'warning' : 'info') }}">
                                                    @if ($daysUntil < 0)
                                                        متأخرة بـ {{ abs($daysUntil) }} يوم
                                                    @elseif($daysUntil == 0)
                                                        مستحقة اليوم
                                                    @else
                                                        خلال {{ $daysUntil }} يوم
                                                    @endif
                                                </small>
                                            @else
                                                <small class="text-muted">لم تُجدول بعد</small>
                                            @endif
                                        </div>
                                        <a href="{{ route('maintenance.create', ['vehicle_id' => $vehicle->id]) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-wrench"></i> صيانة
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="mb-0">لا توجد آليات تحتاج صيانة قريبة</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- صيانات جارية -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs"></i>
                            صيانات جارية
                            <span class="badge bg-light text-dark">{{ $pendingMaintenance->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($pendingMaintenance->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($pendingMaintenance as $maintenance)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ $maintenance->vehicle->plate_number ?? '-' }}</strong>
                                                <span
                                                    class="badge bg-{{ $maintenance->maintenance_type == 'emergency' ? 'danger' : 'primary' }}">
                                                    {{ $maintenance->type_label }}
                                                </span>
                                                <br>
                                                <small>{{ Str::limit($maintenance->description, 50) }}</small>
                                                <br>
                                                <small class="text-muted">
                                                    بدأت: {{ $maintenance->started_at->format('Y-m-d H:i') }}
                                                    ({{ $maintenance->started_at->diffForHumans() }})
                                                </small>
                                            </div>
                                            <a href="{{ route('maintenance.show', $maintenance) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check fa-3x text-success mb-3"></i>
                                <p class="mb-0">لا توجد صيانات جارية</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
