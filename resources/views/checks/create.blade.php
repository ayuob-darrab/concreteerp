@extends('layouts.app')

@section('title', 'إنشاء شيك جديد')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-money-check me-2"></i>
                    إنشاء شيك جديد
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('contractor-checks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- معلومات الشيك -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-info-circle me-2"></i>
                                معلومات الشيك
                            </h5>

                            <div class="mb-3">
                                <label for="type" class="form-label">نوع الشيك <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type"
                                    name="type" required>
                                    <option value="incoming" {{ old('type') == 'incoming' ? 'selected' : '' }}>وارد (من
                                        المقاول)</option>
                                    <option value="outgoing" {{ old('type') == 'outgoing' ? 'selected' : '' }}>صادر
                                        (للمقاول)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="contractor_id" class="form-label">المقاول <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('contractor_id') is-invalid @enderror" id="contractor_id"
                                    name="contractor_id" required>
                                    <option value="">-- اختر المقاول --</option>
                                    @foreach ($contractors ?? [] as $contractor)
                                        <option value="{{ $contractor->id }}"
                                            {{ old('contractor_id', request('contractor_id')) == $contractor->id ? 'selected' : '' }}>
                                            {{ $contractor->name }} ({{ $contractor->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('contractor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="check_number" class="form-label">رقم الشيك <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('check_number') is-invalid @enderror"
                                    id="check_number" name="check_number" value="{{ old('check_number') }}" required>
                                @error('check_number')
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
                                <label for="invoice_id" class="form-label">الفاتورة المرتبطة</label>
                                <select class="form-select @error('invoice_id') is-invalid @enderror" id="invoice_id"
                                    name="invoice_id">
                                    <option value="">-- بدون فاتورة --</option>
                                    @foreach ($invoices ?? [] as $invoice)
                                        <option value="{{ $invoice->id }}"
                                            {{ old('invoice_id', request('invoice_id')) == $invoice->id ? 'selected' : '' }}>
                                            {{ $invoice->invoice_number }} -
                                            {{ number_format($invoice->remaining_amount, 2) }} د.ع
                                        </option>
                                    @endforeach
                                </select>
                                @error('invoice_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- معلومات البنك والتواريخ -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-university me-2"></i>
                                معلومات البنك والتواريخ
                            </h5>

                            <div class="mb-3">
                                <label for="bank_name" class="form-label">اسم البنك <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                    id="bank_name" name="bank_name" value="{{ old('bank_name') }}" required>
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="bank_branch" class="form-label">فرع البنك</label>
                                <input type="text" class="form-control @error('bank_branch') is-invalid @enderror"
                                    id="bank_branch" name="bank_branch" value="{{ old('bank_branch') }}">
                                @error('bank_branch')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="account_number" class="form-label">رقم الحساب</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                    id="account_number" name="account_number" value="{{ old('account_number') }}">
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="issue_date" class="form-label">تاريخ الإصدار <span
                                                class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('issue_date') is-invalid @enderror" id="issue_date"
                                            name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                        @error('issue_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="due_date" class="form-label">تاريخ الاستحقاق <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                            id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="drawer_name" class="form-label">اسم الساحب</label>
                                <input type="text" class="form-control @error('drawer_name') is-invalid @enderror"
                                    id="drawer_name" name="drawer_name" value="{{ old('drawer_name') }}">
                                @error('drawer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- صورة الشيك والملاحظات -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="check_image" class="form-label">صورة الشيك</label>
                                <input type="file" class="form-control @error('check_image') is-invalid @enderror"
                                    id="check_image" name="check_image" accept="image/*">
                                <small class="text-muted">الصيغ المسموحة: JPG, PNG, GIF (الحد الأقصى: 2MB)</small>
                                @error('check_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- أزرار الإجراء -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            حفظ الشيك
                        </button>
                        <a href="{{ route('contractor-checks.index') }}" class="btn btn-secondary">
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
            // تحديث الفواتير عند اختيار المقاول
            document.getElementById('contractor_id').addEventListener('change', function() {
                const contractorId = this.value;
                const invoiceSelect = document.getElementById('invoice_id');

                if (contractorId) {
                    // يمكن إضافة AJAX لجلب الفواتير المرتبطة بالمقاول
                    fetch(`/api/contractors/${contractorId}/invoices?status=issued,partially_paid,overdue`)
                        .then(response => response.json())
                        .then(data => {
                            invoiceSelect.innerHTML = '<option value="">-- بدون فاتورة --</option>';
                            if (data.data) {
                                data.data.forEach(invoice => {
                                    invoiceSelect.innerHTML +=
                                        `<option value="${invoice.id}">${invoice.invoice_number} - ${invoice.remaining_amount} د.ع</option>`;
                                });
                            }
                        })
                        .catch(error => console.log('Error fetching invoices'));
                }
            });
        });
    </script>
@endpush
