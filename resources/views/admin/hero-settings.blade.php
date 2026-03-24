@extends('layouts.admin')

@section('title', 'Главная шапка сайта')

@section('style')
<style>
    .hero-admin-hero {
        background: linear-gradient(135deg, #f8fafc 0%, #e8f4fc 100%);
        border-radius: 0.75rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    .hero-admin-hero h1.page-title-soft {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0 0 0.35rem 0;
    }
    .hero-admin-hero p.lead-soft {
        margin: 0;
        color: #64748b;
        font-size: 0.95rem;
        max-width: 42rem;
        line-height: 1.45;
    }
    .hero-constructor-card {
        border: none;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.25rem;
    }
    .hero-constructor-card .card-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 600;
        color: #334155;
        padding: 0.85rem 1.25rem;
    }
    .hero-constructor-card .card-body {
        padding: 1.25rem;
    }
    .hero-live-wrap {
        position: sticky;
        top: 0.75rem;
    }
    .hero-live-preview {
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #0f172a;
        min-height: 280px;
        display: flex;
        flex-direction: column;
    }
    .hero-live-preview .hero-live-bg {
        flex: 1;
        background-size: cover;
        background-position: center;
        padding: 1.5rem 1.25rem 1rem;
        min-height: 200px;
        position: relative;
    }
    .hero-live-preview .hero-live-bg::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(15,23,42,0.55), rgba(15,23,42,0.75));
    }
    .hero-live-preview .hero-live-inner {
        position: relative;
        z-index: 1;
        text-align: center;
        color: #fff;
    }
    .hero-live-preview #pv-title {
        font-size: 1.15rem;
        font-weight: 700;
        margin: 0 0 0.5rem;
        line-height: 1.25;
    }
    .hero-live-preview #pv-subtitle {
        font-size: 0.8rem;
        opacity: 0.92;
        margin: 0 0 1rem;
        line-height: 1.35;
        white-space: pre-wrap;
    }
    .hero-live-btns {
        gap: 0.5rem;
        margin-bottom: 0.25rem;
        flex-wrap: wrap;
        width: 100%;
    }
    .hero-live-btns.hero-live-btns--slots {
        align-items: stretch;
    }
    .hero-live-btns.hero-live-btns--slots .hero-live-cta-slot {
        flex: 1 1 0;
        min-width: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .hero-live-btns:not(.hero-live-btns--slots) .hero-live-cta-slot {
        flex: 0 1 auto;
        min-width: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .hero-live-btns.hero-live-btns--slots .hero-live-cta-slot .pv-pill {
        max-width: 100%;
    }
    .hero-live-btns .pv-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.35);
        background: rgba(255,255,255,0.12);
    }
    .hero-live-btns .pv-pill .hero-preview-btn-icon {
        font-size: 0.85rem;
        opacity: 0.95;
    }
    .hero-live-btns .pv-pill.pv-solid {
        background: #fff;
        color: #0f172a;
        border-color: #fff;
    }
    .hero-live-trust {
        background: #fff;
        padding: 0.65rem 0.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem 0.75rem;
        justify-content: center;
        align-items: center;
        font-size: 0.7rem;
        color: #475569;
        border-top: 1px solid #e2e8f0;
    }
    .hero-live-trust .trust-mock {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .hero-live-trust .trust-mock i { color: #0d6efd; font-size: 0.85rem; }
    .photo-upload-area {
        border: 2px dashed #ced4da;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fafafa;
        position: relative;
        overflow: hidden;
    }
    .photo-upload-area:hover {
        border-color: #0d6efd;
        background: #f0f7ff;
    }
    .photo-upload-area.drag-over {
        border-color: #0d6efd;
        background: #e8f2ff;
    }
    .photo-upload-area.has-image {
        padding: 0;
        border-style: solid;
    }
    .photo-upload-area .upload-placeholder { color: #6c757d; }
    .photo-upload-area .upload-placeholder i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    .photo-preview-container {
        width: 100%;
        max-width: 220px;
        height: 160px;
        overflow: hidden;
        border-radius: 0.5rem;
        margin: 0 auto;
    }
    .photo-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .photo-upload-area .change-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.6);
        color: #fff;
        padding: 0.5rem;
        font-size: 0.85rem;
        display: none;
    }
    .photo-upload-area.has-image:hover .change-overlay { display: block; }
    .btn-saving-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: heroBtnSpin 0.7s linear infinite;
        vertical-align: -0.1rem;
    }
    @keyframes heroBtnSpin { to { transform: rotate(360deg); } }
    #trust-items-list { margin: 0; padding: 0; }
    .hero-trust-item {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        background: #fff;
        margin-bottom: 0.5rem;
        transition: box-shadow 0.15s;
        cursor: grab;
    }
    .hero-trust-item:hover { box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06); }
    .hero-trust-item:active { cursor: grabbing; }
    .hero-trust-ghost { opacity: 0.45; }
    li.hero-trust-sortable-placeholder {
        visibility: visible !important;
        border: 2px dashed #0d6efd;
        background: #f0f7ff;
        min-height: 3.5rem;
        border-radius: 0.5rem;
        box-sizing: border-box;
        list-style: none;
    }
    .hero-trust-handle {
        cursor: grab;
        padding: 0.35rem 0.45rem;
        user-select: none;
        touch-action: none;
        flex-shrink: 0;
    }
    .hero-trust-handle i {
        pointer-events: none;
    }
    .hero-trust-handle:active { cursor: grabbing; }
    .hero-cta-item { transition: box-shadow 0.15s; }
    .hero-cta-item:hover { box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06); }
    .hero-cta-ghost { opacity: 0.45; }
    li.hero-cta-sortable-placeholder {
        visibility: visible !important;
        border: 2px dashed #0d6efd;
        background: #f0f7ff;
        min-height: 5rem;
        border-radius: 0.5rem;
        box-sizing: border-box;
        list-style: none;
    }
    .hero-cta-handle {
        cursor: grab;
        padding: 0.35rem 0.45rem;
        user-select: none;
        touch-action: none;
        flex-shrink: 0;
    }
    .hero-cta-handle i { pointer-events: none; }
    .hero-cta-handle:active { cursor: grabbing; }
    .hero-icon-field .hero-icon-btn {
        min-width: 2.5rem;
        padding: 0.35rem 0.5rem;
    }
    .hero-icon-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(48px, 1fr));
        gap: 4px;
        max-height: 400px;
        overflow-y: auto;
        padding: 0.25rem;
    }
    .hero-icon-picker-item {
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
    .hero-icon-picker-item:hover {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    .hero-icon-picker-item.selected {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    .form-hint { font-size: 0.8rem; color: #64748b; }
    #heroPhotoError { display: none; }
</style>
@endsection

@section('content')
@php
    $bgUrl = $hero->background_image_path
        ? \Illuminate\Support\Facades\Storage::url($hero->background_image_path)
        : '';
    $trustRows = old('trust_items', $hero->trust_items ?? []);
    if (!is_array($trustRows)) {
        $trustRows = [];
    }
    $trustRows = array_values(array_filter($trustRows, fn ($r) => is_array($r)));
    if (count($trustRows) === 0) {
        $trustRows = [['icon' => 'fas fa-check', 'value' => '', 'label' => '']];
    }
@endphp

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-12">
                <div class="hero-admin-hero">
                    <h1 class="page-title-soft">Главная шапка сайта</h1>
                    <p class="lead-soft">Здесь настраивается большой баннер вверху главной страницы: фото, заголовок, кнопки и полоска с иконками под баннером. Меняйте по шагам — справа видно упрощённый макет.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.hero-settings.update') }}" enctype="multipart/form-data" id="hero-settings-form">
            @csrf

            <div class="row">
                <div class="col-xl-5 col-lg-12 mb-4">
                        @php
                            $pvDir = old('buttons_direction', $hero->buttons_direction ?? 'row');
                            $pvAlignRaw = old('buttons_align', $hero->buttons_align ?? 'center');
                            $pvAlign = $pvAlignRaw === 'around' ? 'between' : $pvAlignRaw;
                            $pvJustifyRow = [
                                'center' => 'justify-content-center',
                                'start' => 'justify-content-start',
                                'end' => 'justify-content-end',
                                'between' => 'justify-content-between',
                            ];
                            $pvAlignCross = [
                                'center' => 'align-items-center',
                                'start' => 'align-items-start',
                                'end' => 'align-items-end',
                                'between' => 'align-items-center',
                            ];
                            $pvHeroLiveBtnsClass = 'hero-live-btns d-flex';
                            if ($pvDir !== 'column') {
                                $pvHeroLiveBtnsClass .= ($pvDir === 'row' && $pvAlign === 'between') ? ' hero-live-btns--slots' : '';
                                $pvHeroLiveBtnsClass .= ' '.($pvJustifyRow[$pvAlign] ?? $pvJustifyRow['center']).' flex-row align-items-stretch';
                            } elseif ($pvAlign === 'between') {
                                $pvHeroLiveBtnsClass .= ' flex-column '.$pvAlignCross['between'].' justify-content-between';
                            } else {
                                $pvHeroLiveBtnsClass .= ' flex-column '.($pvAlignCross[$pvAlign] ?? 'align-items-center').' justify-content-start';
                            }
                        @endphp
                        <div class="hero-live-wrap">
                    <p class="small text-muted mb-2 fw-semibold text-uppercase letter-spacing">Как будет на сайте (схема)</p>
                        <div class="hero-live-preview" id="hero-live-preview">
                            <div class="hero-live-bg" id="hero-live-bg" style="@if($bgUrl)background-image:url('{{ $bgUrl }}');@endif">
                                    <div class="hero-live-inner">
                                    <h2 id="pv-title">{{ old('title', $hero->title) ?: 'Заголовок' }}</h2>
                                    <p id="pv-subtitle" class="small">{{ old('subtitle', $hero->subtitle) }}</p>
                                    <div class="{{ $pvHeroLiveBtnsClass }}" id="hero-live-btns">
                                        @foreach($ctaRows as $idx => $cta)
                                            @php $pvSolid = ($cta['variant'] ?? (($idx % 2 === 0) ? 'light' : 'outline-light')) === 'light'; @endphp
                                            <span class="hero-live-cta-slot" data-pv-slot>
                                                <span class="pv-pill {{ $pvSolid ? 'pv-solid' : '' }}" data-pv-cta>
                                                    <i class="{{ $cta['icon'] }} hero-preview-btn-icon" aria-hidden="true"></i>
                                                    <span class="pv-btn-text">{{ $cta['label'] ?: 'Кнопка' }}</span>
                                                </span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="hero-live-trust" id="pv-trust-strip" aria-hidden="true"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7 col-lg-12">
                    <div class="card hero-constructor-card">
                        <div class="card-header">1. Фон баннера</div>
                        <div class="card-body">
                            <p class="form-hint mb-3">Загрузите красивое фото — оно показывается за текстом. Можно перетащить файл в рамку или нажать и выбрать с компьютера.</p>
                            <div class="row align-items-start">
                                <div class="col-md-6">
                                    <div class="photo-upload-area {{ $bgUrl ? 'has-image' : '' }}" id="heroPhotoUploadArea" onclick="document.getElementById('heroImageInput').click()">
                                        <div class="upload-placeholder" id="heroUploadPlaceholder" {!! $bgUrl ? 'style="display:none"' : '' !!}>
                                            <i class="fas fa-camera"></i>
                                            <div>Нажмите или перетащите фото сюда</div>
                                            <small class="text-muted">JPG, PNG или WebP, до 8 МБ</small>
                                        </div>
                                        <div class="photo-preview-container" id="heroPhotoPreviewContainer" {!! $bgUrl ? '' : 'style="display:none"' !!}>
                                            <img id="heroPhotoPreview" src="{{ $bgUrl }}" alt="Превью фона">
                                        </div>
                                        <div class="change-overlay"><i class="fas fa-camera me-1"></i>Заменить фото</div>
                                    </div>
                                    <input type="file" name="background_image" id="heroImageInput" accept="image/jpeg,image/png,image/webp" class="d-none">
                                    <div class="text-danger small mt-2" id="heroPhotoError" role="alert"></div>
                                    @if($bgUrl)
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="checkbox" name="remove_background" value="1" id="remove_background">
                                            <label class="form-check-label" for="remove_background">Убрать это фото (подставится запасной фон с сайта)</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card hero-constructor-card">
                        <div class="card-header">2. Текст на баннере</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="hero_title_input">Большой заголовок</label>
                                <input type="text" name="title" id="hero_title_input" class="form-control" value="{{ old('title', $hero->title) }}" required maxlength="255" placeholder="Например: Обучение вождению в Москве">
                            </div>
                            <div class="mb-0">
                                <label class="form-label" for="hero_subtitle_input">Текст под заголовком</label>
                                <textarea name="subtitle" id="hero_subtitle_input" class="form-control" rows="4" maxlength="5000" placeholder="Коротко: что получит ученик">{{ old('subtitle', $hero->subtitle) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card hero-constructor-card">
                        <div class="card-header">3. Кнопки под текстом (от 1 до 3)</div>
                        <div class="card-body">
                            <p class="form-hint mb-3">Добавьте до трёх кнопок. Ухватите строку за иконку с точками слева и перетащите, чтобы поменять порядок — так же на сайте. При расположении «В один ряд» и выравнивании «С промежутком» кнопки делят ширину поровну, чтобы короткая подпись не смещала соседние.</p>
                            <ul id="hero-cta-buttons-list" class="list-unstyled mb-0">
                                @foreach($ctaRows as $i => $cta)
                                    <li class="hero-cta-item border rounded p-3 mb-2 bg-light">
                                        <div class="d-flex flex-wrap align-items-start gap-2">
                                            <span class="hero-cta-handle text-muted" title="Перетащить кнопку"><i class="fas fa-grip-vertical"></i></span>
                                            <div class="flex-grow-1" style="min-width: 12rem;">
                                                <p class="small fw-semibold text-muted mb-2">Кнопка {{ $i + 1 }}</p>
                                                <div class="mb-2">
                                                    <label class="form-label small mb-1" for="hero_cta_label_{{ $i }}">Надпись на кнопке</label>
                                                    <input type="text" name="cta_buttons[{{ $i }}][label]" id="hero_cta_label_{{ $i }}" class="form-control form-control-sm hero-cta-label-input" value="{{ $cta['label'] }}" required maxlength="120" placeholder="Например: Узнать стоимость">
                                                </div>
                                                <div class="mb-2">
                                                    <span class="form-label small d-block mb-1">Иконка</span>
                                                    <div class="hero-icon-field d-flex align-items-center gap-2 flex-wrap">
                                                        <input type="hidden" name="cta_buttons[{{ $i }}][icon]" class="hero-icon-picker-value" value="{{ $cta['icon'] }}">
                                                        <button type="button" class="btn btn-outline-secondary hero-icon-btn" title="Выбрать иконку" aria-label="Выбрать иконку"><i class="{{ $cta['icon'] }}"></i></button>
                                                        <span class="form-hint mb-0">Нажмите на квадратик</span>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small mb-1" for="hero_cta_variant_{{ $i }}">Стиль кнопки</label>
                                                    <select name="cta_buttons[{{ $i }}][variant]" id="hero_cta_variant_{{ $i }}" class="form-select form-select-sm hero-cta-variant-select" required>
                                                        <option value="light" {{ ($cta['variant'] ?? (($i % 2 === 0) ? 'light' : 'outline-light')) === 'light' ? 'selected' : '' }}>Белая (заливка)</option>
                                                        <option value="outline-light" {{ ($cta['variant'] ?? (($i % 2 === 0) ? 'light' : 'outline-light')) === 'outline-light' ? 'selected' : '' }}>Прозрачная (контур)</option>
                                                    </select>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small mb-1" for="hero_cta_href_{{ $i }}">Куда ведёт кнопка</label>
                                                    <select name="cta_buttons[{{ $i }}][href]" id="hero_cta_href_{{ $i }}" class="form-select form-select-sm hero-cta-href-select" required>
                                                        @foreach($heroCtaLinkOptions as $path => $label)
                                                            <option value="{{ $path }}" {{ ($cta['href'] ?? '') === $path ? 'selected' : '' }}>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger hero-cta-remove" title="Удалить кнопку" aria-label="Удалить кнопку" @if(count($ctaRows) <= 1) style="visibility:hidden" @endif><i class="fas fa-times"></i></button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="hero-cta-add" @if(count($ctaRows) >= 3) style="display:none" @endif><i class="fas fa-plus me-1"></i>Добавить кнопку</button>
                        </div>
                    </div>

                    <div class="card hero-constructor-card">
                        <div class="card-header">4. Как стоят кнопки</div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="buttons_align">По горизонтали</label>
                                    <select name="buttons_align" id="buttons_align" class="form-select">
                                        @php
                                            $alignSel = old('buttons_align', $hero->buttons_align);
                                            if ($alignSel === 'around') {
                                                $alignSel = 'between';
                                            }
                                        @endphp
                                        @foreach(['center' => 'По центру', 'start' => 'Слева', 'end' => 'Справа', 'between' => 'С промежутком'] as $val => $label)
                                            <option value="{{ $val }}" {{ $alignSel === $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="buttons_direction">Расположение</label>
                                    <select name="buttons_direction" id="buttons_direction" class="form-select">
                                        <option value="row" {{ old('buttons_direction', $hero->buttons_direction) === 'row' ? 'selected' : '' }}>В один ряд</option>
                                        <option value="column" {{ old('buttons_direction', $hero->buttons_direction) === 'column' ? 'selected' : '' }}>Друг под другом</option>
                                    </select>
                                </div>
                            </div>
                            <p class="form-hint mb-0 mt-2">«Слева / по центру / справа» сдвигают группу кнопок. «С промежутком» в одном ряду делит ширину поровну между кнопками (короткая подпись не смещает соседние). При нехватке места ряд переносится, без наложения.</p>
                        </div>
                    </div>

                    <div class="card hero-constructor-card">
                        <div class="card-header">5. Полоска под баннером (иконки и короткий текст)</div>
                        <div class="card-body">
                            <p class="form-hint mb-3">Добавьте до восьми пунктов: например филиалы, лицензия, опыт инструкторов. Ухватите строку за иконку с точками слева или за свободное место в строке (не за поля ввода и кнопки) и перетащите. Пустые строки при сохранении не попадут на сайт.</p>
                            <ul id="trust-items-list" class="list-unstyled">
                                @foreach($trustRows as $i => $row)
                                    <li class="hero-trust-item" data-trust-index="{{ $i }}">
                                        <div class="d-flex flex-wrap align-items-center gap-2 p-2">
                                            <span class="hero-trust-handle text-muted" title="Перетащить строку"><i class="fas fa-grip-vertical"></i></span>
                                            <input type="hidden" name="trust_items[{{ $i }}][icon]" class="hero-icon-picker-value" value="{{ old('trust_items.'.$i.'.icon', $row['icon'] ?? 'fas fa-check') }}">
                                            <button type="button" class="btn btn-outline-secondary btn-sm hero-icon-btn" title="Выбрать иконку"><i class="{{ old('trust_items.'.$i.'.icon', $row['icon'] ?? 'fas fa-check') }}"></i></button>
                                            <div class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                                                <div>
                                                    <label class="form-label small mb-0 text-muted">Число или короткий текст (необязательно)</label>
                                                    <input type="text" name="trust_items[{{ $i }}][value]" class="form-control form-control-sm trust-value-inp" style="max-width:7rem" value="{{ old('trust_items.'.$i.'.value', $row['value'] ?? '') }}" placeholder="12+">
                                                </div>
                                                <div class="flex-grow-1" style="min-width: 12rem;">
                                                    <label class="form-label small mb-0 text-muted">Основной текст</label>
                                                    <input type="text" name="trust_items[{{ $i }}][label]" class="form-control form-control-sm trust-label-inp" value="{{ old('trust_items.'.$i.'.label', $row['label'] ?? '') }}" placeholder="лет на рынке">
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger hero-trust-remove" title="Удалить строку" aria-label="Удалить строку"><i class="fas fa-times"></i></button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="hero-trust-add"><i class="fas fa-plus me-1"></i>Добавить пункт</button>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 pb-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-1"></i>Сохранить
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@include('admin.partials.hero-icon-picker-modal')
@endsection

@section('script')
    <script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
    @include('admin.partials.hero-settings-scripts')
@endsection
