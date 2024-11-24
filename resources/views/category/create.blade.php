@extends('layouts.admin')

@section('title', 'Добавление категории')

@section('style')
    <!-- Подключаем стили для Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- Подключаем стили для Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Стили для корректного отображения выпадающего списка и выравнивания -->
    <style>
        .select2-container {
            width: 100% !important; /* Убедимся, что выпадающий список не выходит за пределы родительского контейнера */
        }
        .select2-container--default .select2-results__options {
            max-height: 200px; /* Ограничиваем высоту выпадающего списка */
            overflow-y: auto; /* Добавляем прокрутку, если элементов больше */
        }
        .select2-container--open {
            z-index: 1050; /* Устанавливаем высокий z-index, чтобы список выпадал поверх других элементов */
        }
        .form-group {
            margin-bottom: 1.5rem; /* Отступы между элементами формы */
        }

        /* Стили для выравнивания иконки и текста в поле выбора */
        .select2-selection__rendered {
            display: flex;
            align-items: center;
        }
        .select2-selection__rendered i {
            margin-right: 8px; /* Отступ между иконкой и текстом */
        }

        .select2-selection__rendered span {
            display: inline-block;
        }
    </style>
@endsection

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Добавление категории</h1>
                </div>
            </div>
        </div>
    </div>

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

                        <!-- Выпадающий список с иконками (под полем "Название") -->
                        <div class="form-group">
                            <label for="icon">Выберите иконку</label>
                            <select name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror">
                                <option value="fa fa-motorcycle" data-icon="fa fa-motorcycle">Категория "А"</option>
                                <option value="fa fa-car" data-icon="fa fa-car">Категория "B"</option>
                                <option value="fa fa-truck" data-icon="fa fa-truck">Категория "C"</option>
                                <option value="fa fa-bus" data-icon="fa fa-bus">Категория "D"</option>
                                <option value="fa fa-exchange-alt" data-icon="fa fa-exchange-alt">
                                    Категория "А" ⬌ Категория "B"
                                </option>
                            </select>
                            @error('icon')
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

@endsection

@section('script')
    <!-- Подключаем библиотеку Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Инициализация Quill для текстового редактора
            let quill = new Quill('#quill-editor', {
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

            var description = '';

            if (description) {
                quill.clipboard.dangerouslyPasteHTML(description);
            }

            document.querySelector('form').onsubmit = function(event) {
                var description = quill.root.innerHTML;
                console.log(description);

                if (!description.trim()) {
                    event.preventDefault();
                    alert("Пожалуйста, введите содержимое.");
                    return;
                }

                document.getElementById('quill-editor-area').value = description;
            };

            // Инициализация Select2 для выпадающего списка с иконками
            $('#icon').select2({
                width: '100%',  // Устанавливаем ширину списка
                minimumResultsForSearch: Infinity,  // Отключаем поиск в выпадающем списке
                templateResult: formatIcon,  // Функция для отображения иконок и текста в списке
                templateSelection: formatIcon // Функция для отображения иконки и текста в выбранном элементе
            });

            // Функция для отображения иконки и текста
            function formatIcon(state) {
                if (!state.id) {
                    return state.text; // Возвращаем только текст для пустых элементов
                }
                var $state = $('<span><i class="' + state.element.dataset.icon + '"></i> ' + state.text + '</span>');
                return $state;
            }
        });
    </script>
@endsection
