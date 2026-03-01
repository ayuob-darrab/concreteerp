@extends('layouts.app')

@section('title', 'الطلبات بانتظار رد العميل')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-hourglass-half text-warning"></i>
                            الطلبات بانتظار رد العميل
                        </h4>
                        <p class="text-muted mb-0">الطلبات التي تم إرسال عرض سعر لها وبانتظار الرد</p>
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
                                    <th>السعر المعروض</th>
                                    <th>تاريخ العرض</th>
                                    <th>مدة الانتظار</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->customer_name ?? 'غير محدد' }}</td>
                                        <td>{{ number_format($order->quantity, 2) }} م³</td>
                                        <td>{{ number_format($order->branch_price ?? 0, 2) }} د.ع</td>
                                        <td>{{ $order->branch_offer_sent_at ? \Carbon\Carbon::parse($order->branch_offer_sent_at)->format('Y-m-d H:i') : '-' }}
                                        </td>
                                        <td>
                                            @if ($order->branch_offer_sent_at)
                                                @php
                                                    $hours = \Carbon\Carbon::parse(
                                                        $order->branch_offer_sent_at,
                                                    )->diffInHours(now());
                                                @endphp
                                                <span
                                                    class="badge bg-{{ $hours > 48 ? 'danger' : ($hours > 24 ? 'warning' : 'success') }}">
                                                    {{ $hours }} ساعة
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.negotiation.show', $order) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5>لا توجد طلبات بانتظار رد العميل</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
