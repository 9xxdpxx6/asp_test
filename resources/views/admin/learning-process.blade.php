@extends('layouts.admin')

@section('title', 'Как проходит обучение — главная')

@php
    $formBlocks = old('blocks');
    if (!is_array($formBlocks) || count($formBlocks) !== 4) {
        $formBlocks = $blocks->map(function ($b) {
            return [
                'title' => $b->title,
                'description' => $b->description,
                'icon' => $b->icon,
            ];
        })->values()->all();
    }
    $formHeading = old('heading', $heading);
    $formSubheading = old('subheading', $subheading);
@endphp

@section('style')
<style>
    .hf-admin-intro {
        background: linear-gradient(135deg, #f8fafc 0%, #e8f4fc 100%);
        border-radius: 0.75rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    .hf-admin-intro h1 { font-size: 1.45rem; font-weight: 600; color: #1e293b; margin: 0 0 0.35rem 0; }
    .hf-admin-intro p { margin: 0; color: #64748b; font-size: 0.95rem; max-width: 48rem; }

    #lp-blocks-list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        align-items: stretch;
    }
    .hf-lp-row {
        min-width: 0;
        margin: 0;
    }
    @media (max-width: 991.98px) {
        #lp-blocks-list { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 575.98px) {
        #lp-blocks-list { grid-template-columns: 1fr; }
    }

    .hf-lp-card {
        height: 100%;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        background: #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        cursor: grab;
    }
    .hf-lp-card:active { cursor: grabbing; }
    .hf-lp-card input[type="text"],
    .hf-lp-card textarea { cursor: text; }
    .hf-lp-card .hf-icon-btn { cursor: pointer; }

    .hf-card-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: 0.4rem 0.65rem;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
        font-size: 0.75rem;
        color: #6c757d;
    }
    .hf-drag-handle {
        cursor: grab;
        padding: 0.2rem 0.35rem;
        user-select: none;
        touch-action: none;
        color: #6c757d;
        border-radius: 0.25rem;
    }
    .hf-drag-handle:hover { background: #e9ecef; }
    .hf-drag-handle i { pointer-events: none; }
    .hf-drag-handle:active { cursor: grabbing; }

    .hf-lp-body {
        padding: 1rem 0.85rem 1.1rem;
        text-align: center;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .hf-step-num {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #0d6efd;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        margin: 0 auto 0.65rem;
    }

    .hf-lp-body .hf-icon-btn.hf-step-icon-ring-btn {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(58, 108, 232, 0.1);
        border: none;
        padding: 0;
        margin: 0 auto 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: #0d6efd;
        line-height: 1;
        cursor: pointer;
        transition: background 0.15s, box-shadow 0.15s;
    }
    .hf-lp-body .hf-icon-btn.hf-step-icon-ring-btn:hover {
        background: rgba(58, 108, 232, 0.18);
    }
    .hf-lp-body .hf-icon-btn.hf-step-icon-ring-btn:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.4);
    }

    .hf-lp-body .form-label { font-size: 0.75rem; text-align: left; }
    .hf-lp-body input[type="text"],
    .hf-lp-body textarea { text-align: left; width: 100%; }

    #lp-blocks-list > li.hf-sort-placeholder {
        visibility: visible !important;
        border: 2px dashed #0d6efd !important;
        background: rgba(240, 247, 255, 0.92) !important;
        border-radius: 0.5rem;
        box-sizing: border-box;
        min-width: 0;
    }

    .hf-lp-head-preview {
        background: #fff;
        border: 1px dashed #ced4da;
        border-radius: 0.5rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        text-align: center;
    }
    .hf-lp-head-preview .form-label { font-size: 0.8rem; color: #6c757d; }
    .hf-lp-head-preview .main-title-inp { font-weight: 600; font-size: 1.15rem; text-align: center; }
    .hf-lp-head-preview .sub-inp { text-align: center; color: #6c757d; }

    .home-feature-icon-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(48px, 1fr));
        gap: 4px;
        max-height: 400px;
        overflow-y: auto;
        padding: 0.25rem;
    }
    .home-feature-icon-picker-item {
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
    .home-feature-icon-picker-item:hover,
    .home-feature-icon-picker-item.selected {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }
    .btn-saving-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: hfSpinLp 0.7s linear infinite;
        vertical-align: -0.1rem;
    }
    @keyframes hfSpinLp { to { transform: rotate(360deg); } }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-12">
                <div class="hf-admin-intro">
                    <h1>Как проходит обучение</h1>
                    <p><strong>Ниже — как на главной:</strong> четыре шага в ряд (на планшете по 2, на телефоне столбик). Номера шагов на сайте — по порядку слева направо. <strong>Порядок карточек</strong> меняйте, ухватив за свободную область (не за поля и не за круг с иконкой — там только ввод и выбор иконки). Иконка — клик по большому кругу.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.learning-process.update') }}" id="learning-process-form">
            @csrf

            <div class="hf-lp-head-preview">
                <label class="form-label mb-1" for="lp_heading">Большой заголовок (как на сайте)</label>
                <input type="text" name="heading" id="lp_heading" class="form-control main-title-inp mb-3" value="{{ $formHeading }}" required maxlength="255">
                <label class="form-label mb-1" for="lp_subheading">Серый подзаголовок под ним</label>
                <input type="text" name="subheading" id="lp_subheading" class="form-control sub-inp" value="{{ $formSubheading }}" required maxlength="500">
            </div>

            <p class="small text-muted mb-2">Четыре шага в ряд — как на главной</p>
            <ul id="lp-blocks-list">
                @foreach($formBlocks as $i => $row)
                    <li class="hf-lp-row">
                        <div class="hf-lp-card">
                            <div class="hf-card-toolbar">
                                <span class="hf-drag-handle" title="Перетащить"><i class="fas fa-grip-vertical"></i></span>
                                <span>Шаг <span class="hf-step-toolbar-num">{{ $i + 1 }}</span></span>
                            </div>
                            <div class="hf-lp-body">
                                <div class="hf-step-num hf-step-badge">{{ $i + 1 }}</div>
                                <input type="hidden" name="blocks[{{ $i }}][icon]" class="hf-icon-value" value="{{ $row['icon'] }}">
                                <button type="button" class="hf-icon-btn hf-step-icon-ring-btn" title="Нажмите, чтобы сменить иконку" aria-label="Выбрать иконку">
                                    <i class="{{ $row['icon'] }}" aria-hidden="true"></i>
                                </button>
                                <label class="form-label mb-1">Заголовок шага</label>
                                <input type="text" name="blocks[{{ $i }}][title]" class="form-control form-control-sm mb-2" value="{{ $row['title'] }}" required maxlength="255">
                                <label class="form-label mb-1">Текст</label>
                                <textarea name="blocks[{{ $i }}][description]" class="form-control form-control-sm flex-grow-1" style="min-height: 5.5rem;" required maxlength="5000">{{ $row['description'] }}</textarea>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i>Сохранить
                </button>
            </div>
        </form>
    </div>
</section>

@include('admin.partials.home-feature-icon-picker-modal', [
    'modalId' => 'lpIconPickerModal',
    'gridId' => 'lpIconPickerGrid',
    'helpText' => 'Иконка в круге, как на главной.',
])
@endsection

@section('script')
<script>
(function () {
    var HF_ICONS = [
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
        'fas fa-clipboard-check', 'fas fa-tasks', 'fas fa-file-contract', 'fas fa-file-signature',
        'fas fa-book-open', 'fas fa-book', 'fas fa-trophy'
    ];
    var currentIconBtn = null;
    var currentIconInput = null;
    var iconModal = null;

    document.addEventListener('DOMContentLoaded', function () {
        var grid = document.getElementById('lpIconPickerGrid');
        var modalEl = document.getElementById('lpIconPickerModal');
        var form = document.getElementById('learning-process-form');
        var list = document.getElementById('lp-blocks-list');

        if (modalEl && typeof bootstrap !== 'undefined') {
            iconModal = new bootstrap.Modal(modalEl);
        }

        function reindexLpBlocks() {
            if (!list) return;
            list.querySelectorAll('.hf-lp-row').forEach(function (li, idx) {
                li.querySelectorAll('[name^="blocks["]').forEach(function (inp) {
                    inp.name = inp.name.replace(/blocks\[\d+\]/, 'blocks[' + idx + ']');
                });
                var n = idx + 1;
                var tb = li.querySelector('.hf-step-toolbar-num');
                if (tb) tb.textContent = String(n);
                var bd = li.querySelector('.hf-step-badge');
                if (bd) bd.textContent = String(n);
            });
        }

        if (grid) {
            HF_ICONS.forEach(function (iconClass) {
                var item = document.createElement('button');
                item.type = 'button';
                item.className = 'home-feature-icon-picker-item';
                item.dataset.icon = iconClass;
                item.innerHTML = '<i class="' + iconClass + '"></i>';
                item.addEventListener('click', function () {
                    if (currentIconInput && currentIconBtn) {
                        currentIconInput.value = iconClass;
                        currentIconBtn.innerHTML = '<i class="' + iconClass + '"></i>';
                        grid.querySelectorAll('.home-feature-icon-picker-item').forEach(function (el) {
                            el.classList.toggle('selected', el.dataset.icon === iconClass);
                        });
                        if (iconModal) iconModal.hide();
                    }
                });
                grid.appendChild(item);
            });
        }

        form?.addEventListener('click', function (e) {
            var btn = e.target.closest('.hf-icon-btn');
            if (!btn || !form.contains(btn)) return;
            e.preventDefault();
            var row = btn.closest('.hf-lp-row');
            if (!row) return;
            currentIconBtn = btn;
            currentIconInput = row.querySelector('.hf-icon-value');
            if (!currentIconInput) return;
            var val = currentIconInput.value || 'fas fa-check';
            document.querySelectorAll('#lpIconPickerGrid .home-feature-icon-picker-item').forEach(function (el) {
                el.classList.toggle('selected', el.dataset.icon === val);
            });
            if (iconModal) iconModal.show();
        });

        if (list && typeof window.jQuery !== 'undefined' && jQuery.fn.sortable) {
            jQuery(list).sortable({
                items: '> li.hf-lp-row',
                cursor: 'grabbing',
                opacity: 0.92,
                tolerance: 'pointer',
                scroll: true,
                placeholder: 'hf-sort-placeholder',
                forcePlaceholderSize: true,
                filter: 'input, textarea, button, select, option, label',
                preventOnFilter: true,
                start: function (e, ui) {
                    var h = ui.item.outerHeight();
                    ui.placeholder.css({ width: '100%', height: h, boxSizing: 'border-box' });
                },
                update: function () {
                    reindexLpBlocks();
                }
            });
        }

        document.getElementById('learning-process-form')?.addEventListener('submit', function () {
            var btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="btn-saving-spinner" aria-hidden="true"></span>Сохраняем…';
            }
        });
    });
})();
</script>
@endsection
