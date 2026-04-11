@extends('layouts.app')

@section('page-title', 'طلبات العمل')

@section('content')
    <div class="panel mt-6">
        <!-- <div class="mb-5"> -->
            <h2 class="text-lg font-semibold dark:text-white">طلبات العمل</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="alert alert-info mb-4">{{ session('info') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الحالة</th>
                        <th>الكمية</th>
                        <th>الفرع</th>
                        <th>التاريخ</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $o)
                        <tr>
                            <td>{{ $o->id }}</td>
                            <td>{{ $o->status_label ?: '—' }}</td>
                            <td>{{ $o->quantity }}</td>
                            <td>{{ $o->branch->branch_name ?? '—' }}</td>
                            <td>{{ $o->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('work-orders.show', $o) }}" class="btn btn-sm btn-outline-primary">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500">لا توجد طلبات.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
@endsection
