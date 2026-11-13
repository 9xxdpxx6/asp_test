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

    /** Ошибка с понятным админу текстом. */
    function UserError(message, code) {
        this.name = 'UserError';
        this.message = message;
        this.userMessage = message;
        this.code = code || 'error';
    }
    UserError.prototype = Object.create(Error.prototype);

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

    /** Размеры основного медиа слайда (видео или фото); нули, если показывать нечего. */
    function mediaDims(el) {
        var st = el.__s;
        var type = el.__fields().media_type;
        if (type === 'photo') {
            return [st.image_width || 0, st.image_height || 0];
        }
        if (type === 'none') {
            return [0, 0];
        }
        return [st.video_width || 0, st.video_height || 0];
    }

    function aspectOf(el) {
        var r = parseRatio(el.__fields().frame_ratio);
        if (r) {
            return r;
        }
        var d = mediaDims(el);
        return d[0] > 0 && d[1] > 0 ? d[0] / d[1] : 9 / 16;
    }

    /** Пропорции самого медиа: от них зависит, можно ли ставить видео фоном на всю шапку. */
    function naturalAspectOf(el) {
        var d = mediaDims(el);
        return d[0] > 0 && d[1] > 0 ? d[0] / d[1] : aspectOf(el);
    }

    function aspectCss(el) {
        var v = el.__fields().frame_ratio;
        if (parseRatio(v)) {
            return v.replace(':', ' / ');
        }
        var d = mediaDims(el);
        return d[0] > 0 && d[1] > 0 ? d[0] + ' / ' + d[1] : '9 / 16';
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
        var st = el.__s;
        var f = el.__fields();
        var type = f.media_type || 'video';
        var mobile = st.device === 'mobile';
        var aspect = aspectOf(el);
        var side = f.media_side;
        if (type !== 'video' && side === 'background') {
            side = 'right';
        }
        if (side === 'background' && naturalAspectOf(el) < cfg.backgroundMinRatio) {
            side = 'right';
        }
        var poster = st.poster_url || '';
        var size = f.media_size || 'm';
        var landscape = aspect > 1.2;
        var fit = f.fit_mode === 'contain' ? 'contain' : 'cover';

        var mediaStyle = 'aspect-ratio:' + aspectCss(el) + ';';
        if (landscape) {
            mediaStyle += mobile ? 'width:100%;max-width:330px;' : 'width:' + MEDIA_W[size] + 'px;';
        } else {
            mediaStyle += 'height:' + (mobile ? MEDIA_H_MOBILE[size] : MEDIA_H[size]) + 'px;';
        }

        var media = '';
        if (type !== 'none' && side !== 'background') {
            var picture = type === 'photo' ? (st.image_url || '') : poster;
            if (picture) {
                media = '<div class="hs-media" style="' + mediaStyle + '">' +
                    (fit === 'contain' ? '<img class="hs-bk" src="' + esc(picture) + '" alt="">' : '') +
                    '<img class="hs-fg" src="' + esc(picture) + '" alt="" style="object-fit:' + fit + '">' +
                    (type === 'video' ? '<span class="hs-play"><svg width="10" height="12" viewBox="0 0 10 12" aria-hidden="true"><path d="M0 0l10 6-10 6z" fill="#fff"/></svg>Со звуком</span>' : '') +
                    '</div>';
            } else {
                media = '<div class="hs-media" style="' + mediaStyle + '"><div class="hs-empty">' + (type === 'photo' ? 'Здесь будет фото' : 'Здесь будет видео') + '</div></div>';
            }
        }

        // Фон слайда: видео-фоном → своё фото → размытый кадр видео → фон основной шапки (он всегда под низом)
        var bgUrl = st.bg_remove ? '' : (st.bg_url || '');
        var bg = '';
        if (side === 'background' && poster) {
            bg = '<img class="hs-bgposter" src="' + esc(poster) + '" alt="">';
        } else if (bgUrl) {
            bg = '<img class="hs-bgposter" src="' + esc(bgUrl) + '" alt="">';
        } else if (type === 'video' && poster) {
            bg = '<img class="hs-bgposter hs-blur" src="' + esc(poster) + '" alt="">';
        }

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
            '<div class="hs-slide" data-side="' + side + '" data-va="' + esc(f.text_valign || 'center') + '"><div class="hs-txt" data-ta="' + esc(f.text_align || 'left') + '"><h3>' + esc(f.title || 'Заголовок слайда') + '</h3>' + sub + '<div>' + btn + watch + '</div></div>' + media + '</div>' +
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
        var type = el.__fields().media_type || 'video';
        var select = q(el, '[data-f="media_side"]');
        var bgOpt = select.querySelector('option[value="background"]');
        var help = q(el, '[data-side-help]');
        var allowed = type === 'video' && naturalAspectOf(el) >= cfg.backgroundMinRatio;
        bgOpt.disabled = !allowed;
        if (!allowed && select.value === 'background') {
            select.value = 'right';
        }
        if (type === 'video') {
            help.textContent = allowed ? '' : '«Фоном на всю шапку» доступно только для горизонтального видео (16:9, 4:3): вертикальное при растягивании на всю ширину обрезает лица. Для вертикального выберите «Справа» или «Слева».';
        } else if (type === 'photo') {
            help.textContent = 'Фото ставится справа или слева от текста. Своё фото на весь фон слайда выбирается ниже, в блоке «Фон слайда».';
        } else {
            help.textContent = '';
        }
    }

    /** Показывает нужную панель (видео / фото) и прячет настройки рамки, когда рядом с текстом ничего нет. */
    function applyType(el) {
        var type = el.__fields().media_type || 'video';
        q(el, '[data-panel="video"]').classList.toggle('d-none', type !== 'video');
        q(el, '[data-panel="photo"]').classList.toggle('d-none', type !== 'photo');
        q(el, '[data-layout]').classList.toggle('d-none', type === 'none');
        updateSideOptions(el);
    }

    /** Кнопка без текста или без ссылки просто не показывается: подсказываем, но не блокируем сохранение. */
    function updateButtonHelp(el) {
        var f = el.__fields();
        var help = q(el, '[data-btn-help]');
        var hasLabel = String(f.button_label).trim() !== '';
        var hasHref = !!f.button_href;
        var text = 'Кнопка не обязательна. Она появится, если заполнены и текст, и «куда ведёт».';
        var warn = false;
        if (hasLabel && !hasHref) {
            text = 'Кнопки на слайде не будет: выберите, куда она ведёт (или очистите текст кнопки).';
            warn = true;
        } else if (!hasLabel && hasHref) {
            text = 'Кнопки на слайде не будет: введите текст кнопки.';
            warn = true;
        } else if (hasLabel && hasHref) {
            text = 'Кнопка будет на слайде.';
        }
        help.textContent = text;
        help.classList.toggle('text-warning-emphasis', warn);
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

    function zoneEl(el, slot) {
        return q(el, '[data-zone="' + slot + '"]');
    }

    var ZONE_TEXT = {
        photo: {
            none: ['Фото не выбрано', 'Перетащите фото сюда или нажмите «Выбрать фото». Любой размер: сожмём в браузере.', 'Выбрать фото'],
            saved: ['Фото на сайте', 'Заменить фото'],
            fresh: ['Фото загружено, нажмите «Сохранить»', 'Заменить фото']
        },
        bg: {
            none: ['Своего фона нет', 'Если не выбрать: у слайда с видео фоном будет размытый кадр из видео, у остальных такой же фон, как у основной шапки.', 'Выбрать фото фона'],
            saved: ['Фон на сайте', 'Заменить фон'],
            fresh: ['Фон загружен, нажмите «Сохранить»', 'Заменить фон']
        }
    };

    function showZone(el, slot) {
        var st = el.__s;
        var z = zoneEl(el, slot);
        var url = slot === 'bg' ? (st.bg_remove ? null : st.bg_url) : st.image_url;
        var token = slot === 'bg' ? st.bg_token : st.image_token;
        var w = slot === 'bg' ? st.bg_width : st.image_width;
        var h = slot === 'bg' ? st.bg_height : st.image_height;
        var bytes = slot === 'bg' ? st.bg_bytes : st.image_bytes;
        var thumb = q(z, '[data-z-thumb]');
        var title = q(z, '[data-z-title]');
        var meta = q(z, '[data-z-meta]');
        var pick = q(z, '[data-z-pick-label]');
        var rm = q(z, '[data-z-remove]');
        var t = ZONE_TEXT[slot];

        if (url) {
            thumb.src = url;
            thumb.classList.remove('d-none');
            title.textContent = token ? t.fresh[0] : t.saved[0];
            var parts = [];
            if (w && h) {
                parts.push(w + '×' + h);
            }
            if (bytes) {
                parts.push(fmtMb(bytes));
            }
            meta.textContent = parts.join(' · ');
            pick.textContent = token ? t.fresh[1] : t.saved[1];
            if (rm) {
                rm.classList.remove('d-none');
            }
        } else {
            thumb.classList.add('d-none');
            thumb.removeAttribute('src');
            title.textContent = t.none[0];
            meta.textContent = t.none[1];
            pick.textContent = t.none[2];
            if (rm) {
                rm.classList.add('d-none');
            }
        }
    }

    function zoneError(z, message) {
        var box = q(z, '[data-z-error]');
        box.textContent = message || '';
        box.classList.toggle('d-none', !message);
        z.classList.toggle('has-error', !!message);
    }

    function imageBudget() {
        var upload = limits.uploadMax > 0 ? limits.uploadMax : Infinity;
        var post = limits.postMax > 0 ? limits.postMax - 1.5 * MB : Infinity;
        var app = cfg.maxImageBytes > 0 ? cfg.maxImageBytes : 6 * MB;
        return Math.floor(Math.min(upload, post, app) * 0.9);
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
        saveNote.textContent = busy ? 'Дождитесь окончания загрузки (видео или фото).' : '';
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
                image_token: s.image_token || '',
                bg_token: s.bg_token || '',
                bg_remove: s.bg_remove ? 1 : 0,
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

    /** POST формы с прогрессом отправки. Возвращает JSON ответа; ошибки приходят как UserError с понятным текстом. */
    function postForm(url, fd, onProgress, ctrl) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', url);
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable && onProgress) {
                    onProgress(e.loaded / e.total);
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
                    reject(new UserError('Сервер отклонил файл как слишком большой (лимит post_max_size или upload_max_filesize). Попросите увеличить лимиты загрузки.', 'server'));
                } else if (xhr.status === 419) {
                    reject(new UserError('Страница устарела. Обновите её (F5) и попробуйте снова.', 'csrf'));
                } else if (xhr.status === 401 || xhr.status === 403) {
                    reject(new UserError('Нет доступа: войдите в админку заново.', 'auth'));
                } else if (data && data.errors) {
                    var first = Object.keys(data.errors)[0];
                    reject(new UserError(data.errors[first][0], 'validation'));
                } else {
                    reject(new UserError('Сервер не принял файл (ошибка ' + xhr.status + '). Попробуйте ещё раз.', 'server'));
                }
            };
            xhr.onerror = function () {
                reject(new UserError('Нет связи с сервером. Проверьте интернет и попробуйте снова.', 'network'));
            };
            xhr.onabort = function () {
                reject(new UserError('Отменено.', 'canceled'));
            };
            if (ctrl) {
                ctrl.signal.addEventListener('abort', function () {
                    xhr.abort();
                });
            }
            xhr.send(fd);
        });
    }

    function uploadToServer(el, prepared, ctrl) {
        var fd = new FormData();
        fd.append('video', prepared.video, 'video.mp4');
        fd.append('poster', prepared.poster, 'poster.jpg');
        if (prepared.preview) {
            fd.append('preview', prepared.preview, 'preview.mp4');
        }
        fd.append('width', prepared.width);
        fd.append('height', prepared.height);
        fd.append('duration', prepared.duration);

        return postForm(cfg.uploadUrl, fd, function (p) {
            setProgress(el, 90 + 10 * p);
        }, ctrl);
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

    /** Фото: уменьшаем в браузере (длинная сторона и вес), затем отправляем; сервер страхует ресайзом. */
    async function handleImage(el, file, slot) {
        var st = el.__s;
        if (!file || st.busy) {
            return;
        }
        var z = zoneEl(el, slot);
        zoneError(z, '');
        if (!/^image\/(jpeg|png|webp)$/i.test(file.type)) {
            zoneError(z, 'Выберите фото в формате JPEG, PNG или WebP.');
            return;
        }

        var ctrl = new AbortController();
        st.abort = ctrl;
        st.busy = true;
        var wrap = q(z, '[data-z-progress-wrap]');
        var bar = q(z, '[data-z-progress]');
        var pick = q(z, '[data-z-pick]');
        wrap.classList.remove('d-none');
        bar.style.width = '5%';
        pick.disabled = true;
        updateSaveState();
        window.onbeforeunload = function () { return 'Фото ещё загружается.'; };

        try {
            var budget = imageBudget();
            var maxBytes = Math.min(slot === 'bg' ? 1.5 * MB : 1.0 * MB, budget);
            var maxSide = slot === 'bg' ? 2560 : 1600;
            var prepared = window.AdminImage ? await window.AdminImage.shrink(file, { maxBytes: maxBytes, maxSide: maxSide }) : file;
            if (prepared.size > budget) {
                throw new UserError('Фото получилось слишком тяжёлым для сервера (' + fmtMb(prepared.size) + ', максимум ' + fmtMb(budget) + '). Выберите фото попроще.', 'toolarge');
            }
            bar.style.width = '15%';

            var fd = new FormData();
            fd.append('image', prepared, prepared.name || 'image.jpg');
            fd.append('purpose', slot === 'bg' ? 'background' : 'media');
            var res = await postForm(cfg.uploadImageUrl, fd, function (p) {
                bar.style.width = (15 + 85 * p).toFixed(1) + '%';
            }, ctrl);

            if (slot === 'bg') {
                st.bg_token = res.token;
                st.bg_url = res.url;
                st.bg_width = res.width;
                st.bg_height = res.height;
                st.bg_bytes = res.bytes;
                st.bg_remove = false;
            } else {
                st.image_token = res.token;
                st.image_url = res.url;
                st.image_width = res.width;
                st.image_height = res.height;
                st.image_bytes = res.bytes;
            }
        } catch (e) {
            if (e && e.code !== 'canceled') {
                zoneError(z, e.userMessage || e.message || 'Не получилось загрузить фото.');
            }
        } finally {
            st.busy = false;
            st.abort = null;
            wrap.classList.add('d-none');
            pick.disabled = false;
            updateSaveState();
            if (!anyBusy()) {
                window.onbeforeunload = null;
            }
            showZone(el, slot);
            updateSideOptions(el);
            syncNames();
            refreshAllPreviews();
        }
    }

    function wireZone(node, slot) {
        var z = zoneEl(node, slot);
        var input = q(z, '[data-z-file]');
        q(z, '[data-z-pick]').addEventListener('click', function () {
            input.click();
        });
        input.addEventListener('change', function () {
            var f = input.files && input.files[0];
            input.value = '';
            handleImage(node, f, slot);
        });

        ['dragenter', 'dragover'].forEach(function (ev) {
            z.addEventListener(ev, function (e) {
                e.preventDefault();
                z.classList.add('is-drag');
            });
        });
        ['dragleave', 'drop'].forEach(function (ev) {
            z.addEventListener(ev, function (e) {
                e.preventDefault();
                z.classList.remove('is-drag');
            });
        });
        z.addEventListener('drop', function (e) {
            var f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
            handleImage(node, f, slot);
        });

        var rm = q(z, '[data-z-remove]');
        if (rm) {
            rm.addEventListener('click', function () {
                var st = node.__s;
                st.bg_url = null;
                st.bg_token = '';
                st.bg_remove = true;
                zoneError(z, '');
                showZone(node, 'bg');
                syncNames();
                refreshAllPreviews();
            });
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
            image_token: data.image_token || '',
            image_url: data.image_url || null,
            image_width: data.image_width || 0,
            image_height: data.image_height || 0,
            image_bytes: 0,
            bg_token: data.bg_token || '',
            bg_url: data.bg_url || null,
            bg_width: 0,
            bg_height: 0,
            bg_bytes: 0,
            bg_remove: !!data.bg_remove,
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
            media_type: data.media_type || 'video',
            tab_label: data.tab_label || '',
            title: data.title || '',
            subtitle: data.subtitle || '',
            button_label: data.button_label || '',
            button_href: data.button_href || '',
            frame_ratio: data.frame_ratio || 'auto',
            fit_mode: data.fit_mode || 'cover',
            media_side: data.media_side || 'right',
            media_size: data.media_size || 'm',
            text_align: data.text_align || 'left',
            text_valign: data.text_valign || 'center'
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
            if (key === 'media_type') {
                applyType(node);
            }
            if (key === 'frame_ratio') {
                updateSideOptions(node);
            }
            if (key === 'button_label' || key === 'button_href') {
                updateButtonHelp(node);
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

        wireZone(node, 'photo');
        wireZone(node, 'bg');

        list.appendChild(node);
        showInfo(node);
        showZone(node, 'photo');
        showZone(node, 'bg');
        updateButtonHelp(node);
        applyType(node);
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
            var st = el.__s;
            if (f.media_type === 'video' && !st.video_url) {
                problem = { el: el, text: 'Слайд ' + (i + 1) + ': выберите видео (или выберите «Фото» либо «Ничего» в списке «Что показать рядом с текстом»).' };
            } else if (f.media_type === 'photo' && !st.image_url) {
                problem = { el: el, text: 'Слайд ' + (i + 1) + ': выберите фото (или выберите «Видео» либо «Ничего» в списке «Что показать рядом с текстом»).' };
            } else if (!String(f.tab_label).trim()) {
                problem = { el: el, text: 'Слайд ' + (i + 1) + ': заполните название вкладки.', focus: '[data-f="tab_label"]' };
            } else if (!String(f.title).trim()) {
                problem = { el: el, text: 'Слайд ' + (i + 1) + ': заполните заголовок.', focus: '[data-f="title"]' };
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
