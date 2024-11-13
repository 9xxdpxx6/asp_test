@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <form action="{{ route('category.update', $category->id) }}" method="post">
            @csrf
            @method('patch')
            <div class="mb-3">
                <label for="name">Название</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Название">
            </div>
                <div class="mb-3">
                    <label for="percentage">Проценты</label>
                    <input type="number" name="percentage" step="0.01" class="form-control" id="percentage" placeholder="Проценты">
            </div>
            <div class="mb-3">
                <label for="description">Описание</label>
                <input type="text" name="description" class="form-control" id="description" placeholder="Описание">
            </div>
            <button type="submit" class="btn btn-primary">Обновить</button>
        </form>
    </div>
@endsection
