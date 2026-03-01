@extends('layouts.app')

@section('page-title', 'السلف الموافق عليها - للدفع')

@section('content')

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="panel h-full w-full">
            <div class="mb-5 flex items-center justify-between">
                <h5 class="text-lg font-semibold dark:text-white-light">
                    💰 السلف الموافق عليها - للدفع
                </h5>
                <div class="flex gap-2">
                    <a href="{{ route('advances.create') }}" class="btn btn-primary">
                        ➕ سلفة جديدة
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-5">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger mb-5">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            @if ($advances->count() > 0)
                <div class="table-responsive">
                    <table class="table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم السلفة</th>
                                <th>المستفيد</th>
                                <th>نوع المستفيد</th>
                                <th>المبلغ</th>
                                <th>المتبقي</th>
                                <th>الحالة</th>
                                <th>تاريخ الطلب</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($advances as $advance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('advances.show', $advance) }}"
                                            class="text-primary font-semibold hover:underline">
                                            {{ $advance->advance_number }}
                                        </a>
                                    </td>
                                    <td>{{ $advance->beneficiary_name }}</td>
                                    <td>
                                        @switch($advance->beneficiary_type)
                                            @case('employee')
                                                <span class="badge bg-info">موظف</span>
                                            @break

                                            @case('contractor')
                                                <span class="badge bg-primary">مقاول</span>
                                            @break

                                            @case('supplier')
                                                <span class="badge bg-warning">مورد</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td class="font-semibold">{{ number_format($advance->amount) }} د.ع</td>
                                    <td class="text-danger font-semibold">{{ number_format($advance->remaining_amount) }}
                                        د.ع</td>
                                    <td>
                                        @if ($advance->status == 'approved')
                                            <span class="badge bg-info">موافق عليها</span>
                                        @elseif ($advance->status == 'active')
                                            <span class="badge bg-success">نشطة</span>
                                        @endif
                                    </td>
                                    <td>{{ $advance->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('advances.payment-form', $advance) }}"
                                                class="btn btn-success btn-sm" title="تسديد دفعة">
                                                💵 دفع
                                            </a>
                                            <a href="{{ route('advances.show', $advance) }}"
                                                class="btn btn-outline-primary btn-sm" title="عرض التفاصيل">
                                                👁️
                                            </a>
                                            <a href="{{ route('advances.print', $advance) }}"
                                                class="btn btn-outline-secondary btn-sm" title="طباعة" target="_blank">
                                                🖨️
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10">
                    <div class="text-6xl mb-4">✅</div>
                    <h4 class="text-lg font-semibold mb-2">لا توجد سلف تحتاج دفع</h4>
                    <p class="text-gray-500">جميع السلف الموافق عليها تم دفعها بالكامل</p>
                </div>
            @endif
        </div>
    </div>

@endsection
