@extends('layouts.admin')

@section('title', 'Категории')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{--                    <h1 class="m-0">Категории - {{ $categories->total() }}</h1>--}}
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
                                <form action="{{ route('category.index') }}" method="get"
                                      class="d-flex align-items-center">
                                    <div class="input-group me-2 mb-2">
                                        <select name="sort" class="form-select">
                                            <option value="default" selected>По умолчанию</option>
                                            <option value="date_asc" {{ request('sort') == 'date_asc' ? 'selected' : '' }}>
                                                Сначала старые
                                            </option>
                                            <option value="date_desc" {{ request('sort') == 'date_desc' ? 'selected' : '' }}>
                                                Сначала новые
                                            </option>
                                            <option value="orders_desc" {{ request('sort') == 'orders_asc' ? 'selected' : '' }}>
                                                Больше заказов
                                            </option>
                                            <option value="orders_asc" {{ request('sort') == 'orders_desc' ? 'selected' : '' }}>
                                                Меньше заказов
                                            </option>
                                            <option value="total_price_desc" {{ request('sort') == 'total_price_asc' ? 'selected' : '' }}>
                                                Большая общая стоимость
                                            </option>
                                            <option value="total_price_asc" {{ request('sort') == 'total_price_desc' ? 'selected' : '' }}>
                                                Меньшая общая стоимость
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
                                    <th>ФИО</th>
                                    <th>Телефон</th>
                                    <th class="text-center">Заказы</th>
                                    <th class="text-right">Общая стоимость</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td><a href="{{ route('category.show', $category->id) }}"
                                               class="text-decoration-none">{{ $category->name }}</a></td>
                                        <td>{{ $category->phone }}</td>
                                        <td class="text-center"><span>{{ $category->orders }}</span></td>
                                        <td class="text-right">{{ number_format($category->total, 2, ',', ' ') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            {{--                            {{ $categories->withQueryString()->links() }}--}}
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
