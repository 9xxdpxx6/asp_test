@extends('layouts.main')
@section('content')
<div>
    {{$category->id}}. {{$category->description}}. {{$category->name}}. {{$category->duration}}
</div>

<div>
    {{$category->id}}. {{$category->description}}. {{$category->name}}. {{$category->duration}}
</div>
<div>
    <a href="{{route('category.edit', $category->id)}}">Edit</a>
</div>

<div>
    <form action="{{route('category.delete', $category->id)}}" method="post">
        @csrf
        @method('delete')
        <input type="submit" value ="Delete">
    </form>
</div>
<div>
    <a href="{{route('category.index')}}">Back</a>
</div>

@endsection
