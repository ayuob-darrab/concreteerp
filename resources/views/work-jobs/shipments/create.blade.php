@extends('layouts.app')

@section('title', 'إنشاء شحنة جديدة')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- معلومات أمر العمل -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="alert-heading mb-0">أمر العمل: {{ $workJob->job_number }}</h6>
                            <small>{{ $workJob->customer_name }} - {{ $workJob->concreteType->name ?? '' }}</small>
                        </div>
                        <div class="text-end">
                            <strong>{{ number_format($workJob->remaining_quantity, 1) }}</strong> م³
                            <br><small>متبقي</small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-truck me-2"></i>
                            إضافة شحنة جديدة
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('shipments.store', $workJob) }}" method="POST">
                            @csrf

                            <!-- الكمية -->
                            <div class="mb-4">
                                <label class="form-label">الكمية المخطط لها (م³) <span class="text-danger">*</span></label>
                                <input type="number" name="planned_quantity"
                                    class="form-control @error('planned_quantity') is-invalid @enderror"
                                    value="{{ old('planned_quantity', min(8, $workJob->remaining_quantity)) }}"
                                    step="0.1" min="0.1" max="{{ $workJob->remaining_quantity }}" required>
                                <small class="text-muted">الكمية المتبقية:
                                    {{ number_format($workJob->remaining_quantity, 1) }} م³</small>
                                @error('planned_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- الآليات -->
                            <h6 class="mb-3"><i class="fas fa-truck-loading me-2"></i>الآليات</h6>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">الخلاطة</label>
                                    <select name="mixer_id" class="form-select @error('mixer_id') is-invalid @enderror">
                                        <option value="">-- اختر الخلاطة --</option>
                                        @foreach ($vehicles['mixer'] ?? [] as $vehicle)
                                            <option value="{{ $vehicle->id }}"
                                                {{ old('mixer_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->plate_number }} - {{ $vehicle->model }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">اللوري</label>
                                    <select name="truck_id" class="form-select @error('truck_id') is-invalid @enderror">
                                        <option value="">-- اختر اللوري --</option>
                                        @foreach ($vehicles['truck'] ?? [] as $vehicle)
                                            <option value="{{ $vehicle->id }}"
                                                {{ old('truck_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->plate_number }} - {{ $vehicle->model }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">المضخة</label>
                                    <select name="pump_id" class="form-select @error('pump_id') is-invalid @enderror">
                                        <option value="">-- اختر المضخة --</option>
                                        @foreach ($vehicles['pump'] ?? [] as $vehicle)
                                            <option value="{{ $vehicle->id }}"
                                                {{ old('pump_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->plate_number }} - {{ $vehicle->model }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- السائقين -->
                            <h6 class="mb-3"><i class="fas fa-users me-2"></i>السائقين</h6>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">سائق الخلاطة</label>
                                    <select name="mixer_driver_id" class="form-select">
                                        <option value="">-- اختر السائق --</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}"
                                                {{ old('mixer_driver_id') == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">سائق اللوري</label>
                                    <select name="truck_driver_id" class="form-select">
                                        <option value="">-- اختر السائق --</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}"
                                                {{ old('truck_driver_id') == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">سائق المضخة</label>
                                    <select name="pump_driver_id" class="form-select">
                                        <option value="">-- اختر السائق --</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}"
                                                {{ old('pump_driver_id') == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- ملاحظات -->
                            <div class="mb-4">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('work-jobs.show', $workJob) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-1"></i> رجوع
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i> إنشاء الشحنة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
