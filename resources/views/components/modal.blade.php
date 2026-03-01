{{-- Modal Component --}}
@props([
    'id' => 'modal',
    'title' => '',
    'size' => '', // sm, lg, xl
    'centered' => true,
    'scrollable' => false,
    'static' => false,
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true"
    @if ($static) data-bs-backdrop="static" data-bs-keyboard="false" @endif>
    <div
        class="modal-dialog {{ $size ? 'modal-' . $size : '' }} {{ $centered ? 'modal-dialog-centered' : '' }} {{ $scrollable ? 'modal-dialog-scrollable' : '' }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @isset($footer)
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
