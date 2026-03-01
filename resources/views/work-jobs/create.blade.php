@extends('layouts.app')

@section('title', 'إنشاء أمر عمل')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            إنشاء أمر عمل جديد
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('work-jobs.store') }}" method="POST">
                            @csrf

                            @if ($order)
                                <!-- معلومات الطلب -->
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle me-2"></i>
                                        معلومات الطلب
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>رقم الطلب:</strong> {{ $order->order_number }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>الكمية:</strong> {{ $order->quantity }} م³
                                        </div>
                                        <div class="col-md-4">
                                            <strong>النوع:</strong> {{ $order->concreteType->name ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <strong>العميل:</strong> {{ $order->customer_name }}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>العنوان:</strong> {{ $order->delivery_address }}
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                            @else
                                <div class="mb-3">
                                    <label class="form-label">اختر الطلب <span class="text-danger">*</span></label>
                                    <select name="order_id" class="form-select @error('order_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- اختر طلباً --</option>
                                        <!-- يمكن تحميل الطلبات المتاحة هنا -->
                                    </select>
                                    @error('order_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تاريخ التنفيذ <span class="text-danger">*</span></label>
                                    <input type="date" name="scheduled_date"
                                        class="form-control @error('scheduled_date') is-invalid @enderror"
                                        value="{{ old('scheduled_date', $order->delivery_date ?? now()->toDateString()) }}"
                                        required>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">وقت التنفيذ</label>
                                    <input type="time" name="scheduled_time"
                                        class="form-control @error('scheduled_time') is-invalid @enderror"
                                        value="{{ old('scheduled_time', $order->delivery_time ?? '') }}">
                                    @error('scheduled_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">المشرف</label>
                                <select name="supervisor_id"
                                    class="form-select @error('supervisor_id') is-invalid @enderror">
                                    <option value="">-- بدون مشرف --</option>
                                    @foreach ($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}"
                                            {{ old('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->name }} - {{ $supervisor->position }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supervisor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">خط العرض (Latitude)</label>
                                    <input type="text" name="latitude" class="form-control"
                                        value="{{ old('latitude') }}" placeholder="مثال: 24.7136">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">خط الطول (Longitude)</label>
                                    <input type="text" name="longitude" class="form-control"
                                        value="{{ old('longitude') }}" placeholder="مثال: 46.6753">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $order->notes ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ملاحظات داخلية</label>
                                <textarea name="internal_notes" class="form-control" rows="2" placeholder="ملاحظات للاستخدام الداخلي فقط">{{ old('internal_notes') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('work-jobs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-1"></i> رجوع
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> إنشاء أمر العمل
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
