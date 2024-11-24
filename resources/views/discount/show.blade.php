@extends('layouts.admin')

@section('content')
    <div class="container-fluid mb-4 pt-4">
        <!-- Карточка с информацией о льготной программе -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="card-title my-1">Просмотр льготной программы #{{$discount->id}}</h5>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Название:</h6>
                <p class="card-text">{{$discount->name}}</p>

                <h6 class="card-subtitle text-muted mb-2">Процент:</h6>
                <p class="card-text">{{$discount->percentage}}%</p>

                <h6 class="card-subtitle text-muted mb-2">Описание:</h6>
                <p class="card-text">{!! $discount->description !!}</p>
            </div>
        </div>

        <!-- Действия с программой -->
        <div class="d-flex flex-row gap-2">
            <a href="{{route('discount.edit', $discount->id)}}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>

            <form action="{{route('discount.delete', $discount->id)}}" method="post" class="d-inline">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить льготную программу?')">
                    <i class="fas fa-trash-alt me-2"></i>Удалить
                </button>
            </form>

            <a href="{{route('discount.index')}}" class="btn btn-secondary ms-auto">
                <i class="fas fa-arrow-left me-2"></i>Назад к списку
            </a>
        </div>
    </div>
@endsection
