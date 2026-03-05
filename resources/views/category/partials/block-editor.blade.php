{{-- Блочный редактор категорий --}}
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Контент страницы (блоки)</h5>
        <div class="dropdown">
            <button class="btn btn-success dropdown-toggle" type="button" id="addBlockBtn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-plus me-1"></i> Добавить блок
            </button>
            <ul class="dropdown-menu" aria-labelledby="addBlockBtn">
                <li><a class="dropdown-item" href="#" onclick="addBlock('text'); return false;"><i class="fas fa-align-left me-2"></i>Текст</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBlock('image'); return false;"><i class="fas fa-image me-2"></i>Изображение</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBlock('image_text'); return false;"><i class="fas fa-columns me-2"></i>Картинка + текст</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBlock('features'); return false;"><i class="fas fa-list-ul me-2"></i>Преимущества</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBlock('faq'); return false;"><i class="fas fa-question-circle me-2"></i>FAQ</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBlock('pricing'); return false;"><i class="fas fa-ruble-sign me-2"></i>Детали стоимости</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBlock('gallery'); return false;"><i class="fas fa-images me-2"></i>Галерея</a></li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div id="blocks-container">
            <p id="no-blocks-message" class="text-muted text-center py-3" style="display: none;">
                Нет блоков. Нажмите "Добавить блок", чтобы начать наполнение страницы.
            </p>
        </div>
    </div>
</div>

<input type="hidden" name="blocks" id="blocks-json" value="">

<template id="block-template-text">
    <div class="block-content">
        <label class="form-label fw-bold">Содержимое:</label>
        <div class="quill-editor-block"></div>
    </div>
</template>

<template id="block-template-image">
    <div class="block-content">
        <div class="mb-3">
            <label class="form-label fw-bold">Заголовок блока:</label>
            <input type="text" class="form-control block-field" data-field="title" placeholder="Заголовок (необязательно)">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Изображение:</label>
            <div class="block-image-dropzone" onclick="this.querySelector('input[type=file]').click()">
                <input type="file" class="block-image-input" accept="image/jpeg,image/png,image/heic,image/heif" style="display:none">
                <div class="dropzone-placeholder">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <div>Нажмите или перетащите изображение</div>
                    <small>JPEG, PNG, HEIC — до 5 МБ</small>
                </div>
                <div class="dropzone-preview" style="display:none">
                    <img src="" alt="Preview">
                    <div class="dropzone-change">Заменить</div>
                </div>
            </div>
            <div class="dropzone-error text-danger small mt-1" style="display:none"></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Подпись:</label>
            <input type="text" class="form-control block-field" data-field="caption" placeholder="Подпись к изображению">
        </div>
        <div class="mb-3">
            <label class="form-label">Описание изображения <small class="text-muted">(для людей с нарушениями зрения и поисковиков)</small>:</label>
            <input type="text" class="form-control block-field" data-field="alt" placeholder="Коротко опишите что на фото">
        </div>
    </div>
</template>

<template id="block-template-image_text">
    <div class="block-content">
        <div class="mb-3">
            <label class="form-label fw-bold">Заголовок блока:</label>
            <input type="text" class="form-control block-field" data-field="title" placeholder="Заголовок (необязательно)">
        </div>
        <div class="row mb-2">
            <div class="col-md-6">
                <label class="form-label fw-bold">Размер фото:</label>
                <select class="form-select block-field" data-field="image_size">
                    <option value="3/4">3/4 страницы (75%)</option>
                    <option value="2/3">2/3 страницы (67%)</option>
                    <option value="1/2" selected>1/2 страницы (50%)</option>
                    <option value="1/3">1/3 страницы (33%)</option>
                    <option value="1/4">1/4 страницы (25%)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Расположение:</label>
                <select class="form-select block-field" data-field="layout">
                    <option value="left">Картинка слева</option>
                    <option value="right">Картинка справа</option>
                </select>
            </div>
        </div>
        <div class="row image-text-content-row">
            <div class="col-md-5 image-text-image-col">
                <label class="form-label fw-bold">Изображение:</label>
                <div class="block-image-dropzone" onclick="this.querySelector('input[type=file]').click()">
                    <input type="file" class="block-image-input" accept="image/jpeg,image/png,image/heic,image/heif" style="display:none">
                    <div class="dropzone-placeholder">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <div>Загрузить</div>
                        <small>JPEG, PNG, HEIC — до 5 МБ</small>
                    </div>
                    <div class="dropzone-preview" style="display:none">
                        <img src="" alt="Preview">
                        <div class="dropzone-change">Заменить</div>
                    </div>
                </div>
                <div class="dropzone-error text-danger small mt-1" style="display:none"></div>
            </div>
            <div class="col-md-7 image-text-text-col">
                <label class="form-label fw-bold">Текст:</label>
                <div class="quill-editor-block quill-imagetext"></div>
            </div>
        </div>
    </div>
