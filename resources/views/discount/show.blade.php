@extends('layouts.admin')

@section('content')
<div>
    <tr>
        <td>{{ $discount->id }}</td>
        <td>{{ $discount->name }}</td>
        <td>{{ $discount->percentage }}</td>
        <td>{!! $discount->description !!}</td>

        <td>
            <a href="{{ route('discount.edit', $discount) }}" class="btn btn-info">Изменить</a>
        </td>

        <td>
            <form action="{{ route('discount.delete', $discount) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
        </td>
    </tr>
</div>


@endsection
