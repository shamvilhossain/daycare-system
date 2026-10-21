<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $org = config('parentapp.organization');
    $brand = $org['name'] ?: 'KinderCare';
    $therapyMeta = [
        'slt' => ['title' => 'Speech & Language Therapy', 'short' => 'Speech', 'icon' => 'bi-chat-heart-fill', 'color' => 'purple',
                  'helps' => ['Late talking & limited words', 'Clear speech & articulation', 'Stuttering', 'Understanding & social communication']],
        'ot'  => ['title' => 'Occupational Therapy', 'short' => 'OT', 'icon' => 'bi-puzzle-fill', 'color' => 'teal',
                  'helps' => ['Fine motor & handwriting', 'Sensory processing', 'Self-care: eating, dressing', 'Balance & coordination']],
        'aba' => ['title' => 'Applied Behavior Analysis', 'short' => 'ABA', 'icon' => 'bi-stars', 'color' => 'coral',
                  'helps' => ['Autism support', 'Attention & routines', 'Positive behaviour', 'Social & learning skills']],
    ];
    $therapyNames = collect($therapies)->map(fn ($t) => $therapyMeta[$t['type']]['short'] ?? strtoupper($t['type']))->implode(', ');
    $minAge = collect($programs)->pluck('from')->filter()->first();
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brand }} | Daycare, Preschool & Child Therapy Center</title>
    <meta name="description" content="{{ $brand }}: caring daycare and preschool programs plus 1-on-1 Speech, Occupational and ABA therapy — with a parent app for daily updates, photos, therapy progress and online fee payment.">
    <meta name="theme-color" content="#FF6B6B">
    <link rel="icon" href="{{ asset('icons/favicon-32.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --coral: #FF6B6B; --coral-dark: #E65A5A; --coral-soft: #FFF0F0;
            --teal: #2BB5AB; --teal-soft: #E6F8F6;
            --purple: #7C3AED; --purple-soft: #F3EEFF;
            --sun: #FFC93C; --sun-soft: #FFF8E1;
            --ink: #1F2937; --body: #4B5563; --muted: #6B7280;
            --bg: #FBFAF7; --line: #EEF0F3;
            --radius: 22px;
        }
        html { scroll-behavior: smooth; scroll-padding-top: 80px; }
        body { font-family: 'Quicksand', sans-serif; color: var(--body); background: #fff; overflow-x: hidden; font-weight: 500; }
        h1, h2, h3, h4, h5, h6, .brand-text { font-family: 'Outfit', sans-serif; color: var(--ink); font-weight: 700; }
        a { text-decoration: none; }
        .section { padding: 90px 0; }
        .section-soft { background: var(--bg); }
        .eyebrow {
            display: inline-flex; align-items: center; gap: .45rem; padding: .4rem 1rem; border-radius: 999px;
            font-weight: 700; font-size: .85rem; background: var(--coral-soft); color: var(--coral-dark); margin-bottom: 1rem;
        }
        .eyebrow.purple { background: var(--purple-soft); color: var(--purple); }
        .eyebrow.teal { background: var(--teal-soft); color: #138A82; }
        .eyebrow.sun { background: var(--sun-soft); color: #A16207; }
        .section-title { font-size: clamp(1.9rem, 3.4vw, 2.7rem); line-height: 1.15; margin-bottom: .9rem; }
        .section-lead { font-size: 1.1rem; color: var(--muted); max-width: 640px; }
        .text-gradient { background: linear-gradient(90deg, var(--coral), var(--purple)); -webkit-background-clip: text; background-clip: text; color: transparent; }

        /* Buttons */
        .btn-pill { border-radius: 999px; font-weight: 700; padding: .8rem 1.6rem; font-family: 'Outfit', sans-serif; letter-spacing: .01em; transition: transform .2s, box-shadow .2s; }
        .btn-pill:hover { transform: translateY(-2px); }
        .btn-coral { background: var(--coral); color: #fff; border: 0; box-shadow: 0 10px 24px -8px rgba(255,107,107,.7); }
        .btn-coral:hover { background: var(--coral-dark); color: #fff; }
        .btn-purple { background: var(--purple); color: #fff; border: 0; box-shadow: 0 10px 24px -8px rgba(124,58,237,.6); }
        .btn-purple:hover { background: #6D28D9; color: #fff; }
        .btn-ghost { background: #fff; color: var(--ink); border: 2px solid var(--line); }
        .btn-ghost:hover { border-color: var(--ink); color: var(--ink); }

        /* Navbar */
        .navbar { padding: .9rem 0; background: rgba(255,255,255,.92); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0,0,0,.04); transition: box-shadow .3s; }
        .navbar.scrolled { box-shadow: 0 8px 30px -12px rgba(0,0,0,.15); }
        .navbar-brand { display: flex; align-items: center; gap: .55rem; font-size: 1.5rem; color: var(--ink); }
        .navbar-brand img { width: 38px; height: 38px; border-radius: 11px; }
        .nav-link { font-weight: 700; color: var(--body) !important; padding: .5rem .85rem !important; border-radius: 999px; }
        .nav-link:hover { background: var(--coral-soft); color: var(--coral-dark) !important; }

        /* Ticker */
        .ticker { margin-top: 72px; background: linear-gradient(90deg, #1F2937, #312E81); color: #fff; font-size: .9rem; overflow: hidden; }
        .ticker .inner { display: flex; align-items: center; gap: 1rem; padding: .55rem 0; }
        .ticker-badge { flex-shrink: 0; display: inline-flex; align-items: center; gap: .4rem; background: var(--sun); color: #1F2937; font-weight: 800; border-radius: 999px; padding: .2rem .8rem; font-size: .8rem; }
        .ticker-track { overflow: hidden; flex: 1; mask-image: linear-gradient(90deg, transparent, #000 4%, #000 96%, transparent); }
        .ticker-content { display: inline-flex; gap: 2.5rem; white-space: nowrap; animation: ticker 40s linear infinite; }
        .ticker-track:hover .ticker-content { animation-play-state: paused; }
        @keyframes ticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .ticker-item { background: none; border: 0; color: #fff; display: inline-flex; align-items: center; gap: .5rem; font-weight: 600; cursor: pointer; padding: 0; }
        .ticker-item:hover .t-title { text-decoration: underline; }
        .ticker-item .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--sun); }
        .ticker-item .tag { font-size: .72rem; opacity: .7; }

        /* Hero */
        .hero { position: relative; padding: 130px 0 80px; background: radial-gradient(1100px 500px at 85% 10%, var(--purple-soft), transparent 60%), radial-gradient(900px 500px at 0% 90%, var(--coral-soft), transparent 60%); overflow: hidden; }
        .ticker + .hero { padding-top: 70px; }
        .hero h1 { font-size: clamp(2.3rem, 5vw, 3.9rem); line-height: 1.07; letter-spacing: -.02em; margin-bottom: 1.2rem; }
        .hero .lead { font-size: 1.18rem; color: var(--muted); max-width: 560px; }
        .hero-tags { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.4rem; }
        .hero-tag { display: inline-flex; align-items: center; gap: .4rem; padding: .35rem .9rem; border-radius: 999px; font-weight: 700; font-size: .85rem; background: #fff; border: 1px solid var(--line); box-shadow: 0 4px 14px -8px rgba(0,0,0,.2); }
        .hero-tag i { font-size: .95rem; }
        .trust-row { display: flex; flex-wrap: wrap; gap: 1.4rem; margin-top: 2rem; font-weight: 700; font-size: .92rem; color: var(--ink); }
        .trust-row i { color: var(--teal); margin-right: .35rem; }
        .hero-visual { position: relative; padding: 20px; }
        .hero-blob { position: absolute; inset: 0; background: linear-gradient(135deg, var(--coral), var(--purple)); border-radius: 46% 54% 40% 60% / 55% 40% 60% 45%; opacity: .14; animation: morph 12s ease-in-out infinite; }
        @keyframes morph { 50% { border-radius: 60% 40% 55% 45% / 40% 60% 40% 60%; } }
        .hero-img { position: relative; width: 100%; height: 480px; object-fit: cover; border-radius: 36px; box-shadow: 0 30px 60px -20px rgba(31,41,55,.35); }
        .float-card { position: absolute; background: #fff; border-radius: 18px; padding: .8rem 1rem; box-shadow: 0 18px 40px -14px rgba(31,41,55,.35); display: flex; gap: .7rem; align-items: center; min-width: 220px; animation: bob 5s ease-in-out infinite; }
        .float-card .ico { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; font-size: 1.15rem; flex-shrink: 0; }
        .float-card h6 { margin: 0; font-size: .9rem; }
        .float-card p { margin: 0; font-size: .8rem; color: var(--muted); }
        .fc-1 { top: 30px; left: -30px; }
        .fc-2 { bottom: 120px; right: -30px; animation-delay: 1.4s; }
        .fc-3 { bottom: 10px; left: 30px; animation-delay: 2.6s; }
        .mini-bar { height: 6px; border-radius: 99px; background: #EDE9FE; overflow: hidden; margin-top: 4px; width: 150px; }
        .mini-bar i { display: block; height: 100%; width: 62%; background: var(--purple); border-radius: 99px; }
        @keyframes bob { 50% { transform: translateY(-10px); } }

        /* Pillars */
        .pillar { border-radius: var(--radius); padding: 2.4rem; height: 100%; position: relative; overflow: hidden; color: #fff; }
        .pillar.daycare { background: linear-gradient(135deg, #FF8A65, var(--coral)); }
        .pillar.therapy { background: linear-gradient(135deg, #8B5CF6, #6D28D9); }
        .pillar::after { content: ''; position: absolute; width: 260px; height: 260px; border-radius: 50%; background: rgba(255,255,255,.12); right: -80px; bottom: -110px; }
        .pillar h3 { color: #fff; font-size: 1.8rem; }
        .pillar .p-ico { width: 64px; height: 64px; border-radius: 20px; background: rgba(255,255,255,.2); display: grid; place-items: center; font-size: 1.8rem; margin-bottom: 1.2rem; }
        .pillar ul { list-style: none; padding: 0; margin: 1.2rem 0 1.6rem; position: relative; z-index: 1; }
        .pillar li { padding: .35rem 0; font-weight: 600; }
        .pillar li i { margin-right: .5rem; }
        .pillar .btn { position: relative; z-index: 1; background: #fff; color: var(--ink); }

        /* Program cards */
        .program-card { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 1.6rem; height: 100%; transition: transform .25s, box-shadow .25s; position: relative; }
        .program-card:hover { transform: translateY(-6px); box-shadow: 0 24px 40px -22px rgba(31,41,55,.35); }
        .program-card .p-emoji { font-size: 2.2rem; line-height: 1; margin-bottom: .8rem; }
        .program-card h4 { font-size: 1.25rem; margin-bottom: .35rem; }
        .program-meta { display: flex; flex-direction: column; gap: .35rem; font-size: .92rem; margin-top: .8rem; }
        .program-meta i { width: 20px; color: var(--coral); }
        .seat { position: absolute; top: 1.2rem; right: 1.2rem; font-size: .75rem; font-weight: 800; padding: .25rem .65rem; border-radius: 999px; }
        .seat.ok { background: var(--teal-soft); color: #0F766E; }
        .seat.few { background: var(--sun-soft); color: #A16207; }
        .seat.full { background: #F3F4F6; color: var(--muted); }

        /* Therapy cards */
        .therapy-card { background: #fff; border-radius: var(--radius); padding: 2rem; height: 100%; border: 1px solid var(--line); position: relative; overflow: hidden; transition: transform .25s, box-shadow .25s; }
        .therapy-card:hover { transform: translateY(-6px); box-shadow: 0 24px 40px -22px rgba(124,58,237,.45); }
        .therapy-card .t-ico { width: 60px; height: 60px; border-radius: 18px; display: grid; place-items: center; font-size: 1.6rem; margin-bottom: 1.1rem; }
        .t-purple .t-ico { background: var(--purple-soft); color: var(--purple); }
        .t-teal .t-ico { background: var(--teal-soft); color: #0F766E; }
        .t-coral .t-ico { background: var(--coral-soft); color: var(--coral-dark); }
        .therapy-card .badge-type { position: absolute; top: 1.4rem; right: 1.4rem; font-family: 'Outfit'; font-weight: 800; font-size: .8rem; letter-spacing: .08em; color: var(--muted); }
        .helps { list-style: none; padding: 0; margin: 1rem 0 0; }
        .helps li { padding: .3rem 0; font-size: .95rem; }
        .helps li i { color: var(--teal); margin-right: .5rem; }
        .packages { display: flex; flex-wrap: wrap; gap: .7rem; justify-content: center; }
        .package-chip { background: #fff; border: 1px dashed #C4B5FD; border-radius: 16px; padding: .7rem 1.1rem; text-align: left; }
        .package-chip b { color: var(--ink); font-family: 'Outfit'; display: block; }
        .package-chip span { font-size: .85rem; color: var(--muted); }

        /* Steps */
        .step { text-align: center; padding: 1rem 1.5rem; }
        .step .num { width: 72px; height: 72px; border-radius: 24px; margin: 0 auto 1.1rem; display: grid; place-items: center; font-family: 'Outfit'; font-weight: 800; font-size: 1.7rem; color: #fff; transform: rotate(-6deg); box-shadow: 0 14px 28px -12px rgba(0,0,0,.35); }
        .step:nth-child(1) .num { background: var(--coral); }
        .step:nth-child(2) .num { background: var(--purple); transform: rotate(5deg); }
        .step:nth-child(3) .num { background: var(--teal); }

        /* Phone mockup */
        .phone { width: 300px; margin: 0 auto; background: #111827; border-radius: 44px; padding: 12px; box-shadow: 0 40px 80px -30px rgba(31,41,55,.55); position: relative; }
        .phone::before { content: ''; position: absolute; top: 18px; left: 50%; transform: translateX(-50%); width: 90px; height: 22px; background: #111827; border-radius: 0 0 14px 14px; z-index: 2; }
        .screen { background: #F5F3FF; border-radius: 34px; overflow: hidden; height: 600px; font-family: 'Quicksand'; }
        .scr-top { background: linear-gradient(135deg, #f97066, #c061cb, #7c3aed); color: #fff; padding: 46px 16px 16px; display: flex; align-items: center; gap: 10px; }
        .scr-avatar { width: 38px; height: 38px; border-radius: 50%; background: rgba(255,255,255,.3); border: 2px solid #fff; display: grid; place-items: center; font-weight: 800; font-size: .85rem; }
        .scr-body { padding: 12px; }
        .scr-card { background: #fff; border-radius: 16px; padding: 10px 12px; margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.04); font-size: .78rem; }
        .scr-card .lbl { font-size: .62rem; font-weight: 800; letter-spacing: .08em; color: #9CA3AF; text-transform: uppercase; margin-bottom: 6px; }
        .scr-stat { display: flex; gap: 8px; }
        .scr-stat div { flex: 1; background: #F5F3FF; border-radius: 10px; padding: 6px; text-align: center; }
        .scr-stat b { display: block; color: var(--ink); font-size: .9rem; }
        .scr-row { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; }
        .scr-tabs { display: grid; grid-template-columns: repeat(5, 1fr); background: #fff; padding: 8px 4px 14px; font-size: .6rem; text-align: center; color: #9CA3AF; font-weight: 700; }
        .scr-tabs i { display: block; font-size: 1rem; }
        .scr-tabs .on { color: var(--purple); }
        .app-feature { display: flex; gap: 1rem; margin-bottom: 1.3rem; }
        .app-feature .ico { width: 52px; height: 52px; border-radius: 16px; display: grid; place-items: center; font-size: 1.35rem; flex-shrink: 0; }
        .app-feature h5 { margin-bottom: .2rem; font-size: 1.1rem; }
        .app-feature p { margin: 0; color: var(--muted); }

        /* Safety */
        .safe-card { background: #fff; border-radius: var(--radius); padding: 1.8rem; text-align: center; height: 100%; border: 1px solid var(--line); }
        .safe-card .ico { width: 64px; height: 64px; border-radius: 50%; margin: 0 auto 1rem; display: grid; place-items: center; font-size: 1.6rem; }
        .safe-card h5 { font-size: 1.1rem; }
        .safe-card p { margin: 0; font-size: .95rem; color: var(--muted); }

        /* Owners */
        .owners { background: #111827; color: #CBD5E1; position: relative; overflow: hidden; }
        .owners::before { content: ''; position: absolute; width: 600px; height: 600px; border-radius: 50%; background: radial-gradient(circle, rgba(124,58,237,.35), transparent 70%); top: -250px; right: -200px; }
        .owners h2, .owners h5 { color: #fff; }
        .owners .section-lead { color: #94A3B8; }
        .owner-feature { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); border-radius: 18px; padding: 1.3rem; height: 100%; transition: background .2s, transform .2s; }
        .owner-feature:hover { background: rgba(255,255,255,.08); transform: translateY(-3px); }
        .owner-feature i { font-size: 1.5rem; display: inline-block; margin-bottom: .7rem; }
        .owner-feature h5 { font-size: 1.02rem; margin-bottom: .3rem; }
        .owner-feature p { font-size: .88rem; margin: 0; color: #94A3B8; }

        /* FAQ */
        .accordion-item { border: 1px solid var(--line) !important; border-radius: 16px !important; margin-bottom: .8rem; overflow: hidden; }
        .accordion-button { font-family: 'Outfit'; font-weight: 700; color: var(--ink); font-size: 1.05rem; padding: 1.1rem 1.3rem; }
        .accordion-button:not(.collapsed) { background: var(--coral-soft); color: var(--coral-dark); box-shadow: none; }
        .accordion-button:focus { box-shadow: none; }

        /* CTA */
        .cta { background: linear-gradient(135deg, var(--coral) 0%, #C061CB 50%, var(--purple) 100%); border-radius: 36px; padding: 4rem 2rem; text-align: center; color: #fff; position: relative; overflow: hidden; }
        .cta h2 { color: #fff; font-size: clamp(1.9rem, 3.5vw, 2.8rem); }
        .cta p { font-size: 1.15rem; opacity: .92; max-width: 620px; margin: 0 auto 2rem; }
        .cta .bubble { position: absolute; border-radius: 50%; background: rgba(255,255,255,.12); }
        .cta .btn-white { background: #fff; color: var(--ink); }
        .cta .btn-outline-white { border: 2px solid rgba(255,255,255,.7); color: #fff; }
        .cta .btn-outline-white:hover { background: rgba(255,255,255,.12); color: #fff; }

        /* Footer */
        footer { background: var(--bg); padding: 60px 0 30px; border-top: 1px solid var(--line); }
        footer h6 { font-size: .85rem; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
        footer a { color: var(--body); font-weight: 600; }
        footer a:hover { color: var(--coral-dark); }
        footer ul { list-style: none; padding: 0; }
        footer li { padding: .25rem 0; }

        /* Announcement modal */
        .announcement-modal .modal-content { border: 0; border-radius: 24px; overflow: hidden; }
        .announcement-modal .head { padding: 1.5rem 1.75rem 1rem; display: flex; gap: 1rem; align-items: flex-start; }
        .announcement-modal .head .ico { width: 48px; height: 48px; border-radius: 14px; background: var(--sun-soft); color: #A16207; display: grid; place-items: center; font-size: 1.3rem; flex-shrink: 0; }
        .announcement-modal .content { white-space: pre-line; padding: 1.25rem 1.75rem; margin: 0 1.75rem 1rem; background: var(--bg); border-left: 4px solid var(--teal); border-radius: 0 16px 16px 0; }

        /* Reveal on scroll */
        .reveal { opacity: 0; transform: translateY(26px); transition: opacity .7s ease, transform .7s ease; }
        .reveal.in { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) {
            .reveal { opacity: 1; transform: none; transition: none; }
            .float-card, .hero-blob, .ticker-content { animation: none !important; }
            html { scroll-behavior: auto; }
        }

        @media (max-width: 991.98px) {
            .hero { padding-top: 110px; text-align: center; }
            .hero .lead { margin-left: auto; margin-right: auto; }
            .hero-tags, .trust-row, .hero-ctas { justify-content: center; }
            .hero-img { height: 340px; }
            .fc-1 { left: 0; } .fc-2 { right: 0; }
            .section { padding: 64px 0; }
        }
        @media (max-width: 575.98px) {
            .float-card { min-width: 0; transform: scale(.88); }
            .fc-3 { display: none; }
            .pillar { padding: 1.8rem; }
        }
    </style>
</head>

<body>
    {{-- ================= NAVBAR ================= --}}
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand brand-text" href="#top">
                <img src="{{ asset('icons/icon-192.png') }}" alt="">
                {{ $brand }}
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-1">
                    <li class="nav-item"><a class="nav-link" href="#programs">Daycare</a></li>
                    <li class="nav-item"><a class="nav-link" href="#therapy">Therapy</a></li>
                    <li class="nav-item"><a class="nav-link" href="#app">Parent App</a></li>
                    <li class="nav-item"><a class="nav-link" href="#centers">For Centers</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    @auth
                        <a href="{{ auth()->user()->hasRole('parent') ? route('parent.today') : route('dashboard') }}" class="btn btn-pill btn-coral py-2">
                            {{ auth()->user()->hasRole('parent') ? 'Open Parent App' : 'Dashboard' }} <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-pill btn-ghost py-2">Sign In</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-pill btn-coral py-2">Enroll Now</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ================= NOTICES TICKER ================= --}}
    @if(isset($announcements) && $announcements->isNotEmpty())
        <div class="ticker">
            <div class="container inner">
                <span class="ticker-badge"><i class="bi bi-megaphone-fill"></i>Notices</span>
                <div class="ticker-track">
                    <div class="ticker-content">
                        @for ($i = 0; $i < 2; $i++)
                            @foreach($announcements as $ann)
                                <button type="button" class="ticker-item announcement-trigger"
                                    data-title="{{ $ann->title }}"
                                    data-content="{{ $ann->content }}"
                                    data-published="{{ $ann->published_at ? $ann->published_at->format('M d, Y h:i A') : 'Recently' }}"
                                    data-expires="{{ $ann->expires_at ? $ann->expires_at->format('M d, Y') : '' }}"
                                    data-audience="{{ $ann->audience == 'all' ? 'Everyone' : 'Parents' }}">
                                    <span class="dot"></span>
                                    <span class="t-title">{{ $ann->title }}</span>
                                    <span class="tag">{{ $ann->published_at ? $ann->published_at->format('M d') : 'New' }}</span>
                                </button>
                            @endforeach
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ================= HERO ================= --}}
    <header class="hero" id="top">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="hero-tags">
                        <span class="hero-tag"><i class="bi bi-sun-fill" style="color:#F59E0B"></i>Daycare</span>
                        <span class="hero-tag"><i class="bi bi-book-half" style="color:var(--coral)"></i>Preschool</span>
                        <span class="hero-tag"><i class="bi bi-chat-heart-fill" style="color:var(--purple)"></i>Speech</span>
                        <span class="hero-tag"><i class="bi bi-puzzle-fill" style="color:var(--teal)"></i>OT</span>
                        <span class="hero-tag"><i class="bi bi-stars" style="color:var(--coral)"></i>ABA</span>
                    </div>
                    <h1>Where little ones <span class="text-gradient">play, learn&nbsp;&amp;&nbsp;thrive</span> — with expert therapy under one roof.</h1>
                    <p class="lead mb-4">
                        Warm, safe daycare and preschool programs, plus 1-on-1 speech, occupational and ABA therapy.
                        Follow every moment in our parent app — meals, naps, photos, therapy progress and fees.
                    </p>
                    <div class="d-flex flex-wrap gap-3 hero-ctas">
                        <a href="{{ route('register') }}" class="btn btn-pill btn-coral">
                            Enroll your child <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="#therapy" class="btn btn-pill btn-ghost">
                            <i class="bi bi-heart-pulse me-1" style="color:var(--purple)"></i> Explore therapy
                        </a>
                    </div>
                    <div class="trust-row">
                        <span><i class="bi bi-phone-fill"></i>Live daily updates</span>
                        <span><i class="bi bi-wallet2"></i>Pay by bKash, Nagad or card</span>
                        <span><i class="bi bi-qr-code"></i>QR safety tags</span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-visual">
                        <div class="hero-blob"></div>
                        {{-- Self-hosted (Unsplash licence) so the hero never breaks if a hotlink disappears --}}
                        <img src="{{ asset('images/home/hero-child.jpg') }}" alt="A happy toddler playing with a toy camera in our playroom" class="hero-img" fetchpriority="high">


                        <div class="float-card fc-1">
                            <div class="ico" style="background:var(--teal-soft);color:#0F766E"><i class="bi bi-door-open-fill"></i></div>
                            <div><h6>Checked in · 8:45 AM</h6><p>Have a great day, Aria! ☀️</p></div>
                        </div>
                        <div class="float-card fc-2">
                            <div class="ico" style="background:var(--purple-soft);color:var(--purple)"><i class="bi bi-chat-heart-fill"></i></div>
                            <div>
                                <h6>Speech therapy done ✓</h6>
                                <p>5 of 8 sessions left</p>
                                <div class="mini-bar"><i></i></div>
                            </div>
                        </div>
                        <div class="float-card fc-3">
                            <div class="ico" style="background:var(--coral-soft);color:var(--coral-dark)"><i class="bi bi-check2-circle"></i></div>
                            <div><h6>Fee paid via bKash</h6><p>Receipt saved in the app</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ================= TWO PILLARS ================= --}}
    <section class="section pt-4">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <span class="eyebrow sun"><i class="bi bi-house-heart-fill"></i>One family, two kinds of care</span>
                <h2 class="section-title">Everything your child needs to grow — in one place</h2>
                <p class="section-lead mx-auto">Nurturing everyday care and specialised therapy, delivered by one team that knows your child.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6 reveal">
                    <div class="pillar daycare">
                        <div class="p-ico"><i class="bi bi-balloon-heart-fill"></i></div>
                        <h3>Daycare &amp; Preschool</h3>
                        <p class="mb-0" style="opacity:.92">A happy, structured day full of play, learning and friendship — from tiny infants to school-age kids.</p>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i>Programs from {{ $minAge ?? 'infancy' }} to after-school</li>
                            <li><i class="bi bi-check-circle-fill"></i>Art, music, outdoor play, reading &amp; more</li>
                            <li><i class="bi bi-check-circle-fill"></i>Meals, naps and milestones logged daily</li>
                            <li><i class="bi bi-check-circle-fill"></i>Class photos shared with parents</li>
                        </ul>
                        <a href="#programs" class="btn btn-pill">See programs <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
                <div class="col-lg-6 reveal">
                    <div class="pillar therapy">
                        <div class="p-ico"><i class="bi bi-heart-pulse-fill"></i></div>
                        <h3>Child Therapy Center</h3>
                        <p class="mb-0" style="opacity:.92">One-on-one sessions with specialist therapists to help every child communicate, move and connect with confidence.</p>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i>{{ $therapyNames ?: 'Speech, OT and ABA' }} therapy</li>
                            <li><i class="bi bi-check-circle-fill"></i>Individual, focused 1-on-1 sessions</li>
                            <li><i class="bi bi-check-circle-fill"></i>Flexible session packages</li>
                            <li><i class="bi bi-check-circle-fill"></i>Sessions &amp; remaining balance in the app</li>
                        </ul>
                        <a href="#therapy" class="btn btn-pill">Explore therapy <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= PROGRAMS ================= --}}
    @if(collect($programs)->isNotEmpty())
        <section class="section section-soft" id="programs">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-5 reveal">
                    <div>
                        <span class="eyebrow"><i class="bi bi-sun-fill"></i>Daycare programs</span>
                        <h2 class="section-title mb-1">A program for every age &amp; stage</h2>
                        <p class="section-lead mb-0">Small groups, caring teachers and a day designed around how children learn best.</p>
                    </div>
                    <a href="{{ route('register') }}" class="btn btn-pill btn-coral">Reserve a seat <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                @php $emojis = ['🍼', '🧸', '🎨', '📚', '⚽', '🌈', '🎵', '🧩']; @endphp
                <div class="row g-4">
                    @foreach($programs as $i => $program)
                        <div class="col-md-6 col-lg-4 reveal">
                            <div class="program-card">
                                @if(!is_null($program['seats']))
                                    @if($program['is_full'])
                                        <span class="seat full">Waitlist open</span>
                                    @elseif($program['seats'] <= 3)
                                        <span class="seat few">Only {{ $program['seats'] }} {{ Str::plural('seat', $program['seats']) }} left</span>
                                    @else
                                        <span class="seat ok">Seats available</span>
                                    @endif
                                @endif
                                <div class="p-emoji">{{ $emojis[$i % count($emojis)] }}</div>
                                <h4>{{ $program['name'] }}</h4>
                                <span class="badge rounded-pill text-bg-light border">{{ $program['type'] }}</span>
                                <div class="program-meta">
                                    <span><i class="bi bi-person-arms-up"></i>Ages {{ $program['ages'] }}</span>
                                    @if($program['hours'])<span><i class="bi bi-clock"></i>{{ $program['hours'] }}</span>@endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ================= THERAPY ================= --}}
    <section class="section" id="therapy">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <span class="eyebrow purple"><i class="bi bi-heart-pulse-fill"></i>Therapy center</span>
                <h2 class="section-title">Specialist therapy that helps children <span class="text-gradient">bloom</span></h2>
                <p class="section-lead mx-auto">Every child develops at their own pace. Our therapists work one-on-one with your child and keep you part of every step.</p>
            </div>
            <div class="row g-4 justify-content-center">
                @forelse($therapies as $therapy)
                    @php
                        $meta = $therapyMeta[$therapy['type']] ?? ['title' => $therapy['services']->first()->name, 'short' => strtoupper($therapy['type']), 'icon' => 'bi-heart-pulse-fill', 'color' => 'purple', 'helps' => []];
                        $svc = $therapy['services']->first();
                    @endphp
                    <div class="col-md-6 col-lg-4 reveal">
                        <div class="therapy-card t-{{ $meta['color'] }}">
                            <span class="badge-type">{{ $meta['short'] }}</span>
                            <div class="t-ico"><i class="bi {{ $meta['icon'] }}"></i></div>
                            <h4 class="mb-1">{{ $meta['title'] }}</h4>
                            <p class="mb-0 text-muted">{{ $svc->description ?: '1-on-1 sessions with a specialist therapist.' }}</p>
                            @if($meta['helps'])
                                <ul class="helps">
                                    @foreach($meta['helps'] as $h)
                                        <li><i class="bi bi-check2-circle"></i>{{ $h }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="mt-3 small fw-bold" style="color:var(--purple)">
                                <i class="bi bi-stopwatch me-1"></i>{{ $svc->duration_minutes }}-minute 1-on-1 sessions
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-8 text-center text-muted reveal">Therapy services are coming soon.</div>
                @endforelse
            </div>

            {{-- Static marketing content (not from the database) --}}
            @php
                $packageOptions = [
                    ['Single session', 'Try a session and meet the therapist'],
                    ['4-session pack', 'A focused start on one goal'],
                    ['8-session pack', 'Steady progress over a month or two'],
                    ['Combo packs', 'Mix Speech, OT & ABA in one plan'],
                ];
            @endphp
            <div class="text-center mt-5 reveal">
                <h5 class="mb-3"><i class="bi bi-box2-heart me-2" style="color:var(--purple)"></i>Flexible session packages</h5>
                <div class="packages">
                    @foreach($packageOptions as [$title, $text])
                        <div class="package-chip">
                            <b>{{ $title }}</b>
                            <span>{{ $text }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="small text-muted mt-3 mb-0">Ask us which plan suits your child. Track every session and your remaining balance in the parent app.</p>
            </div>
        </div>
    </section>

    {{-- ================= HOW IT WORKS ================= --}}
    <section class="section section-soft pt-5">
        <div class="container">
            <div class="text-center mb-4 reveal">
                <span class="eyebrow teal"><i class="bi bi-signpost-split-fill"></i>Getting started</span>
                <h2 class="section-title">Joining us is easy</h2>
            </div>
            <div class="row">
                <div class="col-md-4 step reveal">
                    <div class="num">1</div>
                    <h4>Visit &amp; meet us</h4>
                    <p class="text-muted">Tour the center, meet our teachers and therapists, and tell us about your child.</p>
                </div>
                <div class="col-md-4 step reveal">
                    <div class="num">2</div>
                    <h4>Pick the right plan</h4>
                    <p class="text-muted">Choose a daycare program, a therapy package — or both, working together.</p>
                </div>
                <div class="col-md-4 step reveal">
                    <div class="num">3</div>
                    <h4>Watch them grow</h4>
                    <p class="text-muted">Follow their day, sessions and milestones live in the parent app.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= PARENT APP ================= --}}
    <section class="section" id="app">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 order-lg-2 reveal">
                    <div class="phone" aria-hidden="true">
                        <div class="screen">
                            <div class="scr-top">
                                <div class="scr-avatar">AR</div>
                                <div><div style="font-weight:800">Aria Rahman</div><div style="font-size:.7rem;opacity:.85">4 yrs · Preschool</div></div>
                            </div>
                            <div class="scr-body">
                                <div class="scr-card">
                                    <div class="lbl">Attendance</div>
                                    <div class="scr-stat"><div><b>8:45 AM</b>In</div><div><b>—</b>Out</div></div>
                                </div>
                                <div class="scr-card">
                                    <div class="lbl">Today</div>
                                    <div class="scr-row"><span>🍎 Lunch</span><span class="text-success fw-bold">Ate well</span></div>
                                    <div class="scr-row"><span>😴 Nap</span><span>1:00 – 2:15</span></div>
                                    <div class="scr-row"><span>🎨 Finger painting</span><span>✓</span></div>
                                </div>
                                <div class="scr-card">
                                    <div class="lbl">Therapy</div>
                                    <div class="scr-row"><span>Speech · Thu 10:00</span><span style="color:var(--purple);font-weight:800">5 left</span></div>
                                    <div class="mini-bar" style="width:100%"><i></i></div>
                                </div>
                                <div class="scr-card">
                                    <div class="lbl">Fees</div>
                                    <div class="scr-row"><span>October tuition</span><span class="badge text-bg-success">Paid</span></div>
                                </div>
                                <div class="scr-card" style="background:linear-gradient(135deg,#FFE4E6,#EDE9FE)">
                                    <div class="scr-row"><span>📸 6 new class photos</span><i class="bi bi-chevron-right"></i></div>
                                </div>
                            </div>
                            <div class="scr-tabs">
                                <span class="on"><i class="bi bi-sun-fill"></i>Today</span>
                                <span><i class="bi bi-image"></i>Photos</span>
                                <span><i class="bi bi-heart-pulse"></i>Therapy</span>
                                <span><i class="bi bi-wallet"></i>Fees</span>
                                <span><i class="bi bi-grid"></i>More</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 order-lg-1 reveal">
                    <span class="eyebrow purple"><i class="bi bi-phone-fill"></i>The parent app</span>
                    <h2 class="section-title">Their whole day, right in your pocket</h2>
                    <p class="section-lead mb-4">No more wondering. See what your child ate, when they napped, what they learned — and how therapy is going.</p>

                    <div class="app-feature">
                        <div class="ico" style="background:var(--sun-soft);color:#A16207"><i class="bi bi-sun-fill"></i></div>
                        <div><h5>Today at a glance</h5><p>Check-in &amp; check-out times, meals, naps, activities and notes from teachers.</p></div>
                    </div>
                    <div class="app-feature">
                        <div class="ico" style="background:var(--coral-soft);color:var(--coral-dark)"><i class="bi bi-images"></i></div>
                        <div><h5>Class photos &amp; videos</h5><p>See the fun moments from your child's activities.</p></div>
                    </div>
                    <div class="app-feature">
                        <div class="ico" style="background:var(--purple-soft);color:var(--purple)"><i class="bi bi-heart-pulse-fill"></i></div>
                        <div><h5>Therapy tracker</h5><p>Upcoming sessions, completed sessions and how many remain in each package.</p></div>
                    </div>
                    <div class="app-feature">
                        <div class="ico" style="background:var(--teal-soft);color:#0F766E"><i class="bi bi-wallet2"></i></div>
                        <div><h5>Fees, paid in seconds</h5><p>View invoices and pay securely with bKash, Nagad or card — receipts saved automatically.</p></div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{ auth()->check() && auth()->user()->hasRole('parent') ? route('parent.today') : route('login') }}" class="btn btn-pill btn-purple">
                            <i class="bi bi-phone me-1"></i> Open the parent app
                        </a>
                        <span class="d-flex align-items-center small fw-bold text-muted"><i class="bi bi-download me-2"></i>Install it on your phone’s home screen</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= SAFETY ================= --}}
    <section class="section section-soft">
        <div class="container">
            <div class="text-center mb-5 reveal">
                <span class="eyebrow teal"><i class="bi bi-shield-fill-check"></i>Safety first</span>
                <h2 class="section-title">Safe hands, every single day</h2>
            </div>
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3 reveal">
                    <div class="safe-card">
                        <div class="ico" style="background:var(--teal-soft);color:#0F766E"><i class="bi bi-door-open-fill"></i></div>
                        <h5>Recorded check-in &amp; out</h5>
                        <p>Every arrival and pick-up is time-stamped and visible to you.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 reveal">
                    <div class="safe-card">
                        <div class="ico" style="background:var(--coral-soft);color:var(--coral-dark)"><i class="bi bi-person-check-fill"></i></div>
                        <h5>Authorised pick-up</h5>
                        <p>Only guardians and contacts you approve can collect your child.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 reveal">
                    <div class="safe-card">
                        <div class="ico" style="background:var(--purple-soft);color:var(--purple)"><i class="bi bi-qr-code"></i></div>
                        <h5>QR safety tags</h5>
                        <p>If a child is ever lost, anyone who scans the tag can call you instantly.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 reveal">
                    <div class="safe-card">
                        <div class="ico" style="background:var(--sun-soft);color:#A16207"><i class="bi bi-clipboard2-pulse-fill"></i></div>
                        <h5>Health info on hand</h5>
                        <p>Allergies, medical notes and medication logs ready for our staff.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= FOR CENTER OWNERS ================= --}}
    <section class="section owners" id="centers">
        <div class="container position-relative">
            <div class="row align-items-end g-4 mb-5 reveal">
                <div class="col-lg-7">
                    <span class="eyebrow purple" style="background:rgba(124,58,237,.18);color:#C4B5FD"><i class="bi bi-building"></i>For daycare &amp; therapy center owners</span>
                    <h2 class="section-title">Run your whole center from one beautiful dashboard</h2>
                    <p class="section-lead mb-0">Built for centers that do both care and therapy — less paperwork, happier parents, healthier cash flow.</p>
                </div>
                <div class="col-lg-5 text-lg-end">
                    @if($org['email'])
                        <a href="mailto:{{ $org['email'] }}?subject={{ rawurlencode('Center management platform enquiry') }}" class="btn btn-pill btn-coral"><i class="bi bi-envelope me-1"></i> Talk to us</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-pill btn-coral">Staff sign in <i class="bi bi-arrow-right ms-1"></i></a>
                    @endif
                </div>
            </div>
            @php
                $ownerFeatures = [
                    ['bi-person-check', '#5EEAD4', 'Attendance desk', 'Fast check-in / check-out with live status for every program.'],
                    ['bi-journal-text', '#FCA5A5', 'Daily care logs', 'Meals, naps, diapers, medication and incidents in a few taps.'],
                    ['bi-calendar-week', '#FDE68A', 'Activity planning', 'Schedule curriculum sessions and review a monthly calendar report.'],
                    ['bi-heart-pulse', '#C4B5FD', 'Therapy scheduling', 'Book sessions, sell packages and track every child’s remaining balance.'],
                    ['bi-receipt', '#93C5FD', 'Smart invoicing', 'Invoices with item catalog, therapy packages, PDF download and print.'],
                    ['bi-link-45deg', '#86EFAC', 'Online payment links', 'Send a link by SMS or WhatsApp — parents pay with bKash, Nagad or card.'],
                    ['bi-graph-up-arrow', '#F9A8D4', 'Revenue reports', 'Invoiced vs. collected, outstanding balances and print-ready reports.'],
                    ['bi-phone', '#FDBA74', 'Parent app included', 'Installable mobile app for parents — fewer calls, more trust.'],
                ];
            @endphp
            <div class="row g-3">
                @foreach($ownerFeatures as [$icon, $color, $title, $text])
                    <div class="col-sm-6 col-lg-3 reveal">
                        <div class="owner-feature">
                            <i class="bi {{ $icon }}" style="color: {{ $color }}"></i>
                            <h5>{{ $title }}</h5>
                            <p>{{ $text }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= FAQ ================= --}}
    <section class="section" id="faq">
        <div class="container" style="max-width: 860px">
            <div class="text-center mb-5 reveal">
                <span class="eyebrow"><i class="bi bi-question-circle-fill"></i>Questions parents ask</span>
                <h2 class="section-title">Good to know</h2>
            </div>
            <div class="accordion reveal" id="faqAccordion">
                @php
                    $faqs = [
                        ['What ages do you care for?', 'Our daycare programs cover ' . (collect($programs)->isNotEmpty() ? collect($programs)->map(fn ($p) => $p['name'] . ' (' . $p['ages'] . ')')->implode(', ') : 'infants to school-age children') . '. Ask us about the best fit for your child.'],
                        ['Which therapies do you offer?', ($therapyNames ? 'We currently offer ' . collect($therapies)->map(fn ($t) => $therapyMeta[$t['type']]['title'] ?? strtoupper($t['type']))->implode(', ') . '.' : 'Speech, occupational and ABA therapy.') . ' Sessions are one-on-one with a specialist therapist.'],
                        ['How do therapy packages work?', 'A package includes a set number of sessions. Each completed session is deducted automatically, and you can always see upcoming sessions and how many remain in the parent app.'],
                        ['How will I know how my child’s day went?', 'Our parent app shows check-in and check-out times, meals, naps, activities, class photos and notices — updated by our staff throughout the day.'],
                        ['How can I pay fees?', 'Pay online from the app or a secure payment link with bKash, Nagad or card, or pay at the front desk. Receipts are saved in the app.'],
                        ['How do we get started?', 'Create a parent account or visit us for a tour. We’ll help you choose a program, a therapy plan, or both.'],
                    ];
                @endphp
                @foreach($faqs as $i => [$q, $a])
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}" aria-expanded="{{ $i ? 'false' : 'true' }}">{{ $q }}</button>
                        </h3>
                        <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">{{ $a }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="pb-5">
        <div class="container">
            <div class="cta reveal">
                <span class="bubble" style="width:220px;height:220px;top:-80px;left:-60px"></span>
                <span class="bubble" style="width:160px;height:160px;bottom:-60px;right:8%"></span>
                <div class="position-relative">
                    <h2>Give your child the best start 💛</h2>
                    <p>Care, learning and therapy — together, with a team that treats your child like family.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="{{ route('register') }}" class="btn btn-pill btn-white">Enroll your child <i class="bi bi-arrow-right ms-1"></i></a>
                        @if($org['phone'])
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $org['phone']) }}" class="btn btn-pill btn-outline-white"><i class="bi bi-telephone me-1"></i> Call {{ $org['phone'] }}</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-pill btn-outline-white">Parent sign in</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= FOOTER ================= --}}
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ asset('icons/icon-192.png') }}" alt="" width="36" height="36" style="border-radius:10px">
                        <span class="brand-text fs-4" style="color:var(--ink)">{{ $brand }}</span>
                    </div>
                    <p class="text-muted mb-0">Daycare, preschool and child therapy — one caring team.</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>Explore</h6>
                    <ul>
                        <li><a href="#programs">Daycare programs</a></li>
                        <li><a href="#therapy">Therapy</a></li>
                        <li><a href="#app">Parent app</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>Account</h6>
                    <ul>
                        <li><a href="{{ route('login') }}">Sign in</a></li>
                        <li><a href="{{ route('register') }}">Create account</a></li>
                        <li><a href="{{ route('privacy-policy') }}">Privacy policy</a></li>
                        <li><a href="{{ route('account-deletion.show') }}">Delete account</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6>Contact</h6>
                    <ul>
                        @if($org['address'])<li><i class="bi bi-geo-alt me-2"></i>{{ $org['address'] }}</li>@endif
                        @if($org['phone'])<li><i class="bi bi-telephone me-2"></i><a href="tel:{{ preg_replace('/[^\d+]/', '', $org['phone']) }}">{{ $org['phone'] }}</a></li>@endif
                        @if($org['email'])<li><i class="bi bi-envelope me-2"></i><a href="mailto:{{ $org['email'] }}">{{ $org['email'] }}</a></li>@endif
                        @unless($org['address'] || $org['phone'] || $org['email'])<li class="text-muted">Visit us or sign in to contact the office.</li>@endunless
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 small text-muted">
                <span>&copy; {{ date('Y') }} {{ $brand }}. All rights reserved.</span>
                <span>Made with <i class="bi bi-heart-fill" style="color:var(--coral)"></i> for little ones.</span>
            </div>
        </div>
    </footer>

    {{-- ================= ANNOUNCEMENT MODAL ================= --}}
    <div class="modal fade announcement-modal" id="announcementModal" tabindex="-1" aria-labelledby="modalAnnouncementTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="head">
                    <div class="ico"><i class="bi bi-megaphone-fill"></i></div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <span class="badge rounded-pill text-bg-light border" id="modalAnnouncementAudience">Audience</span>
                            <span class="text-muted small" id="modalAnnouncementDate"></span>
                        </div>
                        <h4 class="mb-0" id="modalAnnouncementTitle">Announcement</h4>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="content" id="modalAnnouncementContent"></div>
                <div class="d-flex justify-content-between align-items-center px-4 pb-4">
                    <div class="text-muted small" id="modalAnnouncementExpiresWrapper">
                        <i class="bi bi-clock-history me-1 text-warning"></i><span id="modalAnnouncementExpires"></span>
                    </div>
                    <button type="button" class="btn btn-pill btn-ghost py-2" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Navbar shadow on scroll
            const nav = document.getElementById('mainNav');
            const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 10);
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });

            // Close the mobile menu after choosing a section
            document.querySelectorAll('#navbarNav .nav-link').forEach(link => link.addEventListener('click', () => {
                const menu = bootstrap.Collapse.getInstance(document.getElementById('navbarNav'));
                if (menu) menu.hide();
            }));

            // Reveal sections as they scroll into view
            const items = document.querySelectorAll('.reveal');
            if ('IntersectionObserver' in window) {
                const io = new IntersectionObserver(entries => entries.forEach(e => {
                    if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
                }), { threshold: 0.12 });
                items.forEach(el => io.observe(el));
            } else {
                items.forEach(el => el.classList.add('in'));
            }

            // Announcement details
            const modalEl = document.getElementById('announcementModal');
            if (!modalEl) return;
            const modal = new bootstrap.Modal(modalEl);
            document.querySelectorAll('.announcement-trigger').forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.getElementById('modalAnnouncementTitle').textContent = this.dataset.title;
                    document.getElementById('modalAnnouncementContent').textContent = this.dataset.content;
                    document.getElementById('modalAnnouncementDate').innerHTML = '<i class="bi bi-calendar-event me-1"></i>' + this.dataset.published;
                    document.getElementById('modalAnnouncementAudience').textContent = this.dataset.audience;
                    const wrap = document.getElementById('modalAnnouncementExpiresWrapper');
                    if (this.dataset.expires) {
                        document.getElementById('modalAnnouncementExpires').textContent = 'Valid until ' + this.dataset.expires;
                        wrap.classList.remove('d-none');
                    } else {
                        wrap.classList.add('d-none');
                    }
                    modal.show();
                });
            });
        });
    </script>
</body>
</html>
