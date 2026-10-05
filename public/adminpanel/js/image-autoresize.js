/**
 * Автоматический ресайз изображений в админке.
 *
 * Если выбранное/перетащенное/вставленное фото больше, чем пропустит сервер, оно тихо уменьшается
 * (пропорции сохраняются, сначала снижается разрешение до разумного, качество JPEG не ниже ~0.72).
 * Перед отправкой формы дополнительно проверяется суммарный размер запроса (post_max_size),
 * включая base64-картинки внутри скрытых полей (Quill, редактор блоков), и при необходимости всё ужимается сильнее.
 *
 * Лимиты берутся из window.ADMIN_UPLOAD_LIMITS (php.ini сервера), см. App\Support\UploadLimits.
 */
(function () {
    'use strict';

    var MB = 1024 * 1024;
    var limits = window.ADMIN_UPLOAD_LIMITS || {};

    function positive(value, fallback) {
        return typeof value === 'number' && value > 0 ? value : fallback;
    }

    // Запас на заголовки multipart и прочие поля
    var PER_FILE_MAX = Math.floor(Math.min(positive(limits.uploadMax, 2 * MB), positive(limits.appImageMax, 5 * MB)) * 0.95);
    var POST_BUDGET = Math.floor(positive(limits.postMax, 8 * MB) * 0.9);
    var MIN_TARGET = 150 * 1024;

    // Сначала уменьшаем разрешение до «веб-размера», качество снижаем умеренно — без мыла и артефактов.
    var STEPS = [
        { side: 2560, quality: 0.9 },
        { side: 2560, quality: 0.85 },
        { side: 2048, quality: 0.85 },
        { side: 1920, quality: 0.82 },
        { side: 1600, quality: 0.8 },
        { side: 1440, quality: 0.78 },
        { side: 1280, quality: 0.76 },
        { side: 1024, quality: 0.74 },
        { side: 800, quality: 0.72 }
    ];

    var DATA_URL_RE = /data:image\/(?:jpeg|jpg|png|webp|bmp);base64,[A-Za-z0-9+/=]+/g;

    var replayedEvents = typeof WeakSet === 'function' ? new WeakSet() : null;
    var canReplaceFiles = (function () {
        try {
            return typeof DataTransfer === 'function' && !!new DataTransfer().items;
        } catch (e) {
            return false;
        }
    })();

    // ==================== Ресайз одного изображения ====================

    function isResizable(file) {
        // gif не трогаем (анимация), svg — вектор, heic браузер обычно не декодирует (попробуем, но без гарантий)
        return !!file && /^image\//i.test(file.type || '') && !/^image\/(gif|svg)/i.test(file.type);
    }

    function decodeImage(blob) {
        function viaImg() {
            return new Promise(function (resolve, reject) {
                var url = URL.createObjectURL(blob);
                var img = new Image();
                img.onload = function () {
                    URL.revokeObjectURL(url);
                    resolve(img);
                };
                img.onerror = function () {
                    URL.revokeObjectURL(url);
                    reject(new Error('decode failed'));
                };
                img.src = url;
            });
        }

        if (typeof createImageBitmap === 'function') {
            // from-image — учитываем EXIF-поворот, чтобы фото с телефона не легло набок
            return createImageBitmap(blob, { imageOrientation: 'from-image' }).catch(viaImg);
        }
        return viaImg();
    }

    function sourceSize(source) {
        return {
            width: source.naturalWidth || source.width,
            height: source.naturalHeight || source.height
        };
    }

    function makeCanvas(width, height) {
        var canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        return canvas;
    }

    function hasTransparency(source, size) {
        var scale = Math.min(1, 256 / Math.max(size.width, size.height));
        var w = Math.max(1, Math.round(size.width * scale));
        var h = Math.max(1, Math.round(size.height * scale));
        var canvas = makeCanvas(w, h);
        var ctx = canvas.getContext('2d');
        ctx.drawImage(source, 0, 0, w, h);
        try {
            var data = ctx.getImageData(0, 0, w, h).data;
            for (var i = 3; i < data.length; i += 4) {
                if (data[i] < 250) return true;
            }
        } catch (e) {
            return true;
        }
        return false;
    }

    // Качественное уменьшение: крупные шаги делаем пополам, финальный — с imageSmoothingQuality = high
    function render(source, size, width, height, opaqueBackground) {
        var current = source;
        var cw = size.width;
        var ch = size.height;

        while (cw / 2 >= width && ch / 2 >= height) {
            cw = Math.round(cw / 2);
            ch = Math.round(ch / 2);
            var half = makeCanvas(cw, ch);
            var hctx = half.getContext('2d');
            hctx.imageSmoothingEnabled = true;
            hctx.imageSmoothingQuality = 'high';
            hctx.drawImage(current, 0, 0, cw, ch);
            current = half;
        }

        var canvas = makeCanvas(width, height);
        var ctx = canvas.getContext('2d');
        if (opaqueBackground) {
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, width, height);
        }
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(current, 0, 0, width, height);
        return canvas;
    }

    function canvasToBlob(canvas, type, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) {
                resolve(blob);
            }, type, quality);
        });
    }

    function renameWithExtension(name, extension) {
        var base = (name || 'image').replace(/\.[^.\\/]+$/, '');
        return base + '.' + extension;
    }

    /**
     * Возвращает Blob/File не больше maxBytes (по возможности). Если ресайз не нужен или невозможен — исходный blob.
     */
    function fitImage(blob, maxBytes) {
        if (!blob || blob.size <= maxBytes || !isResizable(blob)) {
            return Promise.resolve(blob);
        }

        return decodeImage(blob).then(function (source) {
            var size = sourceSize(source);
            if (!size.width || !size.height) return blob;

            var keepAlpha = !/jpe?g/i.test(blob.type) && hasTransparency(source, size);
            var type = keepAlpha ? 'image/png' : 'image/jpeg';
            var longSide = Math.max(size.width, size.height);
            var cache = {};
            var best = null;
            var tried = {};

            var steps = STEPS.map(function (step) {
                return { side: Math.min(step.side, longSide), quality: step.quality };
            }).filter(function (step) {
                var key = step.side + ':' + (keepAlpha ? '' : step.quality);
                if (tried[key]) return false;
                tried[key] = true;
                return true;
            });

            function attempt(index) {
                if (index >= steps.length) return Promise.resolve(best);

                var step = steps[index];
                var scale = step.side / longSide;
                var width = Math.max(1, Math.round(size.width * scale));
                var height = Math.max(1, Math.round(size.height * scale));
                var canvas = cache[step.side] || (cache[step.side] = render(source, size, width, height, !keepAlpha));

                return canvasToBlob(canvas, type, keepAlpha ? undefined : step.quality).then(function (result) {
                    if (result && (!best || result.size < best.size)) best = result;
                    if (result && result.size <= maxBytes) return result;
                    return attempt(index + 1);
                });
            }

            return attempt(0).then(function (result) {
                if (source.close) source.close();
                if (!result || result.size >= blob.size) return blob;

                var extension = keepAlpha ? 'png' : 'jpg';
                try {
                    return new File([result], renameWithExtension(blob.name, extension), {
                        type: type,
                        lastModified: Date.now()
                    });
                } catch (e) {
                    result.name = renameWithExtension(blob.name, extension);
                    return result;
                }
            });
        }).catch(function () {
            return blob;
        });
    }

    function fitFiles(files, maxBytes) {
        return Promise.all(Array.prototype.map.call(files, function (file) {
            return fitImage(file, maxBytes);
        }));
    }

    function needsResize(files, maxBytes) {
        return Array.prototype.some.call(files || [], function (file) {
            return isResizable(file) && file.size > maxBytes;
        });
    }

    function toDataTransfer(files) {
        var dt = new DataTransfer();
        files.forEach(function (file) {
            dt.items.add(file instanceof File ? file : new File([file], file.name || 'image.jpg', { type: file.type }));
        });
        return dt;
    }

    function setInputFiles(input, files) {
        if (!canReplaceFiles) return false;
        try {
            input.files = toDataTransfer(files).files;
            return true;
        } catch (e) {
            return false;
        }
    }

    // ==================== Индикатор занятости ====================

    var busyCount = 0;

    function setBusy(delta) {
        busyCount = Math.max(0, busyCount + delta);
        document.documentElement.style.cursor = busyCount > 0 ? 'progress' : '';
    }

    function markReplayed(event) {
        if (replayedEvents) replayedEvents.add(event);
        else event.__autoresizeReplayed = true;
        return event;
    }

    function isReplayed(event) {
        return replayedEvents ? replayedEvents.has(event) : !!event.__autoresizeReplayed;
    }

    function isFileInput(el) {
        return el && el.tagName === 'INPUT' && el.type === 'file';
    }

    function dispatchChange(input) {
        input.dispatchEvent(markReplayed(new Event('input', { bubbles: true })));
        input.dispatchEvent(markReplayed(new Event('change', { bubbles: true })));
    }

    // ==================== Выбор файла через input ====================

    function interceptInputEvent(event) {
        var input = event.target;
        if (!isFileInput(input) || isReplayed(event) || !canReplaceFiles) return;
        if (!needsResize(input.files, PER_FILE_MAX)) return;

        // Обработчики страницы получат событие уже с ужатыми файлами
        event.stopImmediatePropagation();
        if (event.type !== 'change') return;

        var original = Array.prototype.slice.call(input.files);
        setBusy(1);
        fitFiles(original, PER_FILE_MAX).then(function (resized) {
            setInputFiles(input, resized);
        }).finally(function () {
            setBusy(-1);
            dispatchChange(input);
        });
    }

    window.addEventListener('input', interceptInputEvent, true);
    window.addEventListener('change', interceptInputEvent, true);

    // ==================== Drag & drop ====================

    window.addEventListener('drop', function (event) {
        var dt = event.dataTransfer;
        if (isReplayed(event) || !canReplaceFiles || !dt || !needsResize(dt.files, PER_FILE_MAX)) return;

        event.preventDefault();
        event.stopImmediatePropagation();

        var target = event.target;
        var original = Array.prototype.slice.call(dt.files);
        var init = {
            bubbles: true,
            cancelable: true,
            clientX: event.clientX,
            clientY: event.clientY,
            screenX: event.screenX,
            screenY: event.screenY
        };

        setBusy(1);
        fitFiles(original, PER_FILE_MAX).then(function (resized) {
            if (isFileInput(target)) {
                setInputFiles(target, resized);
                dispatchChange(target);
                return;
            }
            init.dataTransfer = toDataTransfer(resized);
            target.dispatchEvent(markReplayed(new DragEvent('drop', init)));
        }).finally(function () {
            setBusy(-1);
        });
    }, true);

    // ==================== Вставка из буфера (Quill) ====================

    window.addEventListener('paste', function (event) {
        var cd = event.clipboardData;
        if (isReplayed(event) || !canReplaceFiles || !cd || !needsResize(cd.files, PER_FILE_MAX)) return;
        // При вставке HTML редактор берёт разметку, а не файлы — не мешаем
        if (Array.prototype.indexOf.call(cd.types || [], 'text/html') !== -1) return;

        event.preventDefault();
        event.stopImmediatePropagation();

        var target = event.target;
        var original = Array.prototype.slice.call(cd.files);
        setBusy(1);
        fitFiles(original, PER_FILE_MAX).then(function (resized) {
            var replay;
            try {
                replay = new ClipboardEvent('paste', {
                    bubbles: true,
                    cancelable: true,
                    clipboardData: toDataTransfer(resized)
                });
            } catch (e) {
                return;
            }
            target.dispatchEvent(markReplayed(replay));
        }).finally(function () {
            setBusy(-1);
        });
    }, true);

    // ==================== Проверка всего запроса перед отправкой ====================

    function byteLength(str) {
        if (!str) return 0;
        if (typeof TextEncoder === 'function' && str.length < 2 * MB) {
            return new TextEncoder().encode(str).length;
        }
        // для огромных строк (base64) точный подсчёт не нужен — они почти целиком ASCII
        return str.length;
    }

    function collectForm(form) {
        var plan = { files: [], dataUrls: [], otherBytes: 0, imageBytes: 0 };

        Array.prototype.forEach.call(form.elements, function (el) {
            if (!el.name || el.disabled) return;

            if (isFileInput(el)) {
                Array.prototype.forEach.call(el.files || [], function (file, index) {
                    if (isResizable(file)) {
                        plan.files.push({ input: el, index: index, size: file.size });
                        plan.imageBytes += file.size;
                    } else {
                        plan.otherBytes += file.size;
                    }
                });
                return;
            }

            if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;

            var value = el.value || '';
            var imageChars = 0;
            if (value.length > 1024 && value.indexOf('data:image/') !== -1) {
                var matches = value.match(DATA_URL_RE) || [];
                matches.forEach(function (dataUrl) {
                    plan.dataUrls.push({ field: el, dataUrl: dataUrl, size: dataUrl.length });
                    imageChars += dataUrl.length;
                });
            }
            plan.imageBytes += imageChars;
            plan.otherBytes += byteLength(value) - imageChars + el.name.length + 100;
        });

        return plan;
    }

    function planIsTooBig(plan) {
        var fileTooBig = plan.files.some(function (item) {
            return item.size > PER_FILE_MAX;
        });
        return fileTooBig || plan.imageBytes + plan.otherBytes > POST_BUDGET;
    }

    function dataUrlToBlob(dataUrl) {
        var comma = dataUrl.indexOf(',');
        var type = dataUrl.slice(5, dataUrl.indexOf(';'));
        var binary = atob(dataUrl.slice(comma + 1));
        var bytes = new Uint8Array(binary.length);
        for (var i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
        return new Blob([bytes], { type: type });
    }

    function blobToDataUrl(blob) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function () { resolve(reader.result); };
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        });
    }

    function shrinkPlan(plan, squeeze) {
        var available = POST_BUDGET - plan.otherBytes;
        var factor = plan.imageBytes > 0 ? Math.min(1, (available / plan.imageBytes) * squeeze) : 1;
        var tasks = [];

        // Файлы: каждому input'у — свой новый набор файлов
        var byInput = new Map();
        plan.files.forEach(function (item) {
            if (!byInput.has(item.input)) byInput.set(item.input, []);
            byInput.get(item.input).push(item);
        });

        byInput.forEach(function (items, input) {
            var files = Array.prototype.slice.call(input.files);
            var changed = false;
            var jobs = items.map(function (item) {
                var target = Math.max(MIN_TARGET, Math.min(PER_FILE_MAX, Math.floor(item.size * factor)));
                if (files[item.index].size <= target) return Promise.resolve();
                return fitImage(files[item.index], target).then(function (result) {
                    if (result !== files[item.index]) {
                        files[item.index] = result;
                        changed = true;
                    }
                });
            });
            tasks.push(Promise.all(jobs).then(function () {
                if (changed) setInputFiles(input, files);
            }));
        });

        // base64 внутри полей: заменяем строку на ужатую
        plan.dataUrls.forEach(function (item) {
            var targetChars = Math.max(MIN_TARGET, Math.floor(item.size * factor));
            if (item.size <= targetChars) return;
            var blob = dataUrlToBlob(item.dataUrl);
            tasks.push(fitImage(blob, Math.floor(targetChars * 0.75)).then(function (result) {
                if (result === blob) return;
                return blobToDataUrl(result).then(function (newDataUrl) {
                    item.field.value = item.field.value.split(item.dataUrl).join(newDataUrl);
                });
            }));
        });

        return Promise.all(tasks);
    }

    function submitForm(form, submitter) {
        if (submitter && submitter.name) {
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = submitter.name;
            hidden.value = submitter.value;
            form.appendChild(hidden);
        }
        // Через прототип: обработчики submit уже отработали, а у формы может быть поле с name="submit"
        HTMLFormElement.prototype.submit.call(form);
    }

    // Слушаем в фазе всплытия на window — к этому моменту обработчики страницы уже собрали
    // содержимое редакторов в скрытые поля (collectAndSetBlocks, Quill → textarea и т.п.)
    window.addEventListener('submit', function (event) {
        var form = event.target;
        if (!form || form.tagName !== 'FORM') return;
        if (form.__autoresizeBusy) {
            event.preventDefault();
            return;
        }
        if (event.defaultPrevented || (form.method || '').toLowerCase() !== 'post') return;

        var plan = collectForm(form);
        if (!planIsTooBig(plan)) return;

        event.preventDefault();
        form.__autoresizeBusy = true;
        setBusy(1);

        var squeeze = 0.95;
        var round = 0;

        function run() {
            return shrinkPlan(plan, squeeze).then(function () {
                plan = collectForm(form);
                round += 1;
                if (planIsTooBig(plan) && round < 3) {
                    squeeze *= 0.8;
                    return run();
                }
            });
        }

        run().catch(function () {
            // В худшем случае отправляем как есть: сервер вернёт понятную ошибку
        }).finally(function () {
            setBusy(-1);
            form.__autoresizeBusy = false;
            submitForm(form, event.submitter);
        });
    }, false);
})();
