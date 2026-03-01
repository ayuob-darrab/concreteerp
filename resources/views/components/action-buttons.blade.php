{{-- Action Buttons Component --}}
@props([
    'showUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'printUrl' => null,
    'customActions' => [],
    'size' => 'sm',
])

<div class="btn-group" role="group">
    @if ($showUrl)
        <a href="{{ $showUrl }}" class="btn btn-{{ $size }} btn-outline-info" title="عرض">
            <i class="fas fa-eye"></i>
        </a>
    @endif

    @if ($editUrl)
        <a href="{{ $editUrl }}" class="btn btn-{{ $size }} btn-outline-primary" title="تعديل">
            <i class="fas fa-edit"></i>
        </a>
    @endif

    @if ($printUrl)
        <a href="{{ $printUrl }}" class="btn btn-{{ $size }} btn-outline-secondary" title="طباعة"
            target="_blank">
            <i class="fas fa-print"></i>
        </a>
    @endif

    @foreach ($customActions as $action)
        <a href="{{ $action['url'] }}"
            class="btn btn-{{ $size }} btn-outline-{{ $action['color'] ?? 'secondary' }}"
            title="{{ $action['title'] ?? '' }}"
            @if (isset($action['target'])) target="{{ $action['target'] }}" @endif
            @if (isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif>
            <i class="fas fa-{{ $action['icon'] }}"></i>
            @if (isset($action['text']))
                {{ $action['text'] }}
            @endif
        </a>
    @endforeach

    @if ($deleteUrl)
        <form action="{{ $deleteUrl }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-{{ $size }} btn-outline-danger btn-delete" title="حذف">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endif
</div>
