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

        <div class="form-group">
            <div id="editor-container"></div>
            <input type="hidden" name="content" id="content">
        </div>



        <input type="submit" class="btn btn-primary" value="Сохранить изменения">
        <a href="{{ route('post.index') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script>

    // Получаем содержимое из data-атрибута
    document.addEventListener('DOMContentLoaded', function() {
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    ['link', 'image'],
                    [{'list': 'ordered'}, {'list': 'bullet'}]
                ]
            }
        });

        // Получаем содержимое из переменной PHP
        var content = {!! json_encode($post->content) !!};

        // Проверка на наличие контента
        if (content) {
            quill.clipboard.dangerouslyPasteHTML(content);
        }

        // При отправке формы
        document.getElementById('edit-post-form').onsubmit = function(event) {
            var content = quill.root.innerHTML;

            // Валидация: если контент пустой, предотвратить отправку формы
            if (!content.trim()) {
                event.preventDefault();
                alert("Пожалуйста, введите содержимое.");
                return;
            }

            // Записываем его в скрытое поле
            document.getElementById('content').value = content;
        };
    });



</script>
@endsection
