@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Список скидок</h1>

    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Описание</th>
            <th>Скидка</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($discounts as $discount) <!-- Перебираем все посты -->
        <tr>
            <td>{{ $discount->id }}</td>
            <td>{{ $discount->name }}</td>
            <td>{{ $discount->percentage }}</td>
            <td>{!! $discount->description !!}</td>
            <td>
                <a href="{{ route('discount.show', $discount) }}" class="btn btn-info">Просмотреть</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <a href="{{ route('discount.create') }}" class="btn btn-primary">Создать новый пост</a>


</div>

@endsection
