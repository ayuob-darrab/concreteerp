{{-- Delete Button with Confirmation --}}
@props([
    'url' => '',
    'message' => 'هل أنت متأكد من الحذف؟ هذا الإجراء لا يمكن التراجع عنه.',
    'title' => 'حذف',
    'icon' => 'trash',
    'class' => 'btn-outline-danger',
    'size' => 'sm',
])

<form action="{{ $url }}" method="POST" class="d-inline delete-form">
    @csrf
    @method('DELETE')
    <button type="button" class="btn btn-{{ $size }} {{ $class }} btn-delete-confirm"
        data-message="{{ $message }}" title="{{ $title }}">
        <i class="fas fa-{{ $icon }}"></i>
        {{ $slot }}
    </button>
</form>

@pushOnce('scripts')
    <script>
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete-confirm')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-delete-confirm');
                const form = btn.closest('form');
                const message = btn.dataset.message || 'هل أنت متأكد من الحذف؟';

                if (typeof confirmAction === 'function') {
                    confirmAction(message, function() {
                        form.submit();
                    });
                } else if (confirm(message)) {
                    form.submit();
                }
            }
        });
    </script>
@endPushOnce
