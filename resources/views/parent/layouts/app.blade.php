<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#7c3aed">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Parent') | KinderCare</title>

    {{-- PWA --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" href="{{ asset('icons/favicon-32.png') }}" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="KinderCare">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/font/bootstrap-icons.min.css') }}">
    @vite(['resources/css/parent.css', 'resources/js/parent.js'])
    @stack('styles')
</head>
<body>
    @php
        $tabs = [
            ['route' => 'parent.today',      'match' => 'parent.today',   'icon' => 'bi-sun',         'active' => 'bi-sun-fill',          'label' => 'Today'],
            ['route' => 'parent.photos',     'match' => 'parent.photos',  'icon' => 'bi-image',       'active' => 'bi-image-fill',        'label' => 'Photos'],
            ['route' => 'parent.therapy',    'match' => 'parent.therapy', 'icon' => 'bi-heart-pulse', 'active' => 'bi-heart-pulse-fill',  'label' => 'Therapy'],
            ['route' => 'parent.fees.index', 'match' => 'parent.fees.*',  'icon' => 'bi-wallet',      'active' => 'bi-wallet-fill',       'label' => 'Fees'],
            ['route' => 'parent.more',       'match' => ['parent.more', 'parent.notices', 'parent.profile'], 'icon' => 'bi-grid', 'active' => 'bi-grid-fill', 'label' => 'More'],
        ];
    @endphp

    {{-- ===== Top bar: active child + switcher ===== --}}
    <header class="pp-topbar">
        <div class="pp-topbar-inner">
            @if ($activeChild)
                @if ($parentChildren->count() > 1)
                    <div class="dropdown flex-grow-1" style="min-width:0">
                        <button class="pp-child-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @include('parent.partials.avatar', ['child' => $activeChild])
                            <span class="d-flex flex-column" style="min-width:0">
                                <span class="pp-child-name">{{ $activeChild->full_name }}</span>
                                <span class="pp-child-sub">Tap to switch child</span>
                            </span>
                        </button>
                        <ul class="dropdown-menu mt-2">
                            @foreach ($parentChildren as $c)
                                <li>
                                    <form method="POST" action="{{ route('parent.children.switch', $c->id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 {{ $c->id === $activeChild->id ? 'active' : '' }}">
                                            @include('parent.partials.avatar', ['child' => $c, 'size' => 'sm'])
                                            <span class="fw-semibold">{{ $c->full_name }}</span>
                                            @if ($c->id === $activeChild->id)
                                                <i class="bi bi-check2 ms-auto"></i>
                                            @endif
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="pp-child-btn flex-grow-1" style="min-width:0">
                        @include('parent.partials.avatar', ['child' => $activeChild])
                        <span class="d-flex flex-column" style="min-width:0">
                            <span class="pp-child-name">{{ $activeChild->full_name }}</span>
                            <span class="pp-child-sub">{{ $activeChild->formatted_age }}</span>
                        </span>
                    </div>
                @endif
            @else
                <div class="flex-grow-1 fw-bold">KinderCare</div>
            @endif

            <a href="{{ route('parent.notices') }}" class="text-white fs-4 lh-1" aria-label="Notices">
                <i class="bi bi-megaphone"></i>
            </a>
        </div>
    </header>

    <div class="pp-shell">
        <main class="pp-main">
            @if (session('success'))
                <div class="alert alert-success rounded-4 border-0">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger rounded-4 border-0">{{ session('error') }}</div>
            @endif

            @if (!$activeChild && !request()->routeIs('parent.more', 'parent.notices'))
                <div class="pp-card pp-empty">
                    <i class="bi bi-person-plus"></i>
                    <div class="fw-bold text-dark mb-1">No child linked yet</div>
                    <div>Please contact the daycare office to link your child to your account.</div>
                </div>
            @else
                @yield('content')
            @endif
        </main>
    </div>

    {{-- ===== Bottom tabs ===== --}}
    <nav class="pp-tabbar" aria-label="Main">
        <div class="pp-tabbar-inner">
            @foreach ($tabs as $tab)
                @php $isActive = request()->routeIs(...(array) $tab['match']); @endphp
                <a href="{{ route($tab['route']) }}" class="pp-tab {{ $isActive ? 'active' : '' }}" @if ($isActive) aria-current="page" @endif>
                    <i class="bi {{ $isActive ? $tab['active'] : $tab['icon'] }}"></i>
                    <span>{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    {{-- PWA service worker: scoped to /parent/ so it never touches the admin portal. --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker
                    .register(@json(asset('sw.js')), { scope: @json(url('parent') . '/') })
                    .catch(function () {});
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
