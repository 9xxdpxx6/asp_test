{{-- $modalId, $gridId, optional $helpText --}}
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Выберите иконку</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body pt-2">
                @if(!empty($helpText))
                    <p class="text-muted small mb-2">{{ $helpText }}</p>
                @endif
                <div class="home-feature-icon-picker-grid" id="{{ $gridId }}"></div>
            </div>
        </div>
    </div>
</div>
