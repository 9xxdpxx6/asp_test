{{-- Модалка выбора иконки (общая разметка; набор иконок задаётся в hero-settings-scripts) --}}
<div class="modal fade" id="heroIconPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold">Выберите иконку</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted small mb-2">Подойдёт для полоски под баннером: филиалы, лицензия, инструкторы и т.д.</p>
                <div class="hero-icon-picker-grid" id="heroIconPickerGrid"></div>
            </div>
        </div>
    </div>
</div>
