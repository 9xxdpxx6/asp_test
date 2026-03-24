@extends('layouts.admin')

@section('title', 'Конструктор блока отзывов')

@section('style')
<style>
    .review-pool {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1rem;
        min-height: 110px;
        border: 2px dashed #d6dbe3;
        border-radius: 1rem;
        padding: 1rem;
        background: #f8fafc;
        user-select: none;
        -webkit-user-select: none;
    }

    .review-pool--home {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        border: 1px solid #d8e5df;
        background: linear-gradient(180deg, #ffffff 0%, #f7fcf9 100%);
        padding: 2rem;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.06);
    }

    .review-pool--home.review-pool--single {
        grid-template-columns: minmax(0, 980px);
        justify-content: center;
    }

    .review-pool--home.review-pool--single .review-card {
        width: 980px;
        max-width: 100%;
        margin: 0;
    }

    .review-pool--home .review-card--span-3,
    .review-pool--home .review-card--span-4,
    .review-pool--home .review-card--span-6 {
        grid-column: span 1;
    }

    .review-site-intro {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .review-site-intro__eyebrow {
        display: none;
    }

    .review-site-intro__title {
        margin: 0;
        color: #152238;
        font-size: clamp(1.9rem, 2.7vw, 3rem);
        font-weight: 500;
    }

    .review-site-intro__copy {
        margin: 0.7rem auto 0;
        max-width: 680px;
        color: #607086;
        line-height: 1.5;
    }

    .review-site-intro__line {
        width: 64px;
        height: 4px;
        margin: 1rem auto 0;
        border-radius: 999px;
        background: #2f80ed;
    }

    .review-card--span-3 {
        grid-column: span 3;
    }

    .review-card--span-4 {
        grid-column: span 4;
    }

    .review-card--span-6 {
        grid-column: span 6;
    }

    .review-pool--home .review-card {
        border-radius: 1.25rem;
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    .review-card {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
        min-height: 220px;
        border-radius: 1rem;
        border: 1px solid #dbe3ef;
        background: #fff;
        padding: 1rem;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        cursor: grab;
        user-select: none;
        -webkit-user-select: none;
    }

    body.reviews-builder--dragging {
        user-select: none !important;
        -webkit-user-select: none !important;
        cursor: grabbing !important;
    }

    .review-card--catalog {
        min-height: 260px;
    }

    .review-card--site {
        gap: 0.85rem;
        padding: 0;
        border: 0;
        box-shadow: none;
        background: transparent;
    }

    .review-card--site .review-card__preview {
        margin-top: 0;
        border-radius: 1.25rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .review-card--site .review-card__chips,
    .review-card--site .review-card__title,
    .review-card--site .review-card__description {
        display: none;
    }

    .review-card--span-3 .review-card__preview {
        min-height: 180px;
    }

    .review-card--span-4 .review-card__preview {
        min-height: 210px;
    }

    .review-card--span-6 .review-card__preview {
        min-height: 250px;
    }

    .review-pool--home .review-card--span-3 .review-card__preview {
        min-height: 220px;
    }

    .review-pool--home .review-card--span-4 .review-card__preview {
        min-height: 270px;
    }

    .review-pool--home .review-card--span-6 .review-card__preview {
        min-height: 360px;
    }

    .review-pool--home .review-card[data-provider="yandex"][data-render-type="iframe_src"] .review-card__preview--frame {
        min-height: 400px;
    }

    .review-card__remove {
        position: absolute;
        top: 0.8rem;
        right: 0.8rem;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        padding: 0;
        border: 1px solid #dbe3ef;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.95);
        color: #42536a;
        font-size: 0.92rem;
        cursor: pointer;
    }

    .review-card__remove:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    .review-card__chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        padding-right: 0.25rem;
    }

    .review-card__chip {
        border-radius: 999px;
        padding: 0.25rem 0.6rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        background: #eef2f7;
        color: #52606d;
    }

    .review-card__chip--provider-yandex {
        background: #fff4cd;
        color: #8a6200;
    }

    .review-card__chip--provider-2gis {
        background: #def6e6;
        color: #157347;
    }

    .review-card__title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.3;
        color: #152238;
    }

    .review-card__description {
        margin: 0;
        color: #5d6b82;
        font-size: 0.94rem;
        line-height: 1.45;
    }

    .review-card__preview {
        position: relative;
        margin-top: auto;
        border-radius: 1rem;
        border: 1px solid #e8edf5;
        background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
        overflow: hidden;
    }

    .review-card__preview--frame {
        height: var(--preview-height, 250px);
    }

    .review-card__preview-shell {
        width: 100%;
        height: 100%;
    }

    /* Яндекс отрисовывает ленту в расчёте на ~760px; при большей ширине iframe внутри остаётся «пустое поле».
       Для превью держим 760×height и подгоняем scale под оболочку (JS: --yandex-preview-scale). */
    .review-card__preview-shell--yandex {
        position: relative;
        overflow: hidden;
        width: 100%;
        height: 100%;
        min-width: 0;
    }

    .review-card__preview-frame--yandex {
        position: absolute;
        left: 50%;
        top: 0;
        width: 760px;
        height: var(--source-height, 824px);
        margin: 0;
        border: 0;
        display: block;
        transform: translateX(-50%) scale(var(--yandex-preview-scale, 1));
        transform-origin: top center;
        pointer-events: none;
        background: #fff;
    }

    .review-card__preview-frame:not(.review-card__preview-frame--yandex) {
        width: calc(100% / var(--preview-scale, 1));
        height: calc(var(--source-height, 260px) / var(--preview-scale, 1));
        border: 0;
        display: block;
        transform: scale(var(--preview-scale, 1));
        transform-origin: top left;
        pointer-events: none;
        background: #fff;
    }

    .review-card__preview-badge {
        display: none;
    }

    .review-card__preview-text {
        margin: 0;
        color: #22324a;
        font-size: 0.88rem;
        line-height: 1.45;
    }

    .review-card__manual {
        display: flex;
        gap: 0.9rem;
        align-items: flex-start;
        min-height: 210px;
        padding: 1rem;
    }

    .review-card__manual-mark {
        width: 54px;
        height: 54px;
        flex: 0 0 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 800;
        letter-spacing: 0.04em;
    }

    .review-card__manual--yandex .review-card__manual-mark {
        background: #ffd53d;
        color: #1f2937;
    }

    .review-card__manual--2gis .review-card__manual-mark {
        background: #16a34a;
        color: #fff;
    }

    .review-card__manual-eyebrow {
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8190a6;
        margin-bottom: 0.35rem;
    }

    .review-card__manual-title {
        margin: 0 0 0.45rem;
        color: #132238;
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.3;
    }

    .review-card__manual-copy {
        color: #5d6b82;
        line-height: 1.5;
        font-size: 0.94rem;
    }

    .review-card__manual-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 0.85rem;
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        background: #111827;
        color: #fff;
        font-size: 0.83rem;
        font-weight: 700;
    }

    .review-card__manual-button--primary {
        background: #198754;
    }

    .review-card__split {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) 124px;
        min-height: 210px;
    }

    .review-card--catalog.review-card--span-3 .review-card__split,
    .review-card--catalog.review-card--span-4 .review-card__split {
        grid-template-columns: 1fr;
    }

    .review-card--catalog.review-card--span-3 .review-card__split-side,
    .review-card--catalog.review-card--span-4 .review-card__split-side {
        border-left: 0;
        border-top: 1px solid #e8edf5;
    }

    .review-card--catalog.review-card--span-3 .review-card__manual,
    .review-card--catalog.review-card--span-4 .review-card__manual {
        min-height: 160px;
    }

    .review-card__split-side {
        border-left: 1px solid #e8edf5;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: #fff;
    }

    .review-card__qr {
        width: 104px;
        height: 104px;
        border-radius: 18px;
        border: 1px solid #e8edf5;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .review-card__qr canvas,
    .review-card__qr img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 18px;
    }

    .review-card__site-shell {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-height: 100%;
        padding: 1.5rem;
        border: 1px solid #e8edf5;
        border-radius: 1.5rem;
        background: #fff;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .review-card__site-shell--compact {
        border: 1px solid #e8edf5;
        border-radius: 1.5rem;
        padding: 1.25rem;
        background: #fff;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .review-card__site-shell--compact .review-card__site-title {
        display: none;
    }

    .review-card__site-shell--compact .review-card__site-text {
        margin-top: -0.25rem;
    }

    .review-card__site-header {
        display: flex;
        align-items: flex-start;
    }

    .review-card__site-eyebrow {
        margin: 0 0 0.5rem;
        color: #6c757d;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .review-card__site-title {
        margin: 0;
        font-size: 1.45rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.25;
    }

    .review-card__site-text {
        margin: 0;
        color: #5b6472;
        line-height: 1.55;
    }

    .review-card__site-action {
        align-self: flex-start;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 52px;
        padding: 0.75rem 1.5rem;
        border-radius: 999px;
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1;
        border: 1px solid transparent;
    }

    .review-card__site-action--primary {
        background: #0d6efd;
        color: #fff;
    }

    .review-card__site-action--dark {
        background: transparent;
        border-color: #212529;
        color: #212529;
    }

    .review-card__site-action--light {
        background: #fff;
        border-color: #d2dbe7;
        color: #22324a;
    }

    .review-card--site .review-card__split {
        grid-template-columns: minmax(0, 1fr) 160px;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid #e7ecf3;
        border-radius: 1.5rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .review-card--site .review-card__split-side {
        padding: 0;
        border-left: 0;
        background: transparent;
    }

    .review-card--site .review-card__split .review-card__manual {
        min-height: auto;
        padding: 0;
        border: 0;
        background: transparent;
    }

    .review-card--site .review-card__split .review-card__manual-copy {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .review-card--site .review-card__split .review-card__preview {
        box-shadow: none;
    }

    .review-card--site .review-card__qr {
        width: 140px;
        height: 140px;
        border-radius: 20px;
    }

    .review-card--site .review-card__preview--split {
        min-height: 0 !important;
        border: 0;
        background: transparent;
        box-shadow: none;
        overflow: visible;
    }

    .review-card__site-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.55rem;
    }

    .review-card__site-rating-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: #132238;
        line-height: 1;
    }

    .review-card__site-rating-stars {
        color: #f5b400;
        font-size: 0.9rem;
        letter-spacing: 0.08em;
        line-height: 1;
    }

    .review-card__compact-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 170px;
        gap: 1rem;
        min-height: 214px;
        padding: 1rem;
        border: 1px solid #e7ecf3;
        border-radius: 1.5rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        align-items: stretch;
    }

    .review-card__compact-panel {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 100%;
        padding: 1rem;
        border-radius: 1.25rem;
        background: #ffffff;
    }

    .review-card__compact-panel--badge {
        background: #f7f4ee;
    }

    .review-card__compact-layout--badge {
        display: block;
        padding: 0;
        border: 0;
        background: transparent;
        min-height: 0;
        gap: 0;
    }

    .review-card__compact-badge-inner {
        display: flex;
        align-items: stretch;
        gap: 1.25rem;
    }

    .review-card__compact-badge-content {
        flex: 1;
        min-width: 0;
    }

    .review-card__compact-qr-wrap {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        align-self: center;
        padding: 0.15rem;
    }

    .review-card__compact-service {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.9rem;
    }

    .review-card__compact-mark {
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 800;
    }

    .review-card__compact-mark--yandex {
        background: #ffd53d;
        color: #212121;
    }

    .review-card__compact-mark--2gis {
        background: #15a34a;
        color: #ffffff;
    }

    .review-card__compact-label {
        color: #7b8798;
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .review-card__compact-title {
        color: #132238;
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.3;
        margin-bottom: 0.75rem;
    }

    .review-card__compact-rating {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        margin-bottom: 0.45rem;
    }

    .review-card__compact-value {
        color: #132238;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }

    .review-card__compact-stars {
        color: #f5b400;
        font-size: 1rem;
        letter-spacing: 0.08em;
        line-height: 1;
    }

    .review-card__compact-count {
        color: #7b8798;
        font-size: 0.92rem;
        line-height: 1.4;
    }

    .review-card__compact-copy,
    .review-card__compact-link {
        margin-top: 0.8rem;
        color: #5c6b7f;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .review-card__compact-link {
        color: #4c63d2;
        font-weight: 600;
    }

    .placeholder-text {
        grid-column: 1 / -1;
        color: #7b8798;
        text-align: center;
        padding: 2.2rem 1rem;
    }

    .reviews-builder__rules {
        margin: 0 0 1rem;
        color: #6b778c;
        line-height: 1.5;
    }

    .reviews-builder__status {
        margin: 0 0 1rem;
        padding: 0.9rem 1rem;
        border-radius: 0.9rem;
        border: 1px solid #dbe3ef;
        background: #f8fbff;
        color: #415067;
        font-size: 0.95rem;
    }

    .reviews-builder__status--error {
        border-color: #f1b6bd;
        background: #fff5f6;
        color: #9f1d35;
    }

    .sortable-ghost {
        opacity: 0.35;
    }

    .review-card.sortable-drag {
        cursor: grabbing;
        opacity: 0.96;
        box-shadow: 0 20px 48px rgba(15, 23, 42, 0.2);
    }

    @media (max-width: 767px) {
        .review-pool {
            grid-template-columns: 1fr;
        }

        .review-card--span-3,
        .review-card--span-4,
        .review-card--span-6 {
            grid-column: span 1;
        }

        .review-card__split {
            grid-template-columns: 1fr;
        }

        .review-card__split-side {
            border-left: 0;
            border-top: 1px solid #e8edf5;
        }

        .review-card__compact-layout {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1 class="m-0">Конструктор блока отзывов</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(!$dbReady)
            <div class="alert alert-warning">
                Таблица <code>review_widgets</code> ещё не создана. После выполнения миграций и сидов изменения можно будет сохранять.
            </div>
        @endif

        @php
            $getValue = static function ($item, string $key, $default = null) {
                return data_get($item, $key, $default);
            };
        @endphp

            <div class="card">
            <div class="card-body">
                <p class="text-muted mb-4">
                    Перетаскивайте карточки между секциями. Верхняя зона показывает блок так, как он будет собираться на главной странице: в том же порядке и примерно в тех же пропорциях.
                </p>
                <p class="reviews-builder__rules">
                    Правила: на главной должен быть минимум 1 и максимум 2 виджета. Допускается максимум один виджет Яндекса и максимум один виджет 2ГИС.
                </p>
                <div id="reviews-builder-status" class="reviews-builder__status">
                    Выберите 1 или 2 виджета для главной.
                </div>

                <div class="mb-2 text-uppercase text-muted small fw-bold">На главной</div>
                <div class="review-site-intro">
                    <div class="review-site-intro__eyebrow">Предпросмотр сайта</div>
                    <h2 class="review-site-intro__title">Отзывы наших учеников</h2>
                    <div class="review-site-intro__line"></div>
                    <p class="review-site-intro__copy">Так секция выглядит на сайте: с реальным заголовком, отступами и тем же визуальным весом карточек.</p>
                </div>
                <div class="review-pool review-pool--home {{ count($onHome) <= 1 ? 'review-pool--single' : '' }}" id="home-review-widgets">
                    @foreach($onHome as $widget)
                        @php
                            $provider = $getValue($widget, 'provider');
                            $renderType = $getValue($widget, 'render_type');
                            $config = $getValue($widget, 'config', []);
                            $configJson = e(json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                            $sourceHeight = (int) data_get($config, 'height', data_get($config, 'badge_height', 240));
                            $spanClass = match (true) {
                                $renderType === '2gis_constructor', $sourceHeight >= 680 => 'review-card--span-6',
                                in_array($renderType, ['manual', 'qr', 'badge_qr'], true), data_get($config, 'size') === 'medium', $sourceHeight >= 280 => 'review-card--span-4',
                                default => 'review-card--span-3',
                            };
                            $previewHeight = match (true) {
                                $renderType === '2gis_constructor', $sourceHeight >= 680 => min(max((int) round($sourceHeight * 0.48), 340), 440),
                                $renderType === 'iframe_src' && $provider === 'yandex' && $sourceHeight >= 680 => min(max((int) round($sourceHeight * 0.5), 360), 460),
                                in_array($renderType, ['manual', 'qr', 'badge_qr'], true) => 240,
                                data_get($config, 'size') === 'medium', $sourceHeight >= 280 => min(max((int) round($sourceHeight * 0.74), 250), 330),
                                default => max($sourceHeight, 210),
                            };
                            $previewScale = $sourceHeight <= 240 ? 1 : round($previewHeight / max($sourceHeight, 1), 4);
                        @endphp
                        <div class="review-card review-card--site {{ $spanClass }}"
                             data-id="{{ $getValue($widget, 'id') }}"
                             data-provider="{{ $provider }}"
                             data-render-type="{{ $renderType }}"
                             data-title="{{ e($getValue($widget, 'title')) }}"
                             data-description="{{ e($getValue($widget, 'description', '')) }}"
                             data-config="{{ $configJson }}">
                            <button type="button" class="review-card__remove" data-move-to-catalog aria-label="Переместить в витрину" title="Переместить в витрину">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <article class="review-card__site-shell {{ in_array($renderType, ['manual', 'qr', 'badge_qr'], true) ? 'review-card__site-shell--compact' : '' }}">
                                @if($renderType === 'iframe_src' && $provider === 'yandex')
                                    <div class="review-card__preview review-card__preview--frame" style="--source-height: {{ $sourceHeight }}px; --preview-height: {{ $previewHeight }}px;">
                                        <div class="review-card__preview-badge"><i class="fas fa-eye"></i>Превью</div>
                                        <div class="review-card__preview-shell review-card__preview-shell--yandex">
                                            <iframe src="{{ data_get($config, 'src') }}" loading="lazy" title="{{ $getValue($widget, 'title') }}" class="review-card__preview-frame review-card__preview-frame--yandex" data-yandex-reviews-preview data-src-height="{{ $sourceHeight }}"></iframe>
                                        </div>
                                    </div>
                                @elseif($renderType === 'iframe_src')
                                    <div class="review-card__preview review-card__preview--frame" style="--source-height: {{ $sourceHeight }}px; --preview-height: {{ $previewHeight }}px; --preview-scale: {{ $previewScale }};">
                                        <div class="review-card__preview-badge"><i class="fas fa-eye"></i>Превью</div>
                                        <div class="review-card__preview-shell">
                                            <iframe src="{{ data_get($config, 'src') }}" loading="lazy" title="{{ $getValue($widget, 'title') }}" class="review-card__preview-frame"></iframe>
                                        </div>
                                    </div>
                                @elseif($renderType === '2gis_constructor')
                                    <div class="review-card__preview review-card__preview--frame" style="--source-height: {{ $sourceHeight }}px; --preview-height: {{ $previewHeight }}px; --preview-scale: {{ $previewScale }};">
                                        <div class="review-card__preview-badge"><i class="fas fa-eye"></i>Превью</div>
                                        <div class="review-card__preview-shell">
                                            <iframe title="{{ $getValue($widget, 'title') }}" class="review-card__preview-frame" data-2gis-preview data-config="{{ $configJson }}"></iframe>
                                        </div>
                                    </div>
                                @elseif($renderType === 'manual')
                                    <div class="review-card__preview">
                                        <div class="review-card__manual review-card__manual--{{ $provider }}">
                                            <div class="review-card__manual-mark">{{ $provider === 'yandex' ? 'Я' : '2ГИС' }}</div>
                                            <div>
                                                <div class="review-card__site-rating">
                                                    <span class="review-card__site-rating-value">{{ data_get($config, 'rating_value', '5.0') }}</span>
                                                    <span class="review-card__site-rating-stars">★★★★★</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($renderType === 'qr')
                                    <div class="review-card__preview review-card__preview--split">
                                        <div class="review-card__compact-layout">
                                            <div class="review-card__compact-panel">
                                                <div class="review-card__compact-service">
                                                    <span class="review-card__compact-mark review-card__compact-mark--{{ $provider }}">{{ $provider === 'yandex' ? 'Я' : '2ГИС' }}</span>
                                                    <span class="review-card__compact-label">{{ data_get($config, 'service_label', $provider === 'yandex' ? 'Яндекс Карты' : '2ГИС') }}</span>
                                                </div>
                                                <div class="review-card__compact-rating">
                                                    <span class="review-card__compact-value">{{ data_get($config, 'rating_value', '5.0') }}</span>
                                                    <span class="review-card__compact-stars">★★★★★</span>
                                                </div>
                                                <div class="review-card__compact-count">{{ data_get($config, 'review_count_text', $provider === 'yandex' ? '55 отзывов' : '32 отзыва') }}</div>
                                            </div>
                                            <div class="review-card__split-side">
                                                <div class="review-card__qr" data-qr-url="{{ data_get($config, 'qr_url', data_get($config, 'button_url')) }}"></div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($renderType === 'badge_qr')
                                    <div class="review-card__preview">
                                        <div class="review-card__compact-layout review-card__compact-layout--badge">
                                            <div class="review-card__compact-panel review-card__compact-panel--badge">
                                                <div class="review-card__compact-badge-inner">
                                                    <div class="review-card__compact-badge-content">
                                                        <div class="review-card__compact-service">
                                                            <span class="review-card__compact-mark review-card__compact-mark--{{ $provider }}">{{ $provider === 'yandex' ? 'Я' : '2ГИС' }}</span>
                                                            <span class="review-card__compact-label">{{ data_get($config, 'service_label', $provider === 'yandex' ? 'Яндекс Карты' : '2ГИС') }}</span>
                                                        </div>
                                                        <div class="review-card__compact-rating">
                                                            <span class="review-card__compact-value">{{ data_get($config, 'rating_value', '5.0') }}</span>
                                                            <span class="review-card__compact-stars">★★★★★</span>
                                                        </div>
                                                        <div class="review-card__compact-count">{{ data_get($config, 'review_count_text', $provider === 'yandex' ? '55 отзывов' : '32 отзыва') }}</div>
                                                        <div class="review-card__compact-link">{{ data_get($config, 'compact_link_text', $provider === 'yandex' ? 'Посмотреть и оставить отзыв на Яндекс Картах' : 'Посмотреть и оставить отзыв в 2ГИС') }}</div>
                                                    </div>
                                                    <div class="review-card__compact-qr-wrap">
                                                        <div class="review-card__qr" data-qr-url="{{ data_get($config, 'qr_url', data_get($config, 'button_url')) }}"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(data_get($config, 'button_url'))
                                    <div class="review-card__site-action review-card__site-action--{{ data_get($config, 'button_variant', 'light') }}">{{ data_get($config, 'button_text', 'Оставить отзыв') }}</div>
                                @endif
                            </article>
                        </div>
                    @endforeach
                </div>
                <div id="home-review-widgets-placeholder" class="placeholder-text" style="{{ count($onHome) > 0 ? 'display:none' : '' }}">
                    Перетащите сюда 1 или 2 варианта из каталога ниже
                </div>

                <div class="mt-4 mb-2 text-uppercase text-muted small fw-bold">Каталог вариантов</div>
                <div class="review-pool" id="catalog-review-widgets">
                    @foreach($all as $widget)
                        @continue((bool) $getValue($widget, 'show_on_home', false))

                        @php
                            $provider = $getValue($widget, 'provider');
                            $renderType = $getValue($widget, 'render_type');
                            $config = $getValue($widget, 'config', []);
                            $configJson = e(json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                            $sourceHeight = (int) data_get($config, 'height', data_get($config, 'badge_height', 240));
                            $spanClass = match (true) {
                                $renderType === '2gis_constructor', $sourceHeight >= 680 => 'review-card--span-6',
                                in_array($renderType, ['manual', 'qr', 'badge_qr'], true), data_get($config, 'size') === 'medium', $sourceHeight >= 280 => 'review-card--span-4',
                                default => 'review-card--span-3',
                            };
                            $previewHeight = match (true) {
                                $renderType === '2gis_constructor', $sourceHeight >= 680 => min(max((int) round($sourceHeight * 0.34), 210), 270),
                                $renderType === 'iframe_src' && $provider === 'yandex' && $sourceHeight >= 680 => min(max((int) round($sourceHeight * 0.34), 210), 270),
                                in_array($renderType, ['manual', 'qr', 'badge_qr'], true) => 210,
                                data_get($config, 'size') === 'medium', $sourceHeight >= 280 => min(max((int) round($sourceHeight * 0.56), 200), 260),
                                default => max($sourceHeight, 170),
                            };
                            $previewScale = $sourceHeight <= 240 ? 1 : round($previewHeight / max($sourceHeight, 1), 4);
                        @endphp
                        <div class="review-card review-card--catalog {{ $spanClass }}"
                             data-id="{{ $getValue($widget, 'id') }}"
                             data-provider="{{ $provider }}"
                             data-render-type="{{ $renderType }}"
                             data-title="{{ e($getValue($widget, 'title')) }}"
                             data-description="{{ e($getValue($widget, 'description', '')) }}"
                             data-config="{{ $configJson }}">
                            <div class="review-card__chips">
                                <span class="review-card__chip review-card__chip--provider-{{ $provider }}">{{ strtoupper($provider) }}</span>
                                <span class="review-card__chip">{{ $renderType }}</span>
                            </div>
                            @if($renderType === 'iframe_src' && $provider === 'yandex')
                                <div class="review-card__preview review-card__preview--frame" style="--source-height: {{ $sourceHeight }}px; --preview-height: {{ $previewHeight }}px;">
                                    <div class="review-card__preview-badge"><i class="fas fa-eye"></i>Превью</div>
                                    <div class="review-card__preview-shell review-card__preview-shell--yandex">
                                        <iframe src="{{ data_get($config, 'src') }}" loading="lazy" title="{{ $getValue($widget, 'title') }}" class="review-card__preview-frame review-card__preview-frame--yandex" data-yandex-reviews-preview data-src-height="{{ $sourceHeight }}"></iframe>
                                    </div>
                                </div>
                            @elseif($renderType === 'iframe_src')
                                <div class="review-card__preview review-card__preview--frame" style="--source-height: {{ $sourceHeight }}px; --preview-height: {{ $previewHeight }}px; --preview-scale: {{ $previewScale }};">
                                    <div class="review-card__preview-badge"><i class="fas fa-eye"></i>Превью</div>
                                    <div class="review-card__preview-shell">
                                        <iframe src="{{ data_get($config, 'src') }}" loading="lazy" title="{{ $getValue($widget, 'title') }}" class="review-card__preview-frame"></iframe>
                                    </div>
                                </div>
                            @elseif($renderType === '2gis_constructor')
                                <div class="review-card__preview review-card__preview--frame" style="--source-height: {{ $sourceHeight }}px; --preview-height: {{ $previewHeight }}px; --preview-scale: {{ $previewScale }};">
                                    <div class="review-card__preview-badge"><i class="fas fa-eye"></i>Превью</div>
                                    <div class="review-card__preview-shell">
                                        <iframe title="{{ $getValue($widget, 'title') }}" class="review-card__preview-frame" data-2gis-preview data-config="{{ $configJson }}"></iframe>
                                    </div>
                                </div>
                            @elseif($renderType === 'manual')
                                <div class="review-card__preview">
                                    <div class="review-card__manual review-card__manual--{{ $provider }}">
                                        <div class="review-card__manual-mark">{{ $provider === 'yandex' ? 'Я' : '2ГИС' }}</div>
                                        <div>
                                            <div class="review-card__site-rating">
                                                <span class="review-card__site-rating-value">{{ data_get($config, 'rating_value', '5.0') }}</span>
                                                <span class="review-card__site-rating-stars">★★★★★</span>
                                            </div>
                                            <div class="review-card__manual-button {{ data_get($config, 'button_variant') === 'primary' ? 'review-card__manual-button--primary' : '' }}">{{ data_get($config, 'button_text', 'Открыть отзывы') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($renderType === 'qr')
                                <div class="review-card__preview">
                                    <div class="review-card__compact-layout">
                                        <div class="review-card__compact-panel">
                                            <div class="review-card__compact-service">
                                                <span class="review-card__compact-mark review-card__compact-mark--{{ $provider }}">{{ $provider === 'yandex' ? 'Я' : '2ГИС' }}</span>
                                                <span class="review-card__compact-label">{{ data_get($config, 'service_label', $provider === 'yandex' ? 'Яндекс Карты' : '2ГИС') }}</span>
                                            </div>
                                            <div class="review-card__compact-rating">
                                                <span class="review-card__compact-value">{{ data_get($config, 'rating_value', '5.0') }}</span>
                                                <span class="review-card__compact-stars">★★★★★</span>
                                            </div>
                                            <div class="review-card__compact-count">{{ data_get($config, 'review_count_text', $provider === 'yandex' ? '55 отзывов' : '32 отзыва') }}</div>
                                        </div>
                                        <div class="review-card__split-side">
                                            <div class="review-card__qr" data-qr-url="{{ data_get($config, 'qr_url', data_get($config, 'button_url')) }}"></div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($renderType === 'badge_qr')
                                <div class="review-card__preview">
                                    <div class="review-card__compact-layout review-card__compact-layout--badge">
                                        <div class="review-card__compact-panel review-card__compact-panel--badge">
                                            <div class="review-card__compact-badge-inner">
                                                <div class="review-card__compact-badge-content">
                                                    <div class="review-card__compact-service">
                                                        <span class="review-card__compact-mark review-card__compact-mark--{{ $provider }}">{{ $provider === 'yandex' ? 'Я' : '2ГИС' }}</span>
                                                        <span class="review-card__compact-label">{{ data_get($config, 'service_label', $provider === 'yandex' ? 'Яндекс Карты' : '2ГИС') }}</span>
                                                    </div>
                                                    <div class="review-card__compact-rating">
                                                        <span class="review-card__compact-value">{{ data_get($config, 'rating_value', '5.0') }}</span>
                                                        <span class="review-card__compact-stars">★★★★★</span>
                                                    </div>
                                                    <div class="review-card__compact-count">{{ data_get($config, 'review_count_text', $provider === 'yandex' ? '55 отзывов' : '32 отзыва') }}</div>
                                                    <div class="review-card__compact-link">{{ data_get($config, 'compact_link_text', $provider === 'yandex' ? 'Посмотреть и оставить отзыв на Яндекс Картах' : 'Посмотреть и оставить отзыв в 2ГИС') }}</div>
                                                </div>
                                                <div class="review-card__compact-qr-wrap">
                                                    <div class="review-card__qr" data-qr-url="{{ data_get($config, 'qr_url', data_get($config, 'button_url')) }}"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.reviews-builder.update') }}" class="mt-4" id="reviews-builder-form">
                    @csrf
                    <div id="hidden-review-widget-inputs"></div>
                    <button type="submit" class="btn btn-success btn-lg" {{ $dbReady ? '' : 'disabled' }}>
                        Сохранить порядок на главной
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const homePool = document.getElementById('home-review-widgets');
    const catalogPool = document.getElementById('catalog-review-widgets');
    const placeholder = document.getElementById('home-review-widgets-placeholder');
    const hiddenInputs = document.getElementById('hidden-review-widget-inputs');
    const statusBox = document.getElementById('reviews-builder-status');
    const form = document.getElementById('reviews-builder-form');
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;

    function getHomeCards() {
        return Array.from(homePool.querySelectorAll('.review-card'));
    }

    function getHomeProviderCounts(excludeCard = null) {
        return getHomeCards().reduce((acc, card) => {
            if (excludeCard && card === excludeCard) {
                return acc;
            }

            const provider = card.dataset.provider;
            if (!provider) {
                return acc;
            }

            acc[provider] = (acc[provider] || 0) + 1;
            return acc;
        }, {});
    }

    function getHomeValidation() {
        const cards = getHomeCards();
        const providerCounts = getHomeProviderCounts();

        if (cards.length < 1) {
            return { valid: false, message: 'На главной должен быть хотя бы один виджет.' };
        }

        if (cards.length > 2) {
            return { valid: false, message: 'На главной можно оставить не более двух виджетов.' };
        }

        if ((providerCounts.yandex || 0) > 1 || (providerCounts['2gis'] || 0) > 1) {
            return { valid: false, message: 'Можно выбрать максимум один виджет Яндекса и максимум один виджет 2ГИС.' };
        }

        if (cards.length === 1) {
            const provider = cards[0]?.dataset.provider === 'yandex' ? 'Яндекс' : '2ГИС';
            return { valid: true, message: `На главной сейчас один виджет: ${provider}. Можно добавить ещё один от другого сервиса.` };
        }

        return { valid: true, message: 'На главной два виджета: по одному от Яндекса и 2ГИС.' };
    }

    function updatePlaceholder() {
        placeholder.style.display = homePool.querySelectorAll('.review-card').length === 0 ? '' : 'none';
    }

    function updateHomeLayout() {
        const count = getHomeCards().length;
        homePool.classList.toggle('review-pool--single', count <= 1);
        homePool.style.gridTemplateColumns = count <= 1 ? 'minmax(0, 980px)' : 'repeat(2, minmax(0, 1fr))';
        homePool.style.justifyContent = count <= 1 ? 'center' : '';
    }

    function updateStatus() {
        if (!statusBox) {
            return;
        }

        const state = getHomeValidation();
        statusBox.textContent = state.message;
        statusBox.classList.toggle('reviews-builder__status--error', !state.valid);

        if (submitButton) {
            submitButton.disabled = !state.valid;
        }
    }

    function updateNumbers() {
        const homeCards = homePool.querySelectorAll('.review-card');

        homeCards.forEach((card) => {
            let removeButton = card.querySelector('.review-card__remove');
            if (!removeButton) {
                removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'review-card__remove';
                removeButton.dataset.moveToCatalog = '1';
                removeButton.setAttribute('aria-label', 'Переместить в витрину');
                removeButton.setAttribute('title', 'Переместить в витрину');
                removeButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
                card.prepend(removeButton);
            }

            removeButton.disabled = homeCards.length <= 1;
        });

        catalogPool.querySelectorAll('.review-card').forEach((card) => {
            const removeButton = card.querySelector('.review-card__remove');
            if (removeButton) {
                removeButton.remove();
            }
        });
    }

    function syncHiddenInputs() {
        hiddenInputs.innerHTML = '';
        homePool.querySelectorAll('.review-card').forEach((card) => {
            if (!card.dataset.id) return;
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'widget_ids[]';
            input.value = card.dataset.id;
            hiddenInputs.appendChild(input);
        });
    }

    function getTwoGisConstructorHtml(config) {
        const size = config.size || 'big';
        const theme = config.theme || 'light';
        const branchId = config.branch_id || '';
        const orgId = config.org_id || '70000001046117787';

        return `
            <head>
                <meta charset="UTF-8">
                <style>html,body{margin:0;padding:0;background:#fff;}#iframe{min-height:100vh;}</style>
                <script type="text/javascript">
                    window.__size__=${JSON.stringify(size)};
                    window.__theme__=${JSON.stringify(theme)};
                    window.__branchId__=${JSON.stringify(branchId)};
                    window.__orgId__=${JSON.stringify(orgId)};
                <\/script>
                <script crossorigin="anonymous" type="module" src="https://disk.2gis.com/widget-constructor/assets/iframe.js"><\/script>
                <link rel="modulepreload" crossorigin="anonymous" href="https://disk.2gis.com/widget-constructor/assets/defaults.js">
                <link rel="stylesheet" crossorigin="anonymous" href="https://disk.2gis.com/widget-constructor/assets/defaults.css">
            </head>
            <body><div id="iframe"></div></body>
        `;
    }

    const YANDEX_REVIEWS_WIDGET_WIDTH = 760;

    function layoutYandexReviewPreviews(scope) {
        const root = scope || document;
        root.querySelectorAll('[data-yandex-reviews-preview]').forEach((frame) => {
            if (frame.dataset.yandexLoadBound !== '1') {
                frame.dataset.yandexLoadBound = '1';
                frame.addEventListener('load', function() {
                    layoutYandexReviewPreviews(document);
                });
            }
            const shell = frame.closest('.review-card__preview-shell--yandex');
            if (!shell) {
                return;
            }
            const srcH = parseFloat(frame.dataset.srcHeight || '824') || 824;
            const w = shell.clientWidth;
            const h = shell.clientHeight;
            if (w < 1 || h < 1) {
                return;
            }
            const scale = Math.min(w / YANDEX_REVIEWS_WIDGET_WIDTH, h / srcH);
            shell.style.setProperty('--yandex-preview-scale', String(scale));
        });
    }

    function observeYandexPreviewShells(scope) {
        if (typeof ResizeObserver === 'undefined') {
            return;
        }
        const root = scope || document;
        root.querySelectorAll('.review-card__preview-shell--yandex').forEach((shell) => {
            if (shell.dataset.yandexResizeObserved === '1') {
                return;
            }
            shell.dataset.yandexResizeObserved = '1';
            const ro = new ResizeObserver(function() {
                layoutYandexReviewPreviews(shell);
            });
            ro.observe(shell);
        });
    }

    function debounceReviewsBuilder(fn, ms) {
        let t;
        return function() {
            clearTimeout(t);
            t = setTimeout(fn, ms);
        };
    }

    function mountTwoGisPreviews(scope) {
        scope.querySelectorAll('[data-2gis-preview]').forEach((frame) => {
            let config = {};

            try {
                config = JSON.parse(frame.dataset.config || '{}');
            } catch (error) {
                config = {};
            }

            const marker = JSON.stringify(config);
            if (frame.dataset.loaded === marker) {
                return;
            }

            if (!frame.contentWindow || !frame.contentWindow.document) {
                return;
            }

            try {
                const doc = frame.contentWindow.document;
                doc.open();
                doc.write(getTwoGisConstructorHtml(config));
                doc.close();
                frame.dataset.loaded = marker;
            } catch (error) {
                console.error('Не удалось смонтировать preview 2ГИС:', error);
            }
        });
    }

    function mountQrPreviews(scope) {
        if (typeof QRCode === 'undefined') {
            return;
        }

        scope.querySelectorAll('[data-qr-url]').forEach((target) => {
            const value = target.dataset.qrUrl;
            if (!value || target.dataset.loaded === value) {
                return;
            }

            target.innerHTML = '';

            QRCode.toCanvas(value, {
                width: 104,
                margin: 1,
                color: {
                    dark: '#111827',
                    light: '#ffffff',
                },
            }, function(error, canvas) {
                if (error) {
                    console.error('Не удалось сгенерировать QR preview:', error);
                    return;
                }

                target.appendChild(canvas);
                target.dataset.loaded = value;
            });
        });
    }

    function refresh() {
        updatePlaceholder();
        updateHomeLayout();
        updateNumbers();
        updateStatus();
        syncHiddenInputs();
        mountTwoGisPreviews(document);
        mountQrPreviews(document);
        requestAnimationFrame(function() {
            layoutYandexReviewPreviews(document);
            observeYandexPreviewShells(document);
        });
    }

    function canMoveToHome(event) {
        const dragged = event.dragged;
        const fromHome = event.from === homePool;
        const toHome = event.to === homePool;
        const toCatalog = event.to === catalogPool;

        if (fromHome && toCatalog && getHomeCards().length <= 1) {
            return false;
        }

        if (!toHome) {
            return true;
        }

        const incomingProvider = dragged?.dataset?.provider;
        const providerCounts = getHomeProviderCounts(fromHome ? dragged : null);
        const nextCount = fromHome ? getHomeCards().length : getHomeCards().length + 1;

        if (nextCount > 2) {
            return false;
        }

        if (incomingProvider && (providerCounts[incomingProvider] || 0) >= 1) {
            return false;
        }

        return true;
    }

    function setReviewsBuilderDragging(active) {
        document.body.classList.toggle('reviews-builder--dragging', active);
    }

    const reviewSortableOpts = {
        group: 'review-widgets',
        animation: 150,
        forceFallback: true,
        fallbackOnBody: true,
        fallbackTolerance: 5,
        filter: 'button, .review-card__remove',
        preventOnFilter: true,
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        onMove: canMoveToHome,
        onStart: function() {
            setReviewsBuilderDragging(true);
        },
        onEnd: function() {
            setReviewsBuilderDragging(false);
        },
        onAdd: refresh,
        onRemove: refresh,
        onSort: refresh,
    };

    Sortable.create(homePool, reviewSortableOpts);
    Sortable.create(catalogPool, reviewSortableOpts);

    window.addEventListener('resize', debounceReviewsBuilder(function() {
        layoutYandexReviewPreviews(document);
    }, 120));

    if (form) {
        form.addEventListener('submit', function(event) {
            const state = getHomeValidation();
            if (state.valid) {
                return;
            }

            event.preventDefault();
            updateStatus();
        });
    }

    homePool.addEventListener('click', function(event) {
        const button = event.target.closest('[data-move-to-catalog]');
        if (!button) {
            return;
        }

        const card = button.closest('.review-card');
        if (!card || getHomeCards().length <= 1) {
            updateStatus();
            return;
        }

        catalogPool.prepend(card);
        refresh();
    });

    refresh();
});
</script>
@endsection
