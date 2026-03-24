{{--
    Параметры:
    $modalId — id модалки (уникальный на странице)
    $formId — id формы удаления
    $title — заголовок (по умолчанию «Удалить запись?»)
    $message — текст в теле модалки
--}}
@php
    $modalId = $modalId ?? 'confirmDeleteModal';
    $formId = $formId ?? 'deleteRecordForm';
    $title = $title ?? 'Удалить запись?';
    $message = $message ?? 'Это действие нельзя отменить.';
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">{{ $message }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" data-submit-delete-form="{{ $formId }}">Удалить</button>
            </div>
        </div>
    </div>
</div>
@once
    <script>
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-submit-delete-form]');
        if (!btn) {
            return;
        }
        var id = btn.getAttribute('data-submit-delete-form');
        var form = id && document.getElementById(id);
        if (form) {
            form.submit();
        }
    });
    </script>
@endonce
