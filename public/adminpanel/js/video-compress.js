/**
 * Сжатие видео в браузере админа ДО отправки на сервер.
 *
 * Зачем: ролики с телефона весят 100+ МБ, а сервер принимает от силы десятки мегабайт (upload_max_filesize,
 * post_max_size). Здесь видео перекодируется прямо в браузере через WebCodecs (библиотека mediabunny,
 * vendor/mediabunny.min.mjs, MPL-2.0): H.264/AAC в MP4 с метаданными в начале файла (проигрывается сразу,
 * без полной загрузки), размер подбирается под реальные лимиты сервера.
 *
 * Из одного файла получается:
 *   video   сжатый ролик (для просмотра по клику, со звуком);
 *   preview короткий (≈8 с) немой ролик в малом разрешении: им «играет» шапка, без лишнего трафика;
 *   poster  обложка JPEG (кадр из начала ролика).
 *
 * API: window.AdminVideo.prepare(file, { limits, maxVideoBytes, onStage, onProgress, signal })
 * Ошибки (VideoError) всегда содержат понятное админу сообщение по-русски.
 */
(function () {
    'use strict';

    var MB = 1024 * 1024;
    var SCRIPT_URL = (document.currentScript && document.currentScript.src) || location.href;

    // Длинная сторона итогового ролика: берём самую крупную ступень, в которую влезаем по размеру
    var LADDER = [1280, 1024, 854, 640];
    // Биты на пиксель: «приличное» качество и допустимый минимум (H.264)
    var NICE_BPP = 0.045;
    var MIN_BPP = 0.028;
    var AUDIO_BPS = 96000;

    var PREVIEW_SECONDS = 8;
    var PREVIEW_LONG_SIDE = 640;
    var PREVIEW_FPS = 24;
    var POSTER_LONG_SIDE = 960;

    function VideoError(message, code) {
        this.name = 'VideoError';
        this.message = message;
        this.code = code || 'error';
        this.stack = (new Error(message)).stack;
    }
    VideoError.prototype = Object.create(Error.prototype);
    VideoError.prototype.constructor = VideoError;

    var libPromise = null;

    function loadLib() {
        if (!libPromise) {
            var url = new URL('vendor/mediabunny.min.mjs', SCRIPT_URL);
            var version = new URL(SCRIPT_URL).searchParams.get('v');
            if (version) {
                url.searchParams.set('v', version);
            }
            libPromise = import(url.href).catch(function () {
                libPromise = null;
                throw new VideoError('Не удалось загрузить модуль сжатия видео. Обновите страницу и попробуйте снова.', 'lib');
            });
        }
        return libPromise;
    }

    function even(n) {
        var v = Math.max(2, Math.round(n));
        return v % 2 === 0 ? v : v - 1;
    }

    function fmtMb(bytes) {
        return (bytes / MB).toFixed(bytes < 10 * MB ? 1 : 0).replace('.', ',') + ' МБ';
    }

    function hasWebCodecs() {
        return typeof window.VideoEncoder === 'function' && typeof window.VideoDecoder === 'function';
    }

    /**
     * Сколько байт можно отправить: минимум из лимитов PHP, лимита приложения, с запасом на заголовки запроса.
     */
    function budgetBytes(limits, maxVideoBytes) {
        var upload = limits && limits.uploadMax > 0 ? limits.uploadMax : Infinity;
        var post = limits && limits.postMax > 0 ? limits.postMax - 1.5 * MB : Infinity;
        var app = maxVideoBytes > 0 ? maxVideoBytes : 40 * MB;
        return Math.floor(Math.min(upload, post, app) * 0.92);
    }

    /**
     * Подбирает разрешение и битрейт, чтобы ролик заданной длительности уложился в бюджет.
     */
    function plan(w, h, fps, duration, budget, hasAudio) {
        var audioBps = hasAudio ? AUDIO_BPS : 0;
        var totalBps = (budget * 8) / Math.max(duration, 1) * 0.96;
        var videoBps = totalBps - audioBps;
        var longSide = Math.max(w, h);
        var outFps = Math.min(30, Math.max(15, Math.round(fps || 30)));

        for (var i = 0; i < LADDER.length; i++) {
            var side = Math.min(LADDER[i], longSide);
            var k = side / longSide;
            var ow = even(w * k);
            var oh = even(h * k);
            var px = ow * oh;
            var minBps = px * outFps * MIN_BPP;
            if (minBps <= videoBps) {
                return {
                    width: ow,
                    height: oh,
                    fps: outFps,
                    videoBps: Math.round(Math.min(px * outFps * NICE_BPP, videoBps)),
                    audioBps: audioBps
                };
            }
        }

        // Не влезает даже в минимальное разрешение: считаем, на сколько секунд надо сократить ролик
        var smallK = Math.min(LADDER[LADDER.length - 1], longSide) / longSide;
        var smallPx = even(w * smallK) * even(h * smallK);
        var needBps = smallPx * outFps * MIN_BPP + audioBps;
        var maxSeconds = Math.floor(((budget * 8) * 0.96) / needBps);
        return { tooLong: true, maxSeconds: Math.max(5, maxSeconds) };
    }

    function stage(opts, name, text) {
        if (opts.onStage) {
            opts.onStage(name, text);
        }
    }

    function progress(opts, from, to, value) {
        if (opts.onProgress) {
            opts.onProgress(from + (to - from) * Math.max(0, Math.min(1, value)));
        }
    }

    function canvasToJpeg(canvas) {
        return new Promise(function (resolve, reject) {
            var done = function (blob) {
                if (blob) {
                    resolve(blob);
                } else {
                    reject(new VideoError('Не удалось получить обложку из видео.', 'poster'));
                }
            };
            if (typeof canvas.convertToBlob === 'function') {
                canvas.convertToBlob({ type: 'image/jpeg', quality: 0.82 }).then(done, reject);
            } else {
                canvas.toBlob(done, 'image/jpeg', 0.82);
            }
        });
    }

    function throwIfAborted(opts) {
        if (opts.signal && opts.signal.aborted) {
            throw new VideoError('Отменено.', 'canceled');
        }
    }

    function runConversion(lib, conversion, opts, from, to) {
        var onAbort = function () {
            conversion.cancel();
        };
        if (opts.signal) {
            opts.signal.addEventListener('abort', onAbort);
        }
        conversion.onProgress = function (p) {
            progress(opts, from, to, p);
        };
        return conversion.execute().catch(function (e) {
            if (e && e.name === 'ConversionCanceledError' || (opts.signal && opts.signal.aborted)) {
                throw new VideoError('Отменено.', 'canceled');
            }
            throw e;
        }).then(function () {
            if (opts.signal) {
                opts.signal.removeEventListener('abort', onAbort);
            }
        }, function (e) {
            if (opts.signal) {
                opts.signal.removeEventListener('abort', onAbort);
            }
            throw e;
        });
    }

    function newOutput(lib) {
        return new lib.Output({
            format: new lib.Mp4OutputFormat({ fastStart: 'in-memory' }),
            target: new lib.BufferTarget()
        });
    }

    function openInput(lib, file) {
        return new lib.Input({ formats: lib.ALL_FORMATS, source: new lib.BlobSource(file) });
    }

    // ---------- обложка ----------

    function makePoster(lib, file, track, w, h, duration) {
        var k = POSTER_LONG_SIDE / Math.max(w, h);
        var width = k < 1 ? even(w * k) : even(w);
        var sink = new lib.CanvasSink(track, { width: width });
        var t = Math.min(1.5, Math.max(0, duration * 0.08));

        return sink.getCanvas(t).then(function (wrapped) {
            if (wrapped) {
                return wrapped;
            }
            return sink.canvases(0, 3).next().then(function (r) {
                return r.value || null;
            });
        }).then(function (wrapped) {
            if (!wrapped) {
                throw new VideoError('Не удалось получить кадр для обложки.', 'poster');
            }
            return canvasToJpeg(wrapped.canvas);
        });
    }

    // ---------- основной путь (WebCodecs) ----------

    async function prepareWithCodecs(file, opts) {
        var lib = await loadLib();
        var budget = budgetBytes(opts.limits, opts.maxVideoBytes);

        stage(opts, 'read', 'Читаем видео…');
        var input = openInput(lib, file);
        try {
            var track;
            try {
                track = await input.getPrimaryVideoTrack();
            } catch (e) {
                throw new VideoError('Не получилось прочитать файл: он повреждён или это не видео.', 'read');
            }
            if (!track) {
                throw new VideoError('В этом файле нет видео.', 'read');
            }
            if (!(await track.canDecode())) {
                throw new VideoError('Браузер не умеет читать видео в этом формате (часто это H.265/HEVC с телефона). Откройте админку в Chrome или Edge, либо сохраните ролик в формате MP4 (H.264).', 'decode');
            }

            var w = await track.getDisplayWidth();
            var h = await track.getDisplayHeight();
            var duration = await input.computeDuration();
            var codec = await track.getCodec();
            var audioTrack = await input.getPrimaryAudioTrack();
            var fps = 30;
            try {
                var stats = await track.computePacketStats(120);
                fps = stats.averageFrameRate || 30;
            } catch (e) {
                // частота кадров не критична
            }
            if (!(w > 0 && h > 0 && duration > 0)) {
                throw new VideoError('Не удалось определить размер или длительность видео.', 'read');
            }

            throwIfAborted(opts);

            var p = plan(w, h, fps, duration, budget, !!audioTrack);
            if (p.tooLong) {
                throw new VideoError('Ролик слишком длинный для этого сервера: при лимите загрузки ' + fmtMb(budget) + ' можно примерно ' + p.maxSeconds + ' сек. Сократите видео или попросите увеличить upload_max_filesize на сервере.', 'toolong');
            }

            var longSide = Math.max(w, h);
            var needTranscode = !(codec === 'avc' && file.size <= budget && longSide <= LADDER[0] && fps <= 31);

            if (needTranscode && !(await lib.canEncodeVideo('avc', { width: p.width, height: p.height, bitrate: p.videoBps }))) {
                throw new VideoError('Этот браузер не умеет сжимать видео в H.264. Откройте админку в Chrome или Edge.', 'encode');
            }

            // --- сжатие основного ролика (до 2 попыток, если битрейт «перелетел» бюджет) ---
            var videoBlob = null;
            var bps = p.videoBps;
            for (var attempt = 0; attempt < 2; attempt++) {
                stage(opts, 'compress', needTranscode ? 'Сжимаем видео…' : 'Готовим видео…');
                var output = newOutput(lib);
                var audioOpts = {};
                if (audioTrack) {
                    var aacOk = false;
                    try {
                        aacOk = await lib.canEncodeAudio('aac');
                    } catch (e) {
                        aacOk = false;
                    }
                    audioOpts = aacOk ? { codec: 'aac', quality: new lib.Quality({ bitrate: p.audioBps }), forceTranscode: needTranscode } : {};
                }
                var videoOpts = needTranscode ? {
                    width: p.width,
                    height: p.height,
                    fit: 'fill',
                    codec: 'avc',
                    quality: new lib.Quality({ bitrate: bps }),
                    frameRate: p.fps,
                    keyFrameInterval: 2,
                    forceTranscode: true
                } : {};
                var conversion = await lib.Conversion.init({
                    input: openInput(lib, file),
                    output: output,
                    video: videoOpts,
                    audio: audioOpts,
                    showWarnings: false
                });
                if (!conversion.isValid) {
                    throw new VideoError('Не получилось подготовить это видео к загрузке.', 'convert');
                }
                await runConversion(lib, conversion, opts, attempt === 0 ? 0 : 0.8, attempt === 0 ? 0.8 : 0.88);
                videoBlob = new Blob([output.target.buffer], { type: 'video/mp4' });

                if (videoBlob.size <= budget || !needTranscode) {
                    break;
                }
                bps = Math.floor(bps * (budget / videoBlob.size) * 0.9);
                if (bps < p.width * p.height * p.fps * MIN_BPP * 0.8) {
                    break;
                }
            }
            if (!videoBlob || videoBlob.size > budget) {
                throw new VideoError('Даже после сжатия ролик получился больше ' + fmtMb(budget) + '. Сократите видео по времени.', 'toolarge');
            }

            throwIfAborted(opts);

            // --- короткое немое превью для автозапуска в шапке ---
            var previewBlob = null;
            if (duration > PREVIEW_SECONDS + 2 && videoBlob.size > 4 * MB) {
                stage(opts, 'preview', 'Делаем короткое превью…');
                var pk = PREVIEW_LONG_SIDE / longSide;
                var pw = even(w * Math.min(1, pk));
                var ph = even(h * Math.min(1, pk));
                var previewBps = Math.max(150000, Math.round(pw * ph * PREVIEW_FPS * 0.05));
                if (await lib.canEncodeVideo('avc', { width: pw, height: ph, bitrate: previewBps })) {
                    var pOut = newOutput(lib);
                    var pConv = await lib.Conversion.init({
                        input: openInput(lib, file),
                        output: pOut,
                        video: {
                            width: pw, height: ph, fit: 'fill', codec: 'avc',
                            quality: new lib.Quality({ bitrate: previewBps }),
                            frameRate: PREVIEW_FPS, keyFrameInterval: 2, forceTranscode: true
                        },
                        audio: { discard: true },
                        trim: { start: 0, end: PREVIEW_SECONDS },
                        showWarnings: false
                    });
                    if (pConv.isValid) {
                        await runConversion(lib, pConv, opts, 0.88, 0.95);
                        previewBlob = new Blob([pOut.target.buffer], { type: 'video/mp4' });
                    }
                }
            }

            // --- обложка ---
            stage(opts, 'poster', 'Делаем обложку…');
            var poster = await makePoster(lib, file, track, w, h, duration);
            progress(opts, 0.95, 1, 1);

            var outW = needTranscode ? p.width : w;
            var outH = needTranscode ? p.height : h;

            return {
                video: videoBlob,
                preview: previewBlob,
                poster: poster,
                width: outW,
                height: outH,
                duration: Math.max(1, Math.round(duration)),
                originalBytes: file.size,
                finalBytes: videoBlob.size,
                transcoded: needTranscode
            };
        } finally {
            try {
                input.dispose();
            } catch (e) {
                // не критично
            }
        }
    }

    // ---------- запасной путь без WebCodecs: только маленькие MP4 как есть ----------

    function probeWithVideoElement(file) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(file);
            var v = document.createElement('video');
            v.preload = 'auto';
            v.muted = true;
            v.playsInline = true;
            var fail = function () {
                URL.revokeObjectURL(url);
                reject(new VideoError('Не получилось прочитать видео. Проверьте, что это обычный MP4-файл.', 'read'));
            };
            v.onerror = fail;
            v.onloadedmetadata = function () {
                var duration = v.duration;
                var w = v.videoWidth;
                var h = v.videoHeight;
                if (!(w > 0 && h > 0 && duration > 0 && isFinite(duration))) {
                    fail();
                    return;
                }
                v.onseeked = function () {
                    var k = Math.min(1, POSTER_LONG_SIDE / Math.max(w, h));
                    var c = document.createElement('canvas');
                    c.width = even(w * k);
                    c.height = even(h * k);
                    c.getContext('2d').drawImage(v, 0, 0, c.width, c.height);
                    URL.revokeObjectURL(url);
                    canvasToJpeg(c).then(function (poster) {
                        resolve({ poster: poster, width: w, height: h, duration: Math.max(1, Math.round(duration)) });
                    }, reject);
                };
                v.currentTime = Math.min(1, duration * 0.08);
            };
            v.src = url;
        });
    }

    async function prepareWithoutCodecs(file, opts) {
        var budget = budgetBytes(opts.limits, opts.maxVideoBytes);
        var isMp4 = /mp4/i.test(file.type) || /\.(mp4|m4v)$/i.test(file.name);
        if (!isMp4 || file.size > budget) {
            throw new VideoError('Этот браузер не умеет сжимать видео, а файл слишком большой для сервера (' + fmtMb(file.size) + ', максимум ' + fmtMb(budget) + '). Откройте админку в Chrome или Edge.', 'unsupported');
        }
        stage(opts, 'read', 'Читаем видео…');
        var meta = await probeWithVideoElement(file);
        progress(opts, 0, 1, 1);
        return {
            video: file,
            preview: null,
            poster: meta.poster,
            width: meta.width,
            height: meta.height,
            duration: meta.duration,
            originalBytes: file.size,
            finalBytes: file.size,
            transcoded: false
        };
    }

    async function prepare(file, options) {
        var opts = options || {};
        if (!file) {
            throw new VideoError('Файл не выбран.', 'nofile');
        }
        if (!/^video\//i.test(file.type) && !/\.(mp4|m4v|mov|webm|mkv|avi)$/i.test(file.name)) {
            throw new VideoError('Выберите видеофайл (MP4, MOV, WebM).', 'type');
        }
        if (hasWebCodecs()) {
            return prepareWithCodecs(file, opts);
        }
        return prepareWithoutCodecs(file, opts);
    }

    window.AdminVideo = {
        prepare: prepare,
        budgetBytes: budgetBytes,
        hasWebCodecs: hasWebCodecs,
        formatBytes: fmtMb,
        VideoError: VideoError,
        _plan: plan
    };
})();
