@extends('layouts.app')

@section('title', 'الطلبات المعلقة للمراجعة')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-clock text-warning"></i>
                            الطلبات المعلقة للمراجعة
                        </h4>
                        <p class="text-muted mb-0">الطلبات الجديدة التي تحتاج لمراجعة الفرع</p>
                    </div>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                @if ($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>العميل</th>
                                    <th>الهاتف</th>
                                    <th>الكمية</th>
                                    <th>نوع الخلطة</th>
                                    <th>تاريخ الطلب</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->customer_name ?? 'غير محدد' }}</td>
                                        <td>{{ $order->customer_phone ?? '-' }}</td>
                                        <td>{{ number_format($order->quantity, 2) }} م³</td>
                                        <td>{{ $order->concreteMix->name ?? $order->classification }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('orders.negotiation.show', $order) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> مراجعة
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h5>لا توجد طلبات معلقة للمراجعة</h5>
                        <p class="text-muted">جميع الطلبات تمت مراجعتها</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
