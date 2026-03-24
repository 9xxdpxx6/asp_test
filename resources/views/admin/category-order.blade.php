@extends('layouts.admin')

@section('title', 'Порядок категорий для цен')

@section('style')
<style>
    .order-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 0.85rem;
    }

    .order-item {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.75rem;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: grab;
        user-select: none;
        transition: box-shadow 0.15s, border-color 0.15s;
    }

    .order-item:active {
        cursor: grabbing;
    }

    .order-item:hover {
        border-color: #adb5bd;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .order-badge {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #0d6efd;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .order-media {
        width: 42px;
        height: 42px;
        border-radius: 0.4rem;
        overflow: hidden;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        flex-shrink: 0;
    }

    .order-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .order-info {
        min-width: 0;
        flex: 1;
    }

    .order-name {
        font-weight: 600;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .order-price {
        font-size: 0.85rem;
        color: #0d6efd;
        font-weight: 500;
    }

    .order-handle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sortable-ghost {
        opacity: 0.35;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Порядок категорий для страницы цен</h1>
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
                    Перетащите карточки в нужном порядке. Этот порядок будет использован на странице "Цены".
                </p>

                @if($categories->isNotEmpty())
                    <form method="POST" action="{{ route('admin.category-order.update') }}" id="category-order-form">
                        @csrf
                        <div class="order-list" id="category-order-list">
                            @foreach($categories as $category)
                                @php
                                    $categoryImageUrl = $category->image
                                        ? (\Illuminate\Support\Str::startsWith($category->image, ['http://', 'https://'])
                                            ? $category->image
                                            : url('storage/' . $category->image))
                                        : null;
                                @endphp

                                <div class="order-item" data-id="{{ $category->id }}">
                                    <div class="order-badge"></div>
                                    <div class="order-media">
                                        @if($categoryImageUrl)
                                            <img src="{{ $categoryImageUrl }}" alt="{{ $category->name }}">
                                        @elseif($category->icon)
                                            <i class="{{ $category->icon }}"></i>
                                        @else
                                            <i class="fas fa-car"></i>
                                        @endif
                                    </div>
                                    <div class="order-info">
                                        <div class="order-name">{{ $category->name }}</div>
                                        <div class="order-price">{{ number_format($category->price, 0, ',', ' ') }} &#8381;</div>
                                    </div>
                                    <div class="order-handle" title="Перетащить">
                                        <i class="fas fa-grip-lines"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div id="category-order-inputs"></div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-1"></i>Сохранить порядок
                            </button>
                            <a href="{{ route('category.index') }}" class="btn btn-secondary btn-lg">
                                Назад к категориям
                            </a>
                        </div>
                    </form>
                @else
                    <div class="alert alert-light border mb-0">
                        Категории пока не добавлены.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const list = document.getElementById('category-order-list');
    const hiddenInputs = document.getElementById('category-order-inputs');

    if (!list || !hiddenInputs) {
        return;
    }

    function syncOrderData() {
        hiddenInputs.innerHTML = '';

        list.querySelectorAll('.order-item').forEach((item, index) => {
            const badge = item.querySelector('.order-badge');
            if (badge) {
                badge.textContent = String(index + 1);
            }

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'category_ids[]';
            input.value = item.dataset.id;
            hiddenInputs.appendChild(input);
        });
    }

    Sortable.create(list, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        onSort: syncOrderData,
    });

    syncOrderData();
});
</script>
@endsection
