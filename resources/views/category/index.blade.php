@extends('layouts.admin')

@section('title', 'Категории')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Категории - @if(!empty($categories)){{ $categories->total() }}@endif</h1>
                </div>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <a href="{{ route('category.create') }}" class="btn btn-primary">Добавить</a>

                            <div class="card-tools mt-1">
                                <form action="{{ route('category.index') }}" method="get" class="d-flex flex-row align-items-center">
                                    <div class="input-group me-2 mb-2">
                                        <select name="sort" class="form-select">
                                            <option value="default" selected>По умолчанию</option>
                                            <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>
                                                Сначала старые
                                            </option>
                                            <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>
                                                Сначала новые
                                            </option>
                                            <option value="orders_desc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                                По возрастанию цены
                                            </option>
                                            <option value="orders_asc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                                По убыванию цены
                                            </option>
                                        </select>
                                    </div>
                                    <div class="input-group me-2 mb-2">
                                        <input type="text" name="keyword" class="form-control" placeholder="Поиск"
                                               value="{{ request('keyword') }}"/>
                                        <button type="submit" class="btn btn-secondary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- /.card-header -->
                        <div class="card-body p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Название</th>
                                    <th class="text-center">Длительность</th>
                                    <th class="text-right">Цена</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($categories))
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>
                                                @if ($category->icon)
                                                    <i class="{{ $category->icon }}"></i>
                                                @endif
                                            </td>
                                            <td><a href="{{ route('category.show', $category->id) }}" class="text-decoration-none">{{ $category->name }}</a></td>
                                            <td class="text-center">{{ $category->duration }} часов</td>
                                            <td class="text-right">{{ number_format($category->price, 2, ',', ' ') }} &#8381;</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            @if(!empty($categories))
                                {{ $categories->withQueryString()->links() }}
                            @endif
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
