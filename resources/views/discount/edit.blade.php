@extends('layouts.admin')

@section('title', 'Редактирование льготной программы')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Редактирование программы лояльности</h2>
        <form action="{{ route('discount.update', $discount->id) }}" id="discountEdit" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- Название скидки -->
            <div class="form-group">
                <label for="name">Название</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Введите название скидки"
                       value="{{ old('name', $discount->name) }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- URL -->
            <div class="form-group mt-3">
                <label for="slug">URL</label>
                <input type="text" name="slug" id="slug"
                       class="form-control @error('slug') is-invalid @enderror"
                       value="{{ old('slug', $discount->slug) }}" readonly>
                @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Процент скидки -->
            <div class="form-group mt-3">
                <label for="percentage">Процент скидки (%)</label>
                <input type="number" step="0.01" name="percentage" id="percentage"
                       class="form-control @error('percentage') is-invalid @enderror"
                       placeholder="Введите процент скидки"
                       value="{{ old('percentage', $discount->percentage) }}" required>
                @error('percentage')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Описание скидки -->
            <div class="form-group mt-4">
                <label for="description">Описание</label>
                <div id="quill-editor" class="form-control" style="min-height: 200px;"></div>
                <textarea name="description" id="quill-editor-area" class="d-none">{{ old('description', $discount->description) }}</textarea>
                @error('description')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>


            <!-- Кнопки -->
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">Сохранить изменения</button> <!-- Синяя кнопка -->
                <a href="{{ route('discount.index') }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Инициализация Quill-редактора
            var quill = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'image', 'clean']
                    ]
                }
            });


            var quillEditorArea = document.getElementById('quill-editor-area');
            if (quillEditorArea.value) {
                quill.root.innerHTML = quillEditorArea.value;
            }


            document.querySelector('form').onsubmit = function () {
                quillEditorArea.value = quill.root.innerHTML;
            };


            const ruToLatMap = {
                а: 'a', б: 'b', в: 'v', г: 'g', д: 'd', е: 'e', ё: 'yo', ж: 'zh',
                з: 'z', и: 'i', й: 'y', к: 'k', л: 'l', м: 'm', н: 'n', о: 'o',
                п: 'p', р: 'r', с: 's', т: 't', у: 'u', ф: 'f', х: 'h', ц: 'ts',
                ч: 'ch', ш: 'sh', щ: 'sch', ы: 'y', э: 'e', ю: 'yu', я: 'ya',
                ' ': '-', ь: '', ъ: '', '-': '-',
                А: 'A', Б: 'B', В: 'V', Г: 'G', Д: 'D', Е: 'E', Ё: 'Yo', Ж: 'Zh',
                З: 'Z', И: 'I', Й: 'Y', К: 'K', Л: 'L', М: 'M', Н: 'N', О: 'O',
                П: 'P', Р: 'R', С: 'S', Т: 'T', У: 'U', Ф: 'F', Х: 'H', Ц: 'Ts',
                Ч: 'Ch', Ш: 'Sh', Щ: 'Sch', Ы: 'Y', Э: 'E', Ю: 'Yu', Я: 'Ya'
            };

            function ruToLat(str) {
                return str.split('').map(char => ruToLatMap[char] || char).join('')
                    .toLowerCase()
                    .replace(/[^a-z0-9\-]+/g, '')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            document.getElementById('name').addEventListener('input', function () {
                const slugInput = document.getElementById('slug');
                slugInput.value = ruToLat(this.value);
            });

            const form = document.querySelector('#discountEdit');
            form.onsubmit = function (event) {
                const description = quill.root.innerHTML.trim();
                if (description.length < 70) {
                    event.preventDefault();
                    alert('Пожалуйста, введите реальное описание.');
                    return;
                }
                document.getElementById('quill-editor-area').value = description;
            };
        });
    </script>
@endsection
