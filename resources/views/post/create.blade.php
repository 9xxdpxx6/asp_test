@extends('layouts.admin')

@section('title', 'Добавление постов')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Добавить пост</h1>
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
                <form action="{{ route('post.store') }}" id="postCreate" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="title">Название</label>
                        <input type="text" name="title" id="title"
                               class="form-control @error('slug') is-invalid @enderror"
                               placeholder="Название" value="{{ old('title') }}">
                        @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug">URL</label>
                        <input type="text" name="slug" id="slug" class="form-control"
                               value="{{ old('slug', Str::slug(old('title'))) }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="content">Контент</label>
                        <div id="quill-editor" class="mb-3" style="height: 500px;"></div>
                        <textarea rows="3" class="d-none" name="content" id="quill-editor-area"></textarea>
                        @error('content')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Добавить">
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@section('script')
<script>

    document.addEventListener('DOMContentLoaded', function () {
        var quill = new Quill('#quill-editor', {
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

        quill.root.style.fontFamily = 'Cygre, sans-serif';

        let content = {!! json_encode(old('content')) !!};
        if (content) {
            quill.clipboard.dangerouslyPasteHTML(content);
        }

        // Маппинг русских букв на латиницу
        const ruToLat = {
            а: 'a', б: 'b', в: 'v', г: 'g', д: 'd', е: 'e', ё: 'yo', ж: 'zh', з: 'z', и: 'i', й: 'y', к: 'k',
            л: 'l', м: 'm', н: 'n', о: 'o', п: 'p', р: 'r', с: 's', т: 't', у: 'u', ф: 'f', х: 'h', ц: 'ts', ч: 'ch',
            ш: 'sh', щ: 'sch', ы: 'y', э: 'e', ю: 'yu', я: 'ya', ' ': '-', ь: '', ъ: '',
            А: 'A', Б: 'B', В: 'V', Г: 'G', Д: 'D', Е: 'E', Ё: 'Yo', Ж: 'Zh', З: 'Z', И: 'I', Й: 'Y', К: 'K',
            Л: 'L', М: 'M', Н: 'N', О: 'O', П: 'P', Р: 'R', С: 'S', Т: 'T', У: 'U', Ф: 'F', Х: 'H', Ц: 'Ts', Ч: 'Ch',
            Ш: 'Sh', Щ: 'Sch', Ы: 'Y', Э: 'E', Ю: 'Yu', Я: 'Ya'
        };

        function rusToLat(str) {
            return str.split('').map(function(char) {
                return ruToLat[char] || char;
            }).join('');
        }

        // Генерация slug на основе title
        document.getElementById('title').addEventListener('input', function () {
            var title = document.getElementById('title').value;

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

        document.querySelector('#postCreate').onsubmit = function(event) {
            let content = quill.root.innerHTML;

            if (content.trim().length < 70) {
                event.preventDefault();
                alert("Пожалуйста, введите реальное содержимое.");
                return;
            }

            document.getElementById('quill-editor-area').value = content;
        };
    });
</script>


<!-- /.content -->
@endsection
