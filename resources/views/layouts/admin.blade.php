<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Автошкола Политех')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminpanel/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminpanel/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('adminpanel/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('style')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light px-3" @if(!auth()->user()) style="margin-left: 0;" @endif>
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ms-auto">
            @auth
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('admin.logout') }}"
                           onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                            Выйти
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.login') }}">Авторизация</a>
                </li>
                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                    </li>
                @endif
            @endauth
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @if(auth()->user())
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ env('APP_URL', url('/')) }}" class="brand-link text-decoration-none">
{{--                <span class="brand-text font-weight-light h2 text-light font-weight-bold ms-3">Кубанский Политех</span>--}}
                <img src="{{ asset('logo.png') }}" alt="" class="brand-text" style="width: 180px; margin-left: 20px">
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('category.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Категории</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('post.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-th"></i>
                                <p>Новости</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('discount.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Программы лояльности</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('callback.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-phone"></i>
                                <p>Обратная связь</p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
    @endif

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" @if(!auth()->user()) style="margin-left: 0;" @endif>
        @yield('content')

{{--        @if(auth()->user())--}}
{{--            @yield('content')--}}
{{--        @else--}}
{{--            <div class="card text-center">--}}
{{--                <div class="card-header">--}}
{{--                    Ошибка доступа--}}
{{--                </div>--}}
{{--                <div class="card-body">--}}
{{--                    <h5 class="card-title">Нет прав доступа</h5>--}}
{{--                    <p class="card-text">У вас нет прав для доступа к этому разделу.</p>--}}
{{--                    <a href="{{ route('admin.logout') }}"--}}
{{--                       class="btn btn-danger"--}}
{{--                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">--}}
{{--                        Выйти--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer" @if(!auth()->user()) style="margin-left: 0;" @endif>
        <strong>Copyright &copy; {{ now()->year }} <a href="{{ env('APP_URL', url('/')) }}">Автошкола Политех</a>.</strong>
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>
</div>
<!-- ./wrapper -->

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<!-- jQuery -->
<script src="{{ asset('adminpanel/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('adminpanel/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap -->
<script src="{{ asset('adminpanel/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('adminpanel/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminpanel/dist/js/adminlte.js') }}"></script>
@yield('script')
</body>
</html>
