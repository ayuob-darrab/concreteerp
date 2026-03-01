@extends('layouts.app')

@section('title', 'إنشاء حجز جديد')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-1">
                    <i class="fas fa-calendar-plus text-primary"></i>
                    إنشاء حجز جديد
                </h4>
            </div>
        </div>

        <form action="{{ route('vehicle-reservations.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">الآلية <span class="text-danger">*</span></label>
                                    <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror"
                                        required>
                                        <option value="">اختر الآلية</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}"
                                                {{ old('vehicle_id') == $vehicle->id || ($selectedVehicle && $selectedVehicle->id == $vehicle->id) ? 'selected' : '' }}>
                                                {{ $vehicle->plate_number }} - {{ $vehicle->model }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">السائق</label>
                                    <select name="driver_id" class="form-select">
                                        <option value="">بدون تعيين</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}"
                                                {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">من تاريخ/وقت <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="reserved_from"
                                        class="form-control @error('reserved_from') is-invalid @enderror"
                                        value="{{ old('reserved_from') }}" required>
                                    @error('reserved_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">إلى تاريخ/وقت <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="reserved_to"
                                        class="form-control @error('reserved_to') is-invalid @enderror"
                                        value="{{ old('reserved_to') }}" required>
                                    @error('reserved_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">الغرض من الحجز</label>
                                    <input type="text" name="purpose" class="form-control" value="{{ old('purpose') }}"
                                        placeholder="مثال: توصيل طلب، نقل مواد...">
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">ملاحظات</label>
                                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-save"></i> حفظ الحجز
                            </button>
                            <a href="{{ route('vehicle-reservations.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
