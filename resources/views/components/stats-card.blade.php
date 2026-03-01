{{-- Stats Card Component --}}
@props([
    'title' => '',
    'value' => 0,
    'icon' => 'chart-bar',
    'color' => 'primary',
    'format' => 'number', // number, currency, percent
    'trend' => null, // ['direction' => 'up/down', 'value' => 5]
    'link' => null,
])

<div class="card stat-card h-100 border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6 class="text-muted mb-1 small">{{ $title }}</h6>
                <h3 class="mb-0 fw-bold">
                    @switch($format)
                        @case('currency')
                            {{ number_format($value, 2) }}
                            <small class="fs-6 fw-normal text-muted">د.ع</small>
                        @break

                        @case('percent')
                            {{ $value }}%
                        @break

                        @default
                            {{ number_format($value) }}
                    @endswitch
                </h3>

                @if ($trend)
                    <div class="mt-2">
                        <small class="text-{{ $trend['direction'] === 'up' ? 'success' : 'danger' }}">
                            <i class="fas fa-arrow-{{ $trend['direction'] }} me-1"></i>
                            {{ $trend['value'] }}%
                        </small>
                        <small class="text-muted">مقارنة بالفترة السابقة</small>
                    </div>
                @endif
            </div>

            <div class="stat-icon bg-{{ $color }} bg-opacity-10 rounded-circle p-3">
                <i class="fas fa-{{ $icon }} fa-lg text-{{ $color }}"></i>
            </div>
        </div>

        @if ($link)
            <a href="{{ $link }}" class="stretched-link"></a>
        @endif
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
