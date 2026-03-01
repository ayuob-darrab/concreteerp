@extends('layouts.app')

@section('title', 'كشف راتب - ' . $payroll->employee->name)

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">
                <i class="fas fa-file-invoice-dollar text-success"></i>
                كشف راتب: {{ $payroll->employee->name }}
            </h3>
            <div>
                <a href="{{ route('payroll.print', $payroll) }}" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-print"></i> طباعة
                </a>
                <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <!-- معلومات أساسية -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">معلومات الراتب - {{ $payroll->period }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>الموظف:</th>
                                        <td>{{ $payroll->employee->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>الفرع:</th>
                                        <td>{{ $payroll->branch->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>الفترة:</th>
                                        <td>{{ $payroll->period }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>الحالة:</th>
                                        <td>
                                            <span
                                                class="badge bg-{{ $payroll->status == 'paid' ? 'success' : ($payroll->status == 'approved' ? 'info' : 'secondary') }} fs-6">
                                                {{ $payroll->status_name }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if ($payroll->paid_at)
                                        <tr>
                                            <th>تاريخ الصرف:</th>
                                            <td>{{ $payroll->paid_at->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <th>طريقة الدفع:</th>
                                            <td>{{ $payroll->payment_method_name }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تفاصيل الراتب -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">تفاصيل الراتب</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <!-- الراتب الأساسي -->
                                <tr class="table-light">
                                    <td colspan="2"><strong>الراتب الأساسي</strong></td>
                                    <td class="text-end"><strong>{{ number_format($payroll->basic_salary, 2) }}</strong>
                                    </td>
                                </tr>

                                <!-- البدلات -->
                                <tr class="table-success">
                                    <td colspan="3"><strong>البدلات والإضافات</strong></td>
                                </tr>
                                @if ($payroll->allowances_details)
                                    @foreach ($payroll->allowances_details as $allowance)
                                        <tr>
                                            <td width="50"></td>
                                            <td>{{ $allowance['name'] }}</td>
                                            <td class="text-end text-success">+{{ number_format($allowance['amount'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if ($payroll->bonuses_details)
                                    @foreach ($payroll->bonuses_details as $bonus)
                                        <tr>
                                            <td></td>
                                            <td>{{ $bonus['name'] }}</td>
                                            <td class="text-end text-success">+{{ number_format($bonus['amount'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if ($payroll->overtime_amount > 0)
                                    <tr>
                                        <td></td>
                                        <td>أجر إضافي ({{ $payroll->overtime_hours }} ساعة)</td>
                                        <td class="text-end text-success">
                                            +{{ number_format($payroll->overtime_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="table-light">
                                    <td></td>
                                    <td><strong>إجمالي الإضافات</strong></td>
                                    <td class="text-end text-success">
                                        <strong>+{{ number_format($payroll->total_additions, 2) }}</strong></td>
                                </tr>

                                <!-- الخصومات -->
                                <tr class="table-danger">
                                    <td colspan="3"><strong>الخصومات</strong></td>
                                </tr>
                                @if ($payroll->deductions_details)
                                    @foreach ($payroll->deductions_details as $deduction)
                                        <tr>
                                            <td></td>
                                            <td>{{ $deduction['name'] }}</td>
                                            <td class="text-end text-danger">-{{ number_format($deduction['amount'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                @if ($payroll->advances_deducted > 0)
                                    <tr>
                                        <td></td>
                                        <td>استقطاع سلفة</td>
                                        <td class="text-end text-danger">
                                            -{{ number_format($payroll->advances_deducted, 2) }}</td>
                                    </tr>
                                @endif
                                @if ($payroll->absence_deduction > 0)
                                    <tr>
                                        <td></td>
                                        <td>خصم غياب ({{ $payroll->absence_days }} يوم)</td>
                                        <td class="text-end text-danger">
                                            -{{ number_format($payroll->absence_deduction, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="table-light">
                                    <td></td>
                                    <td><strong>إجمالي الخصومات</strong></td>
                                    <td class="text-end text-danger">
                                        <strong>-{{ number_format($payroll->total_deductions, 2) }}</strong></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan="2"><strong class="fs-5">صافي الراتب</strong></td>
                                    <td class="text-end"><strong
                                            class="fs-4">{{ number_format($payroll->net_salary, 2) }} د.ع</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- الإجراءات -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">الإجراءات</h5>
                    </div>
                    <div class="card-body">
                        @if ($payroll->status == 'draft')
                            <form action="{{ route('payroll.approve', $payroll) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check"></i> اعتماد الكشف
                                </button>
                            </form>
                            <form action="{{ route('payroll.cancel', $payroll) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('هل أنت متأكد؟')">
                                    <i class="fas fa-times"></i> إلغاء
                                </button>
                            </form>
                        @elseif($payroll->status == 'approved')
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                                data-bs-target="#payModal">
                                <i class="fas fa-dollar-sign"></i> صرف الراتب
                            </button>
                        @elseif($payroll->status == 'paid')
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle"></i>
                                تم صرف الراتب بتاريخ {{ $payroll->paid_at->format('Y-m-d') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ملخص -->
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">ملخص</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>الراتب الأساسي:</span>
                                <strong>{{ number_format($payroll->basic_salary, 2) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between text-success">
                                <span>الإضافات:</span>
                                <strong>+{{ number_format($payroll->total_additions, 2) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between text-danger">
                                <span>الخصومات:</span>
                                <strong>-{{ number_format($payroll->total_deductions, 2) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between bg-light">
                                <span><strong>الصافي:</strong></span>
                                <strong class="text-primary">{{ number_format($payroll->net_salary, 2) }}</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal الصرف -->
    <div class="modal fade" id="payModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('payroll.pay', $payroll) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">صرف الراتب</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            صافي الراتب: <strong>{{ number_format($payroll->net_salary, 2) }} د.ع</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">طريقة الدفع</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">نقداً</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                                <option value="check">شيك</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رقم المرجع (اختياري)</label>
                            <input type="text" name="payment_reference" class="form-control"
                                placeholder="رقم الحوالة أو الشيك">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تأكيد الصرف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
