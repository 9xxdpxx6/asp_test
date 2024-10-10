@extends('layouts.main')
@section('content')
<div>
    <form action="{{route('category.update', $category->id )}}" method="post">
        @csrf
        @method('patch')
        <div class="mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Name">
        </div>
        <div class="mb-3">
            <label for="description">Description</label>
            <input type="text" name="description" class="form-control" id="description" placeholder="Description">
        </div>
        <div class="mb-3">
            <label for="price">Price</label>
            <input type="number" name="price" step="0.01" class="form-control" id="price" placeholder="Price">
        </div>
        <div class="mb-3">
            <label for="duration">Duration</label>
            <input type="number" name="duration" step="1" class="form-control" id="duration" placeholder="Duration">
        </div>
        <button type="submit" class="btn btn-primary">Updatte</button>
    </form>
</div>
@endsection


