@extends('layouts.app')

@section('title', 'المدفوعات')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">المدفوعات</h4>
                        <div>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#receivePaymentModal">
                                <i class="fas fa-plus"></i> استلام دفعة
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#makePaymentModal">
                                <i class="fas fa-minus"></i> صرف دفعة
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- الفلاتر --}}
                        <form method="GET" class="row mb-3">
                            <div class="col-md-2">
                                <select name="direction" class="form-control">
                                    <option value="">كل الاتجاهات</option>
                                    <option value="in" {{ request('direction') == 'in' ? 'selected' : '' }}>وارد
                                    </option>
                                    <option value="out" {{ request('direction') == 'out' ? 'selected' : '' }}>صادر
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="payment_method" class="form-control">
                                    <option value="">كل الطرق</option>
                                    @foreach (\App\Models\Payment::PAYMENT_METHODS as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ request('payment_method') == $key ? 'selected' : '' }}>{{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">بحث</button>
                            </div>
                        </form>

                        {{-- الجدول --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>رقم الإيصال</th>
                                        <th>الحساب</th>
                                        <th>الاتجاه</th>
                                        <th>المبلغ</th>
                                        <th>طريقة الدفع</th>
                                        <th>التاريخ</th>
                                        <th>الوصف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                        <tr class="{{ $payment->direction == 'in' ? 'table-success' : 'table-danger' }}">
                                            <td>{{ $payment->id }}</td>
                                            <td>{{ $payment->receipt_number }}</td>
                                            <td>{{ $payment->account->account_name ?? '-' }}</td>
                                            <td>
                                                @if ($payment->direction == 'in')
                                                    <span class="badge bg-success">وارد</span>
                                                @else
                                                    <span class="badge bg-danger">صادر</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                            <td>{{ $payment->payment_method_name }}</td>
                                            <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $payment->description }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">لا توجد مدفوعات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal استلام دفعة --}}
    <div class="modal fade" id="receivePaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('financial.payments.receive') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">استلام دفعة</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الحساب *</label>
                            <select name="account_id" class="form-control" required>
                                <option value="">اختر الحساب</option>
                                @foreach (\App\Models\FinancialAccount::where('company_code', auth()->user()->company_code)->active()->get() as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_name }}
                                        ({{ $account->formatted_balance }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">المبلغ *</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">طريقة الدفع *</label>
                            <select name="payment_method" class="form-control" required id="receivePaymentMethod">
                                @foreach (\App\Models\Payment::PAYMENT_METHODS as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 check-fields d-none">
                            <label class="form-label">رقم الشيك</label>
                            <input type="text" name="check_number" class="form-control">
                        </div>
                        <div class="mb-3 check-fields d-none">
                            <label class="form-label">تاريخ الشيك</label>
                            <input type="date" name="check_date" class="form-control">
                        </div>
                        <div class="mb-3 check-fields d-none">
                            <label class="form-label">اسم البنك</label>
                            <input type="text" name="bank_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">استلام</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal صرف دفعة --}}
    <div class="modal fade" id="makePaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('financial.payments.make') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">صرف دفعة</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الحساب *</label>
                            <select name="account_id" class="form-control" required>
                                <option value="">اختر الحساب</option>
                                @foreach (\App\Models\FinancialAccount::where('company_code', auth()->user()->company_code)->active()->get() as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_name }}
                                        ({{ $account->formatted_balance }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">المبلغ *</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">طريقة الدفع *</label>
                            <select name="payment_method" class="form-control" required id="makePaymentMethod">
                                @foreach (\App\Models\Payment::PAYMENT_METHODS as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 check-fields-make d-none">
                            <label class="form-label">رقم الشيك</label>
                            <input type="text" name="check_number" class="form-control">
                        </div>
                        <div class="mb-3 check-fields-make d-none">
                            <label class="form-label">تاريخ الشيك</label>
                            <input type="date" name="check_date" class="form-control">
                        </div>
                        <div class="mb-3 check-fields-make d-none">
                            <label class="form-label">اسم البنك</label>
                            <input type="text" name="bank_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">صرف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('receivePaymentMethod').addEventListener('change', function() {
                document.querySelectorAll('.check-fields').forEach(el => {
                    el.classList.toggle('d-none', this.value !== 'check');
                });
            });

            document.getElementById('makePaymentMethod').addEventListener('change', function() {
                document.querySelectorAll('.check-fields-make').forEach(el => {
                    el.classList.toggle('d-none', this.value !== 'check');
                });
            });
        </script>
    @endpush
@endsection
