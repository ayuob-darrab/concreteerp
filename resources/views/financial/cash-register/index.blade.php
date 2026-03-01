@extends('layouts.app')

@section('title', 'حركات الصندوق')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">حركات الصندوق</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEntryModal">
                            <i class="fas fa-plus"></i> إضافة حركة
                        </button>
                    </div>
                    <div class="card-body">
                        {{-- ملخص اليوم --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h6>الرصيد الافتتاحي</h6>
                                        <h4>{{ number_format($summary['opening_balance'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h6>إجمالي الإيداعات</h6>
                                        <h4>{{ number_format($summary['total_in'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h6>إجمالي السحوبات</h6>
                                        <h4>{{ number_format($summary['total_out'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h6>الرصيد الحالي</h6>
                                        <h4>{{ number_format($summary['closing_balance'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- الفلاتر --}}
                        <form method="GET" class="row mb-3">
                            <div class="col-md-3">
                                <input type="date" name="from" class="form-control" value="{{ request('from') }}"
                                    placeholder="من">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="to" class="form-control" value="{{ request('to') }}"
                                    placeholder="إلى">
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
                                        <th>التاريخ</th>
                                        <th>نوع الحركة</th>
                                        <th>المبلغ</th>
                                        <th>الرصيد قبل</th>
                                        <th>الرصيد بعد</th>
                                        <th>الوصف</th>
                                        <th>بواسطة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($entries as $entry)
                                        <tr
                                            class="{{ $entry->transaction_type == 'cash_in' ? 'table-success' : 'table-danger' }}">
                                            <td>{{ $entry->id }}</td>
                                            <td>{{ $entry->handled_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                @if ($entry->transaction_type == 'cash_in')
                                                    <span class="badge bg-success">إيداع</span>
                                                @else
                                                    <span class="badge bg-danger">سحب</span>
                                                @endif
                                            </td>
                                            <td>{{ $entry->formatted_amount }}</td>
                                            <td>{{ number_format($entry->opening_balance, 2) }}</td>
                                            <td>{{ number_format($entry->closing_balance, 2) }}</td>
                                            <td>{{ $entry->description }}</td>
                                            <td>{{ $entry->handler->name ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">لا توجد حركات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $entries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal إضافة حركة --}}
    <div class="modal fade" id="addEntryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('financial.cash-register.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة حركة صندوق</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">نوع الحركة *</label>
                            <select name="transaction_type" class="form-control" required>
                                <option value="cash_in">إيداع</option>
                                <option value="cash_out">سحب</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">المبلغ *</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الوصف *</label>
                            <input type="text" name="description" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
