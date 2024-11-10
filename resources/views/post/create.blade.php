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
                <form action="{{ route('post.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Название</label>
                        <input type="text" name="title" id="title"
                               class="form-control @error('title') is-invalid @enderror" placeholder="Название"
                               value="{{ old('title') }}">
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="inputEmail">Body:</label>
                        <div id="quill-editor" class="mb-3" style="height: 300px;"></div>
                        <textarea rows="3" class="mb-3 d-none" name="content" id="quill-editor-area"></textarea>

                        @error('body')
                        <span class="text-danger">{{ $message }}</span>
                        @endif
                    </div>


                    <!-- Кнопка для отправки формы -->
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Добавить">
                    </div>
            </div>
            </form>
        </div>
    </div>
    <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>


<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('quill-editor-area')) {
            // Инициализация Quill с поддержкой изображений
            var editor = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'] // Добавление кнопки для вставки изображения
                    ]
                }
            });

            var quillEditor = document.getElementById('quill-editor-area');

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

<!-- /.content -->
@endsection
