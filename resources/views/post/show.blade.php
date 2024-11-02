@extends('layouts.admin')

@section('content')
    <div class="container-fluid mb-4 pt-4">
        <!-- Карточка с информацией о посте -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title">Просмотр поста #{{$post->id}}</h5>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Заголовок:</h6>
                <p class="card-text">{{$post->title}}</p>

                <h6 class="card-subtitle text-muted mb-2">Slug:</h6>
                <p class="card-text">{{$post->slug}}</p>

                <h6 class="card-subtitle text-muted mb-2">Контент:</h6>
                <p class="card-text">{!! $post->content !!}</p>
            </div>
        </div>

        <!-- Действия с постом -->
        <div class="d-flex gap-2">
            <a href="{{route('post.edit', $post->id)}}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>

            <form action="{{route('post.delete', $post->id)}}" method="post" class="d-inline">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить пост?')">
                    <i class="fas fa-trash-alt me-2"></i>Удалить
                </button>
            </form>

            <a href="{{route('post.index')}}" class="btn btn-secondary ms-auto">
                <i class="fas fa-arrow-left me-2"></i>Назад к списку
            </a>
        </div>
    </div>
@endsection
