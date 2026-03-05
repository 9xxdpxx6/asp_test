@extends('layouts.admin')

@section('title', 'Категории на главной')

@section('style')
<style>
    /* ===== Featured grid (РєР°Рє РЅР° СЃР°Р№С‚Рµ, РіРѕСЂРёР·РѕРЅС‚Р°Р»СЊРЅРѕ) ===== */
    .featured-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(220px, 1fr));
        gap: 1rem;
        min-height: 260px;
        border: 2px dashed #198754;
        border-radius: 0.75rem;
        padding: 1rem;
        background: #f0faf4;
        transition: border-color 0.2s, background 0.2s;
        align-items: stretch;
    }

    .featured-card {
        background: #fff;
        border: 1px solid #9ad9b4;
        border-radius: 0.9rem;
        text-align: left;
        padding: 0.75rem;
        cursor: grab;
        user-select: none;
        transition: box-shadow 0.15s, transform 0.15s, border-color 0.15s;
        position: relative;
        display: flex;
        flex-direction: column;
        min-height: 300px;
    }
    .featured-card:active { cursor: grabbing; }
    .featured-card:hover {
        box-shadow: 0 10px 24px rgba(25, 135, 84, 0.14);
        border-color: #53b67d;
        transform: translateY(-1px);
    }
    .featured-card .fc-order {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 28px;
        height: 28px;
        background: #198754;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        z-index: 2;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    .featured-card .fc-drag {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.95);
        color: #6c757d;
        font-size: 0.8rem;
        z-index: 2;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }
    .featured-card .fc-icon {
        width: 86px;
        height: 86px;
        margin: 0 auto;
        background: rgba(13, 110, 253, 0.1);
        border-radius: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #0d6efd;
    }
    .featured-card .fc-media {
        width: 100%;
        aspect-ratio: 4 / 3;
        border-radius: 0.65rem;
        background: #e9ecef;
        overflow: hidden;
        margin-bottom: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .featured-card .fc-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .featured-card .fc-meta {
        margin-top: auto;
        text-align: center;
        padding: 0.15rem 0.2rem 0.1rem;
    }
    .featured-card .fc-name {
        font-weight: 600;
        font-size: 1rem;
        line-height: 1.25;
        margin-bottom: 0.45rem;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        min-height: 2.5em;
    }
    .featured-card .fc-price {
        font-size: 1.28rem;
        font-weight: 700;
        color: #0d6efd;
        line-height: 1.1;
    }

    @media (max-width: 1200px) {
        .featured-grid {
            grid-template-columns: repeat(2, minmax(220px, 1fr));
        }
    }

    @media (max-width: 767px) {
        .featured-grid {
            grid-template-columns: 1fr;
        }
        .featured-card {
            min-height: 280px;
        }
    }

    /* ===== Source pool (РІСЃРµ РєР°С‚РµРіРѕСЂРёРё) ===== */
    .source-pool {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.75rem;
        min-height: 80px;
        border: 2px dashed #dee2e6;
        border-radius: 0.75rem;
        padding: 0.75rem;
        background: #f8f9fa;
    }

    .source-card {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.6rem 0.75rem;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        cursor: grab;
        user-select: none;
        transition: box-shadow 0.15s, border-color 0.15s;
    }
    .source-card:active { cursor: grabbing; }
    .source-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-color: #adb5bd;
    }
    .source-card .sc-icon {
        width: 38px;
        height: 38px;
        border-radius: 0.375rem;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #6c757d;
        flex-shrink: 0;
        overflow: hidden;
    }
    .source-card .sc-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .source-card .sc-info { flex: 1; min-width: 0; }
    .source-card .sc-name {
        font-weight: 600;
        font-size: 0.85rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .source-card .sc-price {
        font-size: 0.8rem;
        color: #0d6efd;
        font-weight: 500;
    }

    .sortable-ghost { opacity: 0.35; }

    .placeholder-text {
        grid-column: 1 / -1;
        text-align: center;
        padding: 2.5rem 1rem;
        color: #6c757d;
        font-size: 0.95rem;
    }

    .section-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Категории на главной</h1>
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

        <div class="card">
            <div class="card-body">
                <p class="text-muted mb-4">
                    Перетащите до 3 категорий в блок «На главной». Порядок слева направо определяет порядок на сайте.
                </p>

                {{-- РќР° РіР»Р°РІРЅРѕР№ вЂ” РіРѕСЂРёР·РѕРЅС‚Р°Р»СЊРЅР°СЏ СЃРµС‚РєР° --}}
                <div class="section-label">На главной <span class="text-muted fw-normal">(макс. 3)</span></div>
                <div class="featured-grid" id="featured-categories">
                    @foreach($featured as $category)
                        @php
                            $categoryImageUrl = $category->image
                                ? (\Illuminate\Support\Str::startsWith($category->image, ['http://', 'https://'])
                                    ? $category->image
                                    : url('storage/' . $category->image))
                                : null;
                        @endphp
                        <div class="featured-card"
                             data-id="{{ $category->id }}"
                             data-name="{{ $category->name }}"
                             data-price="{{ number_format($category->price, 0, ',', ' ') }}"
                             data-image="{{ $categoryImageUrl ?: '' }}"
                             data-icon="{{ $category->icon ?: 'fas fa-car' }}">
                            <div class="fc-order"></div>
                            <div class="fc-drag" title="Перетащить"><i class="fas fa-grip-lines"></i></div>
                            <div class="fc-media">
                                @if($categoryImageUrl)
                                    <img src="{{ $categoryImageUrl }}" alt="" class="fc-img">
                                @elseif($category->icon)
                                    <div class="fc-icon"><i class="{{ $category->icon }}"></i></div>
                                @else
                                    <div class="fc-icon"><i class="fas fa-car"></i></div>
                                @endif
                            </div>
                            <div class="fc-meta">
                                <div class="fc-name">{{ $category->name }}</div>
                                <div class="fc-price">{{ number_format($category->price, 0, ',', ' ') }} &#8381;</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="featured-placeholder" class="placeholder-text" style="{{ $featured->count() > 0 ? 'display:none' : '' }}">
                    <i class="fas fa-arrow-down me-1"></i>Перетащите сюда категории из списка ниже
                </div>

                {{-- Р’СЃРµ РєР°С‚РµРіРѕСЂРёРё вЂ” РєРѕРјРїР°РєС‚РЅР°СЏ СЃРµС‚РєР° --}}
                <div class="section-label mt-4">Все категории</div>
                <div class="source-pool" id="all-categories">
                    @foreach($allCategories as $category)
                        @if(is_null($category->featured_order))
                            @php
                                $categoryImageUrl = $category->image
                                    ? (\Illuminate\Support\Str::startsWith($category->image, ['http://', 'https://'])
                                        ? $category->image
                                        : url('storage/' . $category->image))
                                    : '';
                            @endphp
                            <div class="source-card" data-id="{{ $category->id }}"
                                 data-name="{{ $category->name }}"
                                 data-price="{{ number_format($category->price, 0, ',', ' ') }}"
                                 data-image="{{ $categoryImageUrl }}"
                                 data-icon="{{ $category->icon ?: 'fas fa-car' }}">
                                <div class="sc-icon">
                                    @if($categoryImageUrl)
                                        <img src="{{ $categoryImageUrl }}" alt="">
                                    @elseif($category->icon)
                                        <i class="{{ $category->icon }}"></i>
                                    @else
                                        <i class="fas fa-car"></i>
                                    @endif
                                </div>
                                <div class="sc-info">
                                    <div class="sc-name">{{ $category->name }}</div>
                                    <div class="sc-price">{{ number_format($category->price, 0, ',', ' ') }} &#8381;</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <form method="POST" action="{{ route('admin.featured-categories.update') }}" id="featured-form" class="mt-4">
                    @csrf
                    <div id="hidden-inputs"></div>
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-1"></i>Сохранить
                    </button>
                </form>
            </div>
        </div>

    </div>
</section>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const allPool = document.getElementById('all-categories');
    const featuredPool = document.getElementById('featured-categories');
    const placeholder = document.getElementById('featured-placeholder');
    const hiddenInputs = document.getElementById('hidden-inputs');
    const MAX_FEATURED = 3;

    // When a source-card is dragged into featured, convert it to featured-card
    function convertToFeatured(el) {
        if (el.classList.contains('source-card')) {
            const id = el.dataset.id;
            const name = el.dataset.name || el.querySelector('.sc-name')?.textContent || '';
            const price = el.dataset.price || el.querySelector('.sc-price')?.textContent || '';
            const image = el.dataset.image || '';
            const icon = el.dataset.icon || 'fas fa-car';

            const card = document.createElement('div');
            card.className = 'featured-card';
            card.dataset.id = id;
            card.dataset.name = name;
            card.dataset.price = price;
            card.dataset.image = image;
            card.dataset.icon = icon;

            let visual = '';
            if (image) {
                visual = '<img src="' + image + '" alt="" class="fc-img">';
            } else {
                visual = '<div class="fc-icon"><i class="' + icon + '"></i></div>';
            }

            card.innerHTML = '<div class="fc-order"></div>' +
                '<div class="fc-drag" title="Перетащить"><i class="fas fa-grip-lines"></i></div>' +
                '<div class="fc-media">' + visual + '</div>' +
                '<div class="fc-meta"><div class="fc-name">' + name + '</div>' +
                '<div class="fc-price">' + price + ' &#8381;</div></div>';

            el.replaceWith(card);
            return card;
        }
        return el;
    }

    // When a featured-card is dragged back to all, convert to source-card
    function convertToSource(el) {
        if (el.classList.contains('featured-card')) {
            const id = el.dataset.id;
            const name = el.dataset.name || el.querySelector('.fc-name')?.textContent || '';
            const price = el.dataset.price || el.querySelector('.fc-price')?.textContent || '';
            const image = el.dataset.image || el.querySelector('.fc-img')?.getAttribute('src') || '';
            const icon = el.dataset.icon || el.querySelector('.fc-icon i')?.className || 'fas fa-car';

            const card = document.createElement('div');
            card.className = 'source-card';
            card.dataset.id = id;
            card.dataset.name = name;
            card.dataset.price = price;
            card.dataset.image = image;
            card.dataset.icon = icon;

            let iconHtml = '';
            if (image) {
                iconHtml = '<div class="sc-icon"><img src="' + image + '" alt=""></div>';
            } else {
                iconHtml = '<div class="sc-icon"><i class="' + icon + '"></i></div>';
            }

            card.innerHTML = iconHtml +
                '<div class="sc-info"><div class="sc-name">' + name + '</div>' +
                '<div class="sc-price">' + price + ' &#8381;</div></div>';

            el.replaceWith(card);
            return card;
        }
        return el;
    }

    function updatePlaceholder() {
        const count = featuredPool.querySelectorAll('.featured-card').length;
        placeholder.style.display = count === 0 ? '' : 'none';
    }

    function updateNumbers() {
        featuredPool.querySelectorAll('.featured-card').forEach((card, idx) => {
            const badge = card.querySelector('.fc-order');
            if (badge) badge.textContent = idx + 1;
        });
    }

    function syncHiddenInputs() {
        hiddenInputs.innerHTML = '';
        featuredPool.querySelectorAll('.featured-card').forEach(card => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'featured_ids[]';
            input.value = card.dataset.id;
            hiddenInputs.appendChild(input);
        });
    }

    function refresh() {
        updatePlaceholder();
        updateNumbers();
        syncHiddenInputs();
    }

    // Sortable for "All categories"
    Sortable.create(allPool, {
        group: {
            name: 'categories',
            pull: function() {
                return featuredPool.querySelectorAll('.featured-card').length < MAX_FEATURED;
            },
            put: true,
        },
        animation: 150,
        ghostClass: 'sortable-ghost',
        onAdd: function(evt) {
            convertToSource(evt.item);
            refresh();
        },
        onRemove: refresh,
        onSort: refresh,
    });

    // Sortable for "Featured"
    Sortable.create(featuredPool, {
        group: {
            name: 'categories',
            pull: true,
            put: function() {
                return featuredPool.querySelectorAll('.featured-card').length < MAX_FEATURED;
            },
        },
        animation: 150,
        ghostClass: 'sortable-ghost',
        onAdd: function(evt) {
            convertToFeatured(evt.item);
            refresh();
        },
        onRemove: refresh,
        onSort: refresh,
    });

    // Store data-attributes on initial featured cards
    featuredPool.querySelectorAll('.featured-card').forEach(card => {
        if (!card.dataset.name) card.dataset.name = card.querySelector('.fc-name')?.textContent || '';
        if (!card.dataset.price) card.dataset.price = card.querySelector('.fc-price')?.textContent?.replace(/\s*[^\d\s.,-]+$/, '') || '';
        if (!card.dataset.image) card.dataset.image = card.querySelector('.fc-img')?.getAttribute('src') || '';
        if (!card.dataset.icon) card.dataset.icon = card.querySelector('.fc-icon i')?.className || 'fas fa-car';
    });

    refresh();
});
</script>
@endsection


