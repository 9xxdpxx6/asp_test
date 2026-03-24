@extends('layouts.admin')

@section('title', 'Наши преимущества')

@php
    $rawAdvantages = old('advantages');

    if (is_array($rawAdvantages) && count($rawAdvantages) > 0) {
        $formAdvantages = collect($rawAdvantages)->map(function ($item) {
            $image = $item['existing_image'] ?? null;

            if ($image) {
                if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
                    $imageUrl = $image;
                } elseif (\Illuminate\Support\Str::startsWith($image, ['/'])) {
                    $imageUrl = url(ltrim($image, '/'));
                } else {
                    $imageUrl = url('storage/' . ltrim($image, '/'));
                }
            } else {
                $imageUrl = null;
            }

            return [
                'id' => $item['id'] ?? null,
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
                'existing_image' => $image,
                'image_url' => $imageUrl,
                'pending_delete' => !empty($item['pending_delete']),
                'image_on_left' => array_key_exists('image_on_left', $item)
                    ? filter_var($item['image_on_left'], FILTER_VALIDATE_BOOLEAN)
                    : true,
            ];
        })->values()->all();
    } else {
        $formAdvantages = $advantages->map(function ($advantage) {
            return [
                'id' => $advantage->id,
                'title' => $advantage->title,
                'description' => $advantage->description,
                'existing_image' => $advantage->image,
                'image_url' => $advantage->image_url,
                'pending_delete' => false,
                'image_on_left' => (bool) $advantage->image_on_left,
            ];
        })->values()->all();
    }
@endphp