</template>

<template id="block-template-features">
    <div class="block-content">
        <div class="mb-3">
            <label class="form-label fw-bold">Заголовок секции:</label>
            <input type="text" class="form-control block-field" data-field="title" placeholder="Наши преимущества">
        </div>
        <div class="features-items"></div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-feature-btn">
            <i class="fas fa-plus me-1"></i>Добавить пункт
        </button>
    </div>
</template>

<template id="block-template-faq">
    <div class="block-content">
        <div class="mb-3">
            <label class="form-label fw-bold">Заголовок секции:</label>
            <input type="text" class="form-control block-field" data-field="title" placeholder="Часто задаваемые вопросы">
        </div>
        <div class="faq-items"></div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-faq-btn">
            <i class="fas fa-plus me-1"></i>Добавить вопрос
        </button>
    </div>
</template>

<template id="block-template-pricing">
    <div class="block-content">
        <div class="mb-3">
            <label class="form-label fw-bold">Заголовок блока:</label>
            <input type="text" class="form-control block-field" data-field="title" placeholder="Заголовок (необязательно)">
        </div>
        <div class="pricing-items"></div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-pricing-btn">
            <i class="fas fa-plus me-1"></i>Добавить строку
        </button>
    </div>
</template>

<template id="block-template-gallery">
    <div class="block-content">
        <div class="mb-3">
            <label class="form-label fw-bold">Заголовок блока:</label>
            <input type="text" class="form-control block-field" data-field="title" placeholder="Заголовок (необязательно)">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Изображения:</label>
            <div class="gallery-dropzone" onclick="this.querySelector('input[type=file]').click()">
                <input type="file" class="block-gallery-input" accept="image/jpeg,image/png,image/heic,image/heif" multiple style="display:none">
                <div class="dropzone-placeholder">
                    <i class="fas fa-images"></i>
                    <div>Нажмите или перетащите изображения</div>
                    <small>JPEG, PNG, HEIC — до 5 МБ каждое</small>
                </div>
            </div>
            <div class="dropzone-error text-danger small mt-1" style="display:none"></div>
        </div>
        <div class="gallery-preview mt-2"></div>
    </div>
</template>

<template id="feature-item-template">
    <div class="feature-item card card-body p-2 mb-2">
        <div class="feature-item-row d-flex gap-2 align-items-center">
            <div class="feature-icon-picker-wrap" style="flex: 0 0 42px;">
                <button type="button" class="btn btn-outline-secondary btn-sm feature-icon-btn" style="width:42px;height:38px;font-size:1.2rem;" title="Выбрать иконку">
                    <i class="fas fa-check"></i>
                </button>
                <input type="hidden" class="feature-icon" value="fas fa-check">
            </div>
            <div class="flex-grow-1">
                <input type="text" class="form-control form-control-sm feature-title" placeholder="Заголовок">
            </div>
            <div class="flex-grow-1">
                <input type="text" class="form-control form-control-sm feature-text" placeholder="Описание">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" style="flex: 0 0 auto;"><i class="fas fa-times"></i></button>
        </div>
    </div>
</template>

<template id="faq-item-template">
    <div class="faq-item card card-body p-3 mb-2">
        <div class="d-flex gap-2 align-items-start">
            <div class="flex-grow-1">
                <input type="text" class="form-control mb-2 faq-question" placeholder="Вопрос">
                <textarea class="form-control faq-answer" rows="4" placeholder="Ответ"></textarea>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"><i class="fas fa-times"></i></button>
        </div>
    </div>
</template>

<template id="pricing-item-template">
    <div class="pricing-item card card-body p-2 mb-2">
        <div class="pricing-item-row d-flex gap-2 align-items-center">
            <div class="flex-grow-1">
                <input type="text" class="form-control form-control-sm pricing-label" placeholder="Название (напр. Теория)">
            </div>
            <div class="flex-grow-1">
                <input type="text" class="form-control form-control-sm pricing-value" placeholder="Значение (напр. 20 часов)">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" style="flex: 0 0 auto;"><i class="fas fa-times"></i></button>
        </div>
    </div>
</template>

