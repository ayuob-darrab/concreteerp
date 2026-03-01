@extends('layouts.app')

@section('title', 'الطلبات الجاهزة للموافقة النهائية')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-check-double text-success"></i>
                            الطلبات الجاهزة للموافقة النهائية
                        </h4>
                        <p class="text-muted mb-0">الطلبات التي وافق عليها العميل وتحتاج لموافقة نهائية</p>
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
                                    <th>الكمية</th>
                                    <th>السعر النهائي</th>
                                    <th>الإجمالي</th>
                                    <th>تاريخ موافقة العميل</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->customer_name ?? 'غير محدد' }}</td>
                                        <td>{{ number_format($order->quantity, 2) }} م³</td>
                                        <td>{{ number_format($order->final_price ?? 0, 2) }} د.ع</td>
                                        <td class="fw-bold">
                                            {{ number_format(($order->final_price ?? 0) * $order->quantity, 2) }} د.ع</td>
                                        <td>{{ $order->requester_response_at ? \Carbon\Carbon::parse($order->requester_response_at)->format('Y-m-d H:i') : '-' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.negotiation.show', $order) }}"
                                                class="btn btn-sm btn-success">
                                                <i class="fas fa-check-double"></i> اعتماد
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-check fa-4x text-muted mb-3"></i>
                        <h5>لا توجد طلبات جاهزة للموافقة النهائية</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
