@extends('layouts.admin')

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
    <div class="container-fluid">
        <form action="{{ route('category.update', $category->id) }}" method="post">
            @csrf
            @method('patch')
            <div class="mb-3">
                <label for="name">Название</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Название" value="{{ old('name', $category->name) }}" required>
            </div>

            <!-- Выпадающий список с категориями (под полем "Название") -->
            <div class="form-group">
                <label for="icon">Выберите иконку</label>
                <select name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror">
                    <option value="fa fa-motorcycle" data-icon="fa fa-motorcycle" {{ old('icon', $category->icon) == 'fa fa-motorcycle' ? 'selected' : '' }}>
                        Категория "А"
                    </option>
                    <option value="fa fa-car" data-icon="fa fa-car" {{ old('icon', $category->icon) == 'fa fa-car' ? 'selected' : '' }}>
                        Категория "B"
                    </option>
                    <option value="fa fa-truck" data-icon="fa fa-truck" {{ old('icon', $category->icon) == 'fa fa-truck' ? 'selected' : '' }}>
                        Категория "C"
                    </option>
                    <option value="fa fa-bus" data-icon="fa fa-bus" {{ old('icon', $category->icon) == 'fa fa-bus' ? 'selected' : '' }}>
                        Категория "D"
                    </option>
                    <option value="fa fa-exchange-alt" data-icon="fa fa-exchange-alt" {{ old('icon', $category->icon) == 'fa fa-exchange-alt' ? 'selected' : '' }}>
                        Категория "А" ⬌ Категория "B"
                    </option>
                </select>
                @error('icon')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div id="editor-container"></div>
                <input type="hidden" name="description" id="description">
            </div>

            <div class="mb-3">
                <label for="price">Цена</label>
                <input type="number" name="price" step="0.01" class="form-control" id="price" placeholder="Цена" value="{{ old('price', $category->price) }}" required>
            </div>

            <div class="mb-3">
                <label for="duration">Длительность</label>
                <input type="number" name="duration" step="1" class="form-control" id="duration" placeholder="Длительность" value="{{ old('duration', $category->duration) }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Обновить</button>
        </form>
    </div>
@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Инициализация Quill для текстового редактора
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

            // Загружаем начальное содержимое в редактор
            let description = {!! json_encode($category->description) !!};
            if (description) {
                quill.clipboard.dangerouslyPasteHTML(description);
            }

            // Обработка отправки формы
            document.querySelector('form').onsubmit = function(event) {
                var description = quill.root.innerHTML;

                // Запрещаем отправку формы, если содержимое пустое
                if (!description.trim()) {
                    event.preventDefault();
                    alert("Пожалуйста, введите содержимое.");
                    return;
                }

                // Сохраняем содержимое редактора в скрытое поле
                document.getElementById('description').value = description;
            };

            // Инициализация Select2 для выпадающего списка с категориями
            $('#icon').select2({
                width: '100%',  // Устанавливаем ширину списка
                minimumResultsForSearch: Infinity,  // Отключаем поиск в выпадающем списке
                templateResult: formatCategory,  // Функция для отображения иконок и текста
                templateSelection: formatCategory // Функция для отображения выбранного элемента
            });

            // Функция для отображения иконки в элементе списка
            function formatCategory(state) {
                if (!state.id) {
                    return state.text; // Возвращаем только текст для пустых элементов
                }
                var $state = $('<span><i class="' + state.element.dataset.icon + '"></i> ' + state.text + '</span>');
                return $state;
            }
        });
    </script>
@endsection
