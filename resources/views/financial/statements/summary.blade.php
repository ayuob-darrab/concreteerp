@extends('layouts.app')

@section('title', 'ملخص الأرصدة')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="fas fa-chart-pie me-2"></i>ملخص الأرصدة</h4>
            <a href="{{ route('statements.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-1"></i> تفاصيل الأرصدة
            </a>
        </div>

        <div class="row">
            @foreach ($summary as $type => $data)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-{{ $data['color'] ?? 'secondary' }} text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-{{ $data['icon'] ?? 'user' }} me-2"></i>
                                {{ $data['label'] }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-6 border-end">
                                    <small class="text-muted d-block">عدد الحسابات</small>
                                    <h3>{{ $data['count'] }}</h3>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">صافي الرصيد</small>
                                    <h3 class="{{ $data['net_balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format(abs($data['net_balance']), 0) }}
                                    </h3>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-25 rounded p-2 me-2">
                                            <i class="fas fa-arrow-down text-success"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">إجمالي المدين (له)</small>
                                            <strong
                                                class="text-success">{{ number_format($data['total_debits'], 0) }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-25 rounded p-2 me-2">
                                            <i class="fas fa-arrow-up text-danger"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">إجمالي الدائن (عليه)</small>
                                            <strong
                                                class="text-danger">{{ number_format($data['total_credits'], 0) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="{{ route('statements.index', ['account_type' => $type]) }}"
                                class="btn btn-outline-{{ $data['color'] ?? 'secondary' }} w-100">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ملخص عام -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>الملخص العام</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">إجمالي الحسابات</small>
                            <h2>{{ collect($summary)->sum('count') }}</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-success bg-opacity-10 rounded">
                            <small class="text-muted d-block">إجمالي المدين</small>
                            <h2 class="text-success">{{ number_format(collect($summary)->sum('total_debits'), 0) }}</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-danger bg-opacity-10 rounded">
                            <small class="text-muted d-block">إجمالي الدائن</small>
                            <h2 class="text-danger">{{ number_format(collect($summary)->sum('total_credits'), 0) }}</h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-primary bg-opacity-10 rounded">
                            <small class="text-muted d-block">صافي الأرصدة</small>
                            @php $netTotal = collect($summary)->sum('net_balance'); @endphp
                            <h2 class="{{ $netTotal >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format(abs($netTotal), 0) }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