<div class="modal fade" id="iconPickerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title">Выберите иконку</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2">
                <div class="icon-picker-grid" id="iconPickerGrid"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .block-wrapper {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
        background: #fff;
    }
    .block-wrapper.collapsed .block-body {
        display: none;
    }
    .block-header {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-radius: 0.375rem 0.375rem 0 0;
        cursor: grab;
    }
    .block-header:active { cursor: grabbing; }
    .block-header .drag-handle { margin-right: 0.75rem; color: #adb5bd; }
    .block-header .block-type-label { font-weight: 600; flex-grow: 1; }
    .block-body { padding: 1rem; overflow: hidden; }
    .sortable-ghost { opacity: 0.4; background: #e9ecef; }

    .block-wrapper .ql-container {
        min-height: 200px;
        font-size: 1rem;
    }
    .block-wrapper .ql-editor {
        min-height: 200px;
        cursor: text;
    }

    .block-wrapper .image-text-text-col .ql-toolbar {
        flex-wrap: wrap;
    }
    .block-wrapper .image-text-text-col {
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .block-wrapper .image-text-text-col .quill-imagetext {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .block-wrapper .image-text-text-col .ql-container {
        min-height: 0;
        flex: 1;
        overflow-y: auto;
    }
    .block-wrapper .image-text-text-col .ql-editor {
        min-height: 150px;
    }
    .block-wrapper .image-text-content-row {
        min-height: 300px;
    }

    .block-image-dropzone,
    .gallery-dropzone {
        border: 2px dashed #ced4da;
        border-radius: 0.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafafa;
        position: relative;
        overflow: hidden;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .block-image-dropzone:hover,
    .gallery-dropzone:hover {
        border-color: #0d6efd;
        background: #f0f7ff;
    }
    .block-image-dropzone.has-image {
        border-style: solid;
    }
    .block-image-dropzone.drag-over,
    .gallery-dropzone.drag-over {
        border-color: #0d6efd;
        background: #f0f7ff;
    }
    .dropzone-placeholder {
        padding: 1.5rem;
        color: #6c757d;
    }
    .dropzone-placeholder i {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .dropzone-placeholder small {
        color: #adb5bd;
    }
    .dropzone-preview {
        width: 100%;
    }
    .dropzone-preview img {
        max-height: 250px;
        max-width: 100%;
        object-fit: contain;
        display: block;
        margin: 0.5rem auto;
    }
    .dropzone-change {
        background: rgba(0,0,0,0.6);
        color: #fff;
        text-align: center;
        padding: 0.4rem;
        font-size: 0.85rem;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .block-image-dropzone:hover .dropzone-change {
        opacity: 1;
    }

    .icon-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(48px, 1fr));
        gap: 4px;
        max-height: 400px;
        overflow-y: auto;
        padding: 0.25rem;
    }
    .icon-picker-item {
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
    .icon-picker-item:hover {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    .icon-picker-item.selected {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    .gallery-preview {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 0.5rem;
    }
    @media (max-width: 1199.98px) {
        .gallery-preview { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    @media (max-width: 991.98px) {
        .gallery-preview { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    @media (max-width: 575.98px) {
        .gallery-preview { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    .gallery-preview img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        display: block;
    }
    @media (max-width: 991.98px) {
        .gallery-preview img { height: 150px; }
    }
    @media (max-width: 575.98px) {
        .gallery-preview img { height: 130px; }
    }
    .gallery-item {
        position: relative;
    }
    .gallery-item .remove-gallery-img {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        border: none;
        font-size: 14px;
        line-height: 24px;
        text-align: center;
        padding: 0;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .gallery-item:hover .remove-gallery-img {
        opacity: 1;
    }
    .gallery-item .remove-gallery-img:hover {
        background: rgba(220, 53, 69, 0.85);
    }
    .gallery-item {
        cursor: grab;
    }
    .gallery-item:active {
        cursor: grabbing;
    }

    .feature-item-row,
    .pricing-item-row {
        width: 100%;
    }
    .feature-item-row > .flex-grow-1,
    .pricing-item-row > .flex-grow-1 {
        min-width: 0;
    }
    .feature-item-row .feature-title,
    .feature-item-row .feature-text,
    .pricing-item-row .pricing-label,
    .pricing-item-row .pricing-value {
        width: 100%;
    }

    @media (max-width: 767.98px) {
        .feature-item-row,
        .pricing-item-row {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
        }
        .feature-item-row .feature-icon-picker-wrap,
        .feature-item-row .remove-item-btn,
        .pricing-item-row .remove-item-btn {
            align-self: center;
        }
    }

    @media (min-width: 768px) {
        .feature-item-row,
        .pricing-item-row {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
        }
    }
</style>
