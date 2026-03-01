@extends('layouts.app')

@section('title', 'تعديل المقاول: ' . $contractor->name)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-edit me-2"></i>
                            تعديل المقاول: {{ $contractor->name }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('contractors.update', $contractor) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- البيانات الأساسية -->
                                <div class="col-md-6">
                                    <h5 class="mb-3 text-primary">
                                        <i class="fas fa-info-circle me-2"></i>
                                        البيانات الأساسية
                                    </h5>

                                    <div class="mb-3">
                                        <label for="code" class="form-label">كود المقاول</label>
                                        <input type="text" class="form-control" id="code"
                                            value="{{ $contractor->code }}" disabled>
                                        <small class="text-muted">لا يمكن تغيير الكود</small>
                                    </div>

                                    <div class="mb-3">
                                        <label for="name" class="form-label">اسم المقاول <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $contractor->name) }}"
                                            required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">رقم الهاتف <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                            id="phone" name="phone" value="{{ old('phone', $contractor->phone) }}"
                                            required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="mobile" class="form-label">رقم الجوال</label>
                                        <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                            id="mobile" name="mobile" value="{{ old('mobile', $contractor->mobile) }}">
                                        @error('mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">البريد الإلكتروني</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $contractor->email) }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">العنوان</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $contractor->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- البيانات التجارية والمالية -->
                                <div class="col-md-6">
                                    <h5 class="mb-3 text-primary">
                                        <i class="fas fa-building me-2"></i>
                                        البيانات التجارية
                                    </h5>

                                    <div class="mb-3">
                                        <label for="commercial_register" class="form-label">السجل التجاري</label>
                                        <input type="text"
                                            class="form-control @error('commercial_register') is-invalid @enderror"
                                            id="commercial_register" name="commercial_register"
                                            value="{{ old('commercial_register', $contractor->commercial_register) }}">
                                        @error('commercial_register')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="tax_number" class="form-label">الرقم الضريبي</label>
                                        <input type="text" class="form-control @error('tax_number') is-invalid @enderror"
                                            id="tax_number" name="tax_number"
                                            value="{{ old('tax_number', $contractor->tax_number) }}">
                                        @error('tax_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <h5 class="mb-3 text-primary mt-4">
                                        <i class="fas fa-wallet me-2"></i>
                                        البيانات المالية
                                    </h5>

                                    <div class="mb-3">
                                        <label for="credit_limit" class="form-label">حد الائتمان</label>
                                        <div class="input-group">
                                            <input type="number"
                                                class="form-control @error('credit_limit') is-invalid @enderror"
                                                id="credit_limit" name="credit_limit"
                                                value="{{ old('credit_limit', $contractor->credit_limit) }}"
                                                min="0" step="0.01">
                                            <span class="input-group-text">د.ع</span>
                                        </div>
                                        @error('credit_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="payment_terms" class="form-label">شروط الدفع (أيام)</label>
                                        <input type="number"
                                            class="form-control @error('payment_terms') is-invalid @enderror"
                                            id="payment_terms" name="payment_terms"
                                            value="{{ old('payment_terms', $contractor->payment_terms) }}"
                                            min="0">
                                        @error('payment_terms')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="classification" class="form-label">التصنيف</label>
                                        <select class="form-select @error('classification') is-invalid @enderror"
                                            id="classification" name="classification">
                                            <option value="A"
                                                {{ old('classification', $contractor->classification) == 'A' ? 'selected' : '' }}>
                                                A - ممتاز</option>
                                            <option value="B"
                                                {{ old('classification', $contractor->classification) == 'B' ? 'selected' : '' }}>
                                                B - جيد</option>
                                            <option value="C"
                                                {{ old('classification', $contractor->classification) == 'C' ? 'selected' : '' }}>
                                                C - متوسط</option>
                                            <option value="D"
                                                {{ old('classification', $contractor->classification) == 'D' ? 'selected' : '' }}>
                                                D - ضعيف</option>
                                        </select>
                                        @error('classification')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- معلومات الرصيد -->
                                    <div class="card bg-light mt-4">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-chart-line me-2"></i>
                                                الرصيد الحالي
                                            </h6>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>الرصيد:</span>
                                                <span
                                                    class="fw-bold {{ ($contractor->account?->balance ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format(abs($contractor->account?->balance ?? 0), 2) }} د.ع
                                                    @if (($contractor->account?->balance ?? 0) >= 0)
                                                        <small>(للمقاول)</small>
                                                    @else
                                                        <small>(على المقاول)</small>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ملاحظات -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">ملاحظات</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $contractor->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- أزرار الإجراء -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        حفظ التعديلات
                                    </button>
                                    <a href="{{ route('contractors.show', $contractor) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>
                                        إلغاء
                                    </a>
                                    <a href="{{ route('contractors.statement', $contractor) }}" class="btn btn-info">
                                        <i class="fas fa-file-alt me-2"></i>
                                        كشف الحساب
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
