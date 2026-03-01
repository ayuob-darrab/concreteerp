@extends('layouts.app')

@section('title', 'أوامر اليوم')

@section('content')
    <div class="container-fluid">
        <!-- العنوان -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="fas fa-calendar-day me-2"></i>أوامر اليوم</h4>
                <p class="text-muted mb-0">{{ now()->format('Y-m-d') }} - {{ now()->locale('ar')->dayName }}</p>
            </div>
            <div>
                <a href="{{ route('work-jobs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list"></i> جميع الأوامر
                </a>
            </div>
        </div>

        <!-- ملخص اليوم -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h2>{{ $jobs->count() }}</h2>
                        <small>إجمالي أوامر اليوم</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h2>{{ $jobs->whereIn('status', ['pending', 'materials_reserved', 'in_progress', 'partially_completed'])->count() }}
                        </h2>
                        <small>قيد التنفيذ</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h2>{{ $jobs->where('status', 'completed')->count() }}</h2>
                        <small>مكتملة</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h2>{{ number_format($jobs->sum('total_quantity'), 0) }} م³</h2>
                        <small>إجمالي الكميات</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- قائمة الأوامر -->
        <div class="row">
            @forelse($jobs as $job)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-{{ $job->status_badge }}">
                        <div class="card-header bg-{{ $job->status_badge }} text-white d-flex justify-content-between">
                            <span>{{ $job->job_number }}</span>
                            <span>{{ $job->scheduled_time ? $job->scheduled_time->format('H:i') : '--:--' }}</span>
                        </div>
                        <div class="card-body">
                            <!-- التقدم -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>التقدم</small>
                                    <small>{{ number_format($job->completion_percentage, 0) }}%</small>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" style="width: {{ $job->completion_percentage }}%">
                                    </div>
                                </div>
                            </div>

                            <!-- المعلومات -->
                            <p class="mb-2">
                                <i class="fas fa-user text-muted me-2"></i>
                                {{ $job->customer_name ?? 'عميل غير محدد' }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                {{ Str::limit($job->location_address, 50) }}
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-cube text-muted me-2"></i>
                                {{ $job->concreteType->name ?? '-' }} |
                                <strong>{{ number_format($job->total_quantity, 1) }}</strong> م³
                            </p>

                            <!-- الشحنات -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge bg-secondary">
                                    <i class="fas fa-truck"></i> {{ $job->total_shipments }} شحنة
                                </span>
                                <span class="badge bg-success">
                                    منفذ: {{ number_format($job->executed_quantity, 1) }} م³
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('work-jobs.show', $job) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> عرض
                                </a>
                                @if ($job->can_add_shipment)
                                    <a href="{{ route('shipments.create', $job) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus"></i> شحنة
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد أوامر عمل لليوم</h5>
                            <a href="{{ route('work-jobs.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus"></i> إنشاء أمر عمل
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
