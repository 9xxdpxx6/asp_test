@extends('layouts.admin')

@section('style')
    <style>
        .discount-description img {
            max-width: 100%;
            height: auto;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid mb-4 pt-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title my-1">Просмотр программы лояльности #{{ $discount->id }}</h5>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Название</h6>
                <p class="card-text">{{ $discount->name }}</p>

                @if($discount->excerpt)
                    <h6 class="card-subtitle text-muted mb-2 mt-3">Краткий текст</h6>
                    <p class="card-text">{{ $discount->excerpt }}</p>
                @endif

                @if($discount->percentage !== null)
                    <h6 class="card-subtitle text-muted mb-2 mt-3">Процент</h6>
                    <p class="card-text">{{ $discount->percentage }}%</p>
                @endif

                <h6 class="card-subtitle text-muted mb-2 mt-3">Контент</h6>
                <p class="card-text text-muted">
                    Страница собрана из блоков ({{ $discount->blocks->count() }}).
                    Для правок откройте <a href="{{ route('discount.edit', $discount->id) }}">редактор</a>.
                </p>

                @if($discount->description)
                    <h6 class="card-subtitle text-muted mb-2 mt-3">Старое описание (если есть)</h6>
                    <div class="discount-description">{!! $discount->description !!}</div>
                @endif
            </div>
        </div>

        <div class="d-flex flex-row gap-2">
            <a href="{{ route('discount.edit', $discount->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>

            <form id="deleteDiscountForm" action="{{ route('discount.delete', $discount->id) }}" method="post" class="d-inline">
                @csrf
                @method('delete')
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDiscountModal">
                    <i class="fas fa-trash-alt me-2"></i>Удалить
                </button>
            </form>

            <a href="{{ route('discount.index') }}" class="btn btn-secondary ms-auto">
                <i class="fas fa-arrow-left me-2"></i>Назад к списку
            </a>
        </div>
    </div>

    @include('admin.partials.confirm-delete-modal', [
        'modalId' => 'deleteDiscountModal',
        'formId' => 'deleteDiscountForm',
        'title' => 'Удалить программу лояльности?',
        'message' => 'Вы уверены, что хотите удалить эту программу?',
    ])
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const images = document.querySelectorAll('.discount-description img');
            images.forEach(img => {
                img.onload = () => {
                    const containerWidth = img.parentElement.offsetWidth;
                    if (img.naturalWidth > containerWidth) {
                        img.style.maxWidth = '100%';
                        img.style.height = 'auto';
                    }
                };
            });
        });
    </script>
@endsection
