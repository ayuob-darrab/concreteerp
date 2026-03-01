@extends('layouts.app')

@section('title', 'التقارير')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    التقارير والإحصائيات
                </h2>
            </div>
        </div>

        @foreach ($reportCategories as $categoryKey => $category)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-{{ $category['icon'] }} me-2"></i>
                        {{ $category['title'] }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($category['reports'] as $report)
                            <div class="col-md-4 col-lg-3 mb-3">
                                <a href="{{ route($report['route']) }}" class="text-decoration-none">
                                    <div class="card h-100 report-card border-0 shadow-sm">
                                        <div class="card-body text-center py-4">
                                            <div class="report-icon mb-3">
                                                <i
                                                    class="fas fa-{{ $report['icon'] ?? 'file-alt' }} fa-2x text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-0">{{ $report['title'] }}</h6>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .report-card {
            transition: all 0.3s ease;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .report-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
    </style>
@endsection
