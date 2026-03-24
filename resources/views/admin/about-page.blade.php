@extends('layouts.admin')

@section('title', 'Страница «О нас»')

@php
    $formSettings = [
        'hero_title' => old('hero_title', $settings->hero_title ?? ''),
        'hero_subtitle' => old('hero_subtitle', $settings->hero_subtitle ?? ''),
        'cta_title' => old('cta_title', $settings->cta_title ?? ''),
        'cta_text' => old('cta_text', $settings->cta_text ?? ''),
        'cta_button_text' => old('cta_button_text', $settings->cta_button_text ?? ''),
        'cta_icon' => old('cta_icon', $settings->cta_icon ?? 'fas fa-graduation-cap'),
        'cta_href' => old('cta_href', $settings->cta_href ?? '/prices'),
    ];

    $rawBlocks = old('blocks');

    if (is_array($rawBlocks) && count($rawBlocks) > 0) {
        $formBlocks = collect($rawBlocks)->map(function ($item) {
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
        $formBlocks = $blocks->map(function ($block) {
            return [
                'id' => $block->id,
                'title' => $block->title ?? '',
                'description' => $block->description,
                'existing_image' => $block->image,
                'image_url' => $block->image_url,
                'pending_delete' => false,
                'image_on_left' => (bool) $block->image_on_left,
            ];
        })->values()->all();
    }
@endphp

@section('style')
<style>
    .about-blocks-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .about-block-item {
        border: 1px solid #dee2e6;
        border-radius: 0.85rem;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
        transition: opacity 0.2s, border-color 0.2s, background-color 0.2s;
    }

    .about-block-item.sortable-ghost {
        opacity: 0.35;
    }

    .about-block-item.is-pending-delete {
        border-color: #f1aeb5;
        background: #fff5f5;
    }

    .about-block-item.is-pending-delete .about-block-title-preview,
    .about-block-item.is-pending-delete .form-label,
    .about-block-item.is-pending-delete .about-block-note {
        text-decoration: line-through;
    }

    .about-block-item.is-pending-delete .about-block-body {
        opacity: 0.55;
    }

    .about-block-item.is-pending-delete .about-block-handle {
        opacity: 0.4;
        pointer-events: none;
    }

    .about-block-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem 1rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .about-block-order {
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

    .about-block-handle {
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

    .about-block-handle:active {
        cursor: grabbing;
    }

    .about-block-title-preview {
        min-width: 0;
        flex: 1;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .about-block-body {
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

    .about-block-note {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .about-block-photo-meta {
        margin-bottom: 0.6rem;
    }

    .about-block-photo-meta .form-label {
        display: block;
        margin-bottom: 0.15rem;
    }

    .about-block-item:not(.is-pending-delete) .about-block-delete-status {
        display: none !important;
    }

    .about-block-item.is-pending-delete .about-block-delete-status {
        display: inline-flex !important;
    }

    .about-block-body [data-layout-row].flex-row-reverse {
        flex-direction: row-reverse;
    }

    .about-cta-live-wrap {
        position: sticky;
        top: 0.75rem;
    }
    .about-cta-live-card {
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
    }
    .hero-icon-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(48px, 1fr));
        gap: 4px;
        max-height: 400px;
        overflow-y: auto;
        padding: 0.25rem;
    }
    .hero-icon-picker-item {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        cursor: pointer;
        font-size: 1.2rem;
        color: #495057;
        transition: all 0.15s;
        background: #fff;
    }
    .hero-icon-picker-item:hover {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    .hero-icon-picker-item.selected {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    .about-cta-icon-btn {
        min-width: 2.5rem;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Страница «О нас»</h1>
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
                <form method="POST" action="{{ route('admin.about-page.update') }}" enctype="multipart/form-data" id="about-page-form">
                    @csrf

                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h3 class="card-title mb-0">Шапка страницы</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Заголовок и подзаголовок над блоками с фото (как на сайте).</p>
                            <div class="mb-3">
                                <label class="form-label" for="hero_title">Заголовок</label>
                                <input type="text" name="hero_title" id="hero_title" class="form-control form-control-lg" value="{{ $formSettings['hero_title'] }}" required maxlength="255" placeholder="О нашей автошколе">
                            </div>
                            <div class="mb-0">
                                <label class="form-label" for="hero_subtitle">Подзаголовок</label>
                                <input type="text" name="hero_subtitle" id="hero_subtitle" class="form-control" value="{{ $formSettings['hero_subtitle'] }}" maxlength="500" placeholder="Добро пожаловать в «Автошкола Политех»">
                            </div>
                        </div>
                    </div>

                    <p class="text-muted mb-4">
                        Текстовые блоки с фото: заголовок можно оставить пустым (тогда на сайте не будет подзаголовка у блока). Абзацы разделяйте пустой строкой. Изображения: JPG, JPEG, PNG до 5 МБ.
                    </p>

                    <div class="alert alert-light border d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="fas fa-up-down text-primary"></i>
                        <span>Перетаскивайте карточки за иконку со стрелками, чтобы изменить порядок.</span>
                    </div>

                    <div class="about-blocks-list" id="about-blocks-list">
                        @foreach($formBlocks as $block)
                            <div class="about-block-item {{ $block['pending_delete'] ? 'is-pending-delete' : '' }}" data-item>
                                <div class="about-block-head">
                                    <button type="button" class="about-block-handle" data-handle title="Перетащить">
                                        <i class="fas fa-up-down"></i>
                                    </button>
                                    <div class="about-block-order" data-order></div>
                                    <div class="about-block-title-preview" data-title-preview>
                                        {{ $block['title'] ? $block['title'] : 'Блок без заголовка' }}
                                    </div>
                                    <span class="badge bg-danger about-block-delete-status">Будет удалён после сохранения</span>
                                    <button type="button"
                                            class="btn btn-outline-secondary btn-sm"
                                            data-toggle-layout
                                            title="Поменять местами фото и текст (как на сайте)">
                                        <i class="fas fa-arrows-alt-h me-1"></i><span data-layout-label>{{ $block['image_on_left'] ? 'Фото слева' : 'Фото справа' }}</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-remove>
                                        <i class="fas fa-trash me-1"></i>Удалить
                                    </button>
                                </div>

                                <div class="about-block-body">
                                    <input type="hidden" data-field="id" value="{{ $block['id'] }}">
                                    <input type="hidden" data-field="existing_image" value="{{ $block['existing_image'] }}">
                                    <input type="hidden" data-field="pending_delete" value="{{ $block['pending_delete'] ? 1 : 0 }}">
                                    <input type="hidden" data-field="image_on_left" value="{{ $block['image_on_left'] ? 1 : 0 }}">

                                    <div class="row g-4 align-items-start {{ !$block['image_on_left'] ? 'flex-row-reverse' : '' }}" data-layout-row>
                                        <div class="col-lg-4">
                                            <div class="about-block-photo-meta">
                                                <label class="form-label">Фото</label>
                                                <div class="about-block-note">Рекомендуемый формат: горизонтальное изображение.</div>
                                            </div>
                                            <div class="photo-upload-area {{ $block['image_url'] ? 'has-image' : '' }} mb-3" data-photo-area>
                                                <div class="upload-placeholder" {!! $block['image_url'] ? 'style="display:none"' : '' !!}>
                                                    <i class="fas fa-camera"></i>
                                                    <div>Нажмите для загрузки</div>
                                                    <small class="text-muted">JPG, JPEG, PNG до 5 МБ</small>
                                                </div>
                                                <div class="photo-preview-container" data-preview-container {!! $block['image_url'] ? 'style="display:block"' : '' !!}>
                                                    <img src="{{ $block['image_url'] }}" alt="Предпросмотр" data-preview-image>
                                                </div>
                                                <div class="change-overlay">
                                                    <i class="fas fa-camera me-1"></i>Заменить фото
                                                </div>
                                            </div>

                                            <label class="form-label d-none">Фото</label>
                                            <input type="file"
                                                   class="form-control"
                                                   data-field="image"
                                                   accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                                   style="display: none;">
                                        </div>

                                        <div class="col-lg-8">
                                            <div class="mb-3">
                                                <label class="form-label">Заголовок блока <span class="text-muted fw-normal">(необязательно)</span></label>
                                                <input type="text"
                                                       class="form-control"
                                                       data-field="title"
                                                       maxlength="255"
                                                       value="{{ $block['title'] }}"
                                                       placeholder="Например: Обучение с комфортом">
                                            </div>

                                            <div>
                                                <label class="form-label">Текст</label>
                                                <textarea class="form-control"
                                                          rows="6"
                                                          data-field="description"
                                                          placeholder="Несколько абзацев можно разделить пустой строкой">{{ $block['description'] }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card mt-4 border">
                        <div class="card-header bg-light">
                            <h3 class="card-title mb-0">Нижний блок (призыв к действию)</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Те же цели ссылок, что в редакторе Hero. Справа — живое превью.</p>
                            <div class="row g-4 align-items-start">
                                <div class="col-xl-7">
                                    <div class="mb-3">
                                        <label class="form-label" for="cta_title">Заголовок</label>
                                        <input type="text" name="cta_title" id="cta_title" class="form-control about-cta-sync" value="{{ $formSettings['cta_title'] }}" required maxlength="255">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="cta_text">Текст</label>
                                        <textarea name="cta_text" id="cta_text" class="form-control about-cta-sync" rows="4" required>{{ $formSettings['cta_text'] }}</textarea>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="cta_button_text">Текст кнопки</label>
                                            <input type="text" name="cta_button_text" id="cta_button_text" class="form-control about-cta-sync" value="{{ $formSettings['cta_button_text'] }}" required maxlength="255">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="cta_href">Куда ведёт кнопка</label>
                                            <select name="cta_href" id="cta_href" class="form-select about-cta-sync" required>
                                                @foreach($heroCtaLinkOptions as $path => $label)
                                                    <option value="{{ $path }}" {{ ($formSettings['cta_href'] ?? '/prices') === $path ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <span class="form-label d-block">Иконка у кнопки</span>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <input type="hidden" name="cta_icon" id="about_cta_icon" class="about-cta-icon-value" value="{{ $formSettings['cta_icon'] }}">
                                            <button type="button" class="btn btn-outline-secondary about-cta-icon-btn" id="aboutCtaIconBtn" title="Выбрать иконку" aria-label="Выбрать иконку"><i class="{{ $formSettings['cta_icon'] }}"></i></button>
                                            <span class="text-muted small mb-0">Выбор иконки из списка</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-5">
                                    <div class="about-cta-live-wrap">
                                        <p class="small text-muted mb-2 fw-semibold text-uppercase letter-spacing">Как будет на сайте</p>
                                        <div class="about-cta-live-card border rounded-3 p-4 bg-white text-center" id="about-cta-live-root">
                                            <h3 class="h5 fw-bold mb-3" id="about-cta-pv-title">{{ $formSettings['cta_title'] }}</h3>
                                            <p class="text-muted small mb-3 mb-md-4" id="about-cta-pv-text" style="white-space: pre-wrap;">{{ $formSettings['cta_text'] }}</p>
                                            <span class="btn btn-primary btn-lg rounded-pill px-4 mt-1 d-inline-flex align-items-center pointer-events-none" id="about-cta-pv-btn" aria-hidden="true">
                                                <i id="about-cta-pv-icon" class="{{ $formSettings['cta_icon'] }} me-2"></i><span id="about-cta-pv-label">{{ $formSettings['cta_button_text'] }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="button" class="btn btn-outline-primary" id="add-about-block">
                            <i class="fas fa-plus me-1"></i>Добавить блок
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

<div class="modal fade" id="aboutBlockDeleteConfirmModal" tabindex="-1" aria-labelledby="aboutBlockDeleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aboutBlockDeleteConfirmModalLabel">Удалить блок?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Он перестанет отображаться на сайте после нажатия «Сохранить». До сохранения можно отменить пометку кнопкой «Вернуть».</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="aboutBlockDeleteConfirmModalOk">Удалить</button>
            </div>
        </div>
    </div>
</div>

<template id="about-block-template">
    <div class="about-block-item" data-item>
        <div class="about-block-head">
            <button type="button" class="about-block-handle" data-handle title="Перетащить">
                <i class="fas fa-up-down"></i>
            </button>
            <div class="about-block-order" data-order></div>
            <div class="about-block-title-preview" data-title-preview>Новый блок</div>
            <span class="badge bg-danger about-block-delete-status">Будет удалён после сохранения</span>
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

        <div class="about-block-body">
            <input type="hidden" data-field="id" value="">
            <input type="hidden" data-field="existing_image" value="">
            <input type="hidden" data-field="pending_delete" value="0">
            <input type="hidden" data-field="image_on_left" value="1">

            <div class="row g-4 align-items-start" data-layout-row>
                <div class="col-lg-4">
                    <div class="about-block-photo-meta">
                        <label class="form-label">Фото</label>
                        <div class="about-block-note">Рекомендуемый формат: горизонтальное изображение.</div>
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

                    <input type="file"
                           class="form-control"
                           data-field="image"
                           accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                           style="display: none;">
                </div>

                <div class="col-lg-8">
                    <div class="mb-3">
                        <label class="form-label">Заголовок блока <span class="text-muted fw-normal">(необязательно)</span></label>
                        <input type="text"
                               class="form-control"
                               data-field="title"
                               maxlength="255"
                               value=""
                               placeholder="Например: Обучение с комфортом">
                    </div>

                    <div>
                        <label class="form-label">Текст</label>
                        <textarea class="form-control"
                                  rows="6"
                                  data-field="description"
                                  placeholder="Несколько абзацев можно разделить пустой строкой"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

@include('admin.partials.hero-icon-picker-modal')
@endsection

@section('script')
<script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('about-blocks-list');
    const addButton = document.getElementById('add-about-block');
    const template = document.getElementById('about-block-template');
    const form = document.getElementById('about-page-form');

    if (!list || !addButton || !template || !form) {
        return;
    }

    const deleteConfirmModalEl = document.getElementById('aboutBlockDeleteConfirmModal');
    const deleteConfirmOk = document.getElementById('aboutBlockDeleteConfirmModalOk');
    const deleteConfirmModal = deleteConfirmModalEl && typeof bootstrap !== 'undefined'
        ? new bootstrap.Modal(deleteConfirmModalEl)
        : null;
    let deleteConfirmContext = null;

    function isBlankNewBlock(item) {
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

    function previewTitleText(titleInput) {
        const t = titleInput.value.trim();
        return t || 'Блок без заголовка';
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

            item.querySelector('[data-field="id"]').setAttribute('name', `blocks[${index}][id]`);
            item.querySelector('[data-field="existing_image"]').setAttribute('name', `blocks[${index}][existing_image]`);
            pendingDeleteInput.setAttribute('name', `blocks[${index}][pending_delete]`);
            item.querySelector('[data-field="image"]').setAttribute('name', `blocks[${index}][image]`);
            titleInput.setAttribute('name', `blocks[${index}][title]`);
            item.querySelector('[data-field="description"]').setAttribute('name', `blocks[${index}][description]`);
            item.querySelector('[data-field="image_on_left"]').setAttribute('name', `blocks[${index}][image_on_left]`);

            if (titlePreview) {
                titlePreview.textContent = previewTitleText(titleInput);
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
            titlePreview.textContent = previewTitleText(titleInput);
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

            if (isBlankNewBlock(item)) {
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const HERO_ICONS = [
        'fas fa-map-marker-alt', 'fas fa-building', 'fas fa-store-alt', 'fas fa-home',
        'fas fa-map-marked-alt', 'fas fa-route', 'fas fa-road', 'fas fa-parking',
        'fas fa-car', 'fas fa-motorcycle', 'fas fa-bus', 'fas fa-truck', 'fas fa-taxi',
        'fas fa-shield-alt', 'fas fa-id-card', 'fas fa-certificate', 'fas fa-award', 'fas fa-medal',
        'fas fa-graduation-cap', 'fas fa-chalkboard-teacher', 'fas fa-user-tie', 'fas fa-user-graduate',
        'fas fa-users', 'fas fa-user-friends', 'fas fa-handshake', 'fas fa-hands-helping',
        'fas fa-hand-holding-usd', 'fas fa-ruble-sign', 'fas fa-percent', 'fas fa-piggy-bank',
        'fas fa-phone-alt', 'fas fa-headset', 'fas fa-envelope', 'fas fa-comment-dots',
        'fas fa-clock', 'fas fa-calendar-check', 'fas fa-calendar-alt',
        'fas fa-star', 'fas fa-thumbs-up', 'fas fa-heart', 'fas fa-check-circle', 'fas fa-check',
        'fas fa-life-ring', 'fas fa-smile', 'fas fa-lightbulb', 'fas fa-rocket',
        'fas fa-clipboard-check', 'fas fa-tasks', 'fas fa-file-contract'
    ];

    const iconHidden = document.getElementById('about_cta_icon');
    const iconBtn = document.getElementById('aboutCtaIconBtn');
    const grid = document.getElementById('heroIconPickerGrid');
    const modalEl = document.getElementById('heroIconPickerModal');
    const pvTitle = document.getElementById('about-cta-pv-title');
    const pvText = document.getElementById('about-cta-pv-text');
    const pvLabel = document.getElementById('about-cta-pv-label');
    const pvIcon = document.getElementById('about-cta-pv-icon');
    const titleIn = document.getElementById('cta_title');
    const textIn = document.getElementById('cta_text');
    const btnTextIn = document.getElementById('cta_button_text');

    if (!pvTitle || !pvText) {
        return;
    }

    let iconModal = null;
    if (modalEl && typeof bootstrap !== 'undefined') {
        iconModal = new bootstrap.Modal(modalEl);
    }

    function syncAboutCtaPreview() {
        if (titleIn) {
            pvTitle.textContent = titleIn.value.trim() || 'Заголовок';
        }
        if (textIn) {
            pvText.textContent = textIn.value.trim() || 'Текст';
        }
        if (btnTextIn && pvLabel) {
            pvLabel.textContent = btnTextIn.value.trim() || 'Кнопка';
        }
        if (iconHidden && pvIcon) {
            const cls = (iconHidden.value || 'fas fa-graduation-cap').trim();
            pvIcon.className = cls + ' me-2';
        }
    }

    document.querySelectorAll('.about-cta-sync').forEach(function (el) {
        el.addEventListener('input', syncAboutCtaPreview);
        el.addEventListener('change', syncAboutCtaPreview);
    });

    if (grid && grid.children.length === 0) {
        HERO_ICONS.forEach(function (iconClass) {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'hero-icon-picker-item';
            item.dataset.icon = iconClass;
            item.innerHTML = '<i class="' + iconClass + '"></i>';
            item.addEventListener('click', function () {
                if (!iconHidden || !iconBtn) {
                    return;
                }
                iconHidden.value = iconClass;
                iconBtn.innerHTML = '<i class="' + iconClass + '"></i>';
                grid.querySelectorAll('.hero-icon-picker-item').forEach(function (el) {
                    el.classList.toggle('selected', el.dataset.icon === iconClass);
                });
                syncAboutCtaPreview();
                if (iconModal) {
                    iconModal.hide();
                }
            });
            grid.appendChild(item);
        });
    }

    if (iconBtn && iconHidden && grid) {
        iconBtn.addEventListener('click', function () {
            const val = (iconHidden.value || 'fas fa-graduation-cap').trim();
            grid.querySelectorAll('.hero-icon-picker-item').forEach(function (el) {
                el.classList.toggle('selected', el.dataset.icon === val);
            });
            if (iconModal) {
                iconModal.show();
            }
        });
    }

    syncAboutCtaPreview();
});
</script>
@endsection
