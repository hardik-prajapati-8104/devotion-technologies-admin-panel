<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Devotion Technology</title>

    <link rel="icon" href="{{ url('public/backend/img/favicon.png') }}">

    <!-- Poppins font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Admin design system -->
    <link rel="stylesheet" href="{{ url('public/backend/css/style.css') }}">
    <script>
        (function () {
            const pref = localStorage.getItem('admin-theme') || 'system';
            const resolved = pref === 'system'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : pref;
            document.documentElement.setAttribute('data-bs-theme', resolved);
        })();
    </script>
    
    @yield('styles')
</head>
<body>

    <div class="app-wrapper" id="appWrapper">

        @include('backend.layouts.partials.sidebar')

        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="app-content">

            @include('backend.layouts.partials.header')

            @hasSection('page-header')
                <div class="page-header">
                    @yield('page-header')
                </div>
            @endif

            <div class="page-body">
                @include('backend.layouts.partials.messages')
                @yield('admin-content')
            </div>

            @include('backend.layouts.partials.footer')
        </div>
    </div>

    <!-- jQuery (needed by Select2 / DataTables in later phases) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ url('public/backend/js/app.js') }}"></script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
