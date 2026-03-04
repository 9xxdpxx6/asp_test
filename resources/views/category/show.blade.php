@extends('layouts.admin')

@section('title', $category->name)

@section('style')
    <style>
        .category-description {
            text-align: left;
        }
        .category-description .ql-align-center { text-align: center; }
        .category-description .ql-align-right { text-align: right; }
        .category-description .ql-align-justify { text-align: justify; }
        .category-description img { max-width: 100%; height: auto; }

        .block-preview { margin-bottom: 1.5rem; }
        .block-preview .block-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
        .feature-card { padding: 1rem; border: 1px solid #dee2e6; border-radius: 0.5rem; }
        .feature-card i { font-size: 1.5rem; color: #0d6efd; margin-bottom: 0.5rem; }
        .faq-item-preview { border-bottom: 1px solid #dee2e6; padding: 0.75rem 0; }
        .faq-item-preview:last-child { border-bottom: none; }
        .pricing-table { width: 100%; }
        .pricing-table td { padding: 0.5rem 0; border-bottom: 1px solid #f0f0f0; }
        .pricing-table tr:last-child td { border-bottom: none; }
        .gallery-grid { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .gallery-grid img { width: 200px; height: 140px; object-fit: cover; border-radius: 0.375rem; }
        .image-text-block { display: flex; gap: 2rem; align-items: flex-start; }
        .image-text-block img { max-width: 50%; height: auto; border-radius: 0.375rem; }
        .image-text-block .text-content { flex: 1; }
    </style>
@endsection

@section('content')
    <div class="container-fluid mb-4 pt-4">

        {{-- Кнопка "Посмотреть на сайте" --}}
        <div class="mb-3">
            <a href="/prices/{{ $category->id }}" target="_blank" class="btn btn-outline-primary">
                <i class="fas fa-external-link-alt me-2"></i>Посмотреть на сайте
            </a>
        </div>

        {{-- Основная информация --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title my-1">{{ $category->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($category->image)
                    <div class="col-md-2 mb-3">
                        <img src="{{ url('storage/' . $category->image) }}" alt="{{ $category->name }}"
                             class="img-fluid rounded" style="width:150px; height:150px; object-fit:cover;">
                    </div>
                    @endif
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Цена:</h6>
                        <p class="h5">{{ number_format($category->price, 0, ',', ' ') }} &#8381;</p>
                    </div>
                    @if($category->duration)
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Длительность:</h6>
                        <p>{{ $category->duration }} мес.</p>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <h6 class="text-muted mb-1">Slug:</h6>
                        <p>{{ $category->slug }}</p>
                    </div>
                </div>

                @if($category->description)
                <div class="mt-3">
                    <h6 class="text-muted mb-1">Краткое описание:</h6>
                    <p>{{ strip_tags($category->description) }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Блоки контента --}}
        @if($category->blocks->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title my-1">Контент страницы ({{ $category->blocks->count() }} блоков)</h5>
            </div>
            <div class="card-body">
                @foreach($category->blocks as $block)
                    <div class="block-preview">
                        @switch($block->type)
                            @case('text')
                                <div class="block-label">Текст</div>
                                <div class="category-description">
                                    {!! $block->content['html'] ?? '' !!}
                                </div>
                                @break

                            @case('image')
                                <div class="block-label">Изображение</div>
                                @if(!empty($block->content['title']))
                                    <h4 class="mb-3">{{ $block->content['title'] }}</h4>
                                @endif
                                @if(!empty($block->content['url']))
                                    <figure class="text-center">
                                        <img src="{{ $block->content['url'] }}" alt="{{ $block->content['alt'] ?? '' }}"
                                             class="img-fluid rounded" style="max-height: 400px;">
                                        @if(!empty($block->content['caption']))
                                            <figcaption class="mt-2 text-muted">{{ $block->content['caption'] }}</figcaption>
                                        @endif
                                    </figure>
                                @endif
                                @break

                            @case('image_text')
                                <div class="block-label">Картинка + текст</div>
                                @if(!empty($block->content['title']))
                                    <h4 class="mb-3">{{ $block->content['title'] }}</h4>
                                @endif
                                <div class="image-text-block @if(($block->content['layout'] ?? 'left') === 'right') flex-row-reverse @endif">
                                    @if(!empty($block->content['image_url']))
                                        <img src="{{ $block->content['image_url'] }}" alt="">
                                    @endif
                                    <div class="text-content category-description">
                                        {!! $block->content['html'] ?? '' !!}
                                    </div>
                                </div>
                                @break

                            @case('features')
                                <div class="block-label">Преимущества</div>
                                @if(!empty($block->content['title']))
                                    <h4 class="mb-3">{{ $block->content['title'] }}</h4>
                                @endif
                                <div class="features-grid">
                                    @foreach(($block->content['items'] ?? []) as $item)
                                        <div class="feature-card">
                                            <i class="{{ $item['icon'] ?? 'fas fa-check' }}"></i>
                                            <h6>{{ $item['title'] ?? '' }}</h6>
                                            <p class="text-muted mb-0">{{ $item['text'] ?? '' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                @break

                            @case('faq')
                                <div class="block-label">FAQ</div>
                                @if(!empty($block->content['title']))
                                    <h4 class="mb-3">{{ $block->content['title'] }}</h4>
                                @endif
                                @foreach(($block->content['items'] ?? []) as $item)
                                    <div class="faq-item-preview">
                                        <strong>{{ $item['question'] ?? '' }}</strong>
                                        <p class="mb-0 mt-1">{{ $item['answer'] ?? '' }}</p>
                                    </div>
                                @endforeach
                                @break

                            @case('pricing')
                                <div class="block-label">Детали стоимости</div>
                                @if(!empty($block->content['title']))
                                    <h4 class="mb-3">{{ $block->content['title'] }}</h4>
                                @endif
                                <table class="pricing-table">
                                    @foreach(($block->content['items'] ?? []) as $item)
                                        <tr>
                                            <td class="fw-bold">{{ $item['label'] ?? '' }}</td>
                                            <td class="text-end">{{ $item['value'] ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                                @break

                            @case('gallery')
                                <div class="block-label">Галерея</div>
                                @if(!empty($block->content['title']))
                                    <h4 class="mb-3">{{ $block->content['title'] }}</h4>
                                @endif
                                <div class="gallery-grid">
                                    @foreach(($block->content['images'] ?? []) as $image)
                                        <img src="{{ $image['url'] }}" alt="{{ $image['alt'] ?? '' }}">
                                    @endforeach
                                </div>
                                @break
                        @endswitch

                        @if(!$loop->last)
                            <hr class="my-3">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @elseif($category->description && $category->blocks->count() === 0)
        {{-- Обратная совместимость: показываем старое описание --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title my-1">Описание (старый формат)</h5>
            </div>
            <div class="card-body">
                <div class="category-description">
                    {!! $category->description !!}
                </div>
            </div>
        </div>
        @endif

        <div class="d-flex flex-row gap-2">
            <a href="{{ route('category.edit', $category->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>
            <form action="{{ route('category.delete', $category->id) }}" method="post" class="d-inline">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить категорию?')">
                    <i class="fas fa-trash-alt me-2"></i>Удалить
                </button>
            </form>
            <a href="{{ route('category.index') }}" class="btn btn-secondary ms-auto">
                <i class="fas fa-arrow-left me-2"></i>Назад к списку
            </a>
        </div>
    </div>
@endsection
