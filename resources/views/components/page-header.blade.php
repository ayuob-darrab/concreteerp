{{-- Page Header Component --}}
@props([
    'title' => '',
    'subtitle' => null,
    'icon' => null,
    'breadcrumb' => [],
    'actions' => [],
])

<div class="page-header mb-4">
    {{-- Breadcrumb --}}
    @if (count($breadcrumb) > 0)
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}"><i class="fas fa-home"></i></a>
                </li>
                @foreach ($breadcrumb as $item)
                    @if (isset($item['url']) && $item['url'])
                        <li class="breadcrumb-item">
                            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                        </li>
                    @else
                        <li class="breadcrumb-item active">{{ $item['label'] }}</li>
                    @endif
                @endforeach
            </ol>
        </nav>
    @endif

    {{-- Title & Actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h2 class="page-title mb-0">
                @if ($icon)
                    <i class="fas fa-{{ $icon }} me-2 text-primary"></i>
                @endif
                {{ $title }}
            </h2>
            @if ($subtitle)
                <p class="text-muted mb-0 mt-1">{{ $subtitle }}</p>
            @endif
        </div>

        @if (count($actions) > 0)
            <div class="page-actions d-flex gap-2 flex-wrap">
                @foreach ($actions as $action)
                    <a href="{{ $action['url'] }}"
                        class="btn btn-{{ $action['color'] ?? 'primary' }} {{ $action['class'] ?? '' }}"
                        @if (isset($action['target'])) target="{{ $action['target'] }}" @endif>
                        @if (isset($action['icon']))
                            <i class="fas fa-{{ $action['icon'] }} me-1"></i>
                        @endif
                        {{ $action['label'] }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Slot for custom actions --}}
        {{ $slot }}
    </div>
</div>

<style>
    .page-header {
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .page-title {
        font-weight: 700;
        color: var(--text-primary);
    }

    .breadcrumb {
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .breadcrumb-item a {
        color: var(--text-secondary);
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: var(--primary);
    }
</style>
