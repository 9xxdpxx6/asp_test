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
                <label for="description">Описание</label>
                <input type="text" name="description" class="form-control" id="description" placeholder="Описание">
            </div>
            <div class="mb-3">
                <label for="price">Цена</label>
                <input type="number" name="price" step="0.01" class="form-control" id="price" placeholder="Цена">
            </div>
            <div class="mb-3">
                <label for="duration">Длительность</label>
                <input type="number" name="duration" step="1" class="form-control" id="duration" placeholder="Длительность">
            </div>
            <button type="submit" class="btn btn-primary">Обновить</button>
        </form>
    </div>
@endsection
