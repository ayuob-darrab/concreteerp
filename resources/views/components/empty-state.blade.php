{{-- Empty State Component --}}
@props([
    'icon' => 'inbox',
    'title' => 'لا توجد بيانات',
    'message' => 'لم يتم العثور على نتائج',
    'actionUrl' => null,
    'actionText' => null,
])

<div class="empty-state text-center py-5">
    <div class="empty-icon mb-4">
        <i class="fas fa-{{ $icon }} fa-4x text-muted"></i>
    </div>
    <h4 class="empty-title">{{ $title }}</h4>
    <p class="empty-message text-muted">{{ $message }}</p>

    @if ($actionUrl && $actionText)
        <a href="{{ $actionUrl }}" class="btn btn-primary mt-3">
            <i class="fas fa-plus me-2"></i>
            {{ $actionText }}
        </a>
    @endif
</div>

<style>
    .empty-state {
        padding: 3rem 1rem;
    }

    .empty-icon {
        opacity: 0.5;
    }

    .empty-title {
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
</style>
