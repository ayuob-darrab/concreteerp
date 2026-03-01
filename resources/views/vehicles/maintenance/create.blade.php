@extends('layouts.app')

@section('title', 'إضافة صيانة جديدة')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-1">
                    <i class="fas fa-wrench text-warning"></i>
                    إضافة صيانة جديدة
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('maintenance.index') }}">سجلات الصيانة</a></li>
                        <li class="breadcrumb-item active">إضافة جديدة</li>
                    </ol>
                </nav>
            </div>
        </div>

        <form action="{{ route('maintenance.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">معلومات الصيانة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الآلية <span class="text-danger">*</span></label>
                                    <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror"
                                        required>
                                        <option value="">اختر الآلية</option>
                                        @foreach ($vehicles as $v)
                                            <option value="{{ $v->id }}"
                                                {{ old('vehicle_id') == $v->id || ($vehicle && $vehicle->id == $v->id) ? 'selected' : '' }}>
                                                {{ $v->plate_number }} - {{ $v->model }} ({{ $v->car_type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">نوع الصيانة <span class="text-danger">*</span></label>
                                    <select name="maintenance_type"
                                        class="form-select @error('maintenance_type') is-invalid @enderror" required>
                                        <option value="">اختر النوع</option>
                                        <option value="scheduled"
                                            {{ old('maintenance_type') == 'scheduled' ? 'selected' : '' }}>دورية مجدولة
                                        </option>
                                        <option value="preventive"
                                            {{ old('maintenance_type') == 'preventive' ? 'selected' : '' }}>وقائية</option>
                                        <option value="corrective"
                                            {{ old('maintenance_type') == 'corrective' ? 'selected' : '' }}>تصحيحية (إصلاح)
                                        </option>
                                        <option value="emergency"
                                            {{ old('maintenance_type') == 'emergency' ? 'selected' : '' }}>طارئة</option>
                                    </select>
                                    @error('maintenance_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">وصف الصيانة <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تاريخ البدء <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="started_at"
                                        class="form-control @error('started_at') is-invalid @enderror"
                                        value="{{ old('started_at', now()->format('Y-m-d\TH:i')) }}" required>
                                    @error('started_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">التكاليف</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تكلفة العمالة</label>
                                    <input type="number" name="labor_cost" class="form-control" step="0.01"
                                        min="0" value="{{ old('labor_cost', 0) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تكلفة قطع الغيار</label>
                                    <input type="number" name="parts_cost" class="form-control" step="0.01"
                                        min="0" value="{{ old('parts_cost', 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">من يقوم بالصيانة</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">اسم الفني/المسؤول</label>
                                    <input type="text" name="performed_by" class="form-control"
                                        value="{{ old('performed_by') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="external_workshop"
                                            id="external_workshop" value="1"
                                            {{ old('external_workshop') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="external_workshop">ورشة خارجية</label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3" id="workshop_name_field" style="display: none;">
                                    <label class="form-label">اسم الورشة</label>
                                    <input type="text" name="workshop_name" class="form-control"
                                        value="{{ old('workshop_name') }}">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">ملاحظات</label>
                                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    @if ($vehicle)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">معلومات الآلية</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>رقم اللوحة:</th>
                                        <td>{{ $vehicle->plate_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>النوع:</th>
                                        <td>{{ $vehicle->car_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>الموديل:</th>
                                        <td>{{ $vehicle->model }}</td>
                                    </tr>
                                    <tr>
                                        <th>قراءة العداد:</th>
                                        <td>{{ number_format($vehicle->odometer_reading ?? 0) }} كم</td>
                                    </tr>
                                    <tr>
                                        <th>ساعات العمل:</th>
                                        <td>{{ number_format($vehicle->working_hours ?? 0) }} ساعة</td>
                                    </tr>
                                    <tr>
                                        <th>آخر صيانة:</th>
                                        <td>{{ $vehicle->last_maintenance_date ?? 'لم تُجرَ' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save"></i> حفظ سجل الصيانة
                            </button>
                            <a href="{{ route('maintenance.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('external_workshop').addEventListener('change', function() {
            document.getElementById('workshop_name_field').style.display = this.checked ? 'block' : 'none';
        });
        // Initial check
        if (document.getElementById('external_workshop').checked) {
            document.getElementById('workshop_name_field').style.display = 'block';
        }
    </script>
@endpush
