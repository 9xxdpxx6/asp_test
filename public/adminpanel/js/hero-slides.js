/**
 * Конструктор слайдера с видео (страница «Слайдер с видео» в админке).
 * Данные и настройки приходят из window.HERO_SLIDES (см. resources/views/admin/hero-slides.blade.php).
 * Видео сжимается в браузере (video-compress.js) и уходит на сервер отдельным запросом, форма хранит только токен.
 */
(function () {
    'use strict';

    var cfg = window.HERO_SLIDES || {};
    var limits = window.ADMIN_UPLOAD_LIMITS || {};
    var MB = 1024 * 1024;

    var list = document.getElementById('hs-list');
    var tpl = document.getElementById('hs-template');
    var form = document.getElementById('hero-slides-form');
    var addBtn = document.getElementById('hs-add');
    var saveBtn = document.getElementById('hs-save');
    var saveNote = document.getElementById('hs-save-note');
    var emptyNote = document.getElementById('hs-empty');
    var limitNote = document.getElementById('hs-limit-note');
    var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var uid = 0;

    if (!list || !tpl || !form) {
        return;
    }

    function esc(t) {
        return String(t == null ? '' : t).replace(/[&<>"]/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c];
        });
    }

    function q(el, sel) {
        return el.querySelector(sel);
    }

    function fmtMb(bytes) {
        return window.AdminVideo ? window.AdminVideo.formatBytes(bytes) : Math.round(bytes / MB) + ' МБ';
    }

    function fmtDuration(sec) {
        sec = Math.max(0, Math.round(sec || 0));
        var m = Math.floor(sec / 60);
        var s = sec % 60;
        return m + ':' + (s < 10 ? '0' : '') + s;
    }

    // ---------- пометка про лимиты сервера ----------

    function renderLimitNote() {
        var budget = window.AdminVideo ? window.AdminVideo.budgetBytes(limits, cfg.maxVideoBytes) : 0;
        var html;
        var cls = 'alert alert-light border hs-limit mb-3';
        if (!window.AdminVideo || !window.AdminVideo.hasWebCodecs()) {
            cls = 'alert alert-warning hs-limit mb-3';
            html = '<i class="fas fa-triangle-exclamation me-1"></i><strong>Этот браузер не умеет сжимать видео.</strong> Откройте админку в Chrome или Edge. Здесь получится загрузить только небольшой MP4-файл (до ' + fmtMb(budget) + ').';
        } else if (budget < 12 * MB) {
            cls = 'alert alert-warning hs-limit mb-3';
            html = '<i class="fas fa-triangle-exclamation me-1"></i><strong>Сервер сейчас принимает файлы до ' + fmtMb(budget) + '.</strong> Короткие ролики пройдут, а длинные придётся сократить. Чтобы загружать ролики на пару минут, попросите того, кто настраивает сервер, увеличить <code>upload_max_filesize</code> до 32M и <code>post_max_size</code> до 64M.';
        } else {
            html = '<i class="fas fa-circle-check text-success me-1"></i>Видео любого размера сжимается прямо в вашем браузере (до ~' + fmtMb(budget) + ') и только потом отправляется на сервер. Исходный файл остаётся у вас на компьютере.';
        }
        limitNote.className = cls;
        limitNote.innerHTML = html;
    }

    // ---------- расчёт пропорций ----------

    function parseRatio(v) {
        var m = /^(\d+):(\d+)$/.exec(v || '');
        return m && +m[2] > 0 ? +m[1] / +m[2] : null;
    }

    function aspectOf(el) {
        var s = el.__s;
        var r = parseRatio(el.__fields().frame_ratio);
        if (r) {
            return r;
        }
        if (s.video_width > 0 && s.video_height > 0) {
            return s.video_width / s.video_height;
        }
        return 9 / 16;
    }

    /** Пропорции самого видео: от них зависит, можно ли ставить его фоном на всю шапку. */
    function naturalAspectOf(el) {
        var s = el.__s;
        if (s.video_width > 0 && s.video_height > 0) {
            return s.video_width / s.video_height;
        }
        return aspectOf(el);
    }

    function aspectCss(el) {
        var s = el.__s;
        var v = el.__fields().frame_ratio;
        if (parseRatio(v)) {
            return v.replace(':', ' / ');
        }
        if (s.video_width > 0 && s.video_height > 0) {
            return s.video_width + ' / ' + s.video_height;
        }
        return '9 / 16';
    }

    // ---------- предпросмотр ----------

    function tabsFor(current) {
        var labels = [];
        Array.prototype.forEach.call(list.querySelectorAll('[data-item]'), function (it) {
            if (it.__s.deleted || !it.__fields().is_active) {
                return;
            }
            labels.push({ el: it, label: it.__fields().tab_label || 'Без названия' });
        });
        var mainLabel = (document.getElementById('main_slide_label') || {}).value || 'Об автошколе';
        var inHero = document.getElementById('slider_in_hero');
        var main = { el: null, label: mainLabel };
        if ((document.getElementById('main_slide_position') || {}).value === 'first') {
            labels.unshift(main);
        } else {
            labels.push(main);
        }
        if (inHero && !inHero.checked) {
            return [];
        }
        return labels.map(function (t) {
            return { label: t.label, on: t.el === current };
        });
    }

    var MEDIA_H = { s: 340, m: 410, l: 480 };
    var MEDIA_H_MOBILE = { s: 240, m: 300, l: 340 };
    var MEDIA_W = { s: 380, m: 470, l: 560 };

    function renderPreview(el) {
        var s = el.__s;
        var f = el.__fields();
        var mobile = s.device === 'mobile';
        var aspect = aspectOf(el);
        var side = f.media_side;
        if (side === 'background' && naturalAspectOf(el) < cfg.backgroundMinRatio) {
            side = 'right';
        }
        var poster = s.poster_url || '';
        var size = f.media_size || 'm';
        var landscape = aspect > 1.2;

        var mediaStyle = 'aspect-ratio:' + aspectCss(el) + ';';
        if (landscape) {
            mediaStyle += mobile ? 'width:100%;max-width:330px;' : 'width:' + MEDIA_W[size] + 'px;';
        } else {
            mediaStyle += 'height:' + (mobile ? MEDIA_H_MOBILE[size] : MEDIA_H[size]) + 'px;';
        }

        var media = '';
        if (side !== 'background') {
            if (poster) {
                var fit = f.fit_mode === 'contain' ? 'contain' : 'cover';
                media = '<div class="hs-media" style="' + mediaStyle + '">' +
                    (fit === 'contain' ? '<img class="hs-bk" src="' + esc(poster) + '" alt="">' : '') +
                    '<img class="hs-fg" src="' + esc(poster) + '" alt="" style="object-fit:' + fit + '">' +
                    '<span class="hs-play"><svg width="10" height="12" viewBox="0 0 10 12" aria-hidden="true"><path d="M0 0l10 6-10 6z" fill="#fff"/></svg>Со звуком</span></div>';
            } else {
                media = '<div class="hs-media" style="' + mediaStyle + '"><div class="hs-empty">Здесь будет видео</div></div>';
            }
        }

        var bg = (side === 'background' && poster) ? '<img class="hs-bgposter" src="' + esc(poster) + '" alt="">' : '';
        var sub = f.subtitle ? '<p>' + esc(f.subtitle) + '</p>' : '';
        var btn = (f.button_label && f.button_href) ? '<span class="hs-btn">' + esc(f.button_label) + '</span>' : '';
        var watch = side === 'background' ? '<span class="hs-btn" style="background:transparent;border:2px solid #fff;color:#fff;margin-left:10px">Смотреть со звуком</span>' : '';

        var tabs = tabsFor(el);
        var pills = tabs.length > 1 ? '<div class="hs-pills">' + tabs.map(function (t) {
            return '<span class="hs-pill' + (t.on ? ' on' : '') + '">' + esc(t.label) + '</span>';
        }).join('') + '</div>' : '';

        var pv = q(el, '[data-pv]');
        pv.innerHTML = '<div class="hs-hero' + (mobile ? ' is-mobile' : '') + '" style="background-image:url(' + esc(cfg.fallbackBackground) + ')">' +
            bg + '<div class="hs-ov"></div>' +
            '<div class="hs-slide" data-side="' + side + '"><div class="hs-txt"><h3>' + esc(f.title || 'Заголовок слайда') + '</h3>' + sub + '<div>' + btn + watch + '</div></div>' + media + '</div>' +
            pills + '</div>';

        fitPreview(el);
    }

    function fitPreview(el) {
        var box = q(el, '[data-pv-box]');
        var pv = q(el, '[data-pv]');
        if (!box || !pv) {
            return;
        }
        var width = el.__s.device === 'mobile' ? 390 : 1060;
        var avail = box.clientWidth || width;
        var k = Math.min(1, avail / width);
        pv.style.width = width + 'px';
        pv.style.transform = 'scale(' + k + ')';
        pv.style.marginLeft = Math.max(0, (avail - width * k) / 2) + 'px';
        box.style.height = Math.ceil(pv.offsetHeight * k) + 'px';
    }

    function refreshAllPreviews() {
        Array.prototype.forEach.call(list.querySelectorAll('[data-item]'), renderPreview);
    }

    // ---------- состояние элемента ----------

    function updateSideOptions(el) {
        var select = q(el, '[data-f="media_side"]');
        var bgOpt = select.querySelector('option[value="background"]');
        var help = q(el, '[data-side-help]');
        var allowed = naturalAspectOf(el) >= cfg.backgroundMinRatio;
        bgOpt.disabled = !allowed;
        if (!allowed && select.value === 'background') {
            select.value = 'right';
        }
        help.textContent = allowed ? '' : '«Фоном на всю шапку» доступно только для горизонтального видео (16:9, 4:3): вертикальное при растягивании на всю ширину обрезает лица. Для вертикального выберите «Справа» или «Слева».';
    }

    function showInfo(el) {
        var s = el.__s;
        var title = q(el, '[data-info-title]');
        var meta = q(el, '[data-info-meta]');
        var pick = q(el, '[data-pick-label]');
        if (s.video_url) {
            title.textContent = s.token ? 'Видео загружено, нажмите «Сохранить»' : 'Видео на сайте';
            var parts = [];
            if (s.video_width && s.video_height) {
                parts.push(s.video_width + '×' + s.video_height);
            }
            if (s.duration_seconds) {
                parts.push(fmtDuration(s.duration_seconds));
            }
            if (s.video_bytes) {
                parts.push(fmtMb(s.video_bytes));
            }
            var text = parts.join(' · ');
            if (s.originalBytes && s.video_bytes && s.originalBytes > s.video_bytes * 1.2) {
                text += ' (исходный файл был ' + fmtMb(s.originalBytes) + ')';
            }
            meta.textContent = text;
            pick.textContent = 'Заменить видео';
        } else {
            title.textContent = 'Видео не выбрано';
            meta.textContent = 'Перетащите файл сюда или нажмите «Выбрать видео». Любой размер: сожмём в браузере.';
            pick.textContent = 'Выбрать видео';
        }
    }

    function setBusy(el, busy) {
        var wrap = q(el, '[data-progress-wrap]');
        q(el, '[data-cancel]').classList.toggle('d-none', !busy);
        q(el, '[data-pick]').disabled = busy;
        wrap.classList.toggle('d-none', !busy);
        if (busy) {
            q(el, '[data-progress]').style.width = '0%';
        }
    }

    function showError(el, message) {
        var box = q(el, '[data-error]');
        box.textContent = message || '';
        box.classList.toggle('d-none', !message);
        q(el, '[data-upload]').classList.toggle('has-error', !!message);
    }

    function anyBusy() {
        return Array.prototype.some.call(list.querySelectorAll('[data-item]'), function (it) {
            return it.__s.busy;
        });
    }

    function updateSaveState() {
        var busy = anyBusy();
        saveBtn.disabled = busy;
        saveNote.classList.toggle('d-none', !busy);
        saveNote.textContent = busy ? 'Дождитесь окончания загрузки видео.' : '';
    }

    function activeCount() {
        return Array.prototype.filter.call(list.querySelectorAll('[data-item]'), function (it) {
            return !it.__s.deleted;
        }).length;
    }

    function ensureHidden(el, name) {
        var holder = q(el, '[data-hidden]');
        var input = holder.querySelector('[data-h="' + name + '"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.setAttribute('data-h', name);
            holder.appendChild(input);
        }
        return input;
    }

    /** Имена полей по порядку и скрытые поля (id, токен загрузки, удаление, показывать). */
    function syncNames() {
        var items = list.querySelectorAll('[data-item]');
        Array.prototype.forEach.call(items, function (el, i) {
            var s = el.__s;
            q(el, '[data-order]').textContent = String(i + 1);
            Array.prototype.forEach.call(el.querySelectorAll('[data-f]'), function (input) {
                if (input.type === 'checkbox') {
                    return;
                }
                input.name = 'slides[' + i + '][' + input.getAttribute('data-f') + ']';
            });
            var map = {
                id: s.id || '',
                token: s.token || '',
                pending_delete: s.deleted ? 1 : 0,
                is_active: el.__fields().is_active ? 1 : 0
            };
            Object.keys(map).forEach(function (key) {
                var h = ensureHidden(el, key);
                h.name = 'slides[' + i + '][' + key + ']';
                h.value = map[key];
            });
            q(el, '[data-title-preview]').textContent = el.__fields().title || el.__fields().tab_label || 'Новый слайд';
            el.classList.toggle('is-pending-delete', s.deleted);
            el.classList.toggle('is-inactive', !el.__fields().is_active);
            q(el, '[data-delete-badge]').classList.toggle('d-none', !s.deleted);
            q(el, '[data-remove-label]').textContent = s.deleted ? 'Вернуть' : 'Удалить';
        });
        emptyNote.classList.toggle('d-none', items.length > 0);
        addBtn.disabled = activeCount() >= cfg.maxSlides;
    }

    // ---------- загрузка видео ----------

    function uploadToServer(el, prepared, ctrl) {
        return new Promise(function (resolve, reject) {
            var fd = new FormData();
            fd.append('video', prepared.video, 'video.mp4');
            fd.append('poster', prepared.poster, 'poster.jpg');
            if (prepared.preview) {
                fd.append('preview', prepared.preview, 'preview.mp4');
            }
            fd.append('width', prepared.width);
            fd.append('height', prepared.height);
            fd.append('duration', prepared.duration);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', cfg.uploadUrl);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    setProgress(el, 90 + 10 * (e.loaded / e.total));
                }
            };
            xhr.onload = function () {
                var data = null;
                try {
                    data = JSON.parse(xhr.responseText);
                } catch (e) {
                    data = null;
                }
                if (xhr.status >= 200 && xhr.status < 300 && data && data.token) {
                    resolve(data);
                    return;
                }
                if (xhr.status === 413) {
                    reject(new window.AdminVideo.VideoError('Сервер отклонил файл как слишком большой (лимит post_max_size или upload_max_filesize). Попросите увеличить лимиты загрузки.', 'server'));
                } else if (xhr.status === 419) {
                    reject(new window.AdminVideo.VideoError('Страница устарела. Обновите её (F5) и попробуйте снова.', 'csrf'));
                } else if (xhr.status === 401 || xhr.status === 403) {
                    reject(new window.AdminVideo.VideoError('Нет доступа: войдите в админку заново.', 'auth'));
                } else if (data && data.errors) {
                    var first = Object.keys(data.errors)[0];
                    reject(new window.AdminVideo.VideoError(data.errors[first][0], 'validation'));
                } else {
                    reject(new window.AdminVideo.VideoError('Сервер не принял видео (ошибка ' + xhr.status + '). Попробуйте ещё раз.', 'server'));
                }
            };
            xhr.onerror = function () {
                reject(new window.AdminVideo.VideoError('Нет связи с сервером. Проверьте интернет и попробуйте снова.', 'network'));
            };
            xhr.onabort = function () {
                reject(new window.AdminVideo.VideoError('Отменено.', 'canceled'));
            };
            ctrl.signal.addEventListener('abort', function () {
                xhr.abort();
            });
            xhr.send(fd);
        });
    }

    function setProgress(el, percent) {
        q(el, '[data-progress]').style.width = Math.max(0, Math.min(100, percent)).toFixed(1) + '%';
    }

    async function handleFile(el, file) {
        var s = el.__s;
        if (!file || s.busy) {
            return;
        }
        if (!window.AdminVideo) {
            showError(el, 'Модуль сжатия видео не загрузился. Обновите страницу.');
            return;
        }
        showError(el, '');
        var ctrl = new AbortController();
        s.abort = ctrl;
        s.busy = true;
        setBusy(el, true);
        updateSaveState();
        window.onbeforeunload = function () { return 'Видео ещё загружается.'; };

        var title = q(el, '[data-info-title]');
        var meta = q(el, '[data-info-meta]');
        title.textContent = file.name;
        meta.textContent = fmtMb(file.size) + ' · готовим…';

        try {
            var prepared = await window.AdminVideo.prepare(file, {
                limits: limits,
                maxVideoBytes: cfg.maxVideoBytes,
                maxBitrate: cfg.maxBitrateBps,
                signal: ctrl.signal,
                onStage: function (name, text) {
                    meta.textContent = text;
                },
                onProgress: function (p) {
                    setProgress(el, p * 90);
                }
            });
            meta.textContent = 'Загружаем на сервер: ' + fmtMb(prepared.finalBytes) + '…';
            var res = await uploadToServer(el, prepared, ctrl);

            s.token = res.token;
            s.video_url = res.video_url;
            s.poster_url = res.poster_url;
            s.preview_url = res.preview_url;
            s.video_width = prepared.width;
            s.video_height = prepared.height;
            s.duration_seconds = prepared.duration;
            s.video_bytes = prepared.finalBytes;
            s.originalBytes = prepared.originalBytes;

            var fields = el.__fields();
            if (!fields.tab_label) {
                q(el, '[data-f="tab_label"]').value = file.name.replace(/\.[^.]+$/, '').slice(0, 60);
            }
        } catch (e) {
            if (e && e.code !== 'canceled') {
                showError(el, e.userMessage || e.message || 'Не получилось обработать видео.');
            }
        } finally {
            s.busy = false;
            s.abort = null;
            setBusy(el, false);
            updateSaveState();
            if (!anyBusy()) {
                window.onbeforeunload = null;
            }
            showInfo(el);
            updateSideOptions(el);
            syncNames();
            refreshAllPreviews();
        }
    }

    // ---------- создание карточки ----------

    var deleteModalEl = document.getElementById('hsDeleteModal');
    var deleteModal = deleteModalEl && window.bootstrap ? new window.bootstrap.Modal(deleteModalEl) : null;
    var pendingDelete = null;

    function applyDelete(el) {
        if (!el.__s.id) {
            list.removeChild(el);
        } else {
            el.__s.deleted = true;
        }
        syncNames();
        refreshAllPreviews();
    }

    if (deleteModalEl) {
        document.getElementById('hsDeleteModalOk').addEventListener('click', function () {
            if (pendingDelete) {
                applyDelete(pendingDelete);
                pendingDelete = null;
            }
            if (deleteModal) {
                deleteModal.hide();
            }
        });
    }

    function addItem(data) {
        data = data || {};
        var node = tpl.content.firstElementChild.cloneNode(true);
        uid += 1;

        node.__s = {
            id: data.id || null,
            token: data.token || '',
            video_url: data.video_url || null,
            poster_url: data.poster_url || null,
            preview_url: data.preview_url || null,
            video_width: data.video_width || 0,
            video_height: data.video_height || 0,
            duration_seconds: data.duration_seconds || 0,
            video_bytes: data.video_bytes || 0,
            originalBytes: 0,
            deleted: false,
            busy: false,
            abort: null,
            device: 'desktop'
        };

        node.__fields = function () {
            var out = {};
            Array.prototype.forEach.call(node.querySelectorAll('[data-f]'), function (input) {
                out[input.getAttribute('data-f')] = input.type === 'checkbox' ? input.checked : input.value;
            });
            return out;
        };

        var defaults = {
            is_active: data.is_active !== undefined ? !!data.is_active : true,
            tab_label: data.tab_label || '',
            title: data.title || '',
            subtitle: data.subtitle || '',
            button_label: data.button_label || '',
            button_href: data.button_href || '',
            frame_ratio: data.frame_ratio || 'auto',
            fit_mode: data.fit_mode || 'cover',
            media_side: data.media_side || 'right',
            media_size: data.media_size || 'm'
        };
        Array.prototype.forEach.call(node.querySelectorAll('[data-f]'), function (input) {
            var key = input.getAttribute('data-f');
            if (input.type === 'checkbox') {
                input.checked = defaults[key];
                input.id = 'hs-active-' + uid;
                q(node, '[data-active-label]').setAttribute('for', input.id);
            } else {
                input.value = defaults[key];
            }
        });

        var holder = document.createElement('div');
        holder.setAttribute('data-hidden', '');
        node.appendChild(holder);

        // события
        var changed = function (e) {
            var key = e.target.getAttribute && e.target.getAttribute('data-f');
            if (!key) {
                return;
            }
            if (key === 'frame_ratio') {
                updateSideOptions(node);
            }
            syncNames();
            if (key === 'tab_label' || key === 'is_active') {
                refreshAllPreviews();
            } else {
                renderPreview(node);
            }
        };
        node.addEventListener('input', changed);
        node.addEventListener('change', changed);

        var fileInput = q(node, '[data-file]');
        q(node, '[data-pick]').addEventListener('click', function () {
            fileInput.click();
        });
        fileInput.addEventListener('change', function () {
            var f = fileInput.files && fileInput.files[0];
            fileInput.value = '';
            handleFile(node, f);
        });
        q(node, '[data-cancel]').addEventListener('click', function () {
            if (node.__s.abort) {
                node.__s.abort.abort();
            }
        });

        var zone = q(node, '[data-upload]');
        ['dragenter', 'dragover'].forEach(function (ev) {
            zone.addEventListener(ev, function (e) {
                e.preventDefault();
                zone.classList.add('is-drag');
            });
        });
        ['dragleave', 'drop'].forEach(function (ev) {
            zone.addEventListener(ev, function (e) {
                e.preventDefault();
                zone.classList.remove('is-drag');
            });
        });
        zone.addEventListener('drop', function (e) {
            var f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
            handleFile(node, f);
        });

        Array.prototype.forEach.call(node.querySelectorAll('[data-device]'), function (btn) {
            btn.addEventListener('click', function () {
                node.__s.device = btn.getAttribute('data-device');
                Array.prototype.forEach.call(node.querySelectorAll('[data-device]'), function (b) {
                    b.classList.toggle('active', b === btn);
                });
                renderPreview(node);
            });
        });

        q(node, '[data-remove]').addEventListener('click', function () {
            if (node.__s.deleted) {
                node.__s.deleted = false;
                syncNames();
                refreshAllPreviews();
            } else if (node.__s.id && deleteModal) {
                pendingDelete = node;
                deleteModal.show();
            } else {
                applyDelete(node);
            }
        });

        list.appendChild(node);
        showInfo(node);
        updateSideOptions(node);
        syncNames();
        return node;
    }

    // ---------- общие события ----------

    addBtn.addEventListener('click', function () {
        if (activeCount() >= cfg.maxSlides) {
            return;
        }
        var node = addItem({});
        refreshAllPreviews();
        node.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    ['main_slide_label', 'main_slide_position', 'slider_in_hero'].forEach(function (id) {
        var input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', refreshAllPreviews);
            input.addEventListener('change', refreshAllPreviews);
        }
    });

    function toggleOptionBlocks() {
        [['slider_in_hero', '[data-hero-options]'], ['slider_show_cards', '[data-cards-options]']].forEach(function (pair) {
            var sw = document.getElementById(pair[0]);
            var block = document.querySelector(pair[1]);
            if (sw && block) {
                block.style.opacity = sw.checked ? '' : '.5';
            }
        });
    }
    ['slider_in_hero', 'slider_show_cards'].forEach(function (id) {
        var input = document.getElementById(id);
        if (input) {
            input.addEventListener('change', toggleOptionBlocks);
        }
    });

    form.addEventListener('submit', function (e) {
        syncNames();
        if (anyBusy()) {
            e.preventDefault();
            return;
        }
        var problem = null;
        Array.prototype.forEach.call(list.querySelectorAll('[data-item]'), function (el, i) {
            if (problem || el.__s.deleted) {
                return;
            }
            var f = el.__fields();
            if (!el.__s.id && !el.__s.token) {
                problem = { el: el, text: 'Слайд ' + (i + 1) + ': выберите видео.' };
            } else if (!String(f.tab_label).trim()) {
                problem = { el: el, text: 'Слайд ' + (i + 1) + ': заполните название вкладки.', focus: '[data-f="tab_label"]' };
            } else if (!String(f.title).trim()) {
                problem = { el: el, text: 'Слайд ' + (i + 1) + ': заполните заголовок.', focus: '[data-f="title"]' };
            } else if ((!!String(f.button_label).trim()) !== (!!f.button_href)) {
                problem = { el: el, text: 'Слайд ' + (i + 1) + ': для кнопки нужны и текст, и ссылка (или оставьте оба поля пустыми).', focus: '[data-f="button_label"]' };
            }
        });
        if (problem) {
            e.preventDefault();
            saveNote.classList.remove('d-none');
            saveNote.textContent = problem.text;
            problem.el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (problem.focus) {
                q(problem.el, problem.focus).focus();
            }
            return;
        }
        window.onbeforeunload = null;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Сохраняем…';
    });

    if (window.Sortable) {
        window.Sortable.create(list, {
            handle: '[data-handle]',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function () {
                syncNames();
                refreshAllPreviews();
            }
        });
    }

    window.addEventListener('resize', function () {
        Array.prototype.forEach.call(list.querySelectorAll('[data-item]'), fitPreview);
    });

    // ---------- старт ----------

    renderLimitNote();
    toggleOptionBlocks();
    (cfg.slides || []).forEach(function (row) {
        addItem(row);
    });
    refreshAllPreviews();
    syncNames();
    updateSaveState();
})();
