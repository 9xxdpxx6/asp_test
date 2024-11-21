@extends('layouts.admin')

@section('style')

@endsection

@section('content')
    <div class="container-fluid mb-4 pt-4">

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title my-1">Просмотр категории #{{$callback->id}}</h5>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Название:</h6>
                <p class="card-text">{{$callback->name}}</p>

                <h6 class="card-subtitle text-muted mb-2">Описание:</h6>

                <div class="category-description">
                    {!! $callback->description !!}
                </div>

                <h6 class="card-subtitle text-muted mb-2">Длительность:</h6>
                <p class="card-text">{{$callback->duration}} дней</p>
            </div>
        </div>


        <div class="d-flex flex-row gap-2">
            <a href="{{route('category.index')}}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Назад к списку
            </a>
            <a href="{{route('category.edit', $callback->id)}}" class="btn btn-warning ms-auto">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>

            <form action="{{route('category.delete', $callback->id)}}" method="post" class="d-inline">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить категорию?')">
                    <i class="fas fa-trash-alt me-2"></i>Удалить
                </button>
            </form>
        </div>
    </div>
@endsection
