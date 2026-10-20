<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#7c3aed">
    <title>@yield('title') | {{ config('parentapp.organization.name') }}</title>
    <link rel="icon" href="{{ asset('icons/favicon-32.png') }}" type="image/png" sizes="32x32">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/font/bootstrap-icons.min.css') }}">
    @vite(['resources/css/parent.css', 'resources/js/parent.js'])
    <style>
        .pp-legal h2 { font-size: 1.05rem; font-weight: 800; margin: 1.5rem 0 .5rem; }
        .pp-legal h2:first-child { margin-top: 0; }
        .pp-legal p, .pp-legal li { font-size: .95rem; line-height: 1.6; }
        .pp-legal ul { padding-left: 1.2rem; }
        .pp-legal table { font-size: .88rem; }
        .pp-shell.pp-shell-public { padding-bottom: calc(24px + env(safe-area-inset-bottom)); }
        @media print {
            .pp-topbar, .btn, .pp-footer-links { display: none !important; }
            .pp-shell { padding-top: 0 !important; }
            body, html { background: #fff !important; }
            .pp-card { box-shadow: none !important; }
        }
    </style>
    @stack('head')
</head>
<body>
    <header class="pp-topbar">
        <div class="pp-topbar-inner">
            @php
                $backUrl = auth()->user()?->hasRole('parent') ? route('parent.more') : (auth()->check() ? route('dashboard') : route('login'));
            @endphp
            <a href="{{ $backUrl }}" class="text-white fs-4 lh-1" aria-label="Back"><i class="bi bi-arrow-left"></i></a>
            <img src="{{ asset('icons/icon-192.png') }}" alt="" width="36" height="36" class="rounded-3">
            <div class="fw-bold text-truncate">@yield('title')</div>
        </div>
    </header>

    <div class="pp-shell pp-shell-public">
        <main class="pp-main">
            @if (session('success'))
                <div class="alert alert-success rounded-4 border-0">{{ session('success') }}</div>
            @endif
            @yield('content')

            <div class="text-center small text-muted mt-4 pp-footer-links">
                <a href="{{ route('privacy-policy') }}" class="text-muted">Privacy policy</a>
                &middot;
                <a href="{{ route('account-deletion.show') }}" class="text-muted">Delete account</a>
                <div class="mt-1">&copy; {{ date('Y') }} {{ config('parentapp.organization.name') }}</div>
            </div>
        </main>
    </div>
</body>
</html>
