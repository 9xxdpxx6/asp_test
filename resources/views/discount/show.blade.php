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
        <!-- Карточка с информацией о льготной программе -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title my-1">Просмотр программы лояльности #{{$discount->id}}</h5>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Название:</h6>
                <p class="card-text">{{$discount->name}}</p>

                <h6 class="card-subtitle text-muted mb-2">Процент:</h6>
                <p class="card-text">{{$discount->percentage}}%</p>

                <h6 class="card-subtitle text-muted mb-2">Описание:</h6>
                <div class="discount-description">{!! $discount->description !!}</div>
            </div>
        </div>

        <!-- Действия с программой -->
        <div class="d-flex flex-row gap-2">
            <a href="{{route('discount.edit', $discount->id)}}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>

            <form action="{{route('discount.delete', $discount->id)}}" method="post" class="d-inline">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить льготную программу?')">
                    <i class="fas fa-trash-alt me-2"></i>Удалить
                </button>
            </form>

            <a href="{{route('discount.index')}}" class="btn btn-secondary ms-auto">
                <i class="fas fa-arrow-left me-2"></i>Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('scripts')
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
                }
            });
        });
    </script>
@endsection
