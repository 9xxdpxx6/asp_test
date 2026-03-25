@extends('layouts.admin')

@section('title', 'Страница «Контакты»')

@php
    $formBranches = old('branches');
    if (!is_array($formBranches) || count($formBranches) === 0) {
        $formBranches = $branchRows;
    }
@endphp

@section('style')
<style>
    .contact-admin-branch {
        border: 1px solid #dee2e6;
        border-radius: 0.85rem;
        background: #fff;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .contact-admin-branch.sortable-ghost { opacity: 0.35; }
    .contact-admin-branch.is-pending-delete {
        border-color: #f1aeb5;
        background: #fff5f5;
    }
    .contact-admin-branch.is-pending-delete .contact-branch-head,
    .contact-admin-branch.is-pending-delete .contact-branch-body {
        opacity: 0.55;
    }
    .contact-branch-head {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 0.85rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        flex-wrap: wrap;
    }
    .contact-branch-body { padding: 1rem; }
    .contact-details-quill-wrap .ql-container { font-size: 0.95rem; min-height: 110px; }
    .contact-details-quill-wrap .ql-toolbar { border-top-left-radius: 0.375rem; border-top-right-radius: 0.375rem; }
    .contact-details-quill-wrap .ql-container { border-bottom-left-radius: 0.375rem; border-bottom-right-radius: 0.375rem; }
    .photo-upload-area.contact-photo-dz {
        width: 100%;
        aspect-ratio: 4 / 3;
        border: 2px dashed #ced4da;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
        position: relative;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
    }
    .photo-upload-area.contact-photo-dz:hover {
        border-color: #0d6efd;
        background: #f0f7ff;
    }
    .photo-upload-area.contact-photo-dz.has-image {
        padding: 0;
        border-style: solid;
        background: #fff;
    }
    .photo-upload-area.contact-photo-dz .upload-placeholder i {
        font-size: 1.75rem;
        margin-bottom: 0.35rem;
        display: block;
    }
    .photo-upload-area.contact-photo-dz .change-overlay {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        padding: 0.4rem;
        font-size: 0.8rem;
        display: none;
    }
    .photo-upload-area.contact-photo-dz.has-image:hover .change-overlay {
        display: block;
    }
    .photo-upload-area.contact-photo-dz .photo-preview-container {
        width: 100%;
        height: 100%;
        display: none;
    }
    .photo-upload-area.contact-photo-dz.has-image .photo-preview-container {
        display: block;
    }
    .photo-upload-area.contact-photo-dz .photo-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .contact-photo-remove-btn {
        position: absolute;
        top: 0.35rem;
        right: 0.35rem;
        z-index: 4;
        padding: 0.2rem 0.5rem;
        line-height: 1;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    }
    .photo-upload-area.contact-photo-dz:not(.has-image) .contact-photo-remove-btn {
        display: none !important;
    }
    .contact-live-preview {
        position: sticky;
        top: 0.75rem;
        border: 1px solid #e9ecef;
        border-radius: 0.85rem;
        background: #f8f9fa;
        padding: 1rem;
        max-height: calc(100vh - 6rem);
        overflow: auto;
        font-size: 0.9rem;
    }
    .contact-live-preview .pv-map iframe {
        width: 100% !important;
        min-height: 220px;
        border: 0;
    }
    .contact-live-preview h2 { font-size: 1.25rem; }
    .contact-live-preview h3 { font-size: 1.05rem; margin-top: 1rem; }
    .contact-live-preview .contact-pv-line .contact-pv-gap {
        display: inline-block;
        width: 0.4rem;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Контакты (филиалы и карты)</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="post" action="{{ route('admin.contacts-page.update') }}" enctype="multipart/form-data" id="contact-page-form">
            @csrf

            <div class="row">
                <div class="col-xl-7 col-lg-12 mb-4">
                    <div class="card">
                        <div class="card-header">Заголовки страницы</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="page_title">Заголовок (H1)</label>
                                <input type="text" name="page_title" id="page_title" class="form-control" required maxlength="255"
                                       value="{{ old('page_title', $settings->page_title) }}" data-pv-field="page_title">
                            </div>
                            <div class="mb-0">
                                <label class="form-label" for="page_subtitle">Подзаголовок под ним (необязательно)</label>
                                <input type="text" name="page_subtitle" id="page_subtitle" class="form-control" maxlength="500"
                                       value="{{ old('page_subtitle', $settings->page_subtitle) }}" data-pv-field="page_subtitle">
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Филиалы</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="contact-branch-add"><i class="fas fa-plus me-1"></i>Добавить филиал</button>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Карта — по желанию (код с <code>&lt;iframe&gt;</code> из Яндекса или Google). Текст под фото набирайте в редакторе (жирный, списки, ссылка). Фото 1 — основное; 2 и 3 — по желанию. На сайте колонки на всю ширину (1–3 фото).</p>
                            <ul class="list-unstyled mb-0" id="contact-branches-list">
                                @foreach($formBranches as $bi => $row)
                                    @php
                                        $slots = $row['photo_slots'] ?? array_pad($row['photos'] ?? [], 3, '');
                                        if (!is_array($slots)) { $slots = ['', '', '']; }
                                        $slots = array_values(array_pad($slots, 3, ''));
                                    @endphp
                                    <li class="contact-admin-branch" data-branch-item>
                                        <div class="contact-branch-head">
                                            <span class="btn btn-light btn-sm contact-branch-handle" title="Перетащить" style="cursor:grab"><i class="fas fa-grip-vertical"></i></span>
                                            <span class="badge bg-primary contact-branch-order" data-order>{{ $loop->iteration }}</span>
                                            <span class="fw-semibold contact-branch-title-preview" data-title-preview>{{ $row['title'] ?: 'Филиал' }}</span>
                                            <span class="badge bg-danger contact-branch-delete-badge" style="display:none">Будет удалён</span>
                                            <button type="button" class="btn btn-sm btn-outline-danger ms-auto" data-remove-branch><i class="fas fa-trash me-1"></i>Удалить</button>
                                        </div>
                                        <div class="contact-branch-body">
                                            <input type="hidden" name="branches[{{ $bi }}][id]" value="{{ $row['id'] ?? '' }}">
                                            <input type="hidden" name="branches[{{ $bi }}][pending_delete]" value="0" data-pending-delete>

                                            <div class="mb-3">
                                                <label class="form-label">Название филиала (заголовок)</label>
                                                <input type="text" class="form-control" name="branches[{{ $bi }}][title]" maxlength="255" required
                                                       value="{{ $row['title'] ?? '' }}" data-field="title">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Карта <span class="text-muted fw-normal small">(необязательно)</span></label>
                                                <div class="form-text small mb-1">Вставьте HTML целиком — обычно блок с <code>&lt;iframe&gt;</code> из конструктора карт.</div>
                                                <textarea class="form-control font-monospace small" name="branches[{{ $bi }}][map_embed_html]" rows="4" data-field="map_embed_html" placeholder="Оставьте пустым, если карта не нужна">{{ $row['map_embed_html'] ?? '' }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Текст под фото</label>
                                                <textarea class="d-none contact-details-source" name="branches[{{ $bi }}][details_text]" data-field="details_text">{{ $row['details_text'] ?? '' }}</textarea>
                                                <div class="contact-details-quill-wrap border rounded bg-white overflow-hidden">
                                                    <div class="contact-quill-editor" data-quill-host></div>
                                                </div>
                                            </div>
                                            <div class="row g-2 contact-photo-row">
                                                @for($pi = 0; $pi < 3; $pi++)
                                                    <div class="col-md-4">
                                                        <label class="form-label small mb-1">
                                                            @if($pi === 0)
                                                                Фото 1
                                                            @else
                                                                Фото {{ $pi + 1 }} <span class="text-muted fw-normal">(необязательно)</span>
                                                            @endif
                                                        </label>
                                                        <input type="hidden" name="branches[{{ $bi }}][existing_photos][{{ $pi }}]" value="{{ $slots[$pi] ?? '' }}" data-existing-photo>
                                                        @php
                                                            $__sp = $slots[$pi] ?? '';
                                                            $__url = '';
                                                            if ($__sp !== '') {
                                                                if (\Illuminate\Support\Str::startsWith($__sp, ['http://', 'https://'])) {
                                                                    $__url = $__sp;
                                                                } elseif (\Illuminate\Support\Str::startsWith($__sp, '/')) {
                                                                    $__url = url($__sp);
                                                                } else {
                                                                    $__url = \Illuminate\Support\Facades\Storage::disk('public')->url($__sp);
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="photo-upload-area contact-photo-dz mb-1 {{ $__url !== '' ? 'has-image' : '' }}" data-photo-area>
                                                            <button type="button" class="btn btn-sm btn-danger contact-photo-remove-btn" data-photo-remove title="Удалить фото" aria-label="Удалить фото">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                            <div class="upload-placeholder" data-photo-placeholder style="{{ $__url !== '' ? 'display:none' : '' }}">
                                                                <i class="fas fa-camera"></i>
                                                                <div>Нажмите или перетащите</div>
                                                                <small class="text-muted d-block">JPG, PNG, WebP до 8 МБ</small>
                                                            </div>
                                                            <div class="photo-preview-container" data-preview-container style="{{ $__url !== '' ? 'display:block' : 'display:none' }}">
                                                                <img src="{{ $__url }}" alt="" data-photo-preview>
                                                            </div>
                                                            <div class="change-overlay"><i class="fas fa-camera me-1"></i>Заменить фото</div>
                                                        </div>
                                                        <input type="file" class="d-none" name="branches[{{ $bi }}][photos][{{ $pi }}]" accept="image/jpeg,image/png,image/webp" data-photo-file>
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">Блок «Свяжитесь с нами»</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="contacts_heading">Заголовок блока</label>
                                <input type="text" name="contacts_heading" id="contacts_heading" class="form-control" maxlength="255"
                                       value="{{ old('contacts_heading', $settings->contacts_heading) }}" data-pv-field="contacts_heading">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="contacts_intro">Вступительный текст</label>
                                <textarea name="contacts_intro" id="contacts_intro" class="form-control" rows="2" data-pv-field="contacts_intro">{{ old('contacts_intro', $settings->contacts_intro) }}</textarea>
                            </div>
                            <div class="mb-3" id="contact-phones-block">
                                <label class="form-label">Телефоны</label>
                                <div id="contact-phones-list" class="d-flex flex-column gap-2">
                                    @foreach($phoneList as $pi => $pv)
                                        <div class="input-group contact-dynamic-row" data-contact-phone-row>
                                            <input type="text" class="form-control" name="phones[]" maxlength="120"
                                                   value="{{ $pv }}" data-contact-phone-input autocomplete="tel">
                                            <button type="button" class="btn btn-outline-danger" data-contact-phone-remove title="Удалить строку" aria-label="Удалить строку">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="contact-phone-add">
                                    <i class="fas fa-plus me-1"></i>Доп. телефон
                                </button>
                            </div>
                            <div class="mb-0" id="contact-emails-block">
                                <label class="form-label">E-mail</label>
                                <div id="contact-emails-list" class="d-flex flex-column gap-2">
                                    @foreach($emailList as $ei => $ev)
                                        <div class="input-group contact-dynamic-row" data-contact-email-row>
                                            <input type="text" class="form-control" name="emails[]" maxlength="255"
                                                   value="{{ $ev }}" data-contact-email-input autocomplete="email" inputmode="email">
                                            <button type="button" class="btn btn-outline-danger" data-contact-email-remove title="Удалить строку" aria-label="Удалить строку">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="contact-email-add">
                                    <i class="fas fa-plus me-1"></i>Доп. e-mail
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg mt-2"><i class="fas fa-save me-1"></i>Сохранить</button>
                </div>

                <div class="col-xl-5 col-lg-12 mb-4">
                    <p class="small text-muted text-uppercase fw-semibold mb-2">Как на сайте</p>
                    <div class="contact-live-preview" id="contact-live-preview">
                        <div class="text-center mb-3">
                            <h2 class="mb-2" id="pv-page-title">—</h2>
                            <p class="text-muted small mb-0" id="pv-page-subtitle" style="display:none"></p>
                        </div>
                        <div id="pv-branches"></div>
                        <hr class="my-3">
                        <h3 id="pv-contacts-heading" class="h6">—</h3>
                        <p class="small text-muted mb-2" id="pv-contacts-intro" style="display:none"></p>
                        <ul class="list-unstyled small mb-0" id="pv-phones"></ul>
                        <ul class="list-unstyled small mb-0" id="pv-emails"></ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<template id="contact-phone-row-template">
    <div class="input-group contact-dynamic-row" data-contact-phone-row>
        <input type="text" class="form-control" name="phones[]" maxlength="120" value="" data-contact-phone-input autocomplete="tel">
        <button type="button" class="btn btn-outline-danger" data-contact-phone-remove title="Удалить строку" aria-label="Удалить строку">
            <i class="fas fa-times"></i>
        </button>
    </div>
</template>
<template id="contact-email-row-template">
    <div class="input-group contact-dynamic-row" data-contact-email-row>
        <input type="text" class="form-control" name="emails[]" maxlength="255" value="" data-contact-email-input autocomplete="email" inputmode="email">
        <button type="button" class="btn btn-outline-danger" data-contact-email-remove title="Удалить строку" aria-label="Удалить строку">
            <i class="fas fa-times"></i>
        </button>
    </div>
</template>

<template id="contact-branch-template">
    <li class="contact-admin-branch" data-branch-item>
        <div class="contact-branch-head">
            <span class="btn btn-light btn-sm contact-branch-handle" title="Перетащить" style="cursor:grab"><i class="fas fa-grip-vertical"></i></span>
            <span class="badge bg-primary contact-branch-order" data-order>1</span>
            <span class="fw-semibold contact-branch-title-preview" data-title-preview>Новый филиал</span>
            <span class="badge bg-danger contact-branch-delete-badge" style="display:none">Будет удалён</span>
            <button type="button" class="btn btn-sm btn-outline-danger ms-auto" data-remove-branch><i class="fas fa-trash me-1"></i>Удалить</button>
        </div>
        <div class="contact-branch-body">
            <input type="hidden" name="branches[__I__][id]" value="">
            <input type="hidden" name="branches[__I__][pending_delete]" value="0" data-pending-delete>
            <div class="mb-3">
                <label class="form-label">Название филиала (заголовок)</label>
                <input type="text" class="form-control" name="branches[__I__][title]" maxlength="255" required value="" data-field="title">
            </div>
            <div class="mb-3">
                <label class="form-label">Карта <span class="text-muted fw-normal small">(необязательно)</span></label>
                <div class="form-text small mb-1">Вставьте HTML целиком — обычно блок с <code>&lt;iframe&gt;</code>.</div>
                <textarea class="form-control font-monospace small" name="branches[__I__][map_embed_html]" rows="4" data-field="map_embed_html" placeholder="Оставьте пустым, если карта не нужна"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Текст под фото</label>
                <textarea class="d-none contact-details-source" name="branches[__I__][details_text]" data-field="details_text"></textarea>
                <div class="contact-details-quill-wrap border rounded bg-white overflow-hidden">
                    <div class="contact-quill-editor" data-quill-host></div>
                </div>
            </div>
            <div class="row g-2 contact-photo-row">
                @for($pi = 0; $pi < 3; $pi++)
                    <div class="col-md-4">
                        <label class="form-label small mb-1">
                            @if($pi === 0)
                                Фото 1
                            @else
                                Фото {{ $pi + 1 }} <span class="text-muted fw-normal">(необязательно)</span>
                            @endif
                        </label>
                        <input type="hidden" name="branches[__I__][existing_photos][{{ $pi }}]" value="" data-existing-photo>
                        <div class="photo-upload-area contact-photo-dz mb-1" data-photo-area>
                            <button type="button" class="btn btn-sm btn-danger contact-photo-remove-btn" data-photo-remove title="Удалить фото" aria-label="Удалить фото">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="upload-placeholder" data-photo-placeholder>
                                <i class="fas fa-camera"></i>
                                <div>Нажмите или перетащите</div>
                                <small class="text-muted d-block">JPG, PNG, WebP до 8 МБ</small>
                            </div>
                            <div class="photo-preview-container" data-preview-container style="display:none">
                                <img src="" alt="" data-photo-preview>
                            </div>
                            <div class="change-overlay"><i class="fas fa-camera me-1"></i>Заменить фото</div>
                        </div>
                        <input type="file" class="d-none" name="branches[__I__][photos][{{ $pi }}]" accept="image/jpeg,image/png,image/webp" data-photo-file>
                    </div>
                @endfor
            </div>
        </div>
    </li>
</template>

<div class="modal fade" id="contactBranchDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Удалить филиал?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Филиал будет помечен на удаление. Чтобы отменить, нажмите «Вернуть» на карточке. Изменения вступят в силу после «Сохранить».</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="contactBranchDeleteConfirmBtn">Удалить</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('contact-branches-list');
    const template = document.getElementById('contact-branch-template');
    const addBtn = document.getElementById('contact-branch-add');
    const form = document.getElementById('contact-page-form');
    const deleteModalEl = document.getElementById('contactBranchDeleteModal');
    const deleteModal = deleteModalEl && typeof bootstrap !== 'undefined' ? new bootstrap.Modal(deleteModalEl) : null;
    let deleteTargetItem = null;

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function isEmptyDetailsHtml(html) {
        if (!html || !String(html).trim()) {
            return true;
        }
        const t = String(html).replace(/\s/g, '');
        return t === '<p><br></p>' || t === '<p></p>' || t === '<br>';
    }

    function getBranchDetailsHtml(item) {
        if (item.__quill) {
            return item.__quill.root.innerHTML;
        }
        const ta = item.querySelector('.contact-details-source');
        return ta ? ta.value : '';
    }

    function initQuillForBranch(item) {
        if (typeof Quill === 'undefined') {
            return;
        }
        const source = item.querySelector('.contact-details-source');
        const host = item.querySelector('[data-quill-host]');
        if (!source || !host || host.getAttribute('data-quill-inited') === '1') {
            return;
        }
        const quill = new Quill(host, {
            theme: 'snow',
            placeholder: 'Адрес, режим работы, подсказки для учеников…',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link'],
                    ['clean'],
                ],
            },
        });
        const initial = (source.value || '').trim();
        if (initial) {
            quill.clipboard.dangerouslyPasteHTML(initial);
        }
        quill.on('text-change', function () {
            source.value = quill.root.innerHTML;
            syncPreview();
        });
        item.__quill = quill;
        host.setAttribute('data-quill-inited', '1');
    }

    function syncPreview() {
        const t = document.getElementById('page_title');
        const st = document.getElementById('page_subtitle');
        const ch = document.getElementById('contacts_heading');
        const ci = document.getElementById('contacts_intro');
        const phonesList = document.getElementById('contact-phones-list');
        const emailsList = document.getElementById('contact-emails-list');

        document.getElementById('pv-page-title').textContent = (t && t.value.trim()) ? t.value.trim() : '—';
        const subEl = document.getElementById('pv-page-subtitle');
        if (st && st.value.trim()) {
            subEl.style.display = '';
            subEl.textContent = st.value.trim();
        } else {
            subEl.style.display = 'none';
        }

        document.getElementById('pv-contacts-heading').textContent = (ch && ch.value.trim()) ? ch.value.trim() : '—';
        const introEl = document.getElementById('pv-contacts-intro');
        if (ci && ci.value.trim()) {
            introEl.style.display = '';
            introEl.textContent = ci.value.trim();
        } else {
            introEl.style.display = 'none';
        }

        const phonesUl = document.getElementById('pv-phones');
        phonesUl.innerHTML = '';
        if (phonesList) {
            let phoneIdx = 0;
            phonesList.querySelectorAll('[data-contact-phone-input]').forEach(function (inp) {
                const line = inp.value.trim();
                if (!line) return;
                const label = phoneIdx === 0 ? 'Телефон' : 'Доп. телефон';
                phoneIdx += 1;
                const li = document.createElement('li');
                li.className = 'contact-pv-line';
                li.innerHTML = '<strong>' + escapeHtml(label) + ':</strong><span class="contact-pv-gap"></span><span class="text-primary">' + escapeHtml(line) + '</span>';
                phonesUl.appendChild(li);
            });
        }
        const emUl = document.getElementById('pv-emails');
        emUl.innerHTML = '';
        if (emailsList) {
            let emailIdx = 0;
            emailsList.querySelectorAll('[data-contact-email-input]').forEach(function (inp) {
                const line = inp.value.trim();
                if (!line) return;
                const label = emailIdx === 0 ? 'Электронная почта' : 'Доп. e-mail';
                emailIdx += 1;
                const li = document.createElement('li');
                li.className = 'contact-pv-line';
                li.innerHTML = '<strong>' + escapeHtml(label) + ':</strong><span class="contact-pv-gap"></span><span class="text-primary">' + escapeHtml(line) + '</span>';
                emUl.appendChild(li);
            });
        }

        const wrap = document.getElementById('pv-branches');
        wrap.innerHTML = '';
        list.querySelectorAll('[data-branch-item]').forEach(function (item) {
            if (item.querySelector('[data-pending-delete]').value === '1') return;
            const title = item.querySelector('[data-field="title"]');
            const map = item.querySelector('[data-field="map_embed_html"]');
            const detHtml = getBranchDetailsHtml(item);
            const block = document.createElement('div');
            block.className = 'mb-4 pb-3 border-bottom';
            if (map && map.value.trim()) {
                const m = document.createElement('div');
                m.className = 'pv-map rounded overflow-hidden mb-2';
                m.innerHTML = map.value.trim();
                block.appendChild(m);
            }
            const h = document.createElement('h3');
            h.className = 'h6';
            h.textContent = (title && title.value.trim()) ? title.value.trim() : 'Филиал';
            block.appendChild(h);

            const photoCols = item.querySelectorAll('.contact-photo-row .col-md-4');
            const urls = [];
            photoCols.forEach(function (col) {
                const fileInp = col.querySelector('[data-photo-file]');
                const img = col.querySelector('img[data-photo-preview]');
                if (fileInp && fileInp.files && fileInp.files[0]) {
                    urls.push(URL.createObjectURL(fileInp.files[0]));
                } else if (img) {
                    const u = img.getAttribute('src');
                    if (u) {
                        urls.push(u);
                    }
                }
            });
            const n = urls.length;
            if (n > 0) {
                const row = document.createElement('div');
                row.className = 'row g-2 mb-2';
                const colClass = n === 1 ? 'col-12' : (n === 2 ? 'col-6' : 'col-4');
                urls.forEach(function (src) {
                    const c = document.createElement('div');
                    c.className = colClass;
                    const im = document.createElement('img');
                    im.src = src;
                    im.className = 'img-fluid rounded w-100';
                    im.style.objectFit = 'cover';
                    im.style.maxHeight = '140px';
                    c.appendChild(im);
                    row.appendChild(c);
                });
                block.appendChild(row);
            }
            if (!isEmptyDetailsHtml(detHtml)) {
                const d = document.createElement('div');
                d.className = 'small contact-pv-details';
                d.innerHTML = detHtml.trim();
                block.appendChild(d);
            }
            wrap.appendChild(block);
        });
    }

    function reindexBranches() {
        let active = 0;
        list.querySelectorAll('[data-branch-item]').forEach(function (item, index) {
            const pending = item.querySelector('[data-pending-delete]');
            const isDel = pending && pending.value === '1';
            item.querySelectorAll('input, textarea, select').forEach(function (el) {
                if (!el.name) return;
                el.name = el.name.replace(/branches\[\d+\]/g, 'branches[' + index + ']');
            });
            const ord = item.querySelector('[data-order]');
            if (ord) {
                ord.textContent = isDel ? '×' : String(++active);
                ord.classList.toggle('bg-danger', isDel);
                ord.classList.toggle('bg-primary', !isDel);
            }
            item.querySelector('.contact-branch-delete-badge').style.display = isDel ? '' : 'none';
        });
    }

    function bindPhotoColumn(col, item) {
        const photoArea = col.querySelector('[data-photo-area]');
        const fileInp = col.querySelector('[data-photo-file]');
        const previewContainer = col.querySelector('[data-preview-container]');
        const previewImg = col.querySelector('[data-photo-preview]');
        const placeholder = col.querySelector('[data-photo-placeholder]');
        const hidden = col.querySelector('[data-existing-photo]');
        const removePhotoBtn = col.querySelector('[data-photo-remove]');
        const pendingIn = item.querySelector('[data-pending-delete]');

        if (!photoArea || !fileInp || !previewImg) {
            return;
        }

        function clearPhotoSlot() {
            if (hidden) {
                hidden.value = '';
            }
            fileInp.value = '';
            previewImg.removeAttribute('src');
            if (previewContainer) {
                previewContainer.style.display = 'none';
            }
            if (placeholder) {
                placeholder.style.display = '';
            }
            photoArea.classList.remove('has-image');
            syncPreview();
        }

        if (removePhotoBtn) {
            removePhotoBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (pendingIn && pendingIn.value === '1') {
                    return;
                }
                clearPhotoSlot();
            });
        }

        photoArea.addEventListener('click', function (e) {
            if (e.target.closest('[data-photo-remove]')) {
                return;
            }
            if (pendingIn && pendingIn.value === '1') {
                return;
            }
            fileInp.click();
        });

        ['dragenter', 'dragover'].forEach(function (ev) {
            photoArea.addEventListener(ev, function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (pendingIn && pendingIn.value === '1') {
                    return;
                }
                photoArea.style.borderColor = '#0d6efd';
            });
        });
        photoArea.addEventListener('dragleave', function () {
            photoArea.style.borderColor = '';
        });
        photoArea.addEventListener('drop', function (e) {
            e.preventDefault();
            photoArea.style.borderColor = '';
            if (pendingIn && pendingIn.value === '1') {
                return;
            }
            const f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
            if (!f || !f.type || f.type.indexOf('image') !== 0) {
                return;
            }
            const dt = new DataTransfer();
            dt.items.add(f);
            fileInp.files = dt.files;
            fileInp.dispatchEvent(new Event('change', { bubbles: true }));
        });

        fileInp.addEventListener('change', function () {
            const file = fileInp.files && fileInp.files[0];
            if (hidden) {
                hidden.value = '';
            }
            if (!file) {
                syncPreview();
                return;
            }
            const reader = new FileReader();
            reader.onload = function (event) {
                previewImg.src = event.target.result;
                if (previewContainer) {
                    previewContainer.style.display = 'block';
                }
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
                photoArea.classList.add('has-image');
                syncPreview();
            };
            reader.readAsDataURL(file);
        });
    }

    function bindBranch(item) {
        const titleIn = item.querySelector('[data-field="title"]');
        const prev = item.querySelector('[data-title-preview]');
        const removeBtn = item.querySelector('[data-remove-branch]');
        const pendingIn = item.querySelector('[data-pending-delete]');

        if (titleIn && prev) {
            titleIn.addEventListener('input', function () {
                prev.textContent = titleIn.value.trim() || 'Филиал';
                syncPreview();
            });
        }

        item.querySelectorAll('[data-field="map_embed_html"]').forEach(function (el) {
            el.addEventListener('input', syncPreview);
        });

        item.querySelectorAll('.contact-photo-row .col-md-4').forEach(function (col) {
            bindPhotoColumn(col, item);
        });

        initQuillForBranch(item);

        removeBtn.addEventListener('click', function () {
            const isMarked = pendingIn.value === '1';
            if (isMarked) {
                pendingIn.value = '0';
                item.classList.remove('is-pending-delete');
                removeBtn.classList.remove('btn-outline-secondary');
                removeBtn.classList.add('btn-outline-danger');
                removeBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Удалить';
                reindexBranches();
                syncPreview();
                return;
            }
            const idInput = item.querySelector('input[name$="[id]"]');
            const hasId = idInput && String(idInput.value).trim() !== '';
            if (!hasId) {
                item.remove();
                reindexBranches();
                syncPreview();
                return;
            }
            deleteTargetItem = item;
            if (deleteModal) deleteModal.show();
        });
    }

    document.getElementById('contactBranchDeleteConfirmBtn')?.addEventListener('click', function () {
        if (!deleteTargetItem) return;
        const item = deleteTargetItem;
        deleteTargetItem = null;
        const pendingIn = item.querySelector('[data-pending-delete]');
        const removeBtn = item.querySelector('[data-remove-branch]');
        pendingIn.value = '1';
        item.classList.add('is-pending-delete');
        removeBtn.classList.remove('btn-outline-danger');
        removeBtn.classList.add('btn-outline-secondary');
        removeBtn.innerHTML = '<i class="fas fa-undo me-1"></i>Вернуть';
        reindexBranches();
        syncPreview();
        if (deleteModal) deleteModal.hide();
    });

    list.querySelectorAll('[data-branch-item]').forEach(bindBranch);

    if (typeof Sortable !== 'undefined' && list) {
        Sortable.create(list, {
            handle: '.contact-branch-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function () {
                reindexBranches();
                syncPreview();
            }
        });
    }

    addBtn?.addEventListener('click', function () {
        const html = template.innerHTML.replace(/__I__/g, String(list.querySelectorAll('[data-branch-item]').length));
        const div = document.createElement('div');
        div.innerHTML = html.trim();
        const li = div.firstElementChild;
        list.appendChild(li);
        bindBranch(li);
        reindexBranches();
        syncPreview();
    });

    ['page_title', 'page_subtitle', 'contacts_heading', 'contacts_intro'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', syncPreview);
    });

    const phonesListEl = document.getElementById('contact-phones-list');
    const emailsListEl = document.getElementById('contact-emails-list');
    if (phonesListEl) {
        phonesListEl.addEventListener('input', syncPreview);
    }
    if (emailsListEl) {
        emailsListEl.addEventListener('input', syncPreview);
    }

    function refreshDynamicRemoveButtons(listSelector, rowSelector, removeAttr) {
        const list = document.querySelector(listSelector);
        if (!list) return;
        const rows = list.querySelectorAll(rowSelector);
        rows.forEach(function (row) {
            const btn = row.querySelector('[' + removeAttr + ']');
            if (btn) {
                btn.style.visibility = rows.length <= 1 ? 'hidden' : 'visible';
            }
        });
    }

    function bindContactDynamicLists() {
        const phoneTpl = document.getElementById('contact-phone-row-template');
        const emailTpl = document.getElementById('contact-email-row-template');

        document.getElementById('contact-phone-add')?.addEventListener('click', function () {
            if (!phoneTpl || !phonesListEl) return;
            phonesListEl.appendChild(phoneTpl.content.cloneNode(true));
            refreshDynamicRemoveButtons('#contact-phones-list', '[data-contact-phone-row]', 'data-contact-phone-remove');
            syncPreview();
        });

        document.getElementById('contact-email-add')?.addEventListener('click', function () {
            if (!emailTpl || !emailsListEl) return;
            emailsListEl.appendChild(emailTpl.content.cloneNode(true));
            refreshDynamicRemoveButtons('#contact-emails-list', '[data-contact-email-row]', 'data-contact-email-remove');
            syncPreview();
        });

        phonesListEl?.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-contact-phone-remove]');
            if (!btn) return;
            const row = btn.closest('[data-contact-phone-row]');
            if (!row || !phonesListEl) return;
            const rows = phonesListEl.querySelectorAll('[data-contact-phone-row]');
            if (rows.length <= 1) {
                const inp = row.querySelector('[data-contact-phone-input]');
                if (inp) inp.value = '';
                syncPreview();
                return;
            }
            row.remove();
            refreshDynamicRemoveButtons('#contact-phones-list', '[data-contact-phone-row]', 'data-contact-phone-remove');
            syncPreview();
        });

        emailsListEl?.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-contact-email-remove]');
            if (!btn) return;
            const row = btn.closest('[data-contact-email-row]');
            if (!row || !emailsListEl) return;
            const rows = emailsListEl.querySelectorAll('[data-contact-email-row]');
            if (rows.length <= 1) {
                const inp = row.querySelector('[data-contact-email-input]');
                if (inp) inp.value = '';
                syncPreview();
                return;
            }
            row.remove();
            refreshDynamicRemoveButtons('#contact-emails-list', '[data-contact-email-row]', 'data-contact-email-remove');
            syncPreview();
        });

        refreshDynamicRemoveButtons('#contact-phones-list', '[data-contact-phone-row]', 'data-contact-phone-remove');
        refreshDynamicRemoveButtons('#contact-emails-list', '[data-contact-email-row]', 'data-contact-email-remove');
    }

    bindContactDynamicLists();

    form.addEventListener('submit', function () {
        list.querySelectorAll('[data-branch-item]').forEach(function (item) {
            if (item.__quill) {
                const ta = item.querySelector('.contact-details-source');
                if (ta) {
                    ta.value = item.__quill.root.innerHTML;
                }
            }
        });
    });

    reindexBranches();
    syncPreview();
});
</script>
@endsection
