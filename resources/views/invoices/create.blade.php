@extends('layouts.app')

@section('title', 'إنشاء فاتورة جديدة')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    إنشاء فاتورة جديدة
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('contractor-invoices.store') }}" method="POST" id="invoice-form">
                    @csrf

                    <div class="row">
                        <!-- معلومات الفاتورة -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-info-circle me-2"></i>
                                معلومات الفاتورة
                            </h5>

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
                                <label for="invoice_date" class="form-label">تاريخ الفاتورة <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                                    id="invoice_date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}"
                                    required>
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="due_date" class="form-label">تاريخ الاستحقاق <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                    id="due_date" name="due_date"
                                    value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- الخصم والضريبة -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary">
                                <i class="fas fa-percentage me-2"></i>
                                الخصم والضريبة
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="discount_type" class="form-label">نوع الخصم</label>
                                        <select class="form-select" id="discount_type" name="discount_type">
                                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>
                                                مبلغ ثابت</option>
                                            <option value="percentage"
                                                {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>نسبة مئوية
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="discount_value" class="form-label">قيمة الخصم</label>
                                        <input type="number" class="form-control" id="discount_value" name="discount_value"
                                            value="{{ old('discount_value', 0) }}" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tax_rate" class="form-label">نسبة الضريبة (%)</label>
                                <input type="number" class="form-control" id="tax_rate" name="tax_rate"
                                    value="{{ old('tax_rate', 15) }}" min="0" max="100" step="0.01">
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- بنود الفاتورة -->
                    <div class="mt-4">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-list me-2"></i>
                            بنود الفاتورة
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="items-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40%">الوصف</th>
                                        <th style="width: 12%">الكمية</th>
                                        <th style="width: 12%">الوحدة</th>
                                        <th style="width: 15%">سعر الوحدة</th>
                                        <th style="width: 15%">الإجمالي</th>
                                        <th style="width: 6%"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-body">
                                    <tr class="item-row">
                                        <td>
                                            <input type="text" class="form-control" name="items[0][description]"
                                                required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-quantity"
                                                name="items[0][quantity]" value="1" min="0.01" step="0.01"
                                                required>
                                        </td>
                                        <td>
                                            <select class="form-select" name="items[0][unit]">
                                                <option value="م³">م³</option>
                                                <option value="طن">طن</option>
                                                <option value="وحدة">وحدة</option>
                                                <option value="رحلة">رحلة</option>
                                                <option value="ساعة">ساعة</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control item-price"
                                                name="items[0][unit_price]" value="0" min="0" step="0.01"
                                                required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control item-total" readonly
                                                value="0.00">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-item"
                                                disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-item">
                                                <i class="fas fa-plus me-2"></i>
                                                إضافة بند
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-start">المجموع الفرعي</th>
                                        <th id="subtotal">0.00</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-start">الخصم</th>
                                        <th id="discount-amount" class="text-danger">0.00</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-start">الضريبة</th>
                                        <th id="tax-amount">0.00</th>
                                        <th></th>
                                    </tr>
                                    <tr class="table-primary">
                                        <th colspan="4" class="text-start">الإجمالي النهائي</th>
                                        <th id="total-amount">0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- أزرار الإجراء -->
                    <div class="mt-4">
                        <button type="submit" name="action" value="save" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            حفظ كمسودة
                        </button>
                        <button type="submit" name="action" value="save_and_issue" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>
                            حفظ وإصدار
                        </button>
                        <a href="{{ route('contractor-invoices.index') }}" class="btn btn-secondary">
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
            let itemIndex = 1;

            // إضافة بند جديد
            document.getElementById('add-item').addEventListener('click', function() {
                const tbody = document.getElementById('items-body');
                const newRow = document.createElement('tr');
                newRow.className = 'item-row';
                newRow.innerHTML = `
            <td>
                <input type="text" class="form-control" name="items[${itemIndex}][description]" required>
            </td>
            <td>
                <input type="number" class="form-control item-quantity" name="items[${itemIndex}][quantity]" 
                       value="1" min="0.01" step="0.01" required>
            </td>
            <td>
                <select class="form-select" name="items[${itemIndex}][unit]">
                    <option value="م³">م³</option>
                    <option value="طن">طن</option>
                    <option value="وحدة">وحدة</option>
                    <option value="رحلة">رحلة</option>
                    <option value="ساعة">ساعة</option>
                </select>
            </td>
            <td>
                <input type="number" class="form-control item-price" name="items[${itemIndex}][unit_price]" 
                       value="0" min="0" step="0.01" required>
            </td>
            <td>
                <input type="text" class="form-control item-total" readonly value="0.00">
            </td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
                tbody.appendChild(newRow);
                itemIndex++;
                updateRemoveButtons();
                attachRowListeners(newRow);
            });

            // حذف بند
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
                    const btn = e.target.classList.contains('remove-item') ? e.target : e.target.closest(
                        '.remove-item');
                    btn.closest('tr').remove();
                    updateRemoveButtons();
                    calculateTotals();
                }
            });

            // تحديث أزرار الحذف
            function updateRemoveButtons() {
                const rows = document.querySelectorAll('.item-row');
                rows.forEach((row, index) => {
                    const btn = row.querySelector('.remove-item');
                    btn.disabled = rows.length === 1;
                });
            }

            // إضافة listeners للصف
            function attachRowListeners(row) {
                row.querySelectorAll('.item-quantity, .item-price').forEach(input => {
                    input.addEventListener('input', function() {
                        calculateRowTotal(row);
                        calculateTotals();
                    });
                });
            }

            // حساب إجمالي الصف
            function calculateRowTotal(row) {
                const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                const price = parseFloat(row.querySelector('.item-price').value) || 0;
                const total = quantity * price;
                row.querySelector('.item-total').value = total.toFixed(2);
            }

            // حساب الإجماليات
            function calculateTotals() {
                let subtotal = 0;
                document.querySelectorAll('.item-total').forEach(input => {
                    subtotal += parseFloat(input.value) || 0;
                });

                const discountType = document.getElementById('discount_type').value;
                const discountValue = parseFloat(document.getElementById('discount_value').value) || 0;
                let discountAmount = 0;

                if (discountType === 'percentage') {
                    discountAmount = (subtotal * discountValue) / 100;
                } else {
                    discountAmount = discountValue;
                }

                const afterDiscount = subtotal - discountAmount;
                const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
                const taxAmount = (afterDiscount * taxRate) / 100;
                const totalAmount = afterDiscount + taxAmount;

                document.getElementById('subtotal').textContent = subtotal.toFixed(2);
                document.getElementById('discount-amount').textContent = discountAmount.toFixed(2);
                document.getElementById('tax-amount').textContent = taxAmount.toFixed(2);
                document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
            }

            // إضافة listeners للصفوف الموجودة
            document.querySelectorAll('.item-row').forEach(attachRowListeners);

            // إضافة listeners للخصم والضريبة
            ['discount_type', 'discount_value', 'tax_rate'].forEach(id => {
                document.getElementById(id).addEventListener('input', calculateTotals);
                document.getElementById(id).addEventListener('change', calculateTotals);
            });

            updateRemoveButtons();
        });
    </script>
@endpush
