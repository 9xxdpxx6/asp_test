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
                            <div id="editor-container" class="form-control" style="min-height: 200px;"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: [
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'header': 1 }, { 'header': 2 }],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['image'], // Adds image button to toolbar
                        ],
                        handlers: {
                            image: imageHandler // Custom handler for images
                        }
                    }
                }
            });

            // Image handler
            function imageHandler() {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = async () => {
                    const file = input.files[0];
                    const formData = new FormData();
                    formData.append('image', file);

                    const response = await fetch('{{ route('post.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await response.json();
                    if (data.url) {
                        const range = quill.getSelection();
                        quill.insertEmbed(range.index, 'image', data.url);
                    }
                };
            }

            document.querySelector('form').onsubmit = function() {
                document.querySelector('#content').value = quill.root.innerHTML;
            };
        });
    </script>
@endsection

