@extends('layouts.app')

@section('title', 'سند صرف جديد')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            سند صرف جديد
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vouchers.store') }}" method="POST">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">الفرع <span class="text-danger">*</span></label>
                                    <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror"
                                        required>
                                        <option value="">-- اختر الفرع --</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">نوع المستفيد <span class="text-danger">*</span></label>
                                    <select name="payee_type" class="form-select @error('payee_type') is-invalid @enderror"
                                        required>
                                        <option value="">-- اختر النوع --</option>
                                        @foreach (\App\Models\PaymentVoucher::PAYEE_TYPES as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('payee_type', $prefill['payee_type'] ?? '') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payee_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label">اسم المستفيد <span class="text-danger">*</span></label>
                                    <input type="text" name="payee_name"
                                        class="form-control @error('payee_name') is-invalid @enderror"
                                        value="{{ old('payee_name', $prefill['payee_name'] ?? '') }}" required>
                                    <input type="hidden" name="payee_id"
                                        value="{{ old('payee_id', $prefill['payee_id'] ?? '') }}">
                                    @error('payee_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم الهاتف</label>
                                    <input type="text" name="payee_phone" class="form-control"
                                        value="{{ old('payee_phone', $prefill['payee_phone'] ?? '') }}">
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                                    <input type="number" name="amount"
                                        class="form-control form-control-lg @error('amount') is-invalid @enderror"
                                        value="{{ old('amount') }}" step="0.01" min="0.01" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">العملة <span class="text-danger">*</span></label>
                                    <select name="currency_code"
                                        class="form-select @error('currency_code') is-invalid @enderror" required>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->code }}"
                                                {{ old('currency_code', 'IQD') == $currency->code ? 'selected' : '' }}>
                                                {{ $currency->name_ar }} ({{ $currency->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('currency_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                                    <select name="payment_method" id="payment_method"
                                        class="form-select @error('payment_method') is-invalid @enderror" required
                                        onchange="toggleCheckFields()">
                                        <option value="">-- اختر --</option>
                                        @foreach (\App\Models\PaymentVoucher::PAYMENT_METHODS as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('payment_method') == $key ? 'selected' : '' }}>{{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">رقم المرجع</label>
                                    <input type="text" name="reference_number" class="form-control"
                                        value="{{ old('reference_number') }}" placeholder="رقم الحوالة أو المرجع">
                                </div>
                            </div>

                            <!-- حقول الشيك -->
                            <div id="check_fields" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">رقم الشيك <span class="text-danger">*</span></label>
                                        <input type="text" name="check_number" class="form-control"
                                            value="{{ old('check_number') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">تاريخ الشيك <span class="text-danger">*</span></label>
                                        <input type="date" name="check_date" class="form-control"
                                            value="{{ old('check_date') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">البنك</label>
                                        <input type="text" name="bank_name" class="form-control"
                                            value="{{ old('bank_name') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">وصف / سبب الصرف <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required
                                    minlength="5" placeholder="مثال: دفعة مقاول عن أمر عمل رقم...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="requires_approval"
                                        id="requires_approval" {{ old('requires_approval', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requires_approval">
                                        يتطلب موافقة قبل الصرف
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('vouchers.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-1"></i> رجوع
                                </a>
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-save me-1"></i> حفظ السند
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCheckFields() {
            const method = document.getElementById('payment_method').value;
            const checkFields = document.getElementById('check_fields');
            checkFields.style.display = method === 'check' ? 'block' : 'none';
        }
        document.addEventListener('DOMContentLoaded', toggleCheckFields);
    </script>
@endsection
