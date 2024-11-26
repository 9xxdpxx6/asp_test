@extends('layouts.admin')

@section('title', 'Добавление льготной программы')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Добавить льготную программу</h1>
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
                    <form action="{{ route('discount.store') }}" id="discountCreate" method="POST" enctype="multipart/form-data">
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
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <footer class="footer">
        <div class="container-fluid">
        </div>
    </footer>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            const form = document.querySelector('#discountCreate');
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
