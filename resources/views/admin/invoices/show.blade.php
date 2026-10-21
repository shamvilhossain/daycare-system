<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* ---------- Hero ---------- */
        .inv-hero {
            position: relative; overflow: hidden;
            border-radius: 18px; color: #fff;
            padding: 1.5rem 1.75rem;
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 45%, #4f46e5 100%);
            box-shadow: 0 12px 30px -12px rgba(13, 148, 136, .55);
        }
        .inv-hero.is-paid      { background: linear-gradient(135deg, #047857 0%, #10b981 55%, #0ea5e9 100%); }
        .inv-hero.is-overdue   { background: linear-gradient(135deg, #9f1239 0%, #e11d48 50%, #f97316 100%); box-shadow: 0 12px 30px -12px rgba(225, 29, 72, .5); }
        .inv-hero.is-cancelled { background: linear-gradient(135deg, #334155 0%, #64748b 100%); box-shadow: 0 12px 30px -12px rgba(51, 65, 85, .5); }
        .inv-hero::before, .inv-hero::after {
            content: ''; position: absolute; border-radius: 50%; background: rgba(255,255,255,.08); pointer-events: none;
        }
        .inv-hero::before { width: 260px; height: 260px; top: -110px; right: -60px; }
        .inv-hero::after  { width: 160px; height: 160px; bottom: -90px; left: 38%; }
        .inv-hero > * { position: relative; z-index: 1; }
        .inv-hero .eyebrow { font-size: .72rem; letter-spacing: .12em; text-transform: uppercase; opacity: .8; font-weight: 600; }
        .inv-hero .inv-no { font-size: 1.65rem; font-weight: 800; letter-spacing: .02em; line-height: 1.15; }
        .inv-hero .meta { font-size: .88rem; opacity: .92; }
        .inv-hero .meta i { opacity: .8; }
        .hero-pill {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .3rem .8rem; border-radius: 999px; font-size: .78rem; font-weight: 700;
            background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3); backdrop-filter: blur(4px);
        }
        .hero-amount-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .1em; opacity: .85; font-weight: 600; }
        .hero-amount { font-size: 2.4rem; font-weight: 800; line-height: 1; letter-spacing: -.02em; }
        .hero-progress { height: 8px; border-radius: 99px; background: rgba(255,255,255,.22); overflow: hidden; }
        .hero-progress > div { height: 100%; border-radius: 99px; background: #fff; transition: width .6s ease; }
        .hero-actions .btn { border-radius: 10px; font-weight: 600; padding: .5rem 1rem; }
        .btn-hero-light { background: #fff; color: #0f172a; border: 0; }
        .btn-hero-light:hover { background: #f1f5f9; color: #0f172a; }
        .btn-hero-ghost { background: rgba(255,255,255,.14); color: #fff; border: 1px solid rgba(255,255,255,.35); }
        .btn-hero-ghost:hover { background: rgba(255,255,255,.24); color: #fff; }

        /* ---------- Cards ---------- */
        .soft-card { border: 0; border-radius: 16px; box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 6px 24px -12px rgba(15,23,42,.12); }
        .soft-card > .card-header { background: transparent; border-bottom: 1px solid #f1f5f9; padding: 1rem 1.25rem; }
        .soft-card > .card-header::after { display: none; } /* AdminLTE clearfix breaks flex spacing */
        .soft-card .card-title-sm { font-size: .95rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: .5rem; }
        .icon-chip { width: 32px; height: 32px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: .95rem; flex-shrink: 0; }

        /* ---------- Invoice document ---------- */
        .inv-doc { position: relative; overflow: hidden; }
        .inv-doc .doc-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; }
        .inv-doc .brand { display: flex; align-items: center; gap: .75rem; }
        .inv-doc .brand img { width: 46px; height: 46px; border-radius: 12px; }
        .inv-doc .brand-name { font-weight: 800; font-size: 1.15rem; color: #0f172a; }
        .inv-doc .doc-title { font-size: 1.9rem; font-weight: 800; letter-spacing: .14em; color: #0d9488; text-align: right; line-height: 1; }
        .inv-doc .label { font-size: .7rem; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; font-weight: 700; margin-bottom: .3rem; }
        .inv-doc .party-name { font-weight: 700; color: #0f172a; }
        .inv-doc .items th { font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: #64748b; font-weight: 700; background: #f8fafc; border-bottom: 0; padding: .7rem .75rem; }
        .inv-doc .items td { padding: .85rem .75rem; vertical-align: middle; border-color: #f1f5f9; background: transparent; }
        .inv-doc .items { --bs-table-bg: transparent; }
        .inv-doc .totals { max-width: 340px; margin-left: auto; }
        .inv-doc .totals .row-line { display: flex; justify-content: space-between; padding: .4rem 0; color: #475569; }
        .inv-doc .totals .grand { border-top: 2px solid #0f172a; margin-top: .4rem; padding-top: .7rem; font-size: 1.15rem; font-weight: 800; color: #0f172a; }
        .stamp {
            position: absolute; top: 55%; left: 50%; transform: translate(-50%, -50%) rotate(-16deg);
            border: 6px solid currentColor; border-radius: 18px; padding: .4rem 1.6rem;
            font-size: 4.5rem; font-weight: 900; letter-spacing: .25em; opacity: .07; pointer-events: none; z-index: 0;
            white-space: nowrap;
        }
        .stamp.paid { color: #059669; }
        .stamp.cancelled { color: #dc2626; }
        .inv-doc .card-body > * { position: relative; z-index: 1; }

        /* ---------- Summary ring ---------- */
        .ring {
            --pct: 0; width: 132px; height: 132px; border-radius: 50%;
            background: conic-gradient(#10b981 calc(var(--pct) * 1%), #e2e8f0 0);
            display: grid; place-items: center; margin: 0 auto;
        }
        .ring > div { width: 104px; height: 104px; border-radius: 50%; background: #fff; display: grid; place-items: center; text-align: center; }
        .ring .pct { font-size: 1.6rem; font-weight: 800; color: #0f172a; line-height: 1; }
        .ring .pct-label { font-size: .7rem; color: #64748b; text-transform: uppercase; letter-spacing: .08em; font-weight: 600; }
        .kv { display: flex; justify-content: space-between; align-items: center; padding: .55rem 0; border-bottom: 1px dashed #e2e8f0; font-size: .9rem; }
        .kv:last-child { border-bottom: 0; }

        /* ---------- Timeline ---------- */
        .timeline-x { list-style: none; padding: 0; margin: 0; position: relative; }
        .timeline-x::before { content: ''; position: absolute; left: 15px; top: 6px; bottom: 6px; width: 2px; background: #e2e8f0; }
        .timeline-x li { position: relative; padding-left: 46px; padding-bottom: 1.1rem; }
        .timeline-x li:last-child { padding-bottom: 0; }
        .timeline-x .dot { position: absolute; left: 0; top: 0; width: 32px; height: 32px; border-radius: 50%; display: grid; place-items: center; font-size: .85rem; border: 3px solid #fff; box-shadow: 0 0 0 1px #e2e8f0; }
        .timeline-x .t-title { font-weight: 600; font-size: .88rem; color: #0f172a; }
        .timeline-x .t-time { font-size: .76rem; color: #94a3b8; }

        /* ---------- Tables ---------- */
        .table-modern thead th { font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: #64748b; font-weight: 700; background: #f8fafc; border-bottom: 0; padding: .75rem; white-space: nowrap; }
        .table-modern td { padding: .8rem .75rem; vertical-align: middle; border-color: #f1f5f9; }
        .method-chip { display: inline-flex; align-items: center; gap: .35rem; padding: .25rem .65rem; border-radius: 999px; font-size: .76rem; font-weight: 600; background: #eef2ff; color: #4338ca; }
        .empty-state { padding: 2.25rem 1rem; text-align: center; color: #94a3b8; }
        .empty-state i { font-size: 2.2rem; display: block; margin-bottom: .5rem; opacity: .5; }

        /* ---------- Print: only the invoice document ---------- */
        @media print {
            @page { margin: 12mm; }
            /* Higher specificity than AdminLTE's own print !important rules */
            body .app-wrapper > aside.app-sidebar, body .app-wrapper > nav.app-header,
            .sidebar-overlay, .inv-hero, .no-print, .modal, .breadcrumb { display: none !important; }
            body .app-wrapper { display: block !important; }
            body .app-wrapper > .app-main, .app-content, .container-fluid { margin: 0 !important; padding: 0 !important; max-width: 100% !important; }
            body { background: #fff !important; }
            a[href]::after { content: none !important; } /* AdminLTE prints link URLs */
            .col-xl-8 { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }
            .soft-card { box-shadow: none !important; border: 0 !important; }
            .inv-doc .card-body { padding: 0 !important; }
            .stamp { opacity: .12; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
@php
    $org = config('parentapp.organization');
    $total = (float) $invoice->total_amount;
    $paid = (float) $invoice->paid_total;
    $balance = (float) $invoice->balance_due;
    $pct = $total > 0 ? min(100, round($paid / $total * 100)) : 0;
    $isPaid = $invoice->status === 'paid' || ($total > 0 && $balance <= 0 && $invoice->status !== 'cancelled');
    $isCancelled = $invoice->status === 'cancelled';
    $isOverdueNow = $invoice->is_open && $invoice->due_date->isPast() && !$invoice->due_date->isToday();
    $heroClass = $isCancelled ? 'is-cancelled' : ($isPaid ? 'is-paid' : (($invoice->status === 'overdue' || $isOverdueNow) ? 'is-overdue' : ''));
    $daysToDue = (int) now()->startOfDay()->diffInDays($invoice->due_date->copy()->startOfDay(), false);
    $dueText = match (true) {
        $isCancelled => 'Cancelled',
        $isPaid => 'Settled' . ($invoice->paid_date ? ' on ' . $invoice->paid_date->format('M d, Y') : ''),
        $daysToDue < 0 => 'Overdue by ' . abs($daysToDue) . ' ' . Str::plural('day', abs($daysToDue)),
        $daysToDue === 0 => 'Due today',
        default => 'Due in ' . $daysToDue . ' ' . Str::plural('day', $daysToDue),
    };
    $statusIcon = match ($invoice->status) {
        'paid' => 'bi-check-circle-fill', 'cancelled' => 'bi-x-circle-fill', 'overdue' => 'bi-exclamation-triangle-fill',
        'sent' => 'bi-send-fill', 'partially_paid' => 'bi-pie-chart-fill', default => 'bi-file-earmark-text-fill',
    };
    $canSendLink = $invoice->is_open && $balance >= config('payments.min_amount');
    $methodIcon = fn ($m) => match ($m) {
        'cash' => 'bi-cash', 'card' => 'bi-credit-card', 'bank_transfer' => 'bi-bank', 'online' => 'bi-globe2', default => 'bi-wallet2',
    };

    // Activity timeline
    $events = collect([[
        'at' => $invoice->created_at, 'title' => 'Invoice created', 'sub' => '৳' . number_format($total, 2) . ' for ' . ($invoice->child->first_name ?? 'child'),
        'icon' => 'bi-receipt', 'class' => 'bg-primary-subtle text-primary',
    ]]);
    foreach ($invoice->payments as $p) {
        $events->push([
            'at' => $p->paid_at, 'title' => '৳' . number_format($p->paid_amount, 2) . ' received',
            'sub' => $p->payment_method_label . ($p->receivedBy ? ' · by ' . $p->receivedBy->name : ($p->payment_method === 'online' ? ' · via SSLCommerz' : '')),
            'icon' => 'bi-check2', 'class' => 'bg-success-subtle text-success',
        ]);
    }
    foreach ($invoice->onlineTransactions->where('status', '!=', \App\Models\PaymentOnlineTransaction::STATUS_SUCCESS) as $t) {
        $events->push([
            'at' => $t->created_at, 'title' => 'Online payment ' . strtolower($t->status_label),
            'sub' => '৳' . number_format($t->amount, 2) . ' · ' . $t->transaction_id,
            'icon' => 'bi-globe2', 'class' => (int) $t->status === 0 ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning-emphasis',
        ]);
    }
    if ($isCancelled) {
        $events->push(['at' => $invoice->updated_at, 'title' => 'Invoice cancelled', 'sub' => null, 'icon' => 'bi-x-lg', 'class' => 'bg-danger-subtle text-danger']);
    }
    $events = $events->sortByDesc('at')->values();
@endphp
    <div class="app-wrapper">
        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a></li></ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="user-footer">
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <button type="submit" class="btn btn-default btn-flat float-end">Sign out</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- Sidebar --}}
        @include('partials.sidebar')

        <main class="app-main">
            <div class="app-content pt-3">
                <div class="container-fluid">

                    {{-- Breadcrumb --}}
                    <nav aria-label="breadcrumb" class="mb-3 no-print">
                        <ol class="breadcrumb small mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}" class="text-decoration-none"><i class="bi bi-receipt me-1"></i>Invoices</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $invoice->invoice_number }}</li>
                        </ol>
                    </nav>

                    {{-- Alerts --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm no-print">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm no-print">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm no-print">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (!empty($pendingAdmission))
                        <div class="alert {{ $invoice->status === 'paid' ? 'alert-success' : 'alert-info' }} border-0 shadow-sm no-print d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <i class="bi bi-receipt-cutoff me-2"></i><strong>Admission invoice</strong> for {{ $invoice->child->full_name }}'s enrolment in
                                {{ $pendingAdmission->program->name ?? 'the programme' }} (pending approval).
                                {{ $invoice->status === 'paid' ? 'It is paid — the enrolment can be approved.' : 'Send the payment link; approve the enrolment once it is paid.' }}
                            </div>
                            <a href="{{ route('admin.enrollments.show', $pendingAdmission) }}" class="btn btn-sm {{ $invoice->status === 'paid' ? 'btn-success' : 'btn-outline-primary' }}">
                                <i class="bi bi-clipboard-check me-1"></i>{{ $invoice->status === 'paid' ? 'Approve enrolment' : 'View enrolment' }}
                            </a>
                        </div>
                    @endif

                    {{-- ================= HERO ================= --}}
                    <div class="inv-hero {{ $heroClass }} mb-4">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-7">
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="eyebrow">Invoice</span>
                                    <span class="hero-pill"><i class="bi {{ $statusIcon }}"></i>{{ $invoice->status_label }}</span>
                                    <span class="hero-pill"><i class="bi bi-tag-fill"></i>{{ $invoice->invoice_type_label }}</span>
                                </div>
                                <div class="inv-no mb-2">{{ $invoice->invoice_number }}</div>
                                <div class="meta d-flex flex-wrap gap-3">
                                    <span><i class="bi bi-person-heart me-1"></i>{{ $invoice->child->full_name ?? '—' }}</span>
                                    <span><i class="bi bi-person me-1"></i>{{ $invoice->parent->full_name ?? '—' }}</span>
                                    <span><i class="bi bi-calendar3 me-1"></i>Issued {{ $invoice->invoice_date->format('M d, Y') }}</span>
                                    <span><i class="bi bi-hourglass-split me-1"></i>{{ $dueText }}</span>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="text-lg-end">
                                    <div class="hero-amount-label">{{ $isPaid ? 'Amount paid' : ($isCancelled ? 'Invoice total' : 'Balance due') }}</div>
                                    <div class="hero-amount my-1">৳{{ number_format($isPaid ? $paid : ($isCancelled ? $total : $balance), 2) }}</div>
                                    <div class="small opacity-75 mb-2">
                                        ৳{{ number_format($paid, 2) }} paid of ৳{{ number_format($total, 2) }} · {{ $pct }}%
                                    </div>
                                    <div class="hero-progress ms-lg-auto" style="max-width: 320px"><div style="width: {{ $pct }}%"></div></div>
                                </div>
                            </div>
                        </div>

                        <div class="hero-actions d-flex flex-wrap gap-2 mt-4 no-print">
                            @if($canSendLink)
                                <form action="{{ route('admin.invoices.payment-link', $invoice) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-hero-light">
                                        <i class="bi bi-send-fill me-1 text-primary"></i>Send Payment Link
                                    </button>
                                </form>
                            @endif
                            @if($invoice->is_open)
                                <button type="button" class="btn {{ $canSendLink ? 'btn-hero-ghost' : 'btn-hero-light' }}" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    <i class="bi bi-cash-stack me-1"></i>Record Payment
                                </button>
                            @endif
                            <button type="button" class="btn btn-hero-ghost" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-hero-ghost">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
                            </a>
                            <div class="ms-auto d-flex gap-2">
                                @if($invoice->is_open)
                                    <form action="{{ route('admin.invoices.cancel', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this invoice?');">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-hero-ghost">
                                            <i class="bi bi-x-circle me-1"></i>Cancel Invoice
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.invoices.index') }}" class="btn btn-hero-ghost">
                                    <i class="bi bi-arrow-left me-1"></i>Back
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- ================= LEFT: invoice document + history ================= --}}
                        <div class="col-xl-8">
                            <div class="card soft-card inv-doc mb-4">
                                @if($isPaid)
                                    <div class="stamp paid">PAID</div>
                                @elseif($isCancelled)
                                    <div class="stamp cancelled">VOID</div>
                                @endif
                                <div class="card-body p-4 p-lg-5">
                                    <div class="doc-head mb-4">
                                        <div class="brand">
                                            <img src="{{ asset('icons/icon-192.png') }}" alt="">
                                            <div>
                                                <div class="brand-name">{{ $org['name'] }}</div>
                                                <div class="small text-muted">
                                                    {{ collect([$org['address'], $org['phone'], $org['email']])->filter()->implode(' · ') ?: 'Daycare & Therapy Center' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="doc-title">INVOICE</div>
                                            <div class="text-end small text-muted mt-1">{{ $invoice->invoice_number }}</div>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-sm-4">
                                            <div class="label">Bill to</div>
                                            @if($invoice->parent)
                                                <div class="party-name">{{ $invoice->parent->full_name }}</div>
                                                <div class="small text-muted">{{ $invoice->parent->mobile ?? '' }}</div>
                                                <div class="small text-muted text-break">{{ $invoice->parent->user->email ?? '' }}</div>
                                            @else
                                                <div class="text-muted small">Parent record not found.</div>
                                            @endif
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="label">For child</div>
                                            <div class="party-name">{{ $invoice->child->full_name ?? '—' }}</div>
                                            @if($invoice->child?->date_of_birth)
                                                <div class="small text-muted">{{ $invoice->child->formatted_age }}</div>
                                            @endif
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Issued</span><span class="fw-semibold">{{ $invoice->invoice_date->format('M d, Y') }}</span></div>
                                            <div class="d-flex justify-content-between small mb-1">
                                                <span class="text-muted">Due</span>
                                                <span class="fw-semibold {{ $isOverdueNow || $invoice->status === 'overdue' ? 'text-danger' : '' }}">{{ $invoice->due_date->format('M d, Y') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between small"><span class="text-muted">Status</span><span class="badge {{ $invoice->status_badge_class }}">{{ $invoice->status_label }}</span></div>
                                        </div>
                                    </div>

                                    <div class="table-responsive mb-4">
                                        <table class="table items mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width:44px">#</th>
                                                    <th>Description</th>
                                                    <th class="text-end">Unit price</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($invoice->items as $i => $item)
                                                    <tr>
                                                        <td class="text-muted">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                                        <td>
                                                            <div class="fw-semibold text-dark">{{ $item->description }}</div>
                                                            @if($item->therapy_session_id)
                                                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle mt-1" style="font-size:.65rem">
                                                                    <i class="bi bi-heart-pulse me-1"></i>Therapy session
                                                                </span>
                                                            @elseif($item->child_therapy_package_id)
                                                                <a href="{{ route('admin.child-therapy-packages.show', $item->child_therapy_package_id) }}" class="badge bg-info-subtle text-info-emphasis border border-info-subtle mt-1 text-decoration-none" style="font-size:.65rem">
                                                                    <i class="bi bi-box2-heart me-1"></i>Therapy package · view balance
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td class="text-end text-muted">৳{{ number_format($item->unit_price, 2) }}</td>
                                                        <td class="text-center text-muted">{{ $item->quantity }}</td>
                                                        <td class="text-end fw-semibold text-dark">৳{{ number_format($item->amount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="totals">
                                        <div class="row-line"><span>Subtotal</span><span class="fw-semibold text-dark">৳{{ number_format($total, 2) }}</span></div>
                                        <div class="row-line"><span>Paid</span><span class="fw-semibold text-success">− ৳{{ number_format($paid, 2) }}</span></div>
                                        <div class="row-line grand"><span>Balance due</span><span>৳{{ number_format($balance, 2) }}</span></div>
                                    </div>

                                    <div class="small text-muted mt-4 pt-3 border-top">
                                        <i class="bi bi-info-circle me-1"></i>Pay at the front desk or online with bKash, Nagad or card. Thank you for choosing {{ $org['name'] }}!
                                    </div>
                                </div>
                            </div>

                            {{-- Payment history --}}
                            <div class="card soft-card mb-4 no-print">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title-sm"><span class="icon-chip bg-success-subtle text-success"><i class="bi bi-clock-history"></i></span>Payment History</h5>
                                    @if($invoice->is_open && $balance > 0)
                                        <button type="button" class="btn btn-sm btn-success rounded-3" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                            <i class="bi bi-plus-lg me-1"></i>Record Payment
                                        </button>
                                    @endif
                                </div>
                                <div class="card-body p-0">
                                    @if($invoice->payments->isEmpty())
                                        <div class="empty-state"><i class="bi bi-cash-coin"></i>No payments recorded yet.</div>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-modern table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="ps-3">Date</th>
                                                        <th>Method</th>
                                                        <th>Reference</th>
                                                        <th>Received by</th>
                                                        <th>Remarks</th>
                                                        <th class="text-end pe-3">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($invoice->payments->sortByDesc('paid_at') as $payment)
                                                        <tr>
                                                            <td class="ps-3">
                                                                <div class="fw-semibold small text-dark">{{ $payment->paid_at->format('M d, Y') }}</div>
                                                                <div class="small text-muted">{{ $payment->paid_at->format('h:i A') }}</div>
                                                            </td>
                                                            <td><span class="method-chip"><i class="bi {{ $methodIcon($payment->payment_method) }}"></i>{{ $payment->payment_method_label }}</span></td>
                                                            <td>
                                                                @if($payment->transaction_id)
                                                                    <code class="small text-dark bg-light px-2 py-1 rounded">{{ $payment->transaction_id }}</code>
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                            <td class="small">{{ $payment->receivedBy->name ?? ($payment->payment_method === 'online' ? 'Online (parent)' : '—') }}</td>
                                                            <td class="small text-muted" style="max-width: 240px">{{ $payment->remarks ?? '—' }}</td>
                                                            <td class="text-end pe-3 fw-bold text-success">৳{{ number_format($payment->paid_amount, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Online payment attempts (SSLCommerz) --}}
                            @if($invoice->onlineTransactions->isNotEmpty())
                                <div class="card soft-card mb-4 no-print">
                                    <div class="card-header">
                                        <h5 class="card-title-sm"><span class="icon-chip bg-primary-subtle text-primary"><i class="bi bi-globe2"></i></span>Online Payment Attempts</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-modern table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="ps-3">Started</th>
                                                        <th>Reference</th>
                                                        <th>Method</th>
                                                        <th>Bank ref.</th>
                                                        <th>Status</th>
                                                        <th>Notes</th>
                                                        <th class="text-end pe-3">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($invoice->onlineTransactions->sortByDesc('id') as $txn)
                                                        <tr>
                                                            <td class="ps-3 small">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                                                            <td><code class="small text-dark">{{ $txn->transaction_id }}</code></td>
                                                            <td class="small">{{ $txn->method_label ?? '—' }}</td>
                                                            <td class="small">{{ $txn->pg_bank_transaction_id ?? '—' }}</td>
                                                            <td><span class="badge {{ $txn->status_badge_class }}">{{ $txn->status_label }}</span></td>
                                                            <td class="small text-muted">{{ $txn->remarks ? Str::limit($txn->remarks, 80) : '—' }}</td>
                                                            <td class="text-end pe-3 small fw-semibold">৳{{ number_format($txn->amount, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- ================= RIGHT: summary, contact, timeline ================= --}}
                        <div class="col-xl-4 no-print">
                            <div class="card soft-card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title-sm"><span class="icon-chip bg-success-subtle text-success"><i class="bi bi-pie-chart-fill"></i></span>Payment Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="ring mb-3" style="--pct: {{ $pct }}">
                                        <div><div><div class="pct">{{ $pct }}%</div><div class="pct-label">Collected</div></div></div>
                                    </div>
                                    <div class="kv"><span class="text-muted">Invoice total</span><span class="fw-semibold">৳{{ number_format($total, 2) }}</span></div>
                                    <div class="kv"><span class="text-muted">Paid</span><span class="fw-semibold text-success">৳{{ number_format($paid, 2) }}</span></div>
                                    <div class="kv"><span class="text-muted">Balance due</span><span class="fw-bold {{ $balance > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($balance, 2) }}</span></div>
                                    <div class="kv"><span class="text-muted">Payments</span><span class="fw-semibold">{{ $invoice->payments->count() }}</span></div>
                                </div>
                            </div>

                            <div class="card soft-card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title-sm"><span class="icon-chip bg-primary-subtle text-primary"><i class="bi bi-person-lines-fill"></i></span>Contact</h5>
                                </div>
                                <div class="card-body">
                                    @if($invoice->parent)
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="icon-chip bg-primary text-white fw-bold" style="width:44px;height:44px;border-radius:50%">
                                                {{ strtoupper(mb_substr($invoice->parent->first_name, 0, 1) . mb_substr($invoice->parent->last_name, 0, 1)) }}
                                            </div>
                                            <div style="min-width:0">
                                                <div class="fw-semibold text-dark">{{ $invoice->parent->full_name }}</div>
                                                <div class="small text-muted text-truncate">{{ $invoice->parent->user->email ?? 'No email' }}</div>
                                            </div>
                                        </div>
                                        @if($invoice->parent->mobile)
                                            @php $tel = preg_replace('/[^\d+]/', '', $invoice->parent->mobile); $wa = app(\App\Services\Payments\PaymentLinkService::class)->whatsappNumber($invoice->parent->mobile); @endphp
                                            <div class="d-flex gap-2">
                                                <a href="tel:{{ $tel }}" class="btn btn-sm btn-outline-secondary flex-fill rounded-3"><i class="bi bi-telephone me-1"></i>{{ $invoice->parent->mobile }}</a>
                                                @if($wa)
                                                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success rounded-3" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted small">Parent record not found.</span>
                                    @endif
                                </div>
                            </div>

                            <div class="card soft-card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title-sm"><span class="icon-chip bg-warning-subtle text-warning-emphasis"><i class="bi bi-activity"></i></span>Activity</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="timeline-x">
                                        @foreach($events as $e)
                                            <li>
                                                <span class="dot {{ $e['class'] }}"><i class="bi {{ $e['icon'] }}"></i></span>
                                                <div class="t-title">{{ $e['title'] }}</div>
                                                @if($e['sub'])<div class="small text-muted">{{ $e['sub'] }}</div>@endif
                                                <div class="t-time">{{ $e['at']->format('M d, Y · h:i A') }} · {{ $e['at']->diffForHumans() }}</div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    {{-- Payment Link Modal (shown right after "Send Payment Link") --}}
    @if(session('payment_link'))
        @php $pl = session('payment_link'); @endphp
        <div class="modal fade" id="paymentLinkModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow" style="border-radius:16px">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <span class="icon-chip bg-primary-subtle text-primary"><i class="bi bi-send-fill"></i></span>Payment link ready
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted mb-3">
                            Send this to {{ $invoice->parent?->full_name ?? 'the parent' }}. No login needed.
                            Valid until <strong>{{ $pl['expires_at'] }}</strong>; it always charges the current balance due.
                        </p>
                        <label class="form-label small fw-semibold">Message</label>
                        <textarea id="paymentLinkMessage" class="form-control mb-3 bg-light" rows="5" readonly>{{ $pl['message'] }}</textarea>
                        <label class="form-label small fw-semibold">Link only</label>
                        <div class="input-group mb-3">
                            <input id="paymentLinkUrl" type="text" class="form-control form-control-sm bg-light" value="{{ $pl['url'] }}" readonly>
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-copy="#paymentLinkUrl"><i class="bi bi-clipboard"></i></button>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @if($pl['whatsapp'])
                                <a href="{{ $pl['whatsapp'] }}" target="_blank" rel="noopener" class="btn btn-success rounded-3">
                                    <i class="bi bi-whatsapp me-1"></i>WhatsApp
                                </a>
                            @endif
                            @if($pl['sms'])
                                <a href="{{ $pl['sms'] }}" class="btn btn-outline-primary rounded-3">
                                    <i class="bi bi-chat-dots me-1"></i>SMS
                                </a>
                            @endif
                            <button type="button" class="btn btn-outline-secondary rounded-3" data-copy="#paymentLinkMessage">
                                <i class="bi bi-clipboard me-1"></i>Copy message
                            </button>
                        </div>
                        @unless($pl['whatsapp'] || $pl['sms'])
                            <div class="small text-warning mt-2">No mobile number on file for this parent. Copy the message instead.</div>
                        @endunless
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Record Payment Modal --}}
    @if($invoice->is_open)
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius:16px">
                <form method="POST" action="{{ route('admin.invoices.add-payment', $invoice) }}">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <span class="icon-chip bg-success-subtle text-success"><i class="bi bi-cash-stack"></i></span>Record Payment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between align-items-center p-3 mb-3 rounded-3" style="background:#f0fdfa">
                            <span class="small text-muted"><i class="bi bi-info-circle me-1"></i>Balance due on {{ $invoice->invoice_number }}</span>
                            <strong class="fs-5 text-dark">৳{{ number_format($balance, 2) }}</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Paid Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" name="paid_amount" class="form-control" step="0.01" min="0.01"
                                           max="{{ $balance }}" value="{{ $balance }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="paymentMethodSelect" class="form-select" required>
                                    <option value="cash" selected>Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="transactionIdGroup" style="display:none;">
                                <label class="form-label fw-semibold">Transaction ID</label>
                                <input type="text" name="transaction_id" id="transactionIdInput" class="form-control" placeholder="Enter transaction reference">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="paid_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Penalty Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" name="penalty_amount" class="form-control" step="0.01" min="0" value="0">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2" placeholder="Optional notes about this payment..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-3 px-4">
                            <i class="bi bi-check-lg me-1"></i>Confirm Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const methodSelect = document.getElementById('paymentMethodSelect');
        const txnGroup = document.getElementById('transactionIdGroup');
        const txnInput = document.getElementById('transactionIdInput');

        if (methodSelect) {
            methodSelect.addEventListener('change', function () {
                if (this.value === 'cash') {
                    txnGroup.style.display = 'none';
                    txnInput.value = '';
                } else {
                    txnGroup.style.display = 'block';
                }
            });
        }

        // Payment link: open the share dialog and wire up copy buttons
        const linkModal = document.getElementById('paymentLinkModal');
        if (linkModal) {
            new bootstrap.Modal(linkModal).show();
        }
        document.querySelectorAll('[data-copy]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const field = document.querySelector(btn.dataset.copy);
                field.select();
                (navigator.clipboard ? navigator.clipboard.writeText(field.value) : Promise.resolve(document.execCommand('copy')))
                    .then(function () {
                        const html = btn.innerHTML;
                        btn.innerHTML = '<i class="bi bi-check2"></i> Copied';
                        setTimeout(function () { btn.innerHTML = html; }, 1500);
                    });
            });
        });

        // Opened from the list's Print button (?print=1): print straight away
        @if(request()->boolean('print'))
            window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 300); });
        @endif

        // Show payment modal if there were validation errors
        @if($errors->any())
            const paymentModal = document.getElementById('paymentModal');
            if (paymentModal) {
                const modal = new bootstrap.Modal(paymentModal);
                modal.show();
            }
        @endif
    });
    </script>
</body>
</html>
