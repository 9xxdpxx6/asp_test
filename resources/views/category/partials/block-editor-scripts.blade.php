<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==================== Состояние ====================
    let blocks = [];
    let blockIdCounter = 0;
    let quillInstances = {};

    // Ограничения на фото
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/heic', 'image/heif'];
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 МБ
    const MAX_GALLERY_IMAGES = 10;
    const MSG_INVALID_FORMAT = '\u041d\u0435\u0434\u043e\u043f\u0443\u0441\u0442\u0438\u043c\u044b\u0439 \u0444\u043e\u0440\u043c\u0430\u0442. \u0420\u0430\u0437\u0440\u0435\u0448\u0435\u043d\u044b: JPEG, PNG, HEIC';
    const MSG_FILE_TOO_LARGE_PREFIX = '\u0424\u0430\u0439\u043b \u0441\u043b\u0438\u0448\u043a\u043e\u043c \u0431\u043e\u043b\u044c\u0448\u043e\u0439. \u041c\u0430\u043a\u0441\u0438\u043c\u0443\u043c 5 \u041c\u0411 (\u0441\u0435\u0439\u0447\u0430\u0441 ';
    const MSG_FILE_TOO_LARGE_SUFFIX = ' \u041c\u0411)';
    const MSG_GALLERY_LIMIT = '\u041c\u043e\u0436\u043d\u043e \u0434\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u043d\u0435 \u0431\u043e\u043b\u0435\u0435 ' + MAX_GALLERY_IMAGES + ' \u0444\u043e\u0442\u043e \u0432 \u0433\u0430\u043b\u0435\u0440\u0435\u044e.';
    const MSG_CONFIRM_DELETE_BLOCK = '\u0423\u0434\u0430\u043b\u0438\u0442\u044c \u044d\u0442\u043e\u0442 \u0431\u043b\u043e\u043a?';
    const MSG_TOGGLE_BLOCK = '\u0421\u0432\u0435\u0440\u043d\u0443\u0442\u044c/\u0420\u0430\u0437\u0432\u0435\u0440\u043d\u0443\u0442\u044c';
    const MSG_DELETE_BLOCK = '\u0423\u0434\u0430\u043b\u0438\u0442\u044c \u0431\u043b\u043e\u043a';
    const MSG_EDITOR_PLACEHOLDER = '\u041d\u0430\u0447\u043d\u0438\u0442\u0435 \u043f\u0435\u0447\u0430\u0442\u0430\u0442\u044c...';

    function validateImageFile(file, errorEl) {
        if (errorEl) errorEl.style.display = 'none';
        if (!ALLOWED_TYPES.includes(file.type)) {
            if (errorEl) {
                errorEl.textContent = MSG_INVALID_FORMAT;
                errorEl.style.display = 'block';
            }
            return false;
        }
        if (file.size > MAX_FILE_SIZE) {
            if (errorEl) {
                errorEl.textContent = MSG_FILE_TOO_LARGE_PREFIX + (file.size / 1024 / 1024).toFixed(1) + MSG_FILE_TOO_LARGE_SUFFIX;
                errorEl.style.display = 'block';
            }
            return false;
        }
        return true;
    }

    const blockTypeLabels = {
        'text': '<i class="fas fa-align-left me-1"></i> \u0422\u0435\u043a\u0441\u0442',
        'image': '<i class="fas fa-image me-1"></i> \u0418\u0437\u043e\u0431\u0440\u0430\u0436\u0435\u043d\u0438\u0435',
        'image_text': '<i class="fas fa-columns me-1"></i> \u041a\u0430\u0440\u0442\u0438\u043d\u043a\u0430 + \u0442\u0435\u043a\u0441\u0442',
        'features': '<i class="fas fa-list-ul me-1"></i> \u041f\u0440\u0435\u0438\u043c\u0443\u0449\u0435\u0441\u0442\u0432\u0430',
        'faq': '<i class="fas fa-question-circle me-1"></i> FAQ',
        'pricing': '<i class="fas fa-ruble-sign me-1"></i> \u0414\u0435\u0442\u0430\u043b\u0438 \u0441\u0442\u043e\u0438\u043c\u043e\u0441\u0442\u0438',
        'gallery': '<i class="fas fa-images me-1"></i> \u0413\u0430\u043b\u0435\u0440\u0435\u044f',
    };

    // ==================== Список иконок для пикера ====================
    const availableIcons = [
        'fas fa-check', 'fas fa-check-circle', 'fas fa-star', 'fas fa-heart',
        'fas fa-thumbs-up', 'fas fa-award', 'fas fa-medal', 'fas fa-trophy',
        'fas fa-shield-alt', 'fas fa-lock', 'fas fa-key', 'fas fa-user-shield',
        'fas fa-car', 'fas fa-motorcycle', 'fas fa-truck', 'fas fa-bus',
        'fas fa-taxi', 'fas fa-road', 'fas fa-route', 'fas fa-map-marked-alt',
        'fas fa-flag-checkered', 'fas fa-gas-pump', 'fas fa-parking',
        'fas fa-clock', 'fas fa-calendar-alt', 'fas fa-hourglass-half',
        'fas fa-graduation-cap', 'fas fa-book', 'fas fa-book-open', 'fas fa-chalkboard-teacher',
        'fas fa-user-graduate', 'fas fa-school', 'fas fa-university',
        'fas fa-clipboard-check', 'fas fa-clipboard-list', 'fas fa-tasks',
        'fas fa-cog', 'fas fa-cogs', 'fas fa-wrench', 'fas fa-tools',
        'fas fa-bolt', 'fas fa-fire', 'fas fa-gem', 'fas fa-crown',
        'fas fa-dollar-sign', 'fas fa-ruble-sign', 'fas fa-percent',
        'fas fa-hand-holding-usd', 'fas fa-piggy-bank', 'fas fa-wallet',
        'fas fa-users', 'fas fa-user-friends', 'fas fa-people-carry',
        'fas fa-headset', 'fas fa-phone-alt', 'fas fa-envelope',
        'fas fa-comment', 'fas fa-comments', 'fas fa-life-ring',
        'fas fa-handshake', 'fas fa-hands-helping',
        'fas fa-chart-line', 'fas fa-chart-bar', 'fas fa-chart-pie',
        'fas fa-arrow-up', 'fas fa-arrow-right', 'fas fa-bullseye',
        'fas fa-lightbulb', 'fas fa-magic', 'fas fa-rocket',
        'fas fa-shield-virus', 'fas fa-camera', 'fas fa-video',
        'fas fa-wifi', 'fas fa-laptop', 'fas fa-mobile-alt',
        'fas fa-map', 'fas fa-map-marker-alt', 'fas fa-compass',
        'fas fa-sun', 'fas fa-moon', 'fas fa-umbrella',
        'fas fa-home', 'fas fa-building', 'fas fa-warehouse',
        'fas fa-certificate', 'fas fa-id-card', 'fas fa-file-alt',
        'fas fa-file-contract', 'fas fa-file-invoice',
        'fas fa-smile', 'fas fa-smile-beam', 'fas fa-laugh-beam',
        'fas fa-eye', 'fas fa-binoculars', 'fas fa-search',
        'fas fa-signal', 'fas fa-tachometer-alt', 'fas fa-battery-full',
    ];

    // ==================== Инициализация модала иконок ====================
    let currentIconBtn = null;
    let currentIconInput = null;
    const iconGrid = document.getElementById('iconPickerGrid');
    const iconModal = new bootstrap.Modal(document.getElementById('iconPickerModal'));

    availableIcons.forEach(iconClass => {
        const item = document.createElement('div');
        item.className = 'icon-picker-item';
        item.innerHTML = '<i class="' + iconClass + '"></i>';
        item.dataset.icon = iconClass;
        item.addEventListener('click', function() {
            if (currentIconBtn && currentIconInput) {
                currentIconInput.value = iconClass;
                currentIconBtn.innerHTML = '<i class="' + iconClass + '"></i>';
                iconGrid.querySelectorAll('.icon-picker-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                iconModal.hide();
            }
        });
        iconGrid.appendChild(item);
    });

    // ==================== Инициализация SortableJS ====================
    const container = document.getElementById('blocks-container');
    Sortable.create(container, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'sortable-ghost',
        onEnd: function() {
            updateBlockOrder();
        }
    });

    // ==================== Публичная функция добавления блока ====================
    window.addBlock = function(type, content = null, existingId = null) {
        const clientId = 'block_' + (blockIdCounter++);
        const blockData = {
            clientId: clientId,
            serverId: existingId || null,
            type: type,
            content: content || getDefaultContent(type),
        };
        blocks.push(blockData);
        renderBlock(blockData);
        updateNoBlocksMessage();
    };

    // ==================== Контент по умолчанию ====================
    function getDefaultContent(type) {
        switch (type) {
            case 'text': return { html: '' };
            case 'image': return { title: '', url: '', caption: '', alt: '' };
            case 'image_text': return { title: '', image_url: '', html: '', layout: 'left' };
            case 'features': return { title: '\u041d\u0430\u0448\u0438 \u043f\u0440\u0435\u0438\u043c\u0443\u0449\u0435\u0441\u0442\u0432\u0430', items: [] };
            case 'faq': return { title: '\u0427\u0430\u0441\u0442\u043e \u0437\u0430\u0434\u0430\u0432\u0430\u0435\u043c\u044b\u0435 \u0432\u043e\u043f\u0440\u043e\u0441\u044b', items: [] };
            case 'pricing': return { title: '', items: [] };
            case 'gallery': return { title: '', images: [] };
            default: return {};
        }
    }

    // ==================== Рендер блока ====================
    function renderBlock(blockData) {
        const wrapper = document.createElement('div');
        wrapper.className = 'block-wrapper';
        wrapper.dataset.clientId = blockData.clientId;

        const header = document.createElement('div');
        header.className = 'block-header';
        header.innerHTML = `
            <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
            <span class="block-type-label">${blockTypeLabels[blockData.type] || blockData.type}</span>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1 toggle-block-btn" title="${MSG_TOGGLE_BLOCK}">
                <i class="fas fa-chevron-up"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger delete-block-btn" title="${MSG_DELETE_BLOCK}">
                <i class="fas fa-trash"></i>
            </button>
        `;
        wrapper.appendChild(header);

        const body = document.createElement('div');
        body.className = 'block-body';

        const template = document.getElementById('block-template-' + blockData.type);
        if (template) {
            body.appendChild(template.content.cloneNode(true));
        }
        wrapper.appendChild(body);
        container.appendChild(wrapper);

        initBlockContent(wrapper, blockData);

        header.querySelector('.toggle-block-btn').addEventListener('click', function() {
            wrapper.classList.toggle('collapsed');
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-chevron-up');
            icon.classList.toggle('fa-chevron-down');
        });

        header.querySelector('.delete-block-btn').addEventListener('click', function() {
            if (confirm(MSG_CONFIRM_DELETE_BLOCK)) {
                if (quillInstances[blockData.clientId]) {
                    delete quillInstances[blockData.clientId];
                }
                wrapper.remove();
                blocks = blocks.filter(b => b.clientId !== blockData.clientId);
                updateNoBlocksMessage();
            }
        });
    }

    // ==================== Инициализация содержимого блока по типу ====================
    function initBlockContent(wrapper, blockData) {
        const type = blockData.type;
        const content = blockData.content;

        switch (type) {
            case 'text': initTextBlock(wrapper, blockData, content); break;
            case 'image': initImageBlock(wrapper, blockData, content); break;
            case 'image_text': initImageTextBlock(wrapper, blockData, content); break;
            case 'features': initFeaturesBlock(wrapper, blockData, content); break;
            case 'faq': initFaqBlock(wrapper, blockData, content); break;
            case 'pricing': initPricingBlock(wrapper, blockData, content); break;
            case 'gallery': initGalleryBlock(wrapper, blockData, content); break;
        }
    }

    // ==================== ТЕКСТ ====================
    function initTextBlock(wrapper, blockData, content) {
        const editorEl = wrapper.querySelector('.quill-editor-block');
        const quill = new Quill(editorEl, {
            theme: 'snow',
            placeholder: MSG_EDITOR_PLACEHOLDER,
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });
        quill.root.style.fontFamily = 'Cygre, sans-serif';
        if (content.html) {
            quill.clipboard.dangerouslyPasteHTML(content.html);
        }
        quillInstances[blockData.clientId] = quill;
    }

    // ==================== ИЗОБРАЖЕНИЕ ====================
    function initImageBlock(wrapper, blockData, content) {
        fillBlockFields(wrapper, content);

        const dropzone = wrapper.querySelector('.block-image-dropzone');
        const fileInput = wrapper.querySelector('.block-image-input');
        const placeholder = dropzone.querySelector('.dropzone-placeholder');
        const previewWrap = dropzone.querySelector('.dropzone-preview');
        const previewImg = previewWrap.querySelector('img');
        const errorEl = wrapper.querySelector('.dropzone-error');

        if (content.url) {
            previewImg.src = content.url;
            previewWrap.style.display = 'block';
            placeholder.style.display = 'none';
            dropzone.classList.add('has-image');
        }

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && validateImageFile(file, errorEl)) {
                readAndShowImage(file, previewImg, previewWrap, placeholder, dropzone, function(dataUrl) {
                    blockData.content.url = dataUrl;
                });
            }
        });

        setupDropzoneDragDrop(dropzone, fileInput, errorEl, function(dataUrl) {
            previewImg.src = dataUrl;
            previewWrap.style.display = 'block';
            placeholder.style.display = 'none';
            dropzone.classList.add('has-image');
            blockData.content.url = dataUrl;
        });
    }

    // ==================== КАРТИНКА + ТЕКСТ ====================
    function initImageTextBlock(wrapper, blockData, content) {
        fillBlockFields(wrapper, content);

        const dropzone = wrapper.querySelector('.block-image-dropzone');
        const fileInput = wrapper.querySelector('.block-image-input');
        const placeholder = dropzone.querySelector('.dropzone-placeholder');
        const previewWrap = dropzone.querySelector('.dropzone-preview');
        const previewImg = previewWrap.querySelector('img');
        const errorEl = wrapper.querySelector('.dropzone-error');
        const editorEl = wrapper.querySelector('.quill-editor-block');

        // Живое переключение раскладки
        const layoutSelect = wrapper.querySelector('.block-field[data-field="layout"]');
        const rowEl = wrapper.querySelector('.block-content > .row');
        if (layoutSelect && rowEl) {
            function applyLayout(val) {
                if (val === 'right') {
                    rowEl.classList.add('flex-row-reverse');
                } else {
                    rowEl.classList.remove('flex-row-reverse');
                }
            }
            applyLayout(layoutSelect.value);
            layoutSelect.addEventListener('change', function() {
                applyLayout(this.value);
            });
        }

        if (content.image_url) {
            previewImg.src = content.image_url;
            previewWrap.style.display = 'block';
            placeholder.style.display = 'none';
            dropzone.classList.add('has-image');
        }

        const quill = new Quill(editorEl, {
            theme: 'snow',
            placeholder: MSG_EDITOR_PLACEHOLDER,
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });
        quill.root.style.fontFamily = 'Cygre, sans-serif';
        if (content.html) {
            quill.clipboard.dangerouslyPasteHTML(content.html);
        }
        quillInstances[blockData.clientId] = quill;

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && validateImageFile(file, errorEl)) {
                readAndShowImage(file, previewImg, previewWrap, placeholder, dropzone, function(dataUrl) {
                    blockData.content.image_url = dataUrl;
                });
            }
        });

        setupDropzoneDragDrop(dropzone, fileInput, errorEl, function(dataUrl) {
            previewImg.src = dataUrl;
            previewWrap.style.display = 'block';
            placeholder.style.display = 'none';
            dropzone.classList.add('has-image');
            blockData.content.image_url = dataUrl;
        });
    }

    // ==================== ПРЕИМУЩЕСТВА ====================
    function initFeaturesBlock(wrapper, blockData, content) {
        fillBlockFields(wrapper, content);
        const itemsContainer = wrapper.querySelector('.features-items');
        const addBtn = wrapper.querySelector('.add-feature-btn');

        if (content.items && content.items.length > 0) {
            content.items.forEach(item => addFeatureItem(itemsContainer, item));
        }

        addBtn.addEventListener('click', function() {
            addFeatureItem(itemsContainer, { icon: 'fas fa-check', title: '', text: '' });
        });
    }

    function addFeatureItem(container, data) {
        const template = document.getElementById('feature-item-template');
        const item = template.content.cloneNode(true);
        const el = item.querySelector('.feature-item');

        const iconBtn = el.querySelector('.feature-icon-btn');
        const iconInput = el.querySelector('.feature-icon');
        const iconClass = data.icon || 'fas fa-check';

        iconInput.value = iconClass;
        iconBtn.innerHTML = '<i class="' + iconClass + '"></i>';

        iconBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            currentIconBtn = iconBtn;
            currentIconInput = iconInput;
            iconGrid.querySelectorAll('.icon-picker-item').forEach(i => {
                i.classList.toggle('selected', i.dataset.icon === iconInput.value);
            });
            iconModal.show();
        });

        el.querySelector('.feature-title').value = data.title || '';
        el.querySelector('.feature-text').value = data.text || '';
        el.querySelector('.remove-item-btn').addEventListener('click', function() {
            el.remove();
        });
        container.appendChild(item);
    }

    // ==================== FAQ ====================
    function initFaqBlock(wrapper, blockData, content) {
        fillBlockFields(wrapper, content);
        const itemsContainer = wrapper.querySelector('.faq-items');
        const addBtn = wrapper.querySelector('.add-faq-btn');

        if (content.items && content.items.length > 0) {
            content.items.forEach(item => addFaqItem(itemsContainer, item));
        }

        addBtn.addEventListener('click', function() {
            addFaqItem(itemsContainer, { question: '', answer: '' });
        });
    }

    function addFaqItem(container, data) {
        const template = document.getElementById('faq-item-template');
        const item = template.content.cloneNode(true);
        const el = item.querySelector('.faq-item');
        el.querySelector('.faq-question').value = data.question || '';
        el.querySelector('.faq-answer').value = data.answer || '';
        el.querySelector('.remove-item-btn').addEventListener('click', function() {
            el.remove();
        });
        container.appendChild(item);
    }

    // ==================== ПРАЙСИНГ ====================
    function initPricingBlock(wrapper, blockData, content) {
        fillBlockFields(wrapper, content);
        const itemsContainer = wrapper.querySelector('.pricing-items');
        const addBtn = wrapper.querySelector('.add-pricing-btn');

        if (content.items && content.items.length > 0) {
            content.items.forEach(item => addPricingItem(itemsContainer, item));
        }

        addBtn.addEventListener('click', function() {
            addPricingItem(itemsContainer, { label: '', value: '' });
        });
    }

    function addPricingItem(container, data) {
        const template = document.getElementById('pricing-item-template');
        const item = template.content.cloneNode(true);
        const el = item.querySelector('.pricing-item');
        el.querySelector('.pricing-label').value = data.label || '';
        el.querySelector('.pricing-value').value = data.value || '';
        el.querySelector('.remove-item-btn').addEventListener('click', function() {
            el.remove();
        });
        container.appendChild(item);
    }

    // ==================== ГАЛЕРЕЯ ====================
    function initGalleryBlock(wrapper, blockData, content) {
        fillBlockFields(wrapper, content);
        const galleryDropzone = wrapper.querySelector('.gallery-dropzone');
        const fileInput = wrapper.querySelector('.block-gallery-input');
        const preview = wrapper.querySelector('.gallery-preview');
        const errorEl = wrapper.querySelector('.dropzone-error');

        if (content.images && content.images.length > 0) {
            content.images.slice(0, MAX_GALLERY_IMAGES).forEach(img => addGalleryPreview(preview, img.url, img.alt));
            if (content.images.length > MAX_GALLERY_IMAGES) {
                showError(errorEl, MSG_GALLERY_LIMIT);
            }
        }

        fileInput.addEventListener('change', function(e) {
            processGalleryFiles(e.target.files, preview, errorEl);
            fileInput.value = '';
        });

        // Drag and drop для загрузки фото
        setupGalleryDropzoneDragDrop(galleryDropzone, fileInput, errorEl, preview);

        // SortableJS для перетаскивания порядка фото
        Sortable.create(preview, {
            animation: 150,
            ghostClass: 'sortable-ghost',
        });
    }

    function addGalleryPreview(container, url, alt, errorEl) {
        const item = document.createElement('div');
        item.className = 'gallery-item';
        item.innerHTML = `
            <img src="${url}" alt="${alt || ''}" data-url="${url}">
            <button type="button" class="remove-gallery-img">&times;</button>
        `;
        item.querySelector('.remove-gallery-img').addEventListener('click', function() {
            item.remove();
            if (errorEl && container.querySelectorAll('.gallery-item').length <= MAX_GALLERY_IMAGES) {
                hideError(errorEl);
            }
        });
        container.appendChild(item);
    }

    function processGalleryFiles(files, previewContainer, errorEl) {
        hideError(errorEl);

        const incoming = Array.from(files || []);
        if (incoming.length === 0) return;

        let freeSlots = MAX_GALLERY_IMAGES - previewContainer.querySelectorAll('.gallery-item').length;
        if (freeSlots <= 0) {
            showError(errorEl, MSG_GALLERY_LIMIT);
            return;
        }

        let overflowByLimit = false;
        incoming.forEach(file => {
            if (!validateImageFile(file, errorEl)) return;

            if (freeSlots <= 0) {
                overflowByLimit = true;
                return;
            }

            freeSlots -= 1;
            const reader = new FileReader();
            reader.onload = function(ev) {
                addGalleryPreview(previewContainer, ev.target.result, file.name, errorEl);
            };
            reader.readAsDataURL(file);
        });

        if (overflowByLimit) {
            showError(errorEl, MSG_GALLERY_LIMIT);
        }
    }

    function showError(errorEl, message) {
        if (!errorEl) return;
        errorEl.textContent = message;
        errorEl.style.display = 'block';
    }

    function hideError(errorEl) {
        if (!errorEl) return;
        errorEl.style.display = 'none';
    }

    // ==================== Хелпер чтения файла ====================
    function readAndShowImage(file, previewImg, previewWrap, placeholder, dropzone, callback) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            previewImg.src = ev.target.result;
            previewWrap.style.display = 'block';
            placeholder.style.display = 'none';
            dropzone.classList.add('has-image');
            if (callback) callback(ev.target.result);
        };
        reader.readAsDataURL(file);
    }

    // ==================== Drag & Drop хелпер (одиночное изображение) ====================
    function setupDropzoneDragDrop(dropzone, fileInput, errorEl, onLoad) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('drag-over');
            });
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('drag-over');
            });
        });
        dropzone.addEventListener('drop', function(e) {
            const files = e.dataTransfer.files;
            if (files.length > 0 && validateImageFile(files[0], errorEl)) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(files[0]);
                fileInput.files = dataTransfer.files;

                const reader = new FileReader();
                reader.onload = function(ev) {
                    onLoad(ev.target.result);
                };
                reader.readAsDataURL(files[0]);
            }
        });
    }

    // ==================== Drag & Drop хелпер (галерея — множественные) ====================
    function setupGalleryDropzoneDragDrop(dropzone, fileInput, errorEl, previewContainer) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('drag-over');
            });
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('drag-over');
            });
        });
        dropzone.addEventListener('drop', function(e) {
            processGalleryFiles(e.dataTransfer.files, previewContainer, errorEl);
        });
    }

    // ==================== Вспомогательные функции ====================
    function fillBlockFields(wrapper, content) {
        wrapper.querySelectorAll('.block-field').forEach(field => {
            const key = field.dataset.field;
            if (content[key] !== undefined) {
                field.value = content[key];
            }
        });
    }

    function updateBlockOrder() {
        const wrappers = container.querySelectorAll('.block-wrapper');
        const newBlocks = [];
        wrappers.forEach(wrapper => {
            const clientId = wrapper.dataset.clientId;
            const block = blocks.find(b => b.clientId === clientId);
            if (block) newBlocks.push(block);
        });
        blocks = newBlocks;
    }

    function updateNoBlocksMessage() {
        const msg = document.getElementById('no-blocks-message');
        if (msg) {
            msg.style.display = blocks.length === 0 ? 'block' : 'none';
        }
    }

    // ==================== Сбор данных перед отправкой ====================
    function collectBlocksData() {
        updateBlockOrder();
        const result = [];
        const wrappers = container.querySelectorAll('.block-wrapper');

        wrappers.forEach((wrapper, index) => {
            const clientId = wrapper.dataset.clientId;
            const blockData = blocks.find(b => b.clientId === clientId);
            if (!blockData) return;

            const collected = {
                id: blockData.serverId || null,
                type: blockData.type,
                content: collectBlockContent(wrapper, blockData),
                sort_order: index,
            };
            result.push(collected);
        });

        return result;
    }

    function collectBlockContent(wrapper, blockData) {
        const type = blockData.type;
        let content = {};

        switch (type) {
            case 'text':
                if (quillInstances[blockData.clientId]) {
                    content.html = quillInstances[blockData.clientId].root.innerHTML;
                }
                break;

            case 'image':
                content.title = getFieldValue(wrapper, 'title');
                const dropzoneImg = wrapper.querySelector('.dropzone-preview img');
                content.url = dropzoneImg && dropzoneImg.src ? dropzoneImg.src : (blockData.content.url || '');
                content.caption = getFieldValue(wrapper, 'caption');
                content.alt = getFieldValue(wrapper, 'alt');
                break;

            case 'image_text':
                content.title = getFieldValue(wrapper, 'title');
                const itDropzoneImg = wrapper.querySelector('.dropzone-preview img');
                content.image_url = itDropzoneImg && itDropzoneImg.src ? itDropzoneImg.src : (blockData.content.image_url || '');
                content.layout = getFieldValue(wrapper, 'layout');
                if (quillInstances[blockData.clientId]) {
                    content.html = quillInstances[blockData.clientId].root.innerHTML;
                }
                break;

            case 'features':
                content.title = getFieldValue(wrapper, 'title');
                content.items = [];
                wrapper.querySelectorAll('.feature-item').forEach(item => {
                    content.items.push({
                        icon: item.querySelector('.feature-icon').value,
                        title: item.querySelector('.feature-title').value,
                        text: item.querySelector('.feature-text').value,
                    });
                });
                break;

            case 'faq':
                content.title = getFieldValue(wrapper, 'title');
                content.items = [];
                wrapper.querySelectorAll('.faq-item').forEach(item => {
                    content.items.push({
                        question: item.querySelector('.faq-question').value,
                        answer: item.querySelector('.faq-answer').value,
                    });
                });
                break;

            case 'pricing':
                content.title = getFieldValue(wrapper, 'title');
                content.items = [];
                wrapper.querySelectorAll('.pricing-item').forEach(item => {
                    content.items.push({
                        label: item.querySelector('.pricing-label').value,
                        value: item.querySelector('.pricing-value').value,
                    });
                });
                break;

            case 'gallery':
                content.title = getFieldValue(wrapper, 'title');
                content.images = [];
                wrapper.querySelectorAll('.gallery-item img').forEach(img => {
                    content.images.push({
                        url: img.dataset.url || img.src,
                        alt: img.alt || '',
                    });
                });
                break;
        }

        return content;
    }

    function getFieldValue(wrapper, fieldName) {
        const field = wrapper.querySelector(`.block-field[data-field="${fieldName}"]`);
        return field ? field.value : '';
    }

    // ==================== Привязка к форме ====================
    window.collectAndSetBlocks = function() {
        const data = collectBlocksData();
        document.getElementById('blocks-json').value = JSON.stringify(data);
    };

    // ==================== Загрузка существующих блоков ====================
    window.loadExistingBlocks = function(existingBlocks) {
        if (!existingBlocks || existingBlocks.length === 0) {
            updateNoBlocksMessage();
            return;
        }
        existingBlocks.forEach(block => {
            addBlock(block.type, block.content, block.id);
        });
        updateNoBlocksMessage();
    };

    updateNoBlocksMessage();
});
</script>

