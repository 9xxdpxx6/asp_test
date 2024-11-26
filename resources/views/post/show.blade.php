@extends('layouts.admin')

@section('style')
    <style>
        .post-content img {
            max-width: 100%;
            height: auto;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid mb-4 pt-4">
        <!-- Карточка с информацией о посте -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title my-1">Просмотр поста #{{$post->id}}</h5>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Заголовок:</h6>
                <p class="card-text">{{$post->title}}</p>

                <h6 class="card-subtitle text-muted mb-2">Slug:</h6>
                <p class="card-text">{{$post->slug}}</p>

                <h6 class="card-subtitle text-muted mb-2">Контент:</h6>
                <div class="post-content">{!! $post->content !!}</div>
            </div>
        </div>

        <!-- Действия с постом -->
        <div class="d-flex flex-row gap-2">
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const images = document.querySelectorAll('.post-content img');
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
