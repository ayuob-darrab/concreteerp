@extends('layouts.app')

@section('title', 'تسجيل خسارة جديدة')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            تسجيل خسارة جديدة
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($job)
                            <div class="alert alert-info">
                                <strong>أمر العمل:</strong> {{ $job->job_number }} - {{ $job->customer_name }}
                            </div>
                        @endif

                        <form action="{{ route('losses.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">الفرع <span class="text-danger">*</span></label>
                                    <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- اختر الفرع --</option>
                                        @foreach (auth()->user()->branches ?? [] as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id', $job->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">نوع الخسارة <span class="text-danger">*</span></label>
                                    <select name="loss_type" class="form-select @error('loss_type') is-invalid @enderror"
                                        required>
                                        <option value="">-- اختر النوع --</option>
                                        @foreach (\App\Models\WorkLoss::TYPES as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('loss_type') == $key ? 'selected' : '' }}>{{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('loss_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">وصف الخسارة <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required
                                    minlength="10">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">الكمية المفقودة (م³)</label>
                                    <input type="number" name="quantity_lost"
                                        class="form-control @error('quantity_lost') is-invalid @enderror"
                                        value="{{ old('quantity_lost') }}" step="0.1" min="0">
                                    @error('quantity_lost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">التكلفة التقديرية</label>
                                    <input type="number" name="estimated_cost"
                                        class="form-control @error('estimated_cost') is-invalid @enderror"
                                        value="{{ old('estimated_cost') }}" step="0.01" min="0">
                                    @error('estimated_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">أمر العمل</label>
                                    <input type="text" name="job_id" class="form-control"
                                        value="{{ old('job_id', $job->id ?? '') }}" placeholder="رقم أمر العمل (اختياري)">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">الآلية</label>
                                    <select name="vehicle_id" class="form-select">
                                        <option value="">-- اختر الآلية --</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}"
                                                {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->plate_number }} - {{ $vehicle->model }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">وصف الموقع</label>
                                <input type="text" name="location_description" class="form-control"
                                    value="{{ old('location_description') }}"
                                    placeholder="مثال: على طريق الملك فهد، كم 15">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">خط العرض</label>
                                    <input type="text" name="latitude" class="form-control"
                                        value="{{ old('latitude') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">خط الطول</label>
                                    <input type="text" name="longitude" class="form-control"
                                        value="{{ old('longitude') }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">المرفقات (صور، مستندات)</label>
                                <input type="file" name="attachments[]" class="form-control" multiple
                                    accept="image/*,.pdf,.doc,.docx">
                                <small class="text-muted">يمكنك إرفاق عدة ملفات (الحد الأقصى 10 ميجا لكل ملف)</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('losses.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-1"></i> رجوع
                                </a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save me-1"></i> تسجيل الخسارة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