@section('style')
<style>
    .advantages-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .advantage-item {
        border: 1px solid #dee2e6;
        border-radius: 0.85rem;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
        transition: opacity 0.2s, border-color 0.2s, background-color 0.2s;
    }

    .advantage-item.sortable-ghost {
        opacity: 0.35;
    }

    .advantage-item.is-pending-delete {
        border-color: #f1aeb5;
        background: #fff5f5;
    }

    .advantage-item.is-pending-delete .advantage-title-preview,
    .advantage-item.is-pending-delete .form-label,
    .advantage-item.is-pending-delete .advantage-note {
        text-decoration: line-through;
    }

    .advantage-item.is-pending-delete .advantage-body {
        opacity: 0.55;
    }

    .advantage-item.is-pending-delete .advantage-handle {
        opacity: 0.4;
        pointer-events: none;
    }

    .advantage-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem 1rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .advantage-order {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #0d6efd;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .advantage-handle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid #d0d7de;
        background: #fff;
        color: #6c757d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: grab;
        flex-shrink: 0;
    }

    .advantage-handle:active {
        cursor: grabbing;
    }

    .advantage-title-preview {
        min-width: 0;
        flex: 1;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .advantage-body {
        padding: 1rem;
    }

    .photo-upload-area {
        width: 100%;
        aspect-ratio: 16 / 9;
        border: 2px dashed #ced4da;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
        position: relative;
        cursor: pointer;
        transition: all 0.2s;
    }

    .photo-upload-area:hover {
        border-color: #0d6efd;
        background: #f0f7ff;
    }

    .photo-upload-area.has-image {
        padding: 0;
        border-style: solid;
        background: #fff;
    }

    .photo-upload-area .upload-placeholder i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .photo-upload-area .change-overlay {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        padding: 0.5rem;
        font-size: 0.85rem;
        display: none;
    }

    .photo-upload-area.has-image:hover .change-overlay {
        display: block;
    }

    .photo-preview-container {
        width: 100%;
        height: 100%;
        display: none;
    }

    .photo-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .advantage-note {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .advantage-photo-meta {
        margin-bottom: 0.6rem;
    }

    .advantage-photo-meta .form-label {
        display: block;
        margin-bottom: 0.15rem;
    }

    /* Скрыто по умолчанию: классы .badge/.bg-danger из Bootstrap перебивали display:none одного класса */
    .advantage-item:not(.is-pending-delete) .advantage-delete-status {
        display: none !important;
    }

    .advantage-item.is-pending-delete .advantage-delete-status {
        display: inline-flex !important;
    }

    .advantage-body [data-layout-row].flex-row-reverse {
        flex-direction: row-reverse;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Наши преимущества</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <div class="fw-bold mb-2">Не удалось сохранить изменения.</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-4">
                    Меняйте текст, загружайте изображения и перетаскивайте карточки вверх или вниз. Поддерживаются только JPG, JPEG и PNG до 5 МБ.
                </p>

                <div class="alert alert-light border d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="fas fa-up-down text-primary"></i>
                    <span>Чтобы изменить порядок, нажмите на кнопку со стрелками и тяните карточку вверх или вниз.</span>
                </div>

                <form method="POST" action="{{ route('admin.advantages.update') }}" enctype="multipart/form-data" id="advantages-form">
                    @csrf

                    <div class="advantages-list" id="advantages-list">
                        @foreach($formAdvantages as $advantage)
                            <div class="advantage-item {{ $advantage['pending_delete'] ? 'is-pending-delete' : '' }}" data-item>
                                <div class="advantage-head">
                                    <button type="button" class="advantage-handle" data-handle title="Перетащить">
                                        <i class="fas fa-up-down"></i>
                                    </button>
                                    <div class="advantage-order" data-order></div>
                                    <div class="advantage-title-preview" data-title-preview>
                                        {{ $advantage['title'] ?: 'Новое преимущество' }}
                                    </div>
                                    <span class="badge bg-danger advantage-delete-status">Будет удалено после сохранения</span>
                                    <button type="button"
                                            class="btn btn-outline-secondary btn-sm"
                                            data-toggle-layout
                                            title="Поменять местами фото и текст (как на сайте)">
                                        <i class="fas fa-arrows-alt-h me-1"></i><span data-layout-label>{{ $advantage['image_on_left'] ? 'Фото слева' : 'Фото справа' }}</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-remove>
                                        <i class="fas fa-trash me-1"></i>Удалить
                                    </button>
                                </div>

                                <div class="advantage-body">
                                    <input type="hidden" data-field="id" value="{{ $advantage['id'] }}">
                                    <input type="hidden" data-field="existing_image" value="{{ $advantage['existing_image'] }}">
                                    <input type="hidden" data-field="pending_delete" value="{{ $advantage['pending_delete'] ? 1 : 0 }}">
                                    <input type="hidden" data-field="image_on_left" value="{{ $advantage['image_on_left'] ? 1 : 0 }}">

                                    <div class="row g-4 align-items-start {{ !$advantage['image_on_left'] ? 'flex-row-reverse' : '' }}" data-layout-row>
                                        <div class="col-lg-4">
                                            <div class="advantage-photo-meta">
                                                <label class="form-label">Фото</label>
                                                <div class="advantage-note">Рекомендуемый формат: горизонтальное изображение.</div>
                                            </div>
                                            <div class="photo-upload-area {{ $advantage['image_url'] ? 'has-image' : '' }} mb-3" data-photo-area>
                                                <div class="upload-placeholder" {!! $advantage['image_url'] ? 'style="display:none"' : '' !!}>
                                                    <i class="fas fa-camera"></i>
                                                    <div>Нажмите для загрузки</div>
                                                    <small class="text-muted">JPG, JPEG, PNG до 5 МБ</small>
                                                </div>
                                                <div class="photo-preview-container" data-preview-container {!! $advantage['image_url'] ? 'style="display:block"' : '' !!}>
                                                    <img src="{{ $advantage['image_url'] }}" alt="Предпросмотр" data-preview-image>
                                                </div>
                                                <div class="change-overlay">
                                                    <i class="fas fa-camera me-1"></i>Заменить фото
                                                </div>
                                            </div>

                                            <label class="form-label">Фото</label>
                                            <input type="file"
                                                   class="form-control"
                                                   data-field="image"
                                                   accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                                   style="display: none;">
                                        </div>

                                        <div class="col-lg-8">
                                            <div class="mb-3">
                                                <label class="form-label">Заголовок</label>
                                                <input type="text"
                                                       class="form-control"
                                                       data-field="title"
                                                       maxlength="255"
                                                       value="{{ $advantage['title'] }}"
                                                       placeholder="Например: Современный автопарк">
                                            </div>

                                            <div>
                                                <label class="form-label">Описание</label>
                                                <textarea class="form-control"
                                                          rows="5"
                                                          data-field="description"
                                                          placeholder="Описание преимущества">{{ $advantage['description'] }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="button" class="btn btn-outline-primary" id="add-advantage">
                            <i class="fas fa-plus me-1"></i>Добавить преимущество
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Сохранить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="advantageDeleteConfirmModal" tabindex="-1" aria-labelledby="advantageDeleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="advantageDeleteConfirmModalLabel">Удалить преимущество?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="advantageDeleteConfirmModalText">Оно перестанет отображаться на сайте после нажатия «Сохранить». До сохранения можно отменить пометку кнопкой «Вернуть».</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="advantageDeleteConfirmModalOk">Удалить</button>
            </div>
        </div>
    </div>
</div>

<template id="advantage-template">
    <div class="advantage-item" data-item>
        <div class="advantage-head">
            <button type="button" class="advantage-handle" data-handle title="Перетащить">
                <i class="fas fa-up-down"></i>
            </button>
            <div class="advantage-order" data-order></div>
            <div class="advantage-title-preview" data-title-preview>Новое преимущество</div>
            <span class="badge bg-danger advantage-delete-status">Будет удалено после сохранения</span>
            <button type="button"
                    class="btn btn-outline-secondary btn-sm"
                    data-toggle-layout
                    title="Поменять местами фото и текст (как на сайте)">
                <i class="fas fa-arrows-alt-h me-1"></i><span data-layout-label>Фото слева</span>
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm" data-remove>
                <i class="fas fa-trash me-1"></i>Удалить
            </button>
        </div>

        <div class="advantage-body">
            <input type="hidden" data-field="id" value="">
            <input type="hidden" data-field="existing_image" value="">
            <input type="hidden" data-field="pending_delete" value="0">
            <input type="hidden" data-field="image_on_left" value="1">

            <div class="row g-4 align-items-start" data-layout-row>
                <div class="col-lg-4">
                    <div class="advantage-photo-meta">
                        <label class="form-label">Фото</label>
                        <div class="advantage-note">Рекомендуемый формат: горизонтальное изображение.</div>
                    </div>
                    <div class="photo-upload-area mb-3" data-photo-area>
                        <div class="upload-placeholder">
                            <i class="fas fa-camera"></i>
                            <div>Нажмите для загрузки</div>
                            <small class="text-muted">JPG, JPEG, PNG до 5 МБ</small>
                        </div>
                        <div class="photo-preview-container" data-preview-container>
                            <img src="" alt="Предпросмотр" data-preview-image>
                        </div>
                        <div class="change-overlay">
                            <i class="fas fa-camera me-1"></i>Заменить фото
                        </div>
                    </div>

                    <label class="form-label">Фото</label>
                    <input type="file"
                           class="form-control"
                           data-field="image"
                           accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                           style="display: none;">
                </div>

                <div class="col-lg-8">
                    <div class="mb-3">
                        <label class="form-label">Заголовок</label>
                        <input type="text"
                               class="form-control"
                               data-field="title"
                               maxlength="255"
                               value=""
                               placeholder="Например: Современный автопарк">
                    </div>

                    <div>
                        <label class="form-label">Описание</label>
                        <textarea class="form-control"
                                  rows="5"
                                  data-field="description"
                                  placeholder="Описание преимущества"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@section('script')
<script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('advantages-list');
    const addButton = document.getElementById('add-advantage');
    const template = document.getElementById('advantage-template');

    if (!list || !addButton || !template) {
        return;
    }

    const deleteConfirmModalEl = document.getElementById('advantageDeleteConfirmModal');
    const deleteConfirmOk = document.getElementById('advantageDeleteConfirmModalOk');
    const deleteConfirmModal = deleteConfirmModalEl && typeof bootstrap !== 'undefined'
        ? new bootstrap.Modal(deleteConfirmModalEl)
        : null;
    let deleteConfirmContext = null;

    function isBlankNewAdvantage(item) {
        const idInput = item.querySelector('[data-field="id"]');
        if (idInput && String(idInput.value).trim() !== '') {
            return false;
        }

        const title = item.querySelector('[data-field="title"]');
        const description = item.querySelector('[data-field="description"]');
        const existingImage = item.querySelector('[data-field="existing_image"]');
        const imageInput = item.querySelector('[data-field="image"]');

        const titleVal = title ? title.value.trim() : '';
        const descVal = description ? description.value.trim() : '';
        const existingVal = existingImage ? existingImage.value.trim() : '';
        const hasFile = imageInput && imageInput.files && imageInput.files.length > 0;

        return !titleVal && !descVal && !existingVal && !hasFile;
    }

    function applyPendingDelete(item, removeButton, pendingDeleteInput) {
        pendingDeleteInput.value = '1';
        item.classList.add('is-pending-delete');
        removeButton.classList.remove('btn-outline-danger');
        removeButton.classList.add('btn-outline-secondary');
        removeButton.innerHTML = '<i class="fas fa-undo me-1"></i>Вернуть';
        updateNames();
    }

    function applyLayoutRow(item) {
        const row = item.querySelector('[data-layout-row]');
        const input = item.querySelector('[data-field="image_on_left"]');
        const label = item.querySelector('[data-layout-label]');

        if (!row || !input) {
            return;
        }

        const onLeft = input.value === '1';

        row.classList.toggle('flex-row-reverse', !onLeft);

        if (label) {
            label.textContent = onLeft ? 'Фото слева' : 'Фото справа';
        }
    }

    function updateNames() {
        let activeIndex = 0;

        list.querySelectorAll('[data-item]').forEach((item, index) => {
            const order = item.querySelector('[data-order]');
            const titleInput = item.querySelector('[data-field="title"]');
            const titlePreview = item.querySelector('[data-title-preview]');
            const pendingDeleteInput = item.querySelector('[data-field="pending_delete"]');
            const isPendingDelete = pendingDeleteInput.value === '1';

            order.textContent = isPendingDelete ? '×' : String(++activeIndex);
            order.classList.toggle('bg-danger', isPendingDelete);
            order.classList.toggle('bg-primary', !isPendingDelete);

            item.querySelector('[data-field="id"]').setAttribute('name', `advantages[${index}][id]`);
            item.querySelector('[data-field="existing_image"]').setAttribute('name', `advantages[${index}][existing_image]`);
            pendingDeleteInput.setAttribute('name', `advantages[${index}][pending_delete]`);
            item.querySelector('[data-field="image"]').setAttribute('name', `advantages[${index}][image]`);
            titleInput.setAttribute('name', `advantages[${index}][title]`);
            item.querySelector('[data-field="description"]').setAttribute('name', `advantages[${index}][description]`);
            item.querySelector('[data-field="image_on_left"]').setAttribute('name', `advantages[${index}][image_on_left]`);

            if (titlePreview) {
                titlePreview.textContent = titleInput.value.trim() || 'Новое преимущество';
            }
        });
    }

    function bindItem(item) {
        const titleInput = item.querySelector('[data-field="title"]');
        const titlePreview = item.querySelector('[data-title-preview]');
        const removeButton = item.querySelector('[data-remove]');
        const imageInput = item.querySelector('[data-field="image"]');
        const photoArea = item.querySelector('[data-photo-area]');
        const previewContainer = item.querySelector('[data-preview-container]');
        const previewImage = item.querySelector('[data-preview-image]');
        const placeholder = item.querySelector('.upload-placeholder');
        const pendingDeleteInput = item.querySelector('[data-field="pending_delete"]');
        const imageOnLeftInput = item.querySelector('[data-field="image_on_left"]');
        const layoutToggle = item.querySelector('[data-toggle-layout]');

        if (layoutToggle && imageOnLeftInput) {
            layoutToggle.addEventListener('click', function () {
                imageOnLeftInput.value = imageOnLeftInput.value === '1' ? '0' : '1';
                applyLayoutRow(item);
            });
            applyLayoutRow(item);
        }

        titleInput.addEventListener('input', function () {
            titlePreview.textContent = titleInput.value.trim() || 'Новое преимущество';
        });

        removeButton.addEventListener('click', function () {
            const isMarked = pendingDeleteInput.value === '1';

            if (isMarked) {
                pendingDeleteInput.value = '0';
                item.classList.remove('is-pending-delete');
                removeButton.classList.add('btn-outline-danger');
                removeButton.classList.remove('btn-outline-secondary');
                removeButton.innerHTML = '<i class="fas fa-trash me-1"></i>Удалить';
                updateNames();
                return;
            }

            if (isBlankNewAdvantage(item)) {
                item.remove();
                updateNames();
                return;
            }

            if (!deleteConfirmModal) {
                applyPendingDelete(item, removeButton, pendingDeleteInput);
                return;
            }

            deleteConfirmContext = { item, removeButton, pendingDeleteInput };
            deleteConfirmModal.show();
        });

        if (pendingDeleteInput.value === '1') {
            removeButton.classList.remove('btn-outline-danger');
            removeButton.classList.add('btn-outline-secondary');
            removeButton.innerHTML = '<i class="fas fa-undo me-1"></i>Вернуть';
        }

        if (photoArea && imageInput) {
            photoArea.addEventListener('click', function () {
                if (pendingDeleteInput.value === '1') {
                    return;
                }

                imageInput.click();
            });
        }

        imageInput.addEventListener('change', function () {
            const file = imageInput.files && imageInput.files[0];

            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                previewImage.src = event.target.result;
                previewContainer.style.display = 'block';
                placeholder.style.display = 'none';
                photoArea.classList.add('has-image');
            };
            reader.readAsDataURL(file);
        });
    }

    Sortable.create(list, {
        animation: 150,
        handle: '[data-handle]',
        ghostClass: 'sortable-ghost',
        onSort: updateNames,
    });

    list.querySelectorAll('[data-item]').forEach(bindItem);

    addButton.addEventListener('click', function () {
        const fragment = template.content.cloneNode(true);

        list.appendChild(fragment);
        bindItem(list.lastElementChild);
        updateNames();
        list.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    if (deleteConfirmOk && deleteConfirmModal) {
        deleteConfirmOk.addEventListener('click', function () {
            if (!deleteConfirmContext) {
                deleteConfirmModal.hide();
                return;
            }

            const { item, removeButton, pendingDeleteInput } = deleteConfirmContext;
            deleteConfirmContext = null;
            deleteConfirmModal.hide();
            applyPendingDelete(item, removeButton, pendingDeleteInput);
        });

        deleteConfirmModalEl.addEventListener('hidden.bs.modal', function () {
            deleteConfirmContext = null;
        });
    }

    updateNames();
});
</script>
@endsection
