@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Редактирование поста</h2>
        <form action="{{ route('post.update', $post->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div class="mb-3">
                <input type="text" name="title" class="form-control" placeholder="Название" value="{{ old('title', $post->title) }}" required>
            </div>

            <div ref="dropzone" class="mb-3 btn d-block p-5 bg-dark text-center text-light">
                Загрузить изображения
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Обновить</button>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Содержимое</label>
                <textarea name="content" id="taContent" class="form-control" cols="30" rows="10">{{ old('content', $post->content) }}</textarea>
{{--                <vue-editor--}}
{{--                    useCustomImageHandler--}}
{{--                    @image-added="handleImageAdded"--}}
{{--                    v-model="content">--}}
{{--                </vue-editor>--}}
            </div>

            <div class="mt-5">
                <h4>Текущие изображения:</h4>
                @foreach ($post->images as $image)
                    <img src="{{ asset($image->path) }}" class="img-fluid mb-2" alt="Post Image">
                @endforeach
            </div>

            <div class="mt-3">
                <a href="{{ route('post.index') }}" class="btn btn-secondary">Назад</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        import {Dropzone} from "dropzone";
        // Инициализация Dropzone
        document.addEventListener('DOMContentLoaded', function() {
            const dropzone = new Dropzone(document.querySelector('[ref="dropzone"]'), {
                url: '/api/posts/images', // Измените на свой URL для загрузки изображений
                autoProcessQueue: false,
                addRemoveLinks: true,
                success: function(file, response) {
                    // Обработка успешной загрузки
                },
                error: function(file, response) {
                    // Обработка ошибок
                }
            });
        });
    </script>
@endsection
