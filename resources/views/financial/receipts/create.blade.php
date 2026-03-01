@extends('layouts.app')

@section('title', 'إيصال قبض جديد')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>
                            إيصال قبض جديد
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('receipts.store') }}" method="POST">
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
                                    <label class="form-label">نوع الدافع <span class="text-danger">*</span></label>
                                    <select name="payer_type" class="form-select @error('payer_type') is-invalid @enderror"
                                        required>
                                        <option value="">-- اختر النوع --</option>
                                        @foreach (\App\Models\PaymentReceipt::PAYER_TYPES as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ old('payer_type', $prefill['payer_type'] ?? '') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payer_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label">اسم الدافع <span class="text-danger">*</span></label>
                                    <input type="text" name="payer_name"
                                        class="form-control @error('payer_name') is-invalid @enderror"
                                        value="{{ old('payer_name', $prefill['payer_name'] ?? '') }}" required>
                                    <input type="hidden" name="payer_id"
                                        value="{{ old('payer_id', $prefill['payer_id'] ?? '') }}">
                                    @error('payer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رقم الهاتف</label>
                                    <input type="text" name="payer_phone" class="form-control"
                                        value="{{ old('payer_phone', $prefill['payer_phone'] ?? '') }}">
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
                                        @foreach (\App\Models\PaymentReceipt::PAYMENT_METHODS as $key => $label)
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

                            <div class="mb-4">
                                <label class="form-label">وصف / سبب الدفع <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required
                                    minlength="5" placeholder="مثال: دفعة من مستحقات أمر عمل رقم...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('receipts.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-right me-1"></i> رجوع
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save me-1"></i> حفظ الإيصال
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
