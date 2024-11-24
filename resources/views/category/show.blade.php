@extends('layouts.admin')

@section('style')
    <style>
        .category-description {
            text-align: left;
        }


        .category-description .ql-align-center {
            text-align: center;
        }

        .category-description .ql-align-right {
            text-align: right;
        }

        .category-description .ql-align-justify {
            text-align: justify;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid mb-4 pt-4">

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title my-1">Просмотр категории #{{$category->id}}</h5>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Название:</h6>
                <p class="card-text">
                    {{$category->name}}
                    @if ($category->icon)
                        <i class="{{ $category->icon }}"></i>
                    @endif
                </p>

                <h6 class="card-subtitle text-muted mb-2">Описание:</h6>

                <div class="category-description">
                    {!! $category->description !!}
                </div>

                <h6 class="card-subtitle text-muted mb-2">Длительность:</h6>
                <p class="card-text">{{$category->duration}} часов</p>
            </div>
        </div>


        <div class="d-flex flex-row gap-2">
            <a href="{{route('category.edit', $category->id)}}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>

            <form action="{{route('category.delete', $category->id)}}" method="post" class="d-inline">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить пост?')">
                    <i class="fas fa-trash-alt me-2"></i>Удалить
                </button>
            </form>

            <a href="{{route('category.index')}}" class="btn btn-secondary ms-auto">
                <i class="fas fa-arrow-left me-2"></i>Назад к списку
            </a>
        </div>
    </div>
@endsection
