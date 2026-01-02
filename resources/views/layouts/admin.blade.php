<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jelena Sports Inventory')</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">

    @stack('styles')
</head>
<body>

@include('partials.sidebar')

<main class="main-content">
    <div class="top-bar">
        <div style="font-size: 1.2rem; font-weight: 600;">@yield('header-title', 'Dashboard')</div>

        <div style="display: flex; gap: 15px; align-items: center;">
            <span style="font-size: 0.9rem; color: #64748b;">Halo, <strong id="globalUserName">Admin</strong></span>
            <button class="btn-logout" id="btnLogoutGlobal">Logout</button>
        </div>
    </div>

    <div style="padding-top: 100px; padding-left: 20px; padding-right: 20px;">
        @yield('content')
    </div>
</main>

<script>
    const tokenGlobal = localStorage.getItem('api_token');
    if (!tokenGlobal) window.location.href = '/login';

    document.getElementById('btnLogoutGlobal').addEventListener('click', () => {
        if(confirm('Logout dari sistem?')) {
            localStorage.removeItem('api_token');
            window.location.href = '/login';
        }
    });
</script>

@stack('scripts')
</body>
</html>
