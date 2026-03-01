@extends('layouts.app')

@section('title', $type === 'receipt' ? 'سند قبض جديد' : 'سند صرف جديد')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header {{ $type === 'receipt' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                <h4 class="card-title mb-0">
                    @if ($type === 'receipt')
                        <i class="fas fa-arrow-down me-2"></i>
                        سند قبض جديد
                    @else
                        <i class="fas fa-arrow-up me-2"></i>
                        سند صرف جديد
                    @endif
                </h4>
            </div>
            <div class="card-body">
                <form
                    action="{{ $type === 'receipt' ? route('contractor-receipts.store-receipt') : route('contractor-receipts.store-payment') }}"
                    method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-info-circle me-2"></i>
                                بيانات السند
                            </h5>

                            <div class="mb-3">
                                <label for="contractor_id" class="form-label">المقاول <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('contractor_id') is-invalid @enderror" id="contractor_id"
                                    name="contractor_id" required>
                                    <option value="">-- اختر المقاول --</option>
                                    @foreach ($contractors ?? [] as $contractor)
                                        <option value="{{ $contractor->id }}"
                                            data-balance="{{ $contractor->account?->balance ?? 0 }}"
                                            {{ old('contractor_id', request('contractor_id')) == $contractor->id ? 'selected' : '' }}>
                                            {{ $contractor->name }} ({{ $contractor->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('contractor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="contractor-balance" class="mt-2" style="display: none;">
                                    <small class="text-muted">الرصيد الحالي: <span id="balance-value">0.00</span>
                                        د.ع</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="receipt_date" class="form-label">التاريخ <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('receipt_date') is-invalid @enderror"
                                    id="receipt_date" name="receipt_date" value="{{ old('receipt_date', date('Y-m-d')) }}"
                                    required>
                                @error('receipt_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                        id="amount" name="amount" value="{{ old('amount') }}" min="0.01"
                                        step="0.01" required>
                                    <span class="input-group-text">د.ع</span>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">طريقة الدفع <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('payment_method') is-invalid @enderror"
                                    id="payment_method" name="payment_method" required>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي
                                    </option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>شيك
                                    </option>
                                    <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>
                                        تحويل بنكي</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-file-invoice me-2"></i>
                                ربط بفاتورة (اختياري)
                            </h5>

                            <div class="mb-3">
                                <label for="invoice_id" class="form-label">الفاتورة</label>
                                <select class="form-select @error('invoice_id') is-invalid @enderror" id="invoice_id"
                                    name="invoice_id">
                                    <option value="">-- بدون فاتورة --</option>
                                    @foreach ($invoices ?? [] as $invoice)
                                        <option value="{{ $invoice->id }}"
                                            data-remaining="{{ $invoice->remaining_amount }}"
                                            {{ old('invoice_id', request('invoice_id')) == $invoice->id ? 'selected' : '' }}>
                                            {{ $invoice->invoice_number }} - متبقي:
                                            {{ number_format($invoice->remaining_amount, 2) }} د.ع
                                        </option>
                                    @endforeach
                                </select>
                                @error('invoice_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- معلومات الشيك -->
                            <div id="check-details" style="display: none;">
                                <h6 class="mb-3 text-secondary">
                                    <i class="fas fa-money-check me-2"></i>
                                    بيانات الشيك
                                </h6>

                                <div class="mb-3">
                                    <label for="check_number" class="form-label">رقم الشيك</label>
                                    <input type="text" class="form-control" id="check_number" name="check_number"
                                        value="{{ old('check_number') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="check_bank" class="form-label">البنك</label>
                                    <input type="text" class="form-control" id="check_bank" name="check_bank"
                                        value="{{ old('check_bank') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="check_date" class="form-label">تاريخ الاستحقاق</label>
                                    <input type="date" class="form-control" id="check_date" name="check_date"
                                        value="{{ old('check_date') }}">
                                </div>
                            </div>

                            <!-- معلومات التحويل -->
                            <div id="transfer-details" style="display: none;">
                                <h6 class="mb-3 text-secondary">
                                    <i class="fas fa-exchange-alt me-2"></i>
                                    بيانات التحويل
                                </h6>

                                <div class="mb-3">
                                    <label for="transfer_reference" class="form-label">رقم المرجع</label>
                                    <input type="text" class="form-control" id="transfer_reference"
                                        name="transfer_reference" value="{{ old('transfer_reference') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="transfer_bank" class="form-label">البنك</label>
                                    <input type="text" class="form-control" id="transfer_bank" name="transfer_bank"
                                        value="{{ old('transfer_bank') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ملاحظات -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات / البيان</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراء -->
                    <div class="mt-4">
                        <button type="submit" class="btn {{ $type === 'receipt' ? 'btn-success' : 'btn-danger' }}">
                            <i class="fas fa-save me-2"></i>
                            حفظ السند
                        </button>
                        <a href="{{ route('contractor-receipts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethod = document.getElementById('payment_method');
            const checkDetails = document.getElementById('check-details');
            const transferDetails = document.getElementById('transfer-details');
            const contractorSelect = document.getElementById('contractor_id');
            const contractorBalance = document.getElementById('contractor-balance');
            const balanceValue = document.getElementById('balance-value');

            // إظهار/إخفاء تفاصيل الدفع حسب الطريقة
            paymentMethod.addEventListener('change', function() {
                checkDetails.style.display = this.value === 'check' ? 'block' : 'none';
                transferDetails.style.display = this.value === 'transfer' ? 'block' : 'none';
            });

            // عرض رصيد المقاول
            contractorSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const balance = parseFloat(selectedOption.dataset.balance) || 0;
                    balanceValue.textContent = balance.toFixed(2);
                    contractorBalance.style.display = 'block';
                } else {
                    contractorBalance.style.display = 'none';
                }
            });

            // التشغيل الأولي
            paymentMethod.dispatchEvent(new Event('change'));
            contractorSelect.dispatchEvent(new Event('change'));
        });
    </script>
@endpush
