@php
    $sizeOptions = [
        '3/4' => '3/4 (75%)',
        '2/3' => '2/3 (67%)',
        '1/2' => '1/2 (50%)',
        '1/3' => '1/3 (33%)',
        '1/4' => '1/4 (25%)',
    ];
@endphp
<div class="promo-item {{ $promo['pending_delete'] ? 'is-pending-delete' : '' }}" data-item>
    <div class="promo-head">
        <button type="button" class="promo-handle" data-handle title="Перетащить">
            <i class="fas fa-up-down"></i>
        </button>
        <div class="promo-order" data-order></div>
        <div class="promo-title-preview" data-title-preview>
            {{ $promo['title'] ? $promo['title'] : 'Акция без заголовка' }}
        </div>
        <span class="badge bg-danger promo-delete-status">Будет удалена после сохранения</span>
        <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" role="switch" data-active-switch {{ $promo['is_active'] ? 'checked' : '' }}>
            <label class="form-check-label small">Показывать</label>
        </div>
        <button type="button" class="btn btn-outline-danger btn-sm" data-remove>
            <i class="fas fa-trash me-1"></i>Удалить
        </button>
    </div>

    <div class="promo-body">
        <input type="hidden" data-field="id" value="{{ $promo['id'] }}">
        <input type="hidden" data-field="existing_image" value="{{ $promo['existing_image'] }}">
        <input type="hidden" data-field="pending_delete" value="{{ $promo['pending_delete'] ? 1 : 0 }}">
        <input type="hidden" data-field="image_on_left" value="{{ $promo['image_on_left'] ? 1 : 0 }}">
        <input type="hidden" data-field="is_active" value="{{ $promo['is_active'] ? 1 : 0 }}">

        <div class="promo-toolbar">
            <div>
                <label class="form-label fw-bold">Размер фото <span class="text-muted fw-normal">(авто по фото)</span></label>
                <select class="form-select form-select-sm" data-field="image_size">
                    @foreach($sizeOptions as $val => $label)
                        <option value="{{ $val }}" {{ $promo['image_size'] === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div data-layout-wrap>
                <label class="form-label fw-bold d-block">Расположение фото <span class="text-muted fw-normal">(для «сверху/снизу»)</span></label>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle-layout
                        title="Поменять местами фото и текст">
                    <i class="fas fa-arrows-alt-h me-1"></i><span data-layout-label>{{ $promo['image_on_left'] ? 'Фото слева' : 'Фото справа' }}</span>
                </button>
            </div>
            <div>
                <label class="form-label fw-bold">Позиция на странице</label>
                <select class="form-select form-select-sm" data-field="placement">
                    <option value="top" {{ $promo['placement'] === 'top' ? 'selected' : '' }}>Сверху (над ценами)</option>
                    <option value="bottom" {{ $promo['placement'] === 'bottom' ? 'selected' : '' }}>Снизу (под ценами)</option>
                    <option value="left" {{ $promo['placement'] === 'left' ? 'selected' : '' }}>Слева от категорий</option>
                    <option value="right" {{ $promo['placement'] === 'right' ? 'selected' : '' }}>Справа от категорий</option>
                </select>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
              <div class="promo-edit-col">
                <label class="form-label">Фото <span class="text-muted fw-normal">(одно, JPG/PNG до 5 МБ)</span></label>
                <div class="promo-photo-area {{ $promo['image_url'] ? 'has-image' : '' }}" data-photo-area>
                    <div class="upload-placeholder" {!! $promo['image_url'] ? 'style="display:none"' : '' !!}>
                        <i class="fas fa-camera"></i>
                        <div>Нажмите для загрузки</div>
                        <small class="text-muted">JPG, JPEG, PNG до 5 МБ</small>
                    </div>
                    <div class="promo-preview-container" data-preview-container {!! $promo['image_url'] ? 'style="display:block"' : '' !!}>
                        <img src="{{ $promo['image_url'] }}" alt="Предпросмотр" data-preview-image>
                    </div>
                    <div class="change-overlay">
                        <i class="fas fa-camera me-1"></i>Заменить фото
                    </div>
                </div>
                <input type="file" class="form-control" data-field="image"
                       accept=".jpg,.jpeg,.png,image/jpeg,image/png" style="display: none;">

                <div class="mb-3 mt-3">
                    <label class="form-label">Заголовок <span class="text-muted fw-normal">(необязательно)</span></label>
                    <input type="text" class="form-control" data-field="title" maxlength="255"
                           value="{{ $promo['title'] }}" placeholder="Например: Дорогу молодым">
                </div>
                <div>
                    <label class="form-label">Описание <span class="text-muted fw-normal">(необязательно)</span></label>
                    <textarea class="form-control" rows="4" data-field="description" maxlength="2000"
                              placeholder="Текст под заголовком. Абзацы разделяйте пустой строкой.">{{ $promo['description'] }}</textarea>
                </div>
              </div>
            </div>

            <div class="col-lg-8">
                <label class="form-label">Превью на странице «Цены» <span class="text-muted fw-normal">(приблизительно)</span></label>
                <div class="promo-pv" data-preview-panel></div>
            </div>
        </div>
    </div>
</div>
