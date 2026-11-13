@extends('layouts.admin')

@section('title', 'Слайдер с видео')

@section('style')
<style>
    .hs-limit { border-radius: .75rem; }

    .hs-list { display: flex; flex-direction: column; gap: 1rem; }

    .hs-item { border: 1px solid #dee2e6; border-radius: .85rem; background: #fff; overflow: hidden; box-shadow: 0 8px 24px rgba(0, 0, 0, .04); transition: opacity .2s, border-color .2s, background-color .2s; }
    .hs-item.sortable-ghost { opacity: .35; }
    .hs-item.is-pending-delete { border-color: #f1aeb5; background: #fff5f5; }
    .hs-item.is-pending-delete .hs-body { opacity: .5; pointer-events: none; }
    .hs-item.is-pending-delete .hs-title-preview { text-decoration: line-through; }
    .hs-item.is-inactive .hs-title-preview { opacity: .55; }

    .hs-head { display: flex; align-items: center; gap: .75rem; padding: .9rem 1rem; background: #f8f9fa; border-bottom: 1px solid #e9ecef; flex-wrap: wrap; }
    .hs-order { width: 34px; height: 34px; border-radius: 50%; background: #0d6efd; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
    .hs-handle { width: 34px; height: 34px; border-radius: 50%; border: 1px solid #d0d7de; background: #fff; color: #6c757d; display: inline-flex; align-items: center; justify-content: center; cursor: grab; flex-shrink: 0; }
    .hs-handle:active { cursor: grabbing; }
    .hs-title-preview { font-weight: 600; flex: 1 1 160px; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .hs-body { padding: 1.1rem 1rem 1.25rem; }
    .hs-body .form-label { font-weight: 600; font-size: .85rem; }

    /* загрузка видео */
    .hs-upload { border: 1.5px dashed #b6c2d2; border-radius: .75rem; padding: .85rem 1rem; background: #f8fafc; transition: border-color .15s, background-color .15s; }
    .hs-upload.is-drag { border-color: #0d6efd; background: #eef5ff; }
    .hs-upload.has-error { border-color: #dc3545; background: #fff5f5; }
    .hs-upload-info { font-size: .9rem; }
    .hs-upload-info .hs-meta { color: #6c757d; font-size: .82rem; }
    .hs-thumb { width: 64px; height: 64px; object-fit: cover; border-radius: .5rem; border: 1px solid #dee2e6; background: #e9ecef; flex-shrink: 0; }
    .hs-progress { height: .6rem; }

    /* предпросмотр: настоящая вёрстка шапки, уменьшенная */
    .hs-pv-box { border-radius: .75rem; overflow: hidden; background: #e9ecef; }
    .hs-pv { width: 1060px; transform-origin: top left; }
    .hs-hero { position: relative; min-height: 520px; overflow: hidden; color: #fff; background: #222 center/cover; font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; }
    .hs-hero.is-mobile { min-height: 0; }
    .hs-ov { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,.45), rgba(0,0,0,.68)); z-index: 1; }
    .hs-bgposter { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }
    .hs-bgposter.hs-blur { filter: blur(28px) brightness(.55); transform: scale(1.15); }
    .hs-slide { position: relative; z-index: 2; min-height: 520px; display: flex; align-items: center; justify-content: space-between; gap: 48px; padding: 40px 64px 86px; }
    .hs-hero.is-mobile .hs-slide { min-height: 0; flex-direction: column; padding: 28px 20px 74px; gap: 20px; align-items: stretch; }
    .hs-hero.is-mobile .hs-media { align-self: center; }
    .hs-slide[data-side="left"] { flex-direction: row-reverse; }
    .hs-slide[data-side="background"] { flex-direction: column; justify-content: center; }
    .hs-txt { flex: 1 1 0; min-width: 0; text-align: left; }
    .hs-txt[data-ta="center"] { text-align: center; }
    .hs-txt[data-ta="right"] { text-align: right; }
    .hs-txt[data-ta="center"] p { margin-left: auto; margin-right: auto; }
    .hs-txt[data-ta="right"] p { margin-left: auto; }
    .hs-slide[data-va="top"] .hs-txt { align-self: flex-start; }
    .hs-slide[data-va="bottom"] .hs-txt { align-self: flex-end; }
    .hs-slide[data-side="background"][data-va="top"] { justify-content: flex-start; }
    .hs-slide[data-side="background"][data-va="bottom"] { justify-content: flex-end; }
    .hs-slide[data-side="background"] .hs-txt { align-self: stretch; }
    .hs-hero.is-mobile .hs-slide[data-va] .hs-txt { align-self: stretch; }
    .hs-txt h3 { font-size: 38px; line-height: 1.12; font-weight: 700; margin: 0 0 12px; text-shadow: 0 2px 8px rgba(0,0,0,.45); }
    .hs-txt p { font-size: 17px; line-height: 1.5; margin: 0 0 22px; max-width: 520px; opacity: .96; }
    .hs-hero.is-mobile .hs-txt h3 { font-size: 25px; }
    .hs-btn { display: inline-block; padding: 11px 22px; border-radius: 99px; background: #fff; color: #212529; font-weight: 600; font-size: 16px; }
    .hs-media { position: relative; flex: 0 0 auto; border: 2px solid rgba(255,255,255,.3); border-radius: 18px; overflow: hidden; background: #000; box-shadow: 0 10px 34px rgba(0,0,0,.55); }
    .hs-media .hs-bk { position: absolute; inset: -12%; width: 124%; height: 124%; object-fit: cover; filter: blur(22px) brightness(.65); }
    .hs-media img.hs-fg { position: relative; width: 100%; height: 100%; display: block; }
    .hs-media .hs-empty { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; color: #cbd5e1; font-size: 15px; text-align: center; padding: 12px; }
    .hs-play { position: absolute; left: 10px; bottom: 10px; display: flex; align-items: center; gap: 6px; background: rgba(0,0,0,.65); color: #fff; border-radius: 99px; padding: 6px 12px; font-size: 13px; font-weight: 600; }
    .hs-pills { position: absolute; z-index: 3; left: 0; right: 0; bottom: 22px; display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; padding: 0 12px; }
    .hs-pill { border: 1px solid rgba(255,255,255,.55); background: rgba(0,0,0,.4); color: #fff; padding: 8px 16px; border-radius: 99px; font-weight: 600; font-size: 14px; }
    .hs-pill.on { background: #fff; color: #111; border-color: #fff; }
    .hs-hero.is-mobile .hs-pill { padding: 6px 12px; font-size: 13px; }

    .hs-help { font-size: .82rem; color: #6c757d; }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1 class="m-0">Слайдер с видео на главной</h1>
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

        <div class="alert alert-light border hs-limit mb-3" id="hs-limit-note" role="status"></div>

        <details class="card hs-guide mb-3" id="hs-guide" open>
            <summary class="card-header fw-semibold" style="cursor:pointer">
                <i class="fas fa-circle-question text-primary me-1"></i>Как пользоваться: добавить видео на главную за 5 шагов
            </summary>
            <div class="card-body">
                <ol class="mb-3 ps-3">
                    <li class="mb-2"><strong>Добавьте слайд.</strong> Внизу страницы нажмите «Добавить слайд»: появится новая карточка.</li>
                    <li class="mb-2"><strong>Выберите, что показать рядом с текстом:</strong> видео, фото или ничего (тогда будет только текст). Для видео нажмите «Выбрать видео» (или перетащите файл в рамку):
                        подойдёт любой ролик с телефона или камеры, хоть 100 МБ, сайт сам его сожмёт. Для фото работает так же, фото уменьшается автоматически.
                        Появится полоска: подождите, пока она дойдёт до конца, и не закрывайте вкладку.</li>
                    <li class="mb-2"><strong>Заполните тексты.</strong> «Название вкладки» это короткая надпись на переключателе внизу слайда (например, «Категория A»).
                        «Заголовок» и «Подпись» показываются на слайде. Кнопка не обязательна: чтобы она появилась, заполните и текст кнопки, и куда она ведёт.</li>
                    <li class="mb-2"><strong>Настройте, как показывать.</strong> Справа от настроек живой предпросмотр (кнопки «ПК» и «Телефон»): меняйте и сразу смотрите, что получится.</li>
                    <li><strong>Нажмите «Сохранить»</strong> (зелёная кнопка внизу). Пока не нажали, на сайте ничего не меняется.</li>
                </ol>

                <div class="fw-semibold mb-1">Что значат настройки</div>
                <ul class="mb-3 ps-3">
                    <li><strong>Пропорции рамки</strong>: форма окошка с видео. «Как в самом видео» подходит почти всегда; «9:16» узкое вертикальное, «1:1» квадрат, «16:9» широкое.</li>
                    <li><strong>Как вписать видео</strong>: если форма рамки не совпадает с формой видео, «Обрезать» уберёт края, «Показать целиком» оставит всё видео и заполнит пустоту размытым фоном.</li>
                    <li><strong>Где расположить</strong>: справа или слева от текста либо «Фоном на всю шапку» (только для горизонтального видео: вертикальное фоном выглядит плохо, и сайт этого не даст выбрать; для фото «фоном» не нужно, у слайда есть отдельный «Фон слайда»).</li>
                    <li><strong>Размер</strong>: маленькое, среднее или большое окошко с видео или фото.</li>
                    <li><strong>Фон слайда</strong>: своё фото на фон (необязательно). Не выбирали: у слайда с видео фоном будет размытый кадр из видео, у остальных такой же фон, как у основной шапки.</li>
                    <li><strong>Выравнивание текста</strong>: слева, по центру или справа, и сверху, по центру или снизу.</li>
                </ul>

                <div class="fw-semibold mb-1">Полезное</div>
                <ul class="mb-0 ps-3">
                    <li><strong>Порядок вкладок</strong>: тяните карточку за кнопку со стрелками слева в её заголовке.</li>
                    <li><strong>Убрать с сайта на время</strong>: выключите переключатель «Показывать» у слайда, он останется в списке. «Удалить» убирает слайд насовсем.</li>
                    <li><strong>Где показывать</strong>: в блоке «Где показывать видео» выше включайте «Слайдер в шапке» и/или «Карточки категорий под шапкой». Можно оба сразу.</li>
                    <li><strong>Пишет «Сервер принимает файлы до …»</strong> или «ролик слишком длинный»: сократите ролик либо попросите того, кто настраивает сервер, увеличить лимиты (подробности в сообщении над этой инструкцией).</li>
                    <li><strong>Видео не сжимается</strong>: откройте админку в Chrome или Edge, в других браузерах загрузятся только небольшие файлы.</li>
                </ul>
            </div>
        </details>

        <form method="POST" action="{{ route('admin.hero-slides.update') }}" id="hero-slides-form" novalidate>
            @csrf

            <div class="card">
                <div class="card-header"><h2 class="card-title h5 mb-0">Где показывать видео</h2></div>
                <div class="card-body">
                    <p class="text-muted">
                        Видео можно показать двумя способами, включайте любой или оба сразу. Если слайдов с видео нет (или все выключены),
                        главная остаётся как раньше.
                    </p>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <input type="hidden" name="slider_in_hero" value="0">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="slider_in_hero" name="slider_in_hero" value="1" @checked($settings['slider_in_hero'])>
                                    <label class="form-check-label fw-semibold" for="slider_in_hero">Слайдер в шапке</label>
                                </div>
                                <div class="hs-help mb-3">Видео на первом экране: у каждого ролика своя вкладка, заголовок, подпись и кнопка. Посетитель листает вкладками, стрелками или свайпом.</div>

                                <div data-hero-options>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="main_slide_label">Вкладка основного слайда</label>
                                        <input type="text" class="form-control" id="main_slide_label" name="main_slide_label" maxlength="60"
                                               value="{{ $settings['main_slide_label'] }}" required>
                                        <div class="hs-help mt-1">Так называется обычная шапка (её текст и кнопки меняются в разделе <a href="{{ route('admin.hero-settings') }}">«Hero (шапка)»</a>).</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="main_slide_position">Где основной слайд</label>
                                        <select class="form-select" id="main_slide_position" name="main_slide_position">
                                            <option value="last" @selected($settings['main_slide_position'] === 'last')>После видео (видео в самом начале)</option>
                                            <option value="first" @selected($settings['main_slide_position'] === 'first')>Перед видео (видео после основной шапки)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold" for="slider_autoplay_seconds">Автопереключение слайдов</label>
                                        <select class="form-select" id="slider_autoplay_seconds" name="slider_autoplay_seconds">
                                            @foreach($autoplayChoices as $sec)
                                                <option value="{{ $sec }}" @selected((int) $settings['slider_autoplay_seconds'] === $sec)>{{ $sec === 0 ? 'Выключено (листает сам посетитель)' : 'Каждые ' . $sec . ' секунд' }}</option>
                                            @endforeach
                                        </select>
                                        <div class="hs-help mt-1">Пока смотрят видео или навели мышь, слайды не листаются.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="border rounded-3 p-3 h-100">
                                <input type="hidden" name="slider_show_cards" value="0">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" role="switch" id="slider_show_cards" name="slider_show_cards" value="1" @checked($settings['slider_show_cards'])>
                                    <label class="form-check-label fw-semibold" for="slider_show_cards">Карточки категорий под шапкой</label>
                                </div>
                                <div class="hs-help mb-3">Блок под шапкой: по карточке на каждый ролик, все видны сразу. Видео открывается на весь экран по клику.</div>

                                <div data-cards-options>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="cards_title">Заголовок блока</label>
                                        <input type="text" class="form-control" id="cards_title" name="cards_title" maxlength="120" value="{{ $settings['cards_title'] }}">
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold" for="cards_subtitle">Подзаголовок</label>
                                        <input type="text" class="form-control" id="cards_subtitle" name="cards_subtitle" maxlength="255" value="{{ $settings['cards_subtitle'] }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h2 class="card-title h5 mb-0">Слайды с видео</h2>
                    <span class="hs-help">Перетаскивайте карточки за кнопку со стрелками, чтобы изменить порядок вкладок.</span>
                </div>
                <div class="card-body">
                    <div class="hs-list" id="hs-list"></div>

                    <div class="alert alert-secondary mt-3 mb-0 d-none" id="hs-empty">
                        Слайдов с видео пока нет. Нажмите «Добавить слайд» и выберите ролик: любого размера, он сожмётся автоматически.
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="button" class="btn btn-outline-primary" id="hs-add">
                            <i class="fas fa-plus me-1"></i>Добавить слайд
                        </button>
                        <button type="submit" class="btn btn-success" id="hs-save">
                            <i class="fas fa-save me-1"></i>Сохранить
                        </button>
                        <span class="align-self-center text-danger small d-none" id="hs-save-note"></span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<div class="modal fade" id="hsDeleteModal" tabindex="-1" aria-labelledby="hsDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hsDeleteModalLabel">Удалить слайд?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Слайд и его видео пропадут с сайта после нажатия «Сохранить». До сохранения можно отменить кнопкой «Вернуть».</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="hsDeleteModalOk">Удалить</button>
            </div>
        </div>
    </div>
</div>

<template id="hs-template">
    <div class="hs-item" data-item>
        <div class="hs-head">
            <button type="button" class="hs-handle" data-handle title="Перетащить" aria-label="Перетащить"><i class="fas fa-up-down"></i></button>
            <div class="hs-order" data-order></div>
            <div class="hs-title-preview" data-title-preview>Новый слайд</div>
            <span class="badge bg-danger d-none" data-delete-badge>Будет удалён после сохранения</span>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch" data-f="is_active" id="">
                <label class="form-check-label" data-active-label>Показывать</label>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm" data-remove><i class="fas fa-trash me-1"></i><span data-remove-label>Удалить</span></button>
        </div>
        <div class="hs-body">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="mb-3">
                        <label class="form-label">Название вкладки</label>
                        <input type="text" class="form-control" maxlength="60" data-f="tab_label" placeholder="Например: Категория A">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Заголовок</label>
                        <input type="text" class="form-control" maxlength="255" data-f="title" placeholder="Крупный заголовок на слайде">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Подпись</label>
                        <textarea class="form-control" rows="3" maxlength="2000" data-f="subtitle" placeholder="Короткий текст под заголовком (можно оставить пустым)"></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <label class="form-label">Текст кнопки</label>
                            <input type="text" class="form-control" maxlength="120" data-f="button_label" placeholder="Записаться">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Куда ведёт кнопка</label>
                            <select class="form-select" data-f="button_href">
                                <option value="">Без кнопки</option>
                                @foreach($linkOptions as $path => $label)
                                    <option value="{{ $path }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="hs-help" data-btn-help>Кнопка не обязательна. Она появится, если заполнены и текст, и «куда ведёт».</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="row g-2 mb-2">
                        <div class="col-sm-7">
                            <label class="form-label">Что показать рядом с текстом</label>
                            <select class="form-select" data-f="media_type">
                                @foreach($mediaTypeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div data-panel="video">
                        <div class="hs-upload mb-3" data-upload>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <div class="hs-upload-info flex-grow-1" data-info>
                                    <div class="fw-semibold" data-info-title>Видео не выбрано</div>
                                    <div class="hs-meta" data-info-meta>Перетащите файл сюда или нажмите «Выбрать видео». Любой размер: сожмём в браузере.</div>
                                </div>
                                <input type="file" accept="video/*,.mp4,.mov,.m4v,.webm,.mkv" class="d-none" data-file>
                                <button type="button" class="btn btn-primary btn-sm" data-pick><i class="fas fa-upload me-1"></i><span data-pick-label>Выбрать видео</span></button>
                                <button type="button" class="btn btn-outline-secondary btn-sm d-none" data-cancel>Отмена</button>
                            </div>
                            <div class="progress hs-progress mt-2 d-none" data-progress-wrap><div class="progress-bar progress-bar-striped progress-bar-animated" data-progress style="width:0%"></div></div>
                            <div class="alert alert-danger py-2 px-3 mt-2 mb-0 d-none" data-error></div>
                        </div>
                    </div>

                    <div data-panel="photo" class="d-none">
                        <div class="hs-upload mb-3" data-zone="photo">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <img class="hs-thumb d-none" data-z-thumb alt="">
                                <div class="hs-upload-info flex-grow-1">
                                    <div class="fw-semibold" data-z-title>Фото не выбрано</div>
                                    <div class="hs-meta" data-z-meta>Перетащите фото сюда или нажмите «Выбрать фото». Любой размер: сожмём в браузере.</div>
                                </div>
                                <input type="file" accept="image/jpeg,image/png,image/webp" class="d-none" data-z-file data-no-autoresize>
                                <button type="button" class="btn btn-primary btn-sm" data-z-pick><i class="fas fa-image me-1"></i><span data-z-pick-label>Выбрать фото</span></button>
                            </div>
                            <div class="progress hs-progress mt-2 d-none" data-z-progress-wrap><div class="progress-bar progress-bar-striped progress-bar-animated" data-z-progress style="width:0%"></div></div>
                            <div class="alert alert-danger py-2 px-3 mt-2 mb-0 d-none" data-z-error></div>
                        </div>
                    </div>

                    <div data-layout>
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6 col-xl-3">
                                <label class="form-label">Пропорции рамки</label>
                                <select class="form-select form-select-sm" data-f="frame_ratio">
                                    @foreach($ratioOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <label class="form-label">Как вписать</label>
                                <select class="form-select form-select-sm" data-f="fit_mode">
                                    @foreach($fitOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <label class="form-label">Где расположить</label>
                                <select class="form-select form-select-sm" data-f="media_side">
                                    @foreach($sideOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-xl-3">
                                <label class="form-label">Размер</label>
                                <select class="form-select form-select-sm" data-f="media_size">
                                    @foreach($sizeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="hs-help mb-3" data-side-help></div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6 col-xl-3">
                            <label class="form-label">Текст по горизонтали</label>
                            <select class="form-select form-select-sm" data-f="text_align">
                                @foreach($textAlignOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <label class="form-label">Текст по высоте</label>
                            <select class="form-select form-select-sm" data-f="text_valign">
                                @foreach($textValignOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="hs-upload mb-3" data-zone="bg">
                        <div class="fw-semibold mb-1">Фон слайда <span class="text-muted fw-normal">(необязательно)</span></div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <img class="hs-thumb d-none" data-z-thumb alt="">
                            <div class="hs-upload-info flex-grow-1">
                                <div class="fw-semibold" data-z-title>Своего фона нет</div>
                                <div class="hs-meta" data-z-meta data-default-meta>Если не выбрать: у слайда с видео фоном будет размытый кадр из видео, у остальных такой же фон, как у основной шапки.</div>
                            </div>
                            <input type="file" accept="image/jpeg,image/png,image/webp" class="d-none" data-z-file data-no-autoresize>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-z-pick><i class="fas fa-image me-1"></i><span data-z-pick-label>Выбрать фото фона</span></button>
                            <button type="button" class="btn btn-outline-secondary btn-sm d-none" data-z-remove>Убрать фон</button>
                        </div>
                        <div class="progress hs-progress mt-2 d-none" data-z-progress-wrap><div class="progress-bar progress-bar-striped progress-bar-animated" data-z-progress style="width:0%"></div></div>
                        <div class="alert alert-danger py-2 px-3 mt-2 mb-0 d-none" data-z-error></div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="form-label mb-0">Так это будет выглядеть</span>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Устройство">
                            <button type="button" class="btn btn-outline-secondary active" data-device="desktop">ПК</button>
                            <button type="button" class="btn btn-outline-secondary" data-device="mobile">Телефон</button>
                        </div>
                    </div>
                    <div class="hs-pv-box" data-pv-box><div class="hs-pv" data-pv></div></div>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@section('script')
<script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
<script src="{{ asset('adminpanel/js/video-compress.js') }}?v={{ @filemtime(public_path('adminpanel/js/video-compress.js')) }}"></script>
@php
    $heroSlidesConfig = [
        'slides' => $slides,
        'uploadUrl' => route('admin.hero-slides.upload'),
        'maxSlides' => $maxSlides,
        'maxVideoBytes' => $maxVideoBytes,
        'maxBitrateBps' => $maxBitrateBps,
        'backgroundMinRatio' => $backgroundMinRatio,
        'fallbackBackground' => $fallbackBackground,
        'uploadImageUrl' => route('admin.hero-slides.upload-image'),
        'maxImageBytes' => $maxImageBytes,
    ];
@endphp
<script>
    window.HERO_SLIDES = @json($heroSlidesConfig);
</script>
<script src="{{ asset('adminpanel/js/hero-slides.js') }}?v={{ @filemtime(public_path('adminpanel/js/hero-slides.js')) }}"></script>
@endsection
