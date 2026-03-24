<script>
(function () {
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

    const ALLOWED_PHOTO_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    const MAX_PHOTO_BYTES = 8 * 1024 * 1024;

    let currentIconBtn = null;
    let currentIconInput = null;
    let iconModal = null;

    document.addEventListener('DOMContentLoaded', function () {
        const grid = document.getElementById('heroIconPickerGrid');
        const modalEl = document.getElementById('heroIconPickerModal');
        const form = document.getElementById('hero-settings-form');
        const list = document.getElementById('trust-items-list');
        const ctaList = document.getElementById('hero-cta-buttons-list');
        const liveBg = document.getElementById('hero-live-bg');
        const trustStrip = document.getElementById('pv-trust-strip');

        if (modalEl && typeof bootstrap !== 'undefined') {
            iconModal = new bootstrap.Modal(modalEl);
        }

        function escapeHtml(s) {
            const d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }

        function syncHeroButtonsPreview() {
            const container = document.getElementById('hero-live-btns');
            if (!container || !ctaList) return;
            const dir = document.getElementById('buttons_direction')?.value || 'row';
            const rows = ctaList.querySelectorAll('.hero-cta-item');
            const parts = [];
            rows.forEach(function (row) {
                const labelIn = row.querySelector('.hero-cta-label-input');
                const iconIn = row.querySelector('.hero-icon-picker-value');
                const varSel = row.querySelector('.hero-cta-variant-select');
                const label = (labelIn && labelIn.value) ? labelIn.value.trim() : '';
                const icon = (iconIn && iconIn.value) ? iconIn.value.trim() : 'fas fa-check';
                const solid = varSel && varSel.value === 'light';
                const pill = '<span class="pv-pill ' + (solid ? 'pv-solid' : '') + '"><i class="' + icon + ' hero-preview-btn-icon" aria-hidden="true"></i><span class="pv-btn-text">' + escapeHtml(label || 'Кнопка') + '</span></span>';
                if (dir === 'row') {
                    parts.push('<span class="hero-live-cta-slot">' + pill + '</span>');
                } else {
                    parts.push(pill);
                }
            });
            container.innerHTML = parts.join('');
        }

        /** Согласовано с HeroSection.vue: в колонке слева/центр/справа — align-items */
        function syncButtonsLayoutPreview() {
            const alignEl = document.getElementById('buttons_align');
            const dirEl = document.getElementById('buttons_direction');
            const el = document.getElementById('hero-live-btns');
            if (!el || !alignEl || !dirEl) return;
            const align = alignEl.value || 'center';
            const dir = dirEl.value || 'row';

            const alignNorm = align === 'around' ? 'between' : align;

            const justifyRow = {
                center: 'justify-content-center',
                start: 'justify-content-start',
                end: 'justify-content-end',
                between: 'justify-content-between',
            };
            const alignCross = {
                center: 'align-items-center',
                start: 'align-items-start',
                end: 'align-items-end',
                between: 'align-items-center',
            };

            if (dir !== 'column') {
                var useSlots = alignNorm === 'between';
                var slotMod = useSlots ? ' hero-live-btns--slots' : '';
                el.className = 'hero-live-btns d-flex' + slotMod + ' ' + (justifyRow[alignNorm] || justifyRow.center) + ' flex-row align-items-stretch';
                return;
            }
            el.classList.remove('hero-live-btns--slots');
            if (alignNorm === 'between') {
                el.className = 'hero-live-btns d-flex flex-column ' + alignCross.between + ' justify-content-between';
                return;
            }
            el.className = 'hero-live-btns d-flex flex-column ' + (alignCross[alignNorm] || 'align-items-center') + ' justify-content-start';
        }

        function reindexCtaButtons() {
            if (!ctaList) return;
            ctaList.querySelectorAll('.hero-cta-item').forEach(function (li, idx) {
                li.querySelectorAll('[name^="cta_buttons"]').forEach(function (inp) {
                    inp.name = inp.name.replace(/cta_buttons\[\d+\]/, 'cta_buttons[' + idx + ']');
                });
                const labelIn = li.querySelector('.hero-cta-label-input');
                if (labelIn) labelIn.id = 'hero_cta_label_' + idx;
                const hrefSel = li.querySelector('select.hero-cta-href-select');
                if (hrefSel) hrefSel.id = 'hero_cta_href_' + idx;
                const varSel = li.querySelector('select.hero-cta-variant-select');
                if (varSel) varSel.id = 'hero_cta_variant_' + idx;
            });
        }

        function updateCtaUiChrome() {
            const items = document.querySelectorAll('#hero-cta-buttons-list .hero-cta-item');
            const n = items.length;
            items.forEach(function (li) {
                const rm = li.querySelector('.hero-cta-remove');
                if (rm) rm.style.visibility = n <= 1 ? 'hidden' : 'visible';
            });
            const addBtn = document.getElementById('hero-cta-add');
            if (addBtn) addBtn.style.display = n >= 3 ? 'none' : '';
        }

        function bindCtaRow(row) {
            row.querySelector('.hero-cta-remove')?.addEventListener('click', function () {
                const items = document.querySelectorAll('#hero-cta-buttons-list .hero-cta-item');
                if (items.length <= 1) return;
                row.remove();
                reindexCtaButtons();
                updateCtaUiChrome();
                syncHeroButtonsPreview();
            });
        }

        if (ctaList && typeof window.Sortable !== 'undefined') {
            Sortable.create(ctaList, {
                handle: '.hero-cta-handle',
                animation: 150,
                ghostClass: 'hero-cta-ghost',
                onEnd: function () {
                    reindexCtaButtons();
                    syncHeroButtonsPreview();
                },
            });
        }

        ctaList?.querySelectorAll('.hero-cta-item').forEach(bindCtaRow);

        document.getElementById('hero-cta-add')?.addEventListener('click', function () {
            const items = ctaList?.querySelectorAll('.hero-cta-item');
            if (!ctaList || !items || items.length >= 3) return;
            const last = items[items.length - 1];
            const newRow = last.cloneNode(true);
            const labelIn = newRow.querySelector('.hero-cta-label-input');
            if (labelIn) labelIn.value = '';
            const iconIn = newRow.querySelector('.hero-icon-picker-value');
            if (iconIn) iconIn.value = 'fas fa-check';
            const ib = newRow.querySelector('.hero-icon-btn');
            if (ib) ib.innerHTML = '<i class="fas fa-check"></i>';
            const hrefSel = newRow.querySelector('select.hero-cta-href-select');
            if (hrefSel && hrefSel.options.length) hrefSel.selectedIndex = 0;
            const varSel = newRow.querySelector('select.hero-cta-variant-select');
            if (varSel) varSel.value = 'outline-light';
            ctaList.appendChild(newRow);
            reindexCtaButtons();
            bindCtaRow(newRow);
            updateCtaUiChrome();
            syncHeroButtonsPreview();
        });

        function openIconPicker(btn) {
            const row = btn.closest('.hero-trust-item') || btn.closest('.hero-cta-item');
            const fieldWrap = btn.closest('.hero-icon-field');
            const container = row || fieldWrap;
            if (!container) return;
            currentIconBtn = btn;
            currentIconInput = container.querySelector('.hero-icon-picker-value');
            if (!currentIconInput) return;
            const val = currentIconInput.value || 'fas fa-check';
            document.querySelectorAll('#heroIconPickerGrid .hero-icon-picker-item').forEach(function (el) {
                el.classList.toggle('selected', el.dataset.icon === val);
            });
            if (iconModal) iconModal.show();
        }

        if (grid) {
            HERO_ICONS.forEach(function (iconClass) {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'hero-icon-picker-item';
                item.dataset.icon = iconClass;
                item.innerHTML = '<i class="' + iconClass + '"></i>';
                item.addEventListener('click', function () {
                    if (currentIconInput && currentIconBtn) {
                        currentIconInput.value = iconClass;
                        currentIconBtn.innerHTML = '<i class="' + iconClass + '"></i>';
                        grid.querySelectorAll('.hero-icon-picker-item').forEach(function (el) {
                            el.classList.toggle('selected', el.dataset.icon === iconClass);
                        });
                        if (iconModal) iconModal.hide();
                        syncTrustStripPreview();
                        syncHeroButtonsPreview();
                    }
                });
                grid.appendChild(item);
            });
        }

        form?.addEventListener('click', function (e) {
            const btn = e.target.closest('.hero-icon-btn');
            if (!btn || !form.contains(btn)) return;
            e.preventDefault();
            openIconPicker(btn);
        });

        function reindexTrustItems() {
            if (!list) return;
            list.querySelectorAll('.hero-trust-item').forEach(function (li, idx) {
                li.querySelectorAll('[name^="trust_items"]').forEach(function (inp) {
                    inp.name = inp.name.replace(/trust_items\[\d+\]/, 'trust_items[' + idx + ']');
                });
            });
        }

        function refreshTrustSortable() {
            if (!list || typeof window.jQuery === 'undefined' || !jQuery.fn.sortable) return;
            var $list = jQuery(list);
            if ($list.hasClass('ui-sortable')) {
                $list.sortable('refresh');
            }
        }

        if (list && typeof window.jQuery !== 'undefined' && jQuery.fn.sortable) {
            jQuery(list).sortable({
                handle: '.hero-trust-handle',
                axis: 'y',
                items: '> li.hero-trust-item',
                cursor: 'grabbing',
                opacity: 0.9,
                tolerance: 'pointer',
                scroll: true,
                placeholder: 'hero-trust-sortable-placeholder',
                forcePlaceholderSize: true,
                update: function () {
                    reindexTrustItems();
                    syncTrustStripPreview();
                }
            });
        }

        function bindTrustRow(row) {
            row.querySelector('.hero-trust-remove')?.addEventListener('click', function () {
                const items = list.querySelectorAll('.hero-trust-item');
                if (items.length <= 1) return;
                row.remove();
                reindexTrustItems();
                syncTrustStripPreview();
                refreshTrustSortable();
            });
            row.querySelectorAll('.trust-value-inp, .trust-label-inp').forEach(function (inp) {
                inp.addEventListener('input', syncTrustStripPreview);
            });
        }

        document.getElementById('hero-trust-add')?.addEventListener('click', function () {
            const items = list.querySelectorAll('.hero-trust-item');
            if (items.length >= 8) return;
            const first = items[0];
            if (!first) return;
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input').forEach(function (inp) {
                if (inp.classList.contains('hero-icon-picker-value')) {
                    inp.value = 'fas fa-check';
                } else {
                    inp.value = '';
                }
            });
            const ib = clone.querySelector('.hero-icon-btn');
            if (ib) ib.innerHTML = '<i class="fas fa-check"></i>';
            list.appendChild(clone);
            bindTrustRow(clone);
            reindexTrustItems();
            syncTrustStripPreview();
            refreshTrustSortable();
        });

        list?.querySelectorAll('.hero-trust-item').forEach(bindTrustRow);

        function syncTrustStripPreview() {
            if (!trustStrip || !list) return;
            const parts = [];
            list.querySelectorAll('.hero-trust-item').forEach(function (row) {
                const labelIn = row.querySelector('.trust-label-inp');
                const valIn = row.querySelector('.trust-value-inp');
                const iconIn = row.querySelector('.hero-icon-picker-value');
                const label = (labelIn && labelIn.value) ? labelIn.value.trim() : '';
                if (!label) return;
                const v = (valIn && valIn.value) ? valIn.value.trim() : '';
                const ic = (iconIn && iconIn.value) ? iconIn.value : 'fas fa-check';
                const text = (v ? v + ' ' : '') + label;
                parts.push('<span class="trust-mock"><i class="' + ic + '"></i><span>' + escapeHtml(text) + '</span></span>');
            });
            trustStrip.innerHTML = parts.join('') || '<span class="text-muted small">Заполните «Основной текст» хотя бы в одной строке — здесь появится превью</span>';
        }

        const photoArea = document.getElementById('heroPhotoUploadArea');
        const photoInput = document.getElementById('heroImageInput');
        const photoPreview = document.getElementById('heroPhotoPreview');
        const photoContainer = document.getElementById('heroPhotoPreviewContainer');
        const placeholder = document.getElementById('heroUploadPlaceholder');
        const photoError = document.getElementById('heroPhotoError');

        function validatePhotoFile(file) {
            if (photoError) {
                photoError.style.display = 'none';
                photoError.textContent = '';
            }
            if (!ALLOWED_PHOTO_TYPES.includes(file.type)) {
                if (photoError) {
                    photoError.textContent = 'Нужен файл JPG, PNG или WebP.';
                    photoError.style.display = 'block';
                }
                return false;
            }
            if (file.size > MAX_PHOTO_BYTES) {
                if (photoError) {
                    photoError.textContent = 'Файл слишком большой. Максимум 8 МБ (сейчас ' + (file.size / 1024 / 1024).toFixed(1) + ' МБ).';
                    photoError.style.display = 'block';
                }
                return false;
            }
            return true;
        }

        function showPreview(src) {
            if (photoPreview) photoPreview.src = src;
            if (photoContainer) photoContainer.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
            if (photoArea) photoArea.classList.add('has-image');
            if (liveBg) liveBg.style.backgroundImage = 'url(' + src + ')';
        }

        photoInput?.addEventListener('change', function (e) {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            if (!validatePhotoFile(file)) {
                photoInput.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function (ev) { showPreview(ev.target.result); };
            reader.readAsDataURL(file);
        });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (evName) {
            photoArea?.addEventListener(evName, function (e) {
                e.preventDefault();
                e.stopPropagation();
            });
        });
        photoArea?.addEventListener('dragover', function () { photoArea.classList.add('drag-over'); });
        photoArea?.addEventListener('dragleave', function () { photoArea.classList.remove('drag-over'); });
        photoArea?.addEventListener('drop', function (e) {
            photoArea.classList.remove('drag-over');
            const f = e.dataTransfer.files && e.dataTransfer.files[0];
            if (!f) return;
            if (!validatePhotoFile(f)) return;
            try {
                photoInput.files = e.dataTransfer.files;
            } catch (err) {
                return;
            }
            const reader = new FileReader();
            reader.onload = function (ev) { showPreview(ev.target.result); };
            reader.readAsDataURL(f);
        });

        const titleIn = document.getElementById('hero_title_input');
        const subIn = document.getElementById('hero_subtitle_input');
        const pvTitle = document.getElementById('pv-title');
        const pvSub = document.getElementById('pv-subtitle');
        function livePreview() {
            if (pvTitle && titleIn) pvTitle.textContent = titleIn.value || 'Заголовок';
            if (pvSub && subIn) pvSub.textContent = subIn.value || '';
            syncHeroButtonsPreview();
            syncButtonsLayoutPreview();
        }
        [titleIn, subIn].forEach(function (el) {
            el?.addEventListener('input', livePreview);
        });
        ctaList?.addEventListener('input', function (e) {
            if (e.target.closest('.hero-cta-item')) livePreview();
        });
        ctaList?.addEventListener('change', function (e) {
            if (e.target.closest('.hero-cta-item')) livePreview();
        });
        function onHeroLayoutChange() {
            syncHeroButtonsPreview();
            syncButtonsLayoutPreview();
        }
        document.getElementById('buttons_align')?.addEventListener('change', onHeroLayoutChange);
        document.getElementById('buttons_direction')?.addEventListener('change', onHeroLayoutChange);
        livePreview();
        updateCtaUiChrome();
        syncTrustStripPreview();

        form?.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="btn-saving-spinner" aria-hidden="true"></span>Сохраняем…';
            }
        });
    });
})();
</script>
