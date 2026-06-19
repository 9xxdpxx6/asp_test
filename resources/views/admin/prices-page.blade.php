@extends('layouts.admin')

@section('title', 'Акции на странице «Цены»')

@php
    $rawPromos = old('promos');

    $resolveImageUrl = function ($image) {
        if (!$image) {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }
        if (\Illuminate\Support\Str::startsWith($image, ['/'])) {
            return url(ltrim($image, '/'));
        }
        return url('storage/' . ltrim($image, '/'));
    };

    if (is_array($rawPromos) && count($rawPromos) > 0) {
        $formPromos = collect($rawPromos)->map(function ($item) use ($resolveImageUrl) {
            $image = $item['existing_image'] ?? null;
            return [
                'id' => $item['id'] ?? null,
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
                'existing_image' => $image,
                'image_url' => $resolveImageUrl($image),
                'image_size' => $item['image_size'] ?? '1/2',
                'image_on_left' => array_key_exists('image_on_left', $item)
                    ? filter_var($item['image_on_left'], FILTER_VALIDATE_BOOLEAN)
                    : true,
                'placement' => $item['placement'] ?? 'top',
                'is_active' => array_key_exists('is_active', $item)
                    ? filter_var($item['is_active'], FILTER_VALIDATE_BOOLEAN)
                    : true,
                'pending_delete' => !empty($item['pending_delete']),
            ];
        })->values()->all();
    } else {
        $formPromos = $promos->map(function ($promo) {
            return [
                'id' => $promo->id,
                'title' => $promo->title ?? '',
                'description' => $promo->description ?? '',
                'existing_image' => $promo->image,
                'image_url' => $promo->image_url,
                'image_size' => $promo->image_size ?: '1/2',
                'image_on_left' => (bool) $promo->image_on_left,
                'placement' => $promo->placement ?: 'top',
                'is_active' => (bool) $promo->is_active,
                'pending_delete' => false,
            ];
        })->values()->all();
    }
@endphp

