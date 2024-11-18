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

            <div class="mb-3">
                <label for="content" class="form-label">Содержимое</label>
                <div id="editor-container" class="form-control" style="min-height: 200px;"></div>
                <input type="hidden" name="content" id="content" value="{{ old('content', $post->content) }}">
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Обновить</button>
            </div>

            <div class="mt-5">
                <h4>Текущие изображения:</h4>
                @if($post->images)
                    @foreach ($post->images as $image)
                        <img src="{{ asset($image->path) }}" class="img-fluid mb-2" alt="Post Image">
                    @endforeach
                @endif
            </div>


            <div class="mt-3">
                <a href="{{ route('post.index') }}" class="btn btn-secondary">Назад</a>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'script': 'sub' }, { 'script': 'super' }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image', 'video'],
                        ['clean']
                    ]
                }
            });

            quill.root.style.fontFamily = 'Cygre, sans-serif';

            let content = {!! json_encode($post->content) !!};
            if (content) {
                quill.clipboard.dangerouslyPasteHTML(content);
            }

            document.querySelector('form').onsubmit = function(event) {
                var content = quill.root.innerHTML;

                if (!content.trim()) {
                    event.preventDefault();
                    alert("Пожалуйста, введите содержимое.");
                    return;
                }

                document.getElementById('content').value = content;
            };
        });
    </script>
@endsection
