@extends('layouts.admin')

@section('style')

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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let quill = new Quill('#editor-container', {
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

            // Enforce Cygre font without font selection
            quill.root.style.fontFamily = 'Cygre, sans-serif';

            // Load initial content into the editor
            let description = {!! json_encode($category->description) !!};
            if (description) {
                quill.clipboard.dangerouslyPasteHTML(description);
            }

            // Form submission
            document.querySelector('form').onsubmit = function(event) {
                var description = quill.root.innerHTML;

                // Prevent form submission if content is empty
                if (!description.trim()) {
                    event.preventDefault();
                    alert("Пожалуйста, введите содержимое.");
                    return;
                }

                // Save editor content to hidden input
                document.getElementById('description').value = description;
            };
        });
    </script>
@endsection
