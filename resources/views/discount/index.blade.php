@extends('layouts.admin')

@section('title', 'Льготные программы')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Льготные программы - {{ $discounts instanceof \Illuminate\Pagination\LengthAwarePaginator ? $discounts->total() : $discounts->count() }}</h1>
                </div>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <a href="{{ route('discount.create') }}" class="btn btn-primary">Добавить</a>

                            <div class="card-tools mt-1">
                                <form action="{{ route('discount.index') }}" method="get" class="d-flex flex-row align-items-center">
                                    <div class="input-group me-2 mb-2">
                                        <select name="sort" class="form-select">
                                            <option value="default" selected>По умолчанию</option>
                                            <option value="percentage_asc" {{ request('sort') == 'percentage_asc' ? 'selected' : '' }}>
                                                Процент: по возрастанию
                                            </option>
                                            <option value="percentage_desc" {{ request('sort') == 'percentage_desc' ? 'selected' : '' }}>
                                                Процент: по убыванию
                                            </option>
                                            <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>
                                                Сначала старые
                                            </option>
                                            <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>
                                                Сначала новые
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
                                    <th style="width: 50px;"></th> <!-- Столбец под изображения -->
                                    <th style="padding-left: 10px;">Название</th> <!-- Уменьшен padding -->
                                    <th class="text-right">Процент</th> <!-- Добавлен класс text-right для выравнивания по правому краю -->
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($discounts as $discount)
                                    <tr>
                                        <td>
                                            <!-- Отображение превью -->
                                            @if ($discount->preview_path)
                                                <img src="{{ Storage::url($discount->preview_path) }}" alt="Category Image" class="img-thumbnail" style="max-width: 50px; max-height: 50px; object-fit: cover;">
                                            @else
                                                <span class="text-muted">Нет изображения</span>
                                            @endif
                                        </td>
                                        <td style="padding-left: 10px;">
                                            <!-- Кликабельное название -->
                                            <a href="{{ route('discount.show', $discount->id) }}" class="text-decoration-none">
                                                {{ $discount->name }}
                                            </a>
                                        </td>
                                        <td class="text-right">{{ $discount->percentage }}%</td> <!-- Процент выравнивается вправо -->
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            @if($discounts instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                {{ $discounts->withQueryString()->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
