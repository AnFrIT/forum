<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Админ-панель - ' . setting('forum_name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Админ-панель
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> Перейти на форум
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="rounded-circle" width="25" height="25">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person"></i> Профиль
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Выход
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ active_route('admin.dashboard') }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Главная
                            </a>
                        </li>
                        
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Управление</span>
                        </h6>
                        
                        @can('manage categories')
                            <li class="nav-item">
                                <a class="nav-link {{ active_route('admin.categories.*') }}" href="{{ route('admin.categories.index') }}">
                                    <i class="bi bi-folder"></i> Категории
                                </a>
                            </li>
                        @endcan
                        
                        @can('manage users')
                            <li class="nav-item">
                                <a class="nav-link {{ active_route('admin.users.*') }}" href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people"></i> Пользователи
                                </a>
                            </li>
                        @endcan
                        
                        @can('view reports')
                            <li class="nav-item">
                                <a class="nav-link {{ active_route('admin.reports.*') }}" href="{{ route('admin.reports.index') }}">
                                    <i class="bi bi-flag"></i> Жалобы
                                    @php
                                        $pendingReports = \App\Models\Report::pending()->count();
                                    @endphp
                                    @if($pendingReports > 0)
                                        <span class="badge bg-danger">{{ $pendingReports }}</span>
                                    @endif
                                </a>
                            </li>
                        @endcan
                        
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                            <span>Настройки</span>
                        </h6>
                        
                        @can('manage settings')
                            <li class="nav-item">
                                <a class="nav-link {{ active_route('admin.settings.*') }}" href="{{ route('admin.settings.index') }}">
                                    <i class="bi bi-gear"></i> Настройки форума
                                </a>
                            </li>
                        @endcan
                        
                        @can('manage roles')
                            <li class="nav-item">
                                <a class="nav-link {{ active_route('admin.roles.*') }}" href="{{ route('admin.roles.index') }}">
                                    <i class="bi bi-shield"></i> Роли и права
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="py-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    @stack('scripts')
</body>
</html>