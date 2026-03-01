@extends('layouts.app')

@section('title', 'تفاصيل المقاول - ' . $contractor->contractor_name)

@section('content')
    <div class="container-fluid py-4" dir="rtl">
        {{-- رأس الصفحة --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div
                                        class="avatar avatar-xl bg-gradient-primary me-3 d-flex align-items-center justify-content-center">
                                        <span
                                            class="text-white text-lg">{{ mb_substr($contractor->contractor_name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <h4 class="mb-0">{{ $contractor->contractor_name }}</h4>
                                        <p class="text-sm text-muted mb-0">{{ $contractor->code }}</p>
                                        <span
                                            class="badge bg-gradient-{{ $contractor->status_color }}">{{ $contractor->status_label }}</span>
                                        @if ($contractor->classification)
                                            <span class="badge bg-gradient-info">تصنيف
                                                {{ $contractor->classification }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('contractors.edit', $contractor) }}"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i> تعديل
                                </a>
                                <a href="{{ route('contractors.statement', $contractor) }}"
                                    class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-file-invoice me-1"></i> كشف الحساب
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- المعلومات المالية --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6>المعلومات المالية</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <p class="text-sm mb-0 text-muted">الرصيد الحالي</p>
                                <h5
                                    class="{{ ($contractor->account?->current_balance ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($contractor->account?->current_balance ?? 0, 2) }} د.ع
                                </h5>
                            </div>
                            <div class="col-6 mb-3">
                                <p class="text-sm mb-0 text-muted">الحد الائتماني</p>
                                <h5>{{ number_format($contractor->credit_limit ?? 0, 2) }} د.ع</h5>
                            </div>
                            <div class="col-6 mb-3">
                                <p class="text-sm mb-0 text-muted">المتاح</p>
                                <h5 class="text-info">{{ number_format($contractor->available_credit ?? 0, 2) }} د.ع</h5>
                            </div>
                            <div class="col-6 mb-3">
                                <p class="text-sm mb-0 text-muted">مدة السداد</p>
                                <h5>{{ $contractor->payment_terms ?? 30 }} يوم</h5>
                            </div>
                        </div>
                        @if ($contractor->is_over_credit_limit)
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                تجاوز الحد الائتماني
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- معلومات الاتصال --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6>معلومات الاتصال</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">الهاتف</span>
                                <span>{{ $contractor->phone }}</span>
                            </li>
                            @if ($contractor->phone_secondary)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">هاتف ثانوي</span>
                                    <span>{{ $contractor->phone_secondary }}</span>
                                </li>
                            @endif
                            @if ($contractor->email)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">البريد الإلكتروني</span>
                                    <span>{{ $contractor->email }}</span>
                                </li>
                            @endif
                            @if ($contractor->address)
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted">العنوان</span>
                                    <span>{{ $contractor->city ?? '' }} {{ $contractor->region ?? '' }}</span>
                                </li>
                            @endif
                        </ul>
                        @if ($contractor->contact_person)
                            <hr>
                            <h6 class="text-sm">جهة الاتصال</h6>
                            <p class="mb-0">{{ $contractor->contact_person }}</p>
                            <p class="text-sm text-muted mb-0">{{ $contractor->contact_position }}</p>
                            <p class="text-sm mb-0">{{ $contractor->contact_phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- الإحصائيات --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6>إحصائيات الطلبات</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4 mb-3">
                                <h4 class="mb-0">{{ $contractor->total_orders ?? 0 }}</h4>
                                <p class="text-sm text-muted mb-0">إجمالي</p>
                            </div>
                            <div class="col-4 mb-3">
                                <h4 class="mb-0 text-success">{{ $contractor->completed_orders ?? 0 }}</h4>
                                <p class="text-sm text-muted mb-0">مكتملة</p>
                            </div>
                            <div class="col-4 mb-3">
                                <h4 class="mb-0 text-danger">{{ $contractor->cancelled_orders ?? 0 }}</h4>
                                <p class="text-sm text-muted mb-0">ملغاة</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <p class="text-sm mb-0 text-muted">إجمالي المشتريات</p>
                                <h6>{{ number_format($contractor->total_purchases ?? 0, 2) }} د.ع</h6>
                            </div>
                            <div class="col-6">
                                <p class="text-sm mb-0 text-muted">إجمالي المدفوع</p>
                                <h6>{{ number_format($contractor->total_paid ?? 0, 2) }} د.ع</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- آخر المعاملات والطلبات --}}
        <div class="row">
            {{-- آخر المعاملات المالية --}}
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>آخر المعاملات المالية</h6>
                        <a href="{{ route('contractors.statement', $contractor) }}" class="btn btn-link btn-sm p-0">عرض
                            الكل</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            التاريخ</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            النوع</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">
                                            المبلغ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contractor->account?->transactions ?? [] as $transaction)
                                        <tr>
                                            <td class="text-sm">{{ $transaction->created_at->format('Y-m-d') }}</td>
                                            <td class="text-sm">{{ $transaction->description }}</td>
                                            <td class="text-end">
                                                <span
                                                    class="{{ $transaction->transaction_type === 'credit' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->transaction_type === 'credit' ? '+' : '-' }}
                                                    {{ number_format($transaction->amount, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3">
                                                <p class="text-muted mb-0">لا توجد معاملات</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- آخر الطلبات --}}
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between">
                        <h6>آخر الطلبات</h6>
                        <a href="#" class="btn btn-link btn-sm p-0">عرض الكل</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">رقم
                                            الطلب</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            التاريخ</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                            الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contractor->workOrders ?? [] as $order)
                                        <tr>
                                            <td class="text-sm">
                                                <a href="#">#{{ $order->id }}</a>
                                            </td>
                                            <td class="text-sm">{{ $order->created_at->format('Y-m-d') }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-gradient-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                                    {{ $order->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-3">
                                                <p class="text-muted mb-0">لا توجد طلبات</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
