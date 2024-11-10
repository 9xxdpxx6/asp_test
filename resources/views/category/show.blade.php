@extends('layouts.admin')

@section('content')
    <div class="container-fluid mb-4 pt-4">
        <!-- Карточка с информацией о категории -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title">Просмотр категории #{{$category->id}}</h5>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Название:</h6>
                <p class="card-text">{{$category->name}}</p>

                <h6 class="card-subtitle text-muted mb-2">Описание:</h6>
                <p class="card-text">{!! $category->description !!}</p>

                <h6 class="card-subtitle text-muted mb-2">Длительность:</h6>
                <p class="card-text">{{$category->duration}} дней</p>
            </div>
        </div>

        <!-- Действия с категорией -->
        <div class="d-flex gap-2">
            <a href="{{route('category.edit', $category->id)}}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>

            <form action="{{route('category.delete', $category->id)}}" method="post" class="d-inline">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить категорию?')">
                    <i class="fas fa-trash-alt me-2"></i>Удалить
                </button>
            </form>

            <a href="{{route('category.index')}}" class="btn btn-secondary ms-auto">
                <i class="fas fa-arrow-left me-2"></i>Назад к списку
            </a>
        </div>
    </div>
@endsection
