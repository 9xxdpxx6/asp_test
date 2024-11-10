@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <form action="{{ route('category.update', $category->id) }}" method="post">
            @csrf
            @method('patch')
            <div class="mb-3">
                <label for="name">Название</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Название">
            </div>
            <div class="form-group">
                <div id="editor-container"></div>
                <input type="hidden" name="description" id="description">
            </div>
            <div class="mb-3">
                <label for="price">Цена</label>
                <input type="number" name="price" step="0.01" class="form-control" id="price" placeholder="Цена">
            </div>
            <div class="mb-3">
                <label for="duration">Длительность</label>
                <input type="number" name="duration" step="1" class="form-control" id="duration" placeholder="Длительность">
            </div>
            <button type="submit" class="btn btn-primary">Обновить</button>
        </form>
    </div>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script>
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
        var description = {!! json_encode($category->description) !!};

        // Проверка на наличие контента
        if (description) {
            quill.clipboard.dangerouslyPasteHTML(description);
        }

        // Привязываем обработчик к отправке формы
        document.querySelector('form').onsubmit = function(event) {
            var description = quill.root.innerHTML;

            // Валидация: если контент пустой, предотвратить отправку формы
            if (!description.trim()) {
                event.preventDefault();
                alert("Пожалуйста, введите содержимое.");
                return;
            }

            // Записываем его в скрытое поле
            document.getElementById('description').value = description;
        };
    });
</script>
@endsection
