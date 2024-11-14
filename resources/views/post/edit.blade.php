@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Редактировать пост</h1>

    <form action="{{ route('post.update', $post->id) }}" method="POST" id="edit-post-form" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="title">Название</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $post->title) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label" for="inputEmail">Body:</label>
            <div id="quill-editor" class="mb-3" style="height: 300px;"></div>
            <textarea rows="3" class="mb-3 d-none" name="content" id="quill-editor-area"></textarea>

            @error('body')
            <span class="text-danger">{{ $message }}</span>
            @endif
        </div>



        <input type="submit" class="btn btn-primary" value="Сохранить изменения">
        <a href="{{ route('post.index') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script>

    // Получаем содержимое из data-атрибута
    document.addEventListener('DOMContentLoaded', function() {
        let quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }], // Header levels
                    ['bold', 'italic', 'underline', 'strike'], // Basic formatting
                    [{ 'color': [] }, { 'background': [] }], // Text color and background color
                    [{ 'script': 'sub' }, { 'script': 'super' }], // Subscript/superscript
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }], // Lists
                    [{ 'align': [] }], // Text alignment
                    ['link', 'image', 'video'], // Links, images, videos
                    ['clean'] // Remove formatting
                ]
            }
        });

        const allowedImageFormats = ['image/jpeg', 'image/png', 'image/jpg'];

        // Переопределяем вставку изображения
        quill.getModule('toolbar').addHandler('image', function() {
            let input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = function() {
                let file = input.files[0];

                // Проверка формата изображения
                if (file && allowedImageFormats.includes(file.type)) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        let range = quill.getSelection();
                        quill.insertEmbed(range.index, 'image', e.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    alert('Недопустимый формат изображения. Поддерживаются только JPEG и PNG.');
                }
            };
        });

        let content = {!! json_encode($post->content) !!};

        // Проверка на наличие контента
        if (content) {
            quill.clipboard.dangerouslyPasteHTML(content);
        }

        document.getElementById('edit-post-form').onsubmit = function(event) {
            let content = quill.root.innerHTML;

            if (!content.trim()) {
                event.preventDefault();
                alert("Пожалуйста, введите содержимое.");
                return;
            }

            document.getElementById('quill-editor-area').value = content;
        };
    });
</script>
@endsection
