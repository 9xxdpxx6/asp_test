@extends('layouts.admin')

@section('title', 'Посты')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Посты</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <a href="{{ route('post.create') }}" class="btn btn-primary">Добавить</a>

                            <div class="card-tools mt-1">
                                <form action="{{ route('post.index') }}" method="get" class="d-flex align-items-center">
                                    <div class="input-group me-2 mb-2">
                                        <select name="sort" class="form-select">
                                            <option value="default" selected>По умолчанию</option>
                                            <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>Сначала старые</option>
                                            <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>Сначала новые</option>
                                        </select>
                                    </div>
                                    <div class="input-group me-2 mb-2">
                                        <input type="text" name="keyword" class="form-control" placeholder="Поиск" value="{{ request('keyword') }}" />
                                        <button type="submit" class="btn btn-secondary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Заголовок</th>
                                    <th>Содержание</th>
                                    <th class="text-right">Дата публикации</th>
                                    <th class="text-right">Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($posts as $post)
                                    <tr>
                                        <td><a href="{{ route('post.show', $post->id) }}" class="text-decoration-none">{{ $post->title }}</a></td>
                                        <td>{{ $post->content }}</td>
                                        <td class="text-right">{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-right">
                                            <a href="{{ route('post.edit', $post->id) }}" class="btn btn-sm btn-warning" title="Редактировать">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
