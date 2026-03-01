@extends('layouts.app')

@section('title', 'الطلبات في التفاوض')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-comments text-primary"></i>
                            الطلبات في التفاوض
                        </h4>
                        <p class="text-muted mb-0">الطلبات التي يجري التفاوض عليها</p>
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
                                    <th>سعر الفرع</th>
                                    <th>سعر العميل المقترح</th>
                                    <th>الفرق</th>
                                    <th>آخر تحديث</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    @php
                                        $diff = ($order->branch_price ?? 0) - ($order->requester_price ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->customer_name ?? 'غير محدد' }}</td>
                                        <td>{{ number_format($order->quantity, 2) }} م³</td>
                                        <td>{{ number_format($order->branch_price ?? 0, 2) }} د.ع</td>
                                        <td>{{ number_format($order->requester_price ?? 0, 2) }} د.ع</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $diff > 0 ? 'danger' : ($diff < 0 ? 'success' : 'secondary') }}">
                                                {{ number_format(abs($diff), 2) }} د.ع
                                            </span>
                                        </td>
                                        <td>{{ $order->updated_at->diffForHumans() }}</td>
                                        <td>
                                            <a href="{{ route('orders.negotiation.show', $order) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-handshake"></i> متابعة التفاوض
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-handshake fa-4x text-muted mb-3"></i>
                        <h5>لا توجد طلبات في التفاوض حالياً</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
