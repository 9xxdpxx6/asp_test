@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Редактирование поста</h2>
        <form action="{{ route('post.update', $post->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('patch')

            <div class="mb-3">
                <label for="title" class="form-label">Название</label>
                <input type="text" name="title" id="title" class="form-control @error('slug') is-invalid @enderror"
                       placeholder="Название" value="{{ old('title', $post->title) }}" required>
                @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">URL</label>
                <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $post->slug) }}" readonly>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Содержимое</label>
                <div id="editor-container" class="form-control" style="min-height: 500px;"></div>
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
            // Инициализация Quill
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

            // Проверка содержимого перед отправкой формы
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
