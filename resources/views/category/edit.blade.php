@extends('layouts.admin')

@section('style')
    <link rel="stylesheet" href="{{ asset('adminpanel/plugins/select2/css/select2.min.css') }}">

    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px); /* Высота как у стандартного поля Bootstrap */
            padding: 0.375rem 0.75rem; /* Внутренний отступ, чтобы текст не прилипал */
            border-radius: 0.375rem; /* Радиус скругления */
            border: 1px solid #ced4da; /* Стандартный бордер Bootstrap */
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            display: flex; /* Flexbox для выравнивания содержимого */
            align-items: center; /* Центрирование по вертикали */
            line-height: 1.5; /* Установка высоты строки для текста */
            color: #495057; /* Цвет текста */
            font-size: 1rem; /* Размер шрифта */
            height: 100%; /* Занимает всю высоту контейнера */
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered img,
        .select2-container--default .select2-selection--single .select2-selection__rendered i {
            margin-right: 8px; /* Отступ между иконкой и текстом */
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 10px;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            height: calc(1.5em + 0.75rem + 2px); /* Высота поля ввода поиска */
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }

        .select2-container--bootstrap4 .select2-selection__rendered {
            color: #495057; /* Тот же цвет текста, что и в стандартных полях Bootstrap */
        }

        .select2-container--bootstrap4 .select2-selection__arrow {
            border-left-color: #ced4da; /* Цвет стрелки в выпадающем списке */
        }

        /* Ошибки с border */
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #80bdff; /* Цвет бордера при фокусе */
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25); /* Эффект при фокусе */
        }

        .select2-container--default .select2-selection--single.is-invalid {
            border-color: #dc3545; /* Красный бордер для ошибки */
            background-color: #f8d7da;
        }

        .select2-container--default .select2-selection--single.is-invalid .select2-selection__rendered {
            color: #721c24; /* Цвет текста при ошибке */
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

            <!-- Выпадающий список с иконками Font Awesome -->
            <div class="form-group">
                <label for="icon">Выберите иконку</label>
                <select name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror">
                    <!-- Опции будут добавляться через JavaScript -->
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
    <script src="{{ asset('adminpanel/plugins/select2/js/select2.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Список иконок FontAwesome
            const icons = [
                "fas fa-car",
                "fas fa-motorcycle",
                "fas fa-bicycle",
                "fas fa-car-alt",
                "fas fa-taxi",
                "fas fa-bus",
                "fas fa-truck",
                "fas fa-truck-loading",
                "fas fa-truck-monster",
                "fas fa-car-crash",
                "fas fa-gas-pump",
                "fas fa-wrench",
                "fas fa-tools",
                "fas fa-road",
                "fas fa-route",
                "fas fa-map-marked",
                "fas fa-flag-checkered",
                "fas fa-sign-in-alt",
                "fas fa-street-view",
                "fas fa-location-arrow",
                "fas fa-clipboard-check",
                "fas fa-helmet-safety",
                "fas fa-car-battery",
                "fas fa-battery-full",
                "fas fa-anchor"
            ];


            // Заполняем селект всеми иконками
            const iconSelect = document.getElementById('icon');
            icons.forEach(iconClass => {
                const option = document.createElement('option');
                option.value = iconClass;
                option.innerHTML = `<i class="${iconClass}"></i> ${iconClass.replace('fas fa-', '').replace('-', ' ')}`;
                iconSelect.appendChild(option);
            });

            // Инициализация Select2 для выпадающего списка с иконками
            $('#icon').select2({
                width: '100%',
                minimumResultsForSearch: 10,
                templateResult: formatCategory,
                templateSelection: formatCategory
            });

            // Функция для отображения иконки в элементе списка
            function formatCategory(state) {
                if (!state.id) {
                    return state.text; // Возвращаем только текст для пустых элементов
                }
                var $state = $('<span><i class="' + state.element.value + '"></i> ' + state.text + '</span>');
                return $state;
            }

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
        });
    </script>
@endsection
