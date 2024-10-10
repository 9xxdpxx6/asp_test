@extends('layouts.main')
@section('content')
<div>

    @foreach($categories as $category)

    <div>{{$category->id}}. {{$category->description}}. {{$category->name}}. {{$category->duration}}</div>
    @endforeach
</div>
@endsection
