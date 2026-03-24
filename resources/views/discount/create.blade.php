@extends('layouts.admin')

@section('title', 'Новая программа лояльности')

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .form-group { margin-bottom: 1.5rem; }
        .photo-upload-area {
            border: 2px dashed #ced4da;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #fafafa;
            position: relative;
            overflow: hidden;
        }
        .photo-upload-area:hover {
            border-color: #0d6efd;
            background: #f0f7ff;
        }
        .photo-upload-area.has-image {
            padding: 0;
            border-style: solid;
        }
        .photo-upload-area .upload-placeholder { color: #6c757d; }
        .photo-upload-area .upload-placeholder i {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        .photo-preview-container {
            width: 100%;
            max-width: 400px;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            border-radius: 0.5rem;
            margin: 0 auto;
        }
        .photo-preview-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-upload-area .change-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.6);
            color: #fff;
            padding: 0.5rem;
            font-size: 0.85rem;
            display: none;
        }
        .photo-upload-area.has-image:hover .change-overlay { display: block; }
    </style>
@endsection

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Новая программа лояльности</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('discount.store') }}" id="discountForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Основные данные</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">Название</label>
                                    <input type="text" name="name" id="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="slug">URL</label>
                                    <input type="text" name="slug" id="slug" class="form-control"
                                           value="{{ old('slug', '') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="percentage">Процент скидки (%)</label>
                                    <input type="number" step="0.01" name="percentage" id="percentage"
                                           class="form-control @error('percentage') is-invalid @enderror"
                                           placeholder="Необязательно"
                                           value="{{ old('percentage') }}">
                                    @error('percentage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="excerpt">Краткий текст для карточек и блока на главной</label>
                            <textarea name="excerpt" id="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror"
                                      placeholder="Подзаголовок на карточке">{{ old('excerpt') }}</textarea>
                            @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Превью (картинка карточки)</label>
                                    <div class="photo-upload-area" id="photoUploadArea" onclick="document.getElementById('previewInput').click()">
                                        <div class="upload-placeholder">
                                            <i class="fas fa-camera"></i>
                                            <div>Нажмите для загрузки</div>
                                            <small class="text-muted">JPEG, PNG, WebP до 5 МБ</small>
                                        </div>
                                        <div class="photo-preview-container" style="display:none">
                                            <img id="photoPreview" src="" alt="Превью">
                                        </div>
                                        <div class="change-overlay">
                                            <i class="fas fa-camera me-1"></i>Заменить
                                        </div>
                                    </div>
                                    <input type="file" name="preview" id="previewInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
                                    @error('preview')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('category.partials.block-editor')

                <div class="form-group mt-3 mb-5">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Создать
                    </button>
                    <a href="{{ route('discount.index') }}" class="btn btn-secondary btn-lg ms-2">Отмена</a>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('script')
    @include('category.partials.block-editor-scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('previewInput');
            const uploadArea = document.getElementById('photoUploadArea');
            const preview = document.getElementById('photoPreview');
            const previewContainer = uploadArea.querySelector('.photo-preview-container');
            const placeholder = uploadArea.querySelector('.upload-placeholder');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        preview.src = ev.target.result;
                        previewContainer.style.display = 'block';
                        placeholder.style.display = 'none';
                        uploadArea.classList.add('has-image');
                    };
                    reader.readAsDataURL(file);
                }
            });

            const ruToLat = {
                а:'a',б:'b',в:'v',г:'g',д:'d',е:'e',ё:'yo',ж:'zh',з:'z',и:'i',й:'y',к:'k',
                л:'l',м:'m',н:'n',о:'o',п:'p',р:'r',с:'s',т:'t',у:'u',ф:'f',х:'h',ц:'ts',ч:'ch',
                ш:'sh',щ:'sch',ы:'y',э:'e',ю:'yu',я:'ya',' ':'-',ь:'',ъ:'',
                А:'A',Б:'B',В:'V',Г:'G',Д:'D',Е:'E',Ё:'Yo',Ж:'Zh',З:'Z',И:'I',Й:'Y',К:'K',
                Л:'L',М:'M',Н:'N',О:'O',П:'P',Р:'R',С:'S',Т:'T',У:'U',Ф:'F',Х:'H',Ц:'Ts',Ч:'Ch',
                Ш:'Sh',Щ:'Sch',Ы:'Y',Э:'E',Ю:'Yu',Я:'Ya'
            };

            function rusToLat(str) {
                return str.split('').map(c => ruToLat[c] || c).join('');
            }

            document.getElementById('name').addEventListener('input', function() {
                var slug = rusToLat(this.value).toLowerCase()
                    .replace(/[^\w\s-]/g, '').trim().replace(/\s+/g, '-').replace(/-+/g, '-')
                    .replace(/^-+/, '').replace(/-+$/, '');
                document.getElementById('slug').value = slug;
            });

            if (typeof loadExistingBlocks === 'function') {
                loadExistingBlocks([]);
            }

            document.getElementById('discountForm').addEventListener('submit', function() {
                if (typeof collectAndSetBlocks === 'function') {
                    collectAndSetBlocks();
                }
            });
        });
    </script>
@endsection
