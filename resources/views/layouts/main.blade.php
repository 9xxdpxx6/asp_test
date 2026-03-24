<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meta['title'] ?? 'По умолчанию' }}</title>
    <meta name="description" content="{{ $meta['description'] ?? '' }}">
    <meta name="keywords" content="{{ $meta['keywords'] ?? '' }}">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
@if (!empty($initialHero))
    <script>window.__INITIAL_HERO__ = @json($initialHero);</script>
@endif
@if (!empty($initialContact))
    <script>window.__INITIAL_CONTACT__ = @json($initialContact);</script>
@endif
<div id="app">
    @yield('content')
</div>
</body>
</html>
