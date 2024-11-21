@extends('layouts.admin')

@section('style')

@endsection

@section('content')
    <div class="container-fluid mb-4 pt-4">

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title my-1">Просмотр Заявки #{{ $callback->id }}</h5>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Название:</h6>
                <p class="card-text">{{ $callback->full_name }}</p>

                <h6 class="card-subtitle text-muted mb-2">Телефон:</h6>
                <p class="card-text">{{ $callback->phone }}</p>

                <h6 class="card-subtitle text-muted mb-2">Email:</h6>
                <p class="card-text">{{ $callback->email }}</p>

                <h6 class="card-subtitle text-muted mb-2">Комментарий:</h6>
                <p class="card-text">{{ $callback->comment ?? 'Комментарий отсутствует' }}</p>

                <h6 class="card-subtitle text-muted mb-2">Примечание администратора:</h6>
                <p class="card-text">{{ $callback->note ?? 'Примечание отсутствует' }}</p>

                <h6 class="card-subtitle text-muted mb-2">Статус:</h6>
                <div class="col-12 col-md-4 col-lg-2 text-center text-white px-3 rounded-pill" style="background-color: {{ $callback->status->color }}">
                    {{ $callback->status->name }}
                </div>
            </div>
        </div>


        <div class="d-flex flex-row gap-2">
            <a href="{{route('callback.edit', $callback->id)}}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>

            <form action="{{route('callback.delete', $callback->id)}}" method="post" class="d-inline">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить пост?')">
                    <i class="fas fa-trash-alt me-2"></i>Удалить
                </button>
            </form>

            <a href="{{route('callback.index')}}" class="btn btn-secondary ms-auto">
                <i class="fas fa-arrow-left me-2"></i>Назад к списку
            </a>
        </div>
    </div>
@endsection
