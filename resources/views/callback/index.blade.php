@extends('layouts.admin')

@section('title', 'Заявки')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Заявки - {{ $callbacks->total() }}</h1>
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
{{--                            <a href="{{ route('callback.create') }}" class="btn btn-primary">Добавить</a>--}}

                            <div class="card-tools mt-1">
                                <form action="{{ route('callback.index') }}" method="get" class="d-flex flex-row align-items-center">
                                    <div class="input-group me-2 mb-2">
                                        <select name="status" class="form-select">
                                            <option value="">Все статусы</option>
                                            @foreach($statuses as $status)
                                                <option
                                                    value="{{ $status->id }}"
                                                    {{ request('status') == $status->id ? 'selected' : '' }}>
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input-group me-2 mb-2">
                                        <select name="sort" class="form-select">
                                            <option value="default" selected>По умолчанию</option>
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
                                    <th>ФИО</th>
                                    <th>Телефон</th>
                                    <th class="text-center">Заказы</th>
                                    <th class="text-right">Статус</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($callbacks as $callback)
                                    <tr>
                                        <td><a href="{{ route('callback.show', $callback->id) }}"
                                               class="text-decoration-none">{{ $callback->full_name }}</a></td>
                                        <td>{{ $callback->phone }}</td>
                                        <td class="text-center"><span>{{ $callback->email }}</span></td>
                                        <td class="text-right">
                                            <div class="text-center text-white px-3 rounded-pill" style="background-color: {{ $callback->status->color }}">
                                                {{ $callback->status->name }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            {{ $callbacks->withQueryString()->links() }}
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
