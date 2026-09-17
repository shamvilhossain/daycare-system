<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapy Sessions | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #c084fc 100%);
            border-radius: 12px; padding: 0.75rem 1.25rem; color: #fff; margin-bottom: 0.75rem;
            position: relative; overflow: hidden;
            box-shadow: 0 4px 15px rgba(124,58,237,0.15);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -35px; right: -25px;
            width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.15rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.82rem; opacity: 0.88; position: relative; z-index: 1; margin: 0; }
        .stat-card {
            border-radius: 12px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }
        .therapist-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: #ede9fe; color: #7c3aed; display: flex;
            align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem;
        }
        .therapy-badge-slt { background: #dbeafe; color: #1e40af; }
        .therapy-badge-aba { background: #fef3c7; color: #92400e; }
        .therapy-badge-ot  { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
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

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header pt-2 pb-0">
                <div class="container-fluid">
                    <div class="page-banner d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h2><i class="bi bi-heart-pulse-fill me-2"></i>Therapy Sessions</h2>
                            <p>Schedule and manage therapy sessions with overlap detection and specialization matching</p>
                        </div>
                        <a href="{{ route('admin.therapy-sessions.create') }}" class="btn btn-light btn-sm fw-bold shadow-sm px-3" style="position:relative;z-index:1;">
                            <i class="bi bi-plus-lg me-1"></i> New Session
                        </a>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{-- Alert Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Stats Cards --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md col-sm-6">
                            <div class="card stat-card shadow-sm border-0 border-start border-primary border-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small fw-semibold">TOTAL SESSIONS</div>
                                            <div class="fs-4 fw-bold text-dark">{{ $stats['total'] }}</div>
                                        </div>
                                        <div class="p-2 rounded bg-primary-subtle text-primary fs-4"><i class="bi bi-collection-fill"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md col-sm-6">
                            <div class="card stat-card shadow-sm border-0 border-start border-info border-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small fw-semibold">SCHEDULED</div>
                                            <div class="fs-4 fw-bold text-info">{{ $stats['scheduled'] }}</div>
                                        </div>
                                        <div class="p-2 rounded bg-info-subtle text-info fs-4"><i class="bi bi-calendar-check"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md col-sm-6">
                            <div class="card stat-card shadow-sm border-0 border-start border-success border-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small fw-semibold">COMPLETED</div>
                                            <div class="fs-4 fw-bold text-success">{{ $stats['completed'] }}</div>
                                        </div>
                                        <div class="p-2 rounded bg-success-subtle text-success fs-4"><i class="bi bi-check2-all"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md col-sm-6">
                            <div class="card stat-card shadow-sm border-0 border-start border-warning border-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small fw-semibold">CANCELLED</div>
                                            <div class="fs-4 fw-bold text-warning">{{ $stats['cancelled'] }}</div>
                                        </div>
                                        <div class="p-2 rounded bg-warning-subtle text-warning fs-4"><i class="bi bi-x-circle"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md col-sm-6">
                            <div class="card stat-card shadow-sm border-0 border-start border-danger border-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small fw-semibold">NO SHOW</div>
                                            <div class="fs-4 fw-bold text-danger">{{ $stats['no_show'] }}</div>
                                        </div>
                                        <div class="p-2 rounded bg-danger-subtle text-danger fs-4"><i class="bi bi-person-slash"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Search & Filter --}}
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body py-2">
                            <form method="GET" action="{{ route('admin.therapy-sessions.index') }}" class="row g-2 align-items-center">
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search child or therapist..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">All Statuses</option>
                                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="therapy_type" class="form-select form-select-sm">
                                        <option value="">All Types</option>
                                        <option value="slt" {{ request('therapy_type') == 'slt' ? 'selected' : '' }}>SLT</option>
                                        <option value="aba" {{ request('therapy_type') == 'aba' ? 'selected' : '' }}>ABA</option>
                                        <option value="ot" {{ request('therapy_type') == 'ot' ? 'selected' : '' }}>OT</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="session_date" class="form-control form-control-sm" value="{{ request('session_date') }}">
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-funnel me-1"></i>Filter</button>
                                    <a href="{{ route('admin.therapy-sessions.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Child</th>
                                            <th>Therapist</th>
                                            <th>Service</th>
                                            <th>Schedule</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($sessions as $session)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $session->child->full_name ?? 'Unknown' }}</div>
                                                    <small class="text-muted">{{ $session->child ? $session->child->formatted_age : '—' }}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="therapist-avatar">
                                                            {{ $session->therapist->initials ?? 'TH' }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium text-dark">{{ $session->therapist->full_name ?? 'Unknown' }}</div>
                                                            @if($session->therapist && $session->therapist->specialization)
                                                                <span class="badge therapy-badge-{{ $session->therapist->specialization }} border" style="font-size:0.7rem;">
                                                                    {{ strtoupper($session->therapist->specialization) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-medium text-dark">{{ $session->service->name ?? 'Unknown' }}</div>
                                                    <small class="text-muted">{{ $session->service->duration_minutes ?? '—' }} min · ৳{{ number_format($session->service->session_rate ?? 0, 2) }}</small>
                                                </td>
                                                <td>
                                                    <div class="small">
                                                        <span class="fw-medium text-dark"><i class="bi bi-calendar-event me-1 text-muted"></i>{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}</span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusConfig = match($session->status) {
                                                            'scheduled' => ['class' => 'bg-info-subtle text-info border-info-subtle', 'icon' => 'bi-calendar-check'],
                                                            'completed' => ['class' => 'bg-success-subtle text-success border-success-subtle', 'icon' => 'bi-check2-all'],
                                                            'cancelled' => ['class' => 'bg-warning-subtle text-warning border-warning-subtle', 'icon' => 'bi-x-circle'],
                                                            'no_show'   => ['class' => 'bg-danger-subtle text-danger border-danger-subtle', 'icon' => 'bi-person-slash'],
                                                            default     => ['class' => 'bg-secondary-subtle text-secondary', 'icon' => 'bi-question-circle'],
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusConfig['class'] }} border">
                                                        <i class="bi {{ $statusConfig['icon'] }} me-1"></i>{{ ucfirst(str_replace('_', ' ', $session->status)) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        {{-- Quick status buttons for scheduled sessions --}}
                                                        @if($session->status === 'scheduled')
                                                            <form action="{{ route('admin.therapy-sessions.update-status', $session) }}" method="POST" class="d-inline">
                                                                @csrf @method('PATCH')
                                                                <input type="hidden" name="status" value="completed">
                                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Mark Completed">
                                                                    <i class="bi bi-check-lg"></i>
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('admin.therapy-sessions.update-status', $session) }}" method="POST" class="d-inline">
                                                                @csrf @method('PATCH')
                                                                <input type="hidden" name="status" value="no_show">
                                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Mark No Show">
                                                                    <i class="bi bi-person-slash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <a href="{{ route('admin.therapy-sessions.show', $session) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.therapy-sessions.edit', $session) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('admin.therapy-sessions.destroy', $session) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this therapy session?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                                    No therapy sessions found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            {{ $sessions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
