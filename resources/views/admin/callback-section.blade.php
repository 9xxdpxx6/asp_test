@extends('layouts.admin')

@section('title', 'Обратный звонок — главная')

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
    .hf-admin-intro p { margin: 0; color: #64748b; font-size: 0.95rem; max-width: 56rem; }

    .hf-callback-preview {
        background: linear-gradient(135deg, #4674ea 0%, #2f56c2 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: #fff;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .hf-callback-preview-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 1.2fr);
        gap: 2rem;
        align-items: center;
    }
    .hf-callback-copy h2 {
        font-size: clamp(1.9rem, 2vw, 2.6rem);
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .hf-callback-copy p {
        font-size: 1.05rem;
        line-height: 1.7;
        margin-bottom: 1.5rem;
        max-width: 32rem;
    }
    .hf-phone-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.85rem 1.35rem;
        border: 1px solid rgba(255, 255, 255, 0.85);
        border-radius: 999px;
        color: #fff;
        text-decoration: none;
        font-size: 1.1rem;
    }

    .hf-callback-card {
        background: #fff;
        border-radius: 1rem;
        padding: 1.4rem;
        box-shadow: 0 1rem 2.5rem rgba(17, 24, 39, 0.18);
    }
    .hf-callback-card h3 {
        font-size: 1.35rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1rem;
    }
    .hf-callback-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    .hf-callback-fields .hf-span-2 {
        grid-column: 1 / -1;
    }
    .hf-callback-mock-input,
    .hf-callback-mock-textarea,
    .hf-callback-mock-button {
        border-radius: 0.65rem;
    }
    .hf-callback-mock-input,
    .hf-callback-mock-textarea {
        border: 1px solid #dbe3ef;
        background: #f8fafc;
        color: #64748b;
        padding: 0.9rem 1rem;
    }
    .hf-callback-mock-textarea {
        min-height: 5.5rem;
    }
    .hf-callback-mock-button {
        border: none;
        background: #4674ea;
        color: #fff;
        padding: 1rem 1.25rem;
        font-size: 1.05rem;
        font-weight: 600;
    }

    .hf-callback-editor-card {
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        background: #fff;
        padding: 1.25rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.05);
        height: 100%;
    }
    .hf-callback-editor-card h4 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1f2937;
    }
    .hf-callback-editor-card .form-label {
        font-size: 0.8rem;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 0.35rem;
    }
    .btn-saving-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: hfSpinCallback 0.7s linear infinite;
        vertical-align: -0.1rem;
    }
    @keyframes hfSpinCallback { to { transform: rotate(360deg); } }

    @media (max-width: 991.98px) {
        .hf-callback-preview-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 575.98px) {
        .hf-callback-preview {
            padding: 1.25rem;
        }
        .hf-callback-fields {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-12">
                <div class="hf-admin-intro">
                    <h1>Обратный звонок</h1>
                    <p><strong>Ниже упрощённый макет как на главной:</strong> слева текст и кнопка телефона, справа карточка формы. Здесь редактируются только тексты и номер телефона, сама структура блока остаётся фиксированной.</p>
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

        <div class="hf-callback-preview">
            <div class="hf-callback-preview-grid">
                <div class="hf-callback-copy">
                    <h2 data-preview-target="heading">{{ $heading }}</h2>
                    <p data-preview-target="subheading">{{ $subheading }}</p>
                    <div class="hf-phone-chip">
                        <i class="fas fa-phone-alt"></i>
                        <span data-preview-target="phone_label">{{ $payload['phone_label'] }}</span>
                    </div>
                </div>
                <div class="hf-callback-card">
                    <h3 data-preview-target="form_title">{{ $payload['form_title'] }}</h3>
                    <div class="hf-callback-fields">
                        <div class="hf-callback-mock-input" data-preview-target="name_placeholder">{{ $payload['name_placeholder'] }}</div>
                        <div class="hf-callback-mock-input" data-preview-target="phone_placeholder">{{ $payload['phone_placeholder'] }}</div>
                        <div class="hf-callback-mock-input hf-span-2" data-preview-target="email_placeholder">{{ $payload['email_placeholder'] }}</div>
                        <div class="hf-callback-mock-textarea hf-span-2" data-preview-target="comment_placeholder">{{ $payload['comment_placeholder'] }}</div>
                        <button type="button" class="hf-callback-mock-button hf-span-2">
                            <i class="fas fa-phone-volume mr-2"></i><span data-preview-target="button_text">{{ $payload['button_text'] }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.callback-section.update') }}" id="callback-section-form">
            @csrf

            <div class="row g-3">
                <div class="col-lg-6 d-flex">
                    <div class="hf-callback-editor-card w-100">
                        <h4>Левая часть блока</h4>

                        <label class="form-label" for="callback_heading">Большой заголовок</label>
                        <input type="text" name="heading" id="callback_heading" class="form-control mb-3" value="{{ $heading }}" required maxlength="255">

                        <label class="form-label" for="callback_subheading">Описание под заголовком</label>
                        <textarea name="subheading" id="callback_subheading" class="form-control mb-3" rows="4" required maxlength="1000">{{ $subheading }}</textarea>

                        <label class="form-label" for="callback_phone_label">Телефон на кнопке</label>
                        <input type="text" name="phone_label" id="callback_phone_label" class="form-control" value="{{ $payload['phone_label'] }}" required maxlength="120">
                    </div>
                </div>

                <div class="col-lg-6 d-flex">
                    <div class="hf-callback-editor-card w-100">
                        <h4>Правая карточка формы</h4>

                        <label class="form-label" for="callback_form_title">Заголовок формы</label>
                        <input type="text" name="form_title" id="callback_form_title" class="form-control mb-3" value="{{ $payload['form_title'] }}" required maxlength="255">

                        <div class="row">
                            <div class="col-sm-6">
                                <label class="form-label" for="callback_name_placeholder">Поле имени</label>
                                <input type="text" name="name_placeholder" id="callback_name_placeholder" class="form-control mb-3" value="{{ $payload['name_placeholder'] }}" required maxlength="120">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="callback_phone_placeholder">Поле телефона</label>
                                <input type="text" name="phone_placeholder" id="callback_phone_placeholder" class="form-control mb-3" value="{{ $payload['phone_placeholder'] }}" required maxlength="120">
                            </div>
                        </div>

                        <label class="form-label" for="callback_email_placeholder">Поле почты</label>
                        <input type="text" name="email_placeholder" id="callback_email_placeholder" class="form-control mb-3" value="{{ $payload['email_placeholder'] }}" required maxlength="120">

                        <label class="form-label" for="callback_comment_placeholder">Поле комментария</label>
                        <input type="text" name="comment_placeholder" id="callback_comment_placeholder" class="form-control mb-3" value="{{ $payload['comment_placeholder'] }}" required maxlength="120">

                        <label class="form-label" for="callback_button_text">Текст кнопки</label>
                        <input type="text" name="button_text" id="callback_button_text" class="form-control" value="{{ $payload['button_text'] }}" required maxlength="120">
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i>Сохранить
                </button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('callback-section-form');
    if (!form) return;

    function syncPreview(input) {
        if (!input || !input.name) return;

        var target = document.querySelector('[data-preview-target="' + input.name + '"]');
        if (!target) return;

        var value = input.value || '';
        target.textContent = value.trim() ? value : '—';
    }

    form.querySelectorAll('input[name], textarea[name]').forEach(function (input) {
        syncPreview(input);
        input.addEventListener('input', function () {
            syncPreview(input);
        });
        input.addEventListener('change', function () {
            syncPreview(input);
        });
    });

    form.addEventListener('submit', function () {
        var btn = this.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="btn-saving-spinner" aria-hidden="true"></span>Сохраняем...';
        }
    });
});
</script>
@endsection
