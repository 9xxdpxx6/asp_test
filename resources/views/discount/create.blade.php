@extends('layouts.admin')

@section('title', 'Добавление скидки')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Добавить скидку</h1>
            </div>
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-12 col-md-8">
                <form action="{{ route('discount.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Название</label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Название" value="{{ old('name', isset($discount) ? $discount->name : '') }}"
                               required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group">
                        <label for="slug">URL</label>
                        <input type="text" name="slug" id="slug" class="form-control"
                               value="{{ old('slug', Str::slug(old('name'))) }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="percentage">Процент скидки</label>
                        <input type="number" step="0.01" name="percentage" id="percentage"
                               class="form-control @error('percentage') is-invalid @enderror"
                               placeholder="Введите процент скидки" value="{{ old('percentage') }}" required>
                        @error('percentage')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content">Описание</label>
                        <div id="quill-editor" class="mb-3" style="height: 500px;"></div>
                        <textarea rows="3" class="d-none" name="description" id="quill-editor-area"></textarea>
                        @error('description')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Добавить">
                    </div>
                </form>
            </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
@endsection

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

@section('script')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('quill-editor-area')) {
            // Инициализация Quill с поддержкой изображений
            var editor = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{'header': [1, 2, 3, false]}], // Header levels
                        ['bold', 'italic', 'underline', 'strike'], // Basic formatting
                        [{'color': []}, {'background': []}], // Text color and background color
                        [{'script': 'sub'}, {'script': 'super'}], // Subscript/superscript
                        [{'list': 'ordered'}, {'list': 'bullet'}], // Lists
                        [{'align': []}], // Text alignment
                        ['link', 'image', 'video'], // Links, images, videos
                        ['clean'] // Remove formatting
                    ]
                }
            });

            var quillEditor = document.getElementById('quill-editor-area');
            const allowedImageFormats = ['image/jpeg', 'image/png', 'image/jpg'];

            // Переопределяем вставку изображения
            editor.getModule('toolbar').addHandler('image', function () {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = function () {
                    var file = input.files[0];

                    // Проверка формата изображения
                    if (file && allowedImageFormats.includes(file.type)) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            var range = editor.getSelection();
                            editor.insertEmbed(range.index, 'image', e.target.result);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        alert('Недопустимый формат изображения. Поддерживаются только JPEG и PNG.');
                    }
                };
            });

            // Маппинг русских букв на латиницу
            const ruToLat = {
                а: 'a',
                б: 'b',
                в: 'v',
                г: 'g',
                д: 'd',
                е: 'e',
                ё: 'yo',
                ж: 'zh',
                з: 'z',
                и: 'i',
                й: 'y',
                к: 'k',
                л: 'l',
                м: 'm',
                н: 'n',
                о: 'o',
                п: 'p',
                р: 'r',
                с: 's',
                т: 't',
                у: 'u',
                ф: 'f',
                х: 'h',
                ц: 'ts',
                ч: 'ch',
                ш: 'sh',
                щ: 'sch',
                ы: 'y',
                э: 'e',
                ю: 'yu',
                я: 'ya',
                ' ': '-',
                ь: '',
                ъ: ''
            };

            function rusToLat(str) {
                return str.split('').map(function (char) {
                    return ruToLat[char] || char;
                }).join('');
            }

            // Генерация slug на основе title
            document.getElementById('name').addEventListener('input', function () {
                var title = document.getElementById('name').value;

                // Преобразуем русский текст в латиницу
                var slug = rusToLat(title)
                    .toLowerCase() // Преобразуем в нижний регистр
                    .replace(/[^\w\s-]/g, '') // Удаляем все символы, кроме букв, цифр и пробела
                    .trim() // Убираем пробелы с концов
                    .replace(/\s+/g, '-') // Заменяем пробелы на дефисы
                    .replace(/-+/g, '-'); // Убираем лишние дефисы

                // Убираем дефисы в начале и в конце строки
                slug = slug.replace(/^-+/, '').replace(/-+$/, '');

                // Обновляем значение инпута slug
                document.getElementById('slug').value = slug;
            });

            // Сохранение HTML-контента в textarea
            editor.on('text-change', function () {
                quillEditor.value = editor.root.innerHTML;
            });

            // Загрузка данных из textarea в редактор при загрузке
            quillEditor.addEventListener('input', function () {
                editor.root.innerHTML = quillEditor.value;
            });

            document.querySelector('form').onsubmit = function(event) {
                var description = editor.root.innerHTML;

                if (!description.trim()) {
                    event.preventDefault();
                    alert("Пожалуйста, введите содержимое.");
                    return;
                }
                document.getElementById('description').value = description;
            };
        }
    });
</script>

<!-- /.content -->
@endsection
