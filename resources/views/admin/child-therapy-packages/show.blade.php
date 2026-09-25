<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package Details | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .detail-label { font-weight: 600; color: #6b7280; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.15rem; }
        .detail-value { font-size: 1rem; color: #1f2937; }
        .therapy-badge-slt { background: #dbeafe; color: #1e40af; }
        .therapy-badge-aba { background: #fef3c7; color: #92400e; }
        .therapy-badge-ot  { background: #d1fae5; color: #065f46; }
        .page-banner {
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #6ee7b7 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 25px rgba(5,150,105,0.18);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -50px; right: -30px;
            width: 170px; height: 170px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .balance-card {
            border-radius: 12px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .balance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
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
        @include('partials.sidebar')

        <main class="app-main">
            <div class="app-content-header pt-2 pb-0">
                <div class="container-fluid">
                    <div class="page-banner">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <h2 class="fw-bold mb-1" style="font-size:1.3rem; position:relative; z-index:1;">
                                    <i class="bi bi-box-seam me-2"></i>{{ $childTherapyPackage->package->name ?? 'Unknown Package' }}
                                </h2>
                                <p class="mb-0" style="opacity:0.9; position:relative; z-index:1;">
                                    Purchased for <strong>{{ $childTherapyPackage->child->full_name ?? 'Unknown' }}</strong>
                                </p>
                            </div>
                            <div class="d-flex gap-2" style="position:relative; z-index:1;">
                                @unless(auth()->user()->hasRole('parent'))
                                    @if($childTherapyPackage->status === 'active')
                                        <form action="{{ route('admin.child-therapy-packages.cancel', $childTherapyPackage) }}" method="POST" onsubmit="return confirm('Cancel this package? This cannot be undone.');">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-light btn-sm fw-bold shadow-sm"><i class="bi bi-x-lg me-1"></i> Cancel Package</button>
                                        </form>
                                    @endif
                                @endunless
                                <a href="{{ route('admin.child-therapy-packages.index') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row g-3">
                        {{-- Purchase Details --}}
                        <div class="col-lg-5">
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>Purchase Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="detail-label">Child</div>
                                            <div class="detail-value fw-semibold">{{ $childTherapyPackage->child->full_name ?? '—' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-label">Package</div>
                                            <div class="detail-value fw-semibold">{{ $childTherapyPackage->package->name ?? '—' }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-label">Purchase Price</div>
                                            <div class="detail-value fw-bold text-success">৳{{ number_format($childTherapyPackage->purchase_price, 2) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-label">Status</div>
                                            <div class="detail-value">
                                                @if($childTherapyPackage->status === 'active')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-6">Active</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6">Cancelled</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-label">Purchased</div>
                                            <div class="detail-value">{{ \Carbon\Carbon::parse($childTherapyPackage->purchased_at)->format('M d, Y') }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-label">Expires</div>
                                            <div class="detail-value">
                                                @if($childTherapyPackage->expires_at)
                                                    <span class="{{ \Carbon\Carbon::parse($childTherapyPackage->expires_at)->isPast() ? 'text-danger fw-bold' : '' }}">
                                                        {{ \Carbon\Carbon::parse($childTherapyPackage->expires_at)->format('M d, Y') }}
                                                    </span>
                                                    @if(\Carbon\Carbon::parse($childTherapyPackage->expires_at)->isPast())
                                                        <span class="badge bg-danger-subtle text-danger border ms-1">Expired</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No expiry</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($childTherapyPackage->invoice)
                                        <div class="col-12">
                                            <div class="detail-label">Invoice</div>
                                            <div class="detail-value">
                                                <a href="{{ route('admin.invoices.show', $childTherapyPackage->invoice) }}" class="text-primary">
                                                    #{{ $childTherapyPackage->invoice->invoice_number }}
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Session Balance --}}
                        <div class="col-lg-7">
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-speedometer2 me-2 text-info"></i>Session Balance</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        @foreach($childTherapyPackage->items as $item)
                                            @php
                                                $remaining = $childTherapyPackage->remainingFor($item->service);
                                                $used = $item->sessions_included - $remaining;
                                                $pct = $item->sessions_included > 0 ? round(($used / $item->sessions_included) * 100) : 0;
                                            @endphp
                                            <div class="col-md-6">
                                                <div class="card balance-card border shadow-sm">
                                                    <div class="card-body p-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <div>
                                                                <span class="badge therapy-badge-{{ $item->service->therapy_type ?? 'slt' }} border me-1" style="font-size:0.65rem;">
                                                                    {{ strtoupper($item->service->therapy_type ?? '?') }}
                                                                </span>
                                                                <span class="fw-semibold small">{{ $item->service->name ?? 'Unknown' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                                            <span>{{ $used }} used of {{ $item->sessions_included }}</span>
                                                            <span class="fw-bold {{ $remaining === 0 ? 'text-danger' : 'text-success' }}">{{ $remaining }} remaining</span>
                                                        </div>
                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar {{ $remaining === 0 ? 'bg-danger' : 'bg-success' }}" style="width: {{ $pct }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($childTherapyPackage->items->isEmpty())
                                            <div class="col-12 text-center text-muted py-3">
                                                <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary"></i>
                                                No services in this package.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Related Sessions --}}
                            @if($childTherapyPackage->sessions && $childTherapyPackage->sessions->count() > 0)
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar2-heart me-2 text-purple"></i>Related Sessions ({{ $childTherapyPackage->sessions->count() }})</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0 table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Service</th>
                                                    <th>Status</th>
                                                    <th class="text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($childTherapyPackage->sessions->sortByDesc('session_date') as $session)
                                                    <tr>
                                                        <td class="small">
                                                            {{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}
                                                            <br><span class="text-muted">{{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }}</span>
                                                        </td>
                                                        <td class="small">{{ $session->service->name ?? '—' }}</td>
                                                        <td>
                                                            @php
                                                                $sc = match($session->status) {
                                                                    'scheduled' => 'bg-info-subtle text-info',
                                                                    'completed' => 'bg-success-subtle text-success',
                                                                    'cancelled' => 'bg-warning-subtle text-warning',
                                                                    'no_show'   => 'bg-danger-subtle text-danger',
                                                                    default     => 'bg-secondary-subtle text-secondary',
                                                                };
                                                            @endphp
                                                            <span class="badge {{ $sc }} border" style="font-size:0.7rem;">{{ ucfirst(str_replace('_', ' ', $session->status)) }}</span>
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="{{ route('admin.therapy-sessions.show', $session) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