@section('style')
<style>
    .promo-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .promo-item {
        border: 1px solid #dee2e6;
        border-radius: 0.85rem;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
        transition: opacity 0.2s, border-color 0.2s, background-color 0.2s;
    }
    .promo-item.sortable-ghost { opacity: 0.35; }
    .promo-item.is-pending-delete {
        border-color: #f1aeb5;
        background: #fff5f5;
    }
    .promo-item.is-pending-delete .promo-title-preview,
    .promo-item.is-pending-delete .form-label {
        text-decoration: line-through;
    }
    .promo-item.is-pending-delete .promo-body { opacity: 0.55; }
    .promo-item.is-pending-delete .promo-handle { opacity: 0.4; pointer-events: none; }

    .promo-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.9rem 1rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        flex-wrap: wrap;
    }
    .promo-order {
        width: 34px; height: 34px;
        border-radius: 50%;
        background: #0d6efd; color: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; flex-shrink: 0;
    }
    .promo-handle {
        width: 34px; height: 34px;
        border-radius: 50%;
        border: 1px solid #d0d7de; background: #fff; color: #6c757d;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: grab; flex-shrink: 0;
    }
    .promo-handle:active { cursor: grabbing; }
    .promo-title-preview {
        min-width: 0; flex: 1; font-weight: 600;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .promo-body { padding: 1rem; }

    .promo-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: flex-end;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px dashed #e3e6ea;
    }
    .promo-toolbar .form-label { margin-bottom: 0.15rem; font-size: 0.85rem; }

    /* Левый столбец редактирования: фото и поля одной ширины */
    .promo-edit-col { width: 100%; }

    .promo-photo-area {
        width: 100%;
        aspect-ratio: 16 / 9;
        max-height: 460px;
        margin-inline: auto;
        border: 2px dashed #ced4da;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #fafafa;
        display: flex; align-items: center; justify-content: center;
        text-align: center; color: #6c757d;
        position: relative; cursor: pointer;
        transition: aspect-ratio 0.2s ease, border-color 0.2s, background-color 0.2s;
    }
    .promo-photo-area:hover { border-color: #0d6efd; background: #f0f7ff; }
    .promo-photo-area.has-image { padding: 0; border-style: solid; background: #fff; }
    .promo-photo-area .upload-placeholder i { font-size: 2rem; margin-bottom: 0.5rem; display: block; }
    .promo-photo-area .change-overlay {
        position: absolute; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.6); color: #fff;
        padding: 0.5rem; font-size: 0.85rem; display: none;
    }
    .promo-photo-area.has-image:hover .change-overlay { display: block; }
    .promo-preview-container { width: 100%; height: 100%; display: none; }
    .promo-preview-container img { width: 100%; height: 100%; object-fit: cover; }

    /* Схематичное превью раскладки на странице «Цены» */
    .promo-pv {
        border: 1px solid #e3e6ea;
        border-radius: 0.6rem;
        background: #f5f7fa;
        padding: 0.85rem;
        min-height: 240px;
    }
    .promo-pv-vert { display: flex; flex-direction: column; gap: 0.7rem; }
    .promo-pv-horiz { display: flex; gap: 0.7rem; align-items: stretch; }
    .promo-pv-grid {
        flex: 1;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        align-content: start;
    }
    .promo-pv-horiz .promo-pv-grid { grid-template-columns: repeat(2, 1fr); }
    /* Карточки-категории — близко к квадрату, как на сайте */
    .promo-pv-card {
        background: #fff;
        border: 1px solid #e3e6ea;
        border-radius: 0.4rem;
        aspect-ratio: 1 / 1.05;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .promo-pv-card .pv-card-img { flex: 1.7; background: #dfe5ec; }
    .promo-pv-card .pv-card-lines {
        flex: 1; padding: 6px 7px;
        display: flex; flex-direction: column; justify-content: center;
    }
    .promo-pv-card .pv-card-lines span {
        display: block; height: 5px; border-radius: 2px;
        background: #e3e6ea; margin-bottom: 4px;
    }
    .promo-pv-card .pv-card-lines span:nth-child(2) { width: 55%; background: #cdd5df; }

    .promo-pv-banner {
        background: #0d6efd0d;
        border: 1px dashed #0d6efd;
        border-radius: 0.45rem;
        overflow: hidden;
        color: #0d6efd;
        font-size: 0.8rem;
        padding: 0.4rem;
    }
    .pv-banner-ph {
        display: flex; align-items: center; justify-content: center;
        min-height: 40px; font-size: 0.75rem;
    }
    .pv-banner-title {
        font-weight: 600;
        line-height: 1.2;
        word-break: break-word;
        text-align: center;
        margin-top: 0.25rem;
    }
    /* Баннер-колонка (слева/справа от категорий) */
    .promo-pv-banner-col {
        display: flex; flex-direction: column; align-items: center;
        align-self: flex-start;
    }
    .promo-pv-banner-col img {
        width: 100%; max-height: 220px; object-fit: contain; border-radius: 0.3rem;
    }
    /* Баннер-полоса (сверху/снизу): фото + текст в ряд, порядок по «фото слева/справа» */
    .promo-pv-banner-strip {
        display: flex; gap: 0.5rem; align-items: center; min-height: 52px;
    }
    .pv-strip-img { display: flex; align-items: center; justify-content: center; }
    .pv-strip-img img { width: 100%; max-height: 70px; object-fit: contain; border-radius: 0.3rem; }
    .pv-strip-text { flex: 1; }
    .pv-strip-text .pv-banner-title { text-align: left; margin: 0 0 5px; font-size: 0.8rem; }
    .pv-strip-text span {
        display: block; height: 5px; border-radius: 2px;
        background: #cfe0ff; margin-bottom: 4px;
    }
    .pv-strip-text span:last-child { width: 60%; }
    .promo-pv-caption {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.4rem;
    }
    .promo-item:not(.is-pending-delete) .promo-delete-status { display: none !important; }
    .promo-item.is-pending-delete .promo-delete-status { display: inline-flex !important; }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Акции на странице «Цены»</h1>
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
                <p class="text-muted mb-3">
                    Рекламные баннеры-акции на странице «Цены». У каждой акции — одно фото, заголовок,
                    необязательное описание, размер и расположение фото (как в блоках «картинка+текст»),
                    а также позиция относительно списка цен (сверху или снизу). На сайте по клику на фото
                    оно открывается в полном размере.
                </p>

                <div class="alert alert-light border d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="fas fa-up-down text-primary"></i>
                    <span>Перетаскивайте карточки за иконку со стрелками, чтобы изменить порядок отображения.</span>
                </div>

                <form method="POST" action="{{ route('admin.prices-page.update') }}" enctype="multipart/form-data" id="prices-page-form">
                    @csrf

                    <div class="promo-list" id="promo-list">
                        @foreach($formPromos as $promo)
                            @include('admin.partials.price-promo-item', ['promo' => $promo])
                        @endforeach
                    </div>

                    <p id="no-promos-message" class="text-muted text-center py-3" @if(count($formPromos)) style="display:none" @endif>
                        Акций пока нет. Нажмите «Добавить акцию», чтобы создать баннер.
                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="button" class="btn btn-outline-primary" id="add-promo">
                            <i class="fas fa-plus me-1"></i>Добавить акцию
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

<div class="modal fade" id="promoDeleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Удалить акцию?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Акция перестанет отображаться на сайте после нажатия «Сохранить». До сохранения можно отменить пометку кнопкой «Вернуть».</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="promoDeleteConfirmOk">Удалить</button>
            </div>
        </div>
    </div>
</div>

<template id="promo-template">
    @include('admin.partials.price-promo-item', ['promo' => [
        'id' => null,
        'title' => '',
        'description' => '',
        'existing_image' => null,
        'image_url' => null,
        'image_size' => '1/2',
        'image_on_left' => true,
        'placement' => 'top',
        'is_active' => true,
        'pending_delete' => false,
    ]])
</template>
@endsection

@section('script')
<script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('promo-list');
    const addButton = document.getElementById('add-promo');
    const template = document.getElementById('promo-template');
    const form = document.getElementById('prices-page-form');
    const noMsg = document.getElementById('no-promos-message');

    if (!list || !addButton || !template || !form) {
        return;
    }

    // Доля ширины блока, которую занимает фото (для схематичного превью).
    const SIZE_FRAC = {
        '3/4': 0.75, '2/3': 0.667, '1/2': 0.5, '1/3': 0.333, '1/4': 0.25,
    };

    // Пресеты соотношений сторон — превью подгоняется к ближайшему,
    // чтобы фото показывалось без сильной обрезки и раскладка не «ломалась».
    const ASPECT_PRESETS = [
        [21, 9], [16, 9], [3, 2], [4, 3], [1, 1],
        [3, 4], [2, 3], [9, 16], [9, 18], [9, 21],
    ];

    function nearestAspect(w, h) {
        if (!w || !h) return '16 / 9';
        const ratio = w / h;
        let best = ASPECT_PRESETS[0];
        let bestDiff = Infinity;
        for (const [pw, ph] of ASPECT_PRESETS) {
            const diff = Math.abs((pw / ph) - ratio);
            if (diff < bestDiff) {
                bestDiff = diff;
                best = [pw, ph];
            }
        }
        return best[0] + ' / ' + best[1];
    }

    // Подсказка размера: широкое фото — больше места, вертикальное — уже.
    function suggestSize(w, h) {
        if (!w || !h) return '1/2';
        const r = w / h;
        if (r >= 1.6) return '3/4';
        if (r >= 1.15) return '2/3';
        if (r >= 0.85) return '1/2';
        if (r >= 0.6) return '1/3';
        return '1/4';
    }

    function applyImageAspect(item) {
        const photoArea = item.querySelector('[data-photo-area]');
        const img = item.querySelector('[data-preview-image]');
        if (!photoArea || !img) return;
        const w = img.naturalWidth;
        const h = img.naturalHeight;
        if (w && h) {
            photoArea.style.aspectRatio = nearestAspect(w, h);
            // Авто-подбор размера только для только что загруженного фото.
            if (item._autoSize) {
                const sel = item.querySelector('[data-field="image_size"]');
                if (sel) sel.value = suggestSize(w, h);
                item._autoSize = false;
            }
        }
        renderPreview(item);
    }

    function buildPvCard() {
        const c = document.createElement('div');
        c.className = 'promo-pv-card';
        c.innerHTML = '<div class="pv-card-img"></div><div class="pv-card-lines"><span></span><span></span></div>';
        return c;
    }

    function buildPvGrid(n) {
        const g = document.createElement('div');
        g.className = 'promo-pv-grid';
        for (let i = 0; i < n; i++) g.appendChild(buildPvCard());
        return g;
    }

    function pvImageEl(item) {
        const previewImage = item.querySelector('[data-preview-image]');
        const photoArea = item.querySelector('[data-photo-area]');
        const hasImg = photoArea && photoArea.classList.contains('has-image') && previewImage && previewImage.src;
        if (hasImg) {
            const im = document.createElement('img');
            im.src = previewImage.src;
            return im;
        }
        const s = document.createElement('div');
        s.className = 'pv-banner-ph';
        s.textContent = 'Фото';
        return s;
    }

    function bannerTitle(item) {
        const titleInput = item.querySelector('[data-field="title"]');
        const t = titleInput ? titleInput.value.trim() : '';
        if (!t) return null;
        const td = document.createElement('div');
        td.className = 'pv-banner-title';
        td.textContent = t;
        return td;
    }

    // Колонка для позиций «слева/справа от категорий».
    function buildPvBannerCol(item, fracPercent) {
        const b = document.createElement('div');
        b.className = 'promo-pv-banner promo-pv-banner-col';
        b.style.flex = '0 0 ' + fracPercent + '%';
        b.appendChild(pvImageEl(item));
        const t = bannerTitle(item);
        if (t) b.appendChild(t);
        return b;
    }

    // Полоса для позиций «сверху/снизу»: фото и текст в ряд, порядок — по «фото слева/справа».
    function buildPvBannerStrip(item, fracPercent) {
        const onLeftInput = item.querySelector('[data-field="image_on_left"]');
        const onLeft = !onLeftInput || onLeftInput.value === '1';

        const strip = document.createElement('div');
        strip.className = 'promo-pv-banner promo-pv-banner-strip';

        const imgBox = document.createElement('div');
        imgBox.className = 'pv-strip-img';
        imgBox.style.flex = '0 0 ' + fracPercent + '%';
        imgBox.appendChild(pvImageEl(item));

        const txtBox = document.createElement('div');
        txtBox.className = 'pv-strip-text';
        const t = bannerTitle(item);
        if (t) txtBox.appendChild(t);
        txtBox.appendChild(document.createElement('span'));
        txtBox.appendChild(document.createElement('span'));

        if (onLeft) { strip.appendChild(imgBox); strip.appendChild(txtBox); }
        else { strip.appendChild(txtBox); strip.appendChild(imgBox); }
        return strip;
    }

    function renderPreview(item) {
        const panel = item.querySelector('[data-preview-panel]');
        if (!panel) return;
        const placementSel = item.querySelector('[data-field="placement"]');
        const sizeSel = item.querySelector('[data-field="image_size"]');
        const placement = placementSel ? placementSel.value : 'top';
        const size = sizeSel ? sizeSel.value : '1/2';
        const frac = Math.round((SIZE_FRAC[size] || 0.5) * 100);

        // Расположение «фото слева/справа» имеет смысл только для полос сверху/снизу.
        const layoutWrap = item.querySelector('[data-layout-wrap]');
        if (layoutWrap) layoutWrap.style.display = (placement === 'left' || placement === 'right') ? 'none' : '';

        panel.innerHTML = '';
        let container;
        if (placement === 'left' || placement === 'right') {
            container = document.createElement('div');
            container.className = 'promo-pv-horiz';
            const banner = buildPvBannerCol(item, frac);
            const grid = buildPvGrid(4);
            if (placement === 'left') { container.appendChild(banner); container.appendChild(grid); }
            else { container.appendChild(grid); container.appendChild(banner); }
        } else {
            container = document.createElement('div');
            container.className = 'promo-pv-vert';
            const banner = buildPvBannerStrip(item, frac);
            const grid = buildPvGrid(6);
            if (placement === 'bottom') { container.appendChild(grid); container.appendChild(banner); }
            else { container.appendChild(banner); container.appendChild(grid); }
        }
        panel.appendChild(container);

        const cap = document.createElement('div');
        cap.className = 'promo-pv-caption';
        cap.textContent = 'Серые карточки — категории цен, синим — ваша акция.';
        panel.appendChild(cap);
    }

    const deleteConfirmModalEl = document.getElementById('promoDeleteConfirmModal');
    const deleteConfirmOk = document.getElementById('promoDeleteConfirmOk');
    const deleteConfirmModal = deleteConfirmModalEl && typeof bootstrap !== 'undefined'
        ? new bootstrap.Modal(deleteConfirmModalEl)
        : null;
    let deleteConfirmContext = null;

    function isBlankNewItem(item) {
        const idInput = item.querySelector('[data-field="id"]');
        if (idInput && String(idInput.value).trim() !== '') {
            return false;
        }
        const title = item.querySelector('[data-field="title"]');
        const description = item.querySelector('[data-field="description"]');
        const existingImage = item.querySelector('[data-field="existing_image"]');
        const imageInput = item.querySelector('[data-field="image"]');
        const hasFile = imageInput && imageInput.files && imageInput.files.length > 0;
        return !(title && title.value.trim())
            && !(description && description.value.trim())
            && !(existingImage && existingImage.value.trim())
            && !hasFile;
    }

    function previewTitleText(titleInput) {
        return (titleInput.value.trim()) || 'Акция без заголовка';
    }

    function applyLayout(item) {
        const input = item.querySelector('[data-field="image_on_left"]');
        const label = item.querySelector('[data-layout-label]');
        if (!input || !label) return;
        label.textContent = input.value === '1' ? 'Фото слева' : 'Фото справа';
    }

    function updateNames() {
        let activeIndex = 0;
        const items = list.querySelectorAll('[data-item]');
        items.forEach((item, index) => {
            const order = item.querySelector('[data-order]');
            const titleInput = item.querySelector('[data-field="title"]');
            const titlePreview = item.querySelector('[data-title-preview]');
            const pendingDeleteInput = item.querySelector('[data-field="pending_delete"]');
            const isPendingDelete = pendingDeleteInput.value === '1';

            order.textContent = isPendingDelete ? '×' : String(++activeIndex);
            order.classList.toggle('bg-danger', isPendingDelete);
            order.classList.toggle('bg-primary', !isPendingDelete);

            item.querySelectorAll('[data-field]').forEach((field) => {
                const name = field.getAttribute('data-field');
                field.setAttribute('name', `promos[${index}][${name}]`);
            });

            if (titlePreview) titlePreview.textContent = previewTitleText(titleInput);
        });
        if (noMsg) noMsg.style.display = items.length ? 'none' : '';
    }

    function applyPendingDelete(item, removeButton, pendingDeleteInput) {
        pendingDeleteInput.value = '1';
        item.classList.add('is-pending-delete');
        removeButton.classList.remove('btn-outline-danger');
        removeButton.classList.add('btn-outline-secondary');
        removeButton.innerHTML = '<i class="fas fa-undo me-1"></i>Вернуть';
        updateNames();
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
        const sizeSelect = item.querySelector('[data-field="image_size"]');
        const placementSelect = item.querySelector('[data-field="placement"]');
        const activeInput = item.querySelector('[data-field="is_active"]');
        const activeSwitch = item.querySelector('[data-active-switch]');

        if (layoutToggle && imageOnLeftInput) {
            layoutToggle.addEventListener('click', function () {
                imageOnLeftInput.value = imageOnLeftInput.value === '1' ? '0' : '1';
                applyLayout(item);
                renderPreview(item);
            });
        }
        applyLayout(item);

        if (sizeSelect) {
            sizeSelect.addEventListener('change', function () { renderPreview(item); });
        }
        if (placementSelect) {
            placementSelect.addEventListener('change', function () { renderPreview(item); });
        }

        if (activeSwitch && activeInput) {
            activeSwitch.checked = activeInput.value === '1';
            activeSwitch.addEventListener('change', function () {
                activeInput.value = activeSwitch.checked ? '1' : '0';
            });
        }

        titleInput.addEventListener('input', function () {
            titlePreview.textContent = previewTitleText(titleInput);
            renderPreview(item);
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
            if (isBlankNewItem(item)) {
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
                if (pendingDeleteInput.value === '1') return;
                imageInput.click();
            });
        }

        if (previewImage && photoArea) {
            previewImage.addEventListener('load', function () {
                applyImageAspect(item);
            });
            // Уже сохранённое фото: если успело загрузиться до навешивания обработчика.
            if (previewImage.complete && previewImage.naturalWidth) {
                applyImageAspect(item);
            }
        }

        imageInput.addEventListener('change', function () {
            const file = imageInput.files && imageInput.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (event) {
                item._autoSize = true; // только что выбранное фото — подобрать размер автоматически
                previewImage.src = event.target.result;
                previewContainer.style.display = 'block';
                placeholder.style.display = 'none';
                photoArea.classList.add('has-image');
            };
            reader.readAsDataURL(file);
        });

        // Первичная отрисовка превью раскладки.
        renderPreview(item);
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
            if (!deleteConfirmContext) { deleteConfirmModal.hide(); return; }
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
