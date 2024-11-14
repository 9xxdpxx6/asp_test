@extends('layouts.admin')

@section('title', 'Добавление категории')

@section('style')

@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Добавление категории</h1>
                </div>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-8">
                    <form action="{{ route('category.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="name">Название</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror" placeholder="Название"
                                   value="{{ old('name') }}">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="inputEmail">Описание:</label>
                            <div id="quill-editor" class="mb-3" style="height: 300px;"></div>
                            <textarea rows="3" class="mb-3 d-none" name="description" id="quill-editor-area"></textarea>
                            @error('description')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="price">Цена</label>
                            <input type="number" name="price" id="price"
                                   class="form-control @error('price') is-invalid @enderror" placeholder="Цена"
                                   value="{{ old('price') }}">
                            @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="duration">Длительность</label>
                            <input type="number" name="duration" id="duration"
                                   class="form-control @error('duration') is-invalid @enderror"
                                   placeholder="Длительность" value="{{ old('duration') }}">
                            @error('duration')
                            <div class="invalid-feedback">{{ $message }}</div>
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
    <!-- /.content -->
@endsection

@section('script')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('quill-editor-area')) {
                // Инициализация Quill с поддержкой изображений
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
