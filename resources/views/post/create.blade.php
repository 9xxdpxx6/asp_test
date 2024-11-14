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
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-8">
                    <form action="{{ route('post.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="title">Название</label>
                            <input type="text" name="title" id="title"
                                   class="form-control @error('title') is-invalid @enderror" placeholder="Название"
                                   value="{{ old('title') }}">
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="preview_path">Путь</label>
                            <textarea name="preview_path" id="preview_path"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Путь">{{ old('preview_path') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editor">Контент</label>
                            <div id="quill-editor" class="form-control" style="min-height: 200px;"></div>
                            <input type="hidden" name="content" id="content">
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

@section('script')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('quill-editor-area')) {
            // Инициализация Quill с поддержкой изображений
            let editor = new Quill('#quill-editor', {
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

            let quillEditor = document.getElementById('quill-editor-area');
            const allowedImageFormats = ['image/jpeg', 'image/png', 'image/jpg'];

            // Переопределяем вставку изображения
            editor.getModule('toolbar').addHandler('image', function() {
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
                            let range = editor.getSelection();
                            editor.insertEmbed(range.index, 'image', e.target.result);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        alert('Недопустимый формат изображения. Поддерживаются только JPEG и PNG.');
                    }
                };
            });

            // Сохранение HTML-контента в textarea
            editor.on('text-change', function() {
                quillEditor.value = editor.root.innerHTML;
            });

            // Загрузка данных из textarea в редактор при загрузке
            quillEditor.addEventListener('input', function() {
                editor.root.innerHTML = quillEditor.value;
            });
        }
    });
</script>
@endsection

