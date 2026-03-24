@extends('layouts.admin')

@section('title', 'Подвал сайта')

@section('style')
<style>
    .footer-admin-intro {
        background: linear-gradient(135deg, #f8fafc 0%, #e8f4fc 100%);
        border-radius: 0.75rem;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    .footer-admin-intro h1 { font-size: 1.45rem; font-weight: 600; color: #1e293b; margin: 0 0 0.35rem 0; }
    .footer-admin-intro p { margin: 0; color: #64748b; font-size: 0.95rem; max-width: 56rem; }
    .footer-doc-card {
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        padding: 1rem 1.15rem;
        margin-bottom: 1rem;
        background: #fff;
    }
    .footer-doc-card h5 { font-size: 0.95rem; font-weight: 700; margin-bottom: 0.75rem; color: #1f2937; }
    .footer-social-row {
        display: grid;
        grid-template-columns: 140px 1fr;
        gap: 0.75rem;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .footer-social-row label { margin: 0; font-size: 0.85rem; color: #475569; font-weight: 600; }

    .footer-pdf-pick {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        gap: 0.5rem;
    }
    .footer-pdf-pick .footer-pdf-filename {
        flex: 1 1 12rem;
        min-height: 2.35rem;
        font-size: 0.875rem;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 0.375rem;
        padding: 0.35rem 0.65rem;
        display: flex;
        align-items: center;
        word-break: break-word;
    }
    .footer-pdf-filename.footer-pdf-filename--selected {
        border-style: solid;
        border-color: #198754;
        background: #f0fdf4;
        color: #166534;
        font-weight: 600;
    }
    .footer-pdf-filename.footer-pdf-filename--empty {
        color: #64748b;
    }

    .footer-live-wrap {
        position: sticky;
        top: 0.75rem;
    }
    .footer-live-preview {
        background: var(--bs-primary, #0d6efd);
        color: #fff;
        border-radius: 0.75rem;
        padding: 1rem 1rem 0.75rem;
        font-size: 0.78rem;
        line-height: 1.45;
        box-shadow: 0 0.35rem 1.2rem rgba(15, 23, 42, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.12);
    }
    .footer-live-preview .footer-live-badge {
        font-size: 0.65rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.65);
        margin-bottom: 0.5rem;
    }
    .footer-live-preview h6 {
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 0.45rem;
        color: #fff;
    }
    .footer-live-preview .footer-live-logo {
        height: 28px;
        width: auto;
        opacity: 0.95;
        margin-bottom: 0.35rem;
    }
    .footer-live-preview .footer-live-social a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 0.6rem;
        font-size: 1.65rem;
        color: #fff !important;
        text-decoration: none !important;
    }
    .footer-live-preview .footer-live-social a:hover {
        opacity: 0.85;
    }
    .footer-live-preview .footer-live-social img.footer-live-social-img {
        width: 2.025rem;
        height: 2.025rem;
        object-fit: contain;
        display: block;
        border-radius: 0.75rem;
    }
    .footer-live-preview .footer-live-copy {
        font-size: 0.65rem;
        color: rgba(255, 255, 255, 0.7);
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        margin-top: 0.65rem;
        padding-top: 0.5rem;
        text-align: center;
    }
    .footer-live-preview a.text-white {
        color: #fff !important;
    }
    .footer-live-preview .list-unstyled li {
        margin-bottom: 0.2rem;
    }
    @media (max-width: 991.98px) {
        .footer-live-wrap {
            position: relative;
            top: auto;
            margin-bottom: 1.25rem;
        }
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-12">
                <div class="footer-admin-intro">
                    <h1>Конструктор подвала</h1>
                    <p>Контакты, текст под логотипом, до трёх PDF (акт самообследования и др.) и ссылки на соцсети. PDF отдаются с заголовком «скачать», чтобы браузер не открывал их вместо загрузки. Справа — живое превью.</p>
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

        <form method="POST" action="{{ route('admin.footer-settings.update') }}" enctype="multipart/form-data" id="footer-settings-form">
            @csrf

            <div class="row g-3">
                <div class="col-lg-7 order-2 order-lg-1">
            <div class="row g-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header"><strong>Контакты</strong></div>
                        <div class="card-body">
                            <label class="form-label" for="footer_phone">Телефон</label>
                            <input type="text" name="phone" id="footer_phone" class="form-control mb-3" value="{{ old('phone', $footer->phone) }}" required maxlength="255" data-footer-preview="phone">

                            <label class="form-label" for="footer_email">E-mail</label>
                            <input type="text" name="email" id="footer_email" class="form-control mb-3" value="{{ old('email', $footer->email) }}" required maxlength="255" data-footer-preview="email">

                            <label class="form-label" for="footer_address">Адрес</label>
                            <textarea name="address" id="footer_address" class="form-control" rows="3" maxlength="2000" data-footer-preview="address">{{ old('address', $footer->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header"><strong>Текст под логотипом</strong></div>
                        <div class="card-body">
                            <label class="form-label" for="footer_logo_description">Описание</label>
                            <textarea name="logo_description" id="footer_logo_description" class="form-control" rows="8" maxlength="5000" data-footer-preview="logo_description">{{ old('logo_description', $footer->logo_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><strong>Документы PDF (максимум 3 активных)</strong></div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Включите «Показывать в подвале» только для документов с загруженным файлом. Подпись ссылки — поле «Текст ссылки».</p>
                    @foreach($documents as $i => $doc)
                        <div class="footer-doc-card" data-footer-doc-slot="{{ $i }}" data-has-file="{{ $doc->file_path ? '1' : '0' }}">
                            <h5>Документ {{ $i + 1 }}</h5>
                            <input type="hidden" name="documents[{{ $i }}][id]" value="{{ $doc->id }}">

                            <label class="form-label" for="doc_title_{{ $i }}">Текст ссылки в подвале</label>
                            <input type="text" name="documents[{{ $i }}][title]" id="doc_title_{{ $i }}" class="form-control mb-2"
                                   value="{{ old('documents.'.$i.'.title', $doc->title) }}" maxlength="500" data-footer-doc-title="{{ $i }}">

                            <div class="form-check mb-2">
                                <input type="hidden" name="documents[{{ $i }}][is_active]" value="0">
                                <input type="checkbox" name="documents[{{ $i }}][is_active]" id="doc_active_{{ $i }}" class="form-check-input" value="1"
                                       {{ filter_var(old('documents.'.$i.'.is_active', $doc->is_active), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
                                       data-footer-doc-active="{{ $i }}">
                                <label class="form-check-label" for="doc_active_{{ $i }}">Показывать в подвале</label>
                            </div>

                            @if($doc->file_path)
                                <div class="alert alert-success py-2 px-3 mb-2 small d-flex align-items-start">
                                    <i class="fas fa-check-circle mt-1 me-2"></i>
                                    <div>
                                        <div class="fw-bold">Файл уже на сайте</div>
                                        <div class="text-break">{{ $doc->original_filename ?: basename($doc->file_path) }}</div>
                                    </div>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="hidden" name="documents[{{ $i }}][remove_file]" value="0">
                                    <input type="checkbox" name="documents[{{ $i }}][remove_file]" id="doc_remove_{{ $i }}" class="form-check-input" value="1"
                                           {{ filter_var(old('documents.'.$i.'.remove_file', false), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
                                           data-footer-doc-remove="{{ $i }}">
                                    <label class="form-check-label text-danger" for="doc_remove_{{ $i }}">Удалить файл с сайта</label>
                                </div>
                            @else
                                <div class="alert alert-light border py-2 px-3 mb-2 small text-muted">
                                    <i class="fas fa-file-upload me-1"></i> На сайте пока нет PDF — выберите файл ниже и нажмите «Сохранить» внизу страницы.
                                </div>
                            @endif

                            <label class="form-label mb-1">Новый PDF</label>
                            <p class="small text-muted mb-2 mb-sm-1">Выбор файла не отправляет его сразу: загрузка произойдёт после нажатия «Сохранить».</p>
                            <div class="footer-pdf-pick">
                                <input type="file" name="documents[{{ $i }}][file]" id="doc_file_{{ $i }}" class="d-none" accept="application/pdf,.pdf" data-footer-doc-file="{{ $i }}">
                                <label for="doc_file_{{ $i }}" class="btn btn-outline-primary btn-sm mb-0 align-self-start">
                                    <i class="fas fa-folder-open me-1"></i>Выбрать PDF
                                </label>
                                <div class="footer-pdf-filename footer-pdf-filename--empty flex-grow-1" id="footer-pdf-filename-{{ $i }}" role="status" aria-live="polite">
                                    Файл не выбран
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm align-self-start" data-footer-pdf-clear="{{ $i }}" title="Сбросить выбор файла">
                                    Сбросить
                                </button>
                            </div>
                            <div class="form-text mt-1" id="footer-pdf-hint-{{ $i }}"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><strong>Соцсети и мессенджеры</strong></div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Заполните только нужные ссылки; пустые строки на сайте не показываются.</p>
                    @foreach($socialRows as $i => $row)
                        <div class="footer-social-row" data-social-code="{{ $row['code'] }}">
                            <input type="hidden" name="social_links[{{ $i }}][code]" value="{{ $row['code'] }}">
                            <label for="social_url_{{ $i }}">{{ $row['label'] }}</label>
                            <input type="text" name="social_links[{{ $i }}][url]" id="social_url_{{ $i }}" class="form-control form-control-sm"
                                   value="{{ old('social_links.'.$i.'.url', $row['url']) }}"
                                   placeholder="https://..." maxlength="2048" data-footer-social-url="{{ $row['code'] }}">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-3 mb-5">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i>Сохранить
                </button>
            </div>
                </div>

                <div class="col-lg-5 order-1 order-lg-2">
                    <div class="footer-live-wrap">
                        <div class="footer-live-preview" id="footer-live-root">
                            <div class="footer-live-badge">Превью подвала</div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <h6>Контакты</h6>
                                    <ul class="list-unstyled mb-0 small">
                                        <li>
                                            <i class="fas fa-phone me-1"></i>
                                            <a href="#" class="text-white" id="pv-phone-link"><span id="pv-phone">—</span></a>
                                        </li>
                                        <li>
                                            <i class="fas fa-envelope me-1"></i>
                                            <a href="#" class="text-white" id="pv-email-link"><span id="pv-email">—</span></a>
                                        </li>
                                        <li id="pv-address-li">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <span id="pv-address">—</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <h6>Полезные ссылки</h6>
                                    <ul class="list-unstyled mb-0 small" id="pv-static-links">
                                        <li class="text-white-50">Цены</li>
                                        <li class="text-white-50">Новости</li>
                                        <li class="text-white-50">Контакты</li>
                                        <li id="pv-doc-0" class="d-none"><a href="#" class="text-white text-decoration-none" data-pv-doc="0">—</a></li>
                                        <li id="pv-doc-1" class="d-none"><a href="#" class="text-white text-decoration-none" data-pv-doc="1">—</a></li>
                                        <li id="pv-doc-2" class="d-none"><a href="#" class="text-white text-decoration-none" data-pv-doc="2">—</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-4">
                                    <img src="{{ asset('logo.png') }}" alt="" class="footer-live-logo d-block">
                                    <p class="small mb-0" id="pv-logo-desc">—</p>
                                </div>
                            </div>
                            <div class="footer-live-social text-center mt-2 pt-1" id="pv-social"></div>
                            <div class="footer-live-copy" id="pv-copy">© <span id="pv-year"></span> Автошкола Политех. Все права защищены.</div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@php
    $footerSocialIconMap = [];
    foreach (\App\Support\FooterSocialCatalog::definitions() as $code => $def) {
        $footerSocialIconMap[$code] = [
            'icon_url' => asset('images/social/'.$def['icon_file']),
        ];
    }
@endphp
<script type="application/json" id="footer-social-icon-map">@json($footerSocialIconMap)</script>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('footer-settings-form');
    var iconMapEl = document.getElementById('footer-social-icon-map');
    var iconMap = iconMapEl ? JSON.parse(iconMapEl.textContent || '{}') : {};

    function digitsForTel(raw) {
        var d = String(raw || '').replace(/\D/g, '');
        if (d.length === 11 && d.charAt(0) === '8') {
            return '7' + d.slice(1);
        }
        if (d.length === 10) {
            return '7' + d;
        }
        return d;
    }

    function escapeAttr(s) {
        return String(s || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function syncContacts() {
        var phone = (document.getElementById('footer_phone') || {}).value || '';
        var email = (document.getElementById('footer_email') || {}).value || '';
        var address = (document.getElementById('footer_address') || {}).value || '';

        var pvPhone = document.getElementById('pv-phone');
        var pvEmail = document.getElementById('pv-email');
        var pvAddress = document.getElementById('pv-address');
        var pvAddressLi = document.getElementById('pv-address-li');
        var phoneLink = document.getElementById('pv-phone-link');
        var emailLink = document.getElementById('pv-email-link');

        pvPhone.textContent = phone.trim() ? phone.trim() : '—';
        pvEmail.textContent = email.trim() ? email.trim() : '—';

        var td = digitsForTel(phone);
        if (td) {
            phoneLink.href = 'tel:+' + td;
        } else {
            phoneLink.href = '#';
        }

        if (email.trim()) {
            emailLink.href = 'mailto:' + email.trim();
        } else {
            emailLink.href = '#';
        }

        if (address.trim()) {
            pvAddress.textContent = address.trim();
            pvAddressLi.classList.remove('d-none');
        } else {
            pvAddress.textContent = '';
            pvAddressLi.classList.add('d-none');
        }
    }

    function docSlotHasFile(i) {
        var card = document.querySelector('[data-footer-doc-slot="' + i + '"]');
        if (!card) return false;
        var hasInitial = card.getAttribute('data-has-file') === '1';
        var removeEl = document.querySelector('[data-footer-doc-remove="' + i + '"]');
        var removeChecked = removeEl ? removeEl.checked : false;
        var fileEl = document.querySelector('[data-footer-doc-file="' + i + '"]');
        var newFile = fileEl && fileEl.files && fileEl.files.length > 0;
        return (hasInitial && !removeChecked) || newFile;
    }

    function syncDocuments() {
        for (var i = 0; i < 3; i++) {
            var titleEl = document.querySelector('[data-footer-doc-title="' + i + '"]');
            var activeEl = document.querySelector('[data-footer-doc-active="' + i + '"]');
            var li = document.getElementById('pv-doc-' + i);
            var a = li ? li.querySelector('a') : null;
            if (!li || !a) continue;

            var title = titleEl ? titleEl.value.trim() : '';
            var active = activeEl ? activeEl.checked : false;
            var show = active && docSlotHasFile(i) && title.length > 0;

            if (show) {
                li.classList.remove('d-none');
                a.textContent = title;
            } else {
                li.classList.add('d-none');
                a.textContent = '—';
            }
        }
    }

    function socialIconHtml(code) {
        var def = iconMap[code];
        if (!def || !def.icon_url) {
            return '<span class="text-white-50" style="font-size:0.75rem">●</span>';
        }
        return '<img src="' + escapeAttr(def.icon_url) + '" alt="" class="footer-live-social-img" width="33" height="33" loading="lazy">';
    }

    function syncSocial() {
        var wrap = document.getElementById('pv-social');
        if (!wrap) return;
        var inputs = document.querySelectorAll('[data-footer-social-url]');
        var html = '';
        inputs.forEach(function (input) {
            var code = input.getAttribute('data-footer-social-url');
            var url = (input.value || '').trim();
            if (!url) return;
            var safeHref = escapeAttr(url);
            html += '<a href="' + safeHref + '" target="_blank" rel="noopener noreferrer" title="' + escapeAttr(code) + '">' + socialIconHtml(code) + '</a>';
        });
        wrap.innerHTML = html;
    }

    function syncLogoDesc() {
        var el = document.getElementById('footer_logo_description');
        var pv = document.getElementById('pv-logo-desc');
        if (!el || !pv) return;
        var t = el.value.trim();
        pv.textContent = t || '—';
    }

    function syncYear() {
        var y = document.getElementById('pv-year');
        if (y) y.textContent = String(new Date().getFullYear());
    }

    function updatePdfFilenameDisplay(i) {
        var input = document.querySelector('[data-footer-doc-file="' + i + '"]');
        var box = document.getElementById('footer-pdf-filename-' + i);
        var hint = document.getElementById('footer-pdf-hint-' + i);
        if (!input || !box) return;
        var f = input.files && input.files[0];
        if (f) {
            box.textContent = f.name;
            box.classList.remove('footer-pdf-filename--empty');
            box.classList.add('footer-pdf-filename--selected');
            if (hint) {
                hint.innerHTML = '<span class="text-success"><i class="fas fa-check me-1"></i>Готово к отправке — нажмите «Сохранить» внизу страницы.</span>';
            }
        } else {
            box.textContent = 'Файл не выбран';
            box.classList.add('footer-pdf-filename--empty');
            box.classList.remove('footer-pdf-filename--selected');
            if (hint) {
                hint.innerHTML = '';
            }
        }
    }

    function syncPdfFilenameDisplays() {
        for (var j = 0; j < 3; j++) {
            updatePdfFilenameDisplay(j);
        }
    }

    function syncAll() {
        syncPdfFilenameDisplays();
        syncContacts();
        syncLogoDesc();
        syncDocuments();
        syncSocial();
        syncYear();
    }

    if (form) {
        form.querySelectorAll('[data-footer-pdf-clear]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var i = btn.getAttribute('data-footer-pdf-clear');
                var input = document.querySelector('[data-footer-doc-file="' + i + '"]');
                if (input) {
                    input.value = '';
                    syncAll();
                }
            });
        });
        form.querySelectorAll('input, textarea').forEach(function (el) {
            if (el.type === 'file') {
                el.addEventListener('change', syncAll);
            } else {
                el.addEventListener('input', syncAll);
                el.addEventListener('change', syncAll);
            }
        });
        form.addEventListener('submit', function () {
            var btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1" aria-hidden="true"></i>Сохраняем...';
            }
        });
    }

    syncAll();

    document.querySelectorAll('#pv-static-links a[data-pv-doc]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
        });
    });
});
</script>
@endsection
