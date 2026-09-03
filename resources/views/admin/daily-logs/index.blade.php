<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Child Logs | Daycare System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 25px rgba(99,102,241,0.2);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -50px; right: -30px;
            width: 170px; height: 170px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .child-pill {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.65rem 1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.15s ease;
            text-decoration: none;
            color: #1e293b;
        }
        .child-pill:hover {
            transform: translateY(-2px);
            border-color: #8b5cf6;
            box-shadow: 0 6px 16px rgba(139,92,246,0.12);
            color: #6366f1;
        }
        .child-mini-avatar {
            width: 34px; height: 34px; border-radius: 50%; object-fit: cover;
            border: 2px solid #e2e8f0;
        }
        .child-mini-placeholder {
            width: 34px; height: 34px; border-radius: 50%;
            background: #ede9fe; color: #6d28d9; display: flex;
            align-items: center; justify-content: center; font-weight: 700; font-size: 0.78rem;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <span class="nav-link text-muted fw-medium">Daily Operations &bull; Child Activity, Meal & Nap Logs</span>
                    </li>
                </ul>
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
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="{{ route('dashboard') }}" class="brand-link">
                    <i class="bi bi-house-heart-fill brand-image" style="font-size:1.4rem;"></i>
                    <span class="brand-text fw-light"><b>Daycare</b>System</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-header">MAIN</li>
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link">
                                <i class="nav-icon bi bi-grid-1x2-fill"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-header">DAILY OPERATIONS</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.attendance.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-check2-circle"></i>
                                <p>Attendance Desk</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.child-daily-logs.index') }}" class="nav-link active">
                                <i class="nav-icon bi bi-journal-text"></i>
                                <p>Daily Child Logs</p>
                            </a>
                        </li>

                        <li class="nav-header">MANAGEMENT</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.children.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-people-fill"></i>
                                <p>Children</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.enrollments.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-clipboard-check-fill"></i>
                                <p>Enrollments</p>
                            </a>
                        </li>

                        @role('admin')
                        <li class="nav-header">ADMIN</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.programs.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-book-half"></i>
                                <p>Programs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-person-lines-fill"></i>
                                <p>Users & Accounts</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.role-permissions.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-shield-lock-fill"></i>
                                <p>Role Permissions</p>
                            </a>
                        </li>
                        @endrole
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="page-banner d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <h2><i class="bi bi-journal-text me-2"></i>Daily Operational Child Logs</h2>
                            <p>Track merged daily routines — naps, meals, learning activities, diaper changes, and health incidents</p>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                            <a href="{{ route('admin.attendance.index', ['date' => $date]) }}" class="btn btn-light fw-bold shadow-sm">
                                <i class="bi bi-clock-history me-1"></i> Attendance Desk
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{-- Alert Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Quick Child Jump Selector --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-person-lines-fill text-indigo me-2" style="color: #6366f1;"></i>
                                Individual Merged Daily Feeds &bull; Select a child to view their timeline
                            </h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($children as $c)
                                    <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $c->id, 'date' => $date]) }}" class="child-pill">
                                        @if ($c->photo_url ?? false)
                                            <img src="{{ $c->photo_url }}" class="child-mini-avatar" alt="{{ $c->full_name }}">
                                        @else
                                            <div class="child-mini-placeholder">
                                                {{ strtoupper(substr($c->first_name, 0, 1) . substr($c->last_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.88rem;">{{ $c->full_name }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $c->formatted_age }}</div>
                                        </div>
                                        <i class="bi bi-chevron-right ms-1 text-muted" style="font-size: 0.75rem;"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Search & Filter Form --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body py-3">
                            <form method="GET" action="{{ route('admin.child-daily-logs.index') }}" class="row g-2 align-items-center" id="logsFilterForm">
                                <div class="col-lg-3 col-md-6 d-flex align-items-center gap-2">
                                    <label class="form-label mb-0 fw-semibold text-muted text-nowrap"><i class="bi bi-calendar3 me-1"></i>Date:</label>
                                    <input type="date" name="date" class="form-control form-control-sm fw-bold" value="{{ request('date', $date) }}" onchange="document.getElementById('logsFilterForm').submit()">
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <select name="child_id" class="form-select form-select-sm" onchange="document.getElementById('logsFilterForm').submit()">
                                        <option value="">All Children</option>
                                        @foreach ($children as $ch)
                                            <option value="{{ $ch->id }}" {{ request('child_id') == $ch->id ? 'selected' : '' }}>
                                                {{ $ch->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <select name="log_type" class="form-select form-select-sm" onchange="document.getElementById('logsFilterForm').submit()">
                                        <option value="">All Log Types</option>
                                        <option value="nap" {{ request('log_type') == 'nap' ? 'selected' : '' }}>Nap & Sleep</option>
                                        <option value="meal" {{ request('log_type') == 'meal' ? 'selected' : '' }}>Meal & Feeding</option>
                                        <option value="activity" {{ request('log_type') == 'activity' ? 'selected' : '' }}>Learning Activity</option>
                                        <option value="diaper_change" {{ request('log_type') == 'diaper_change' ? 'selected' : '' }}>Diaper Change</option>
                                        <option value="incident" {{ request('log_type') == 'incident' ? 'selected' : '' }}>Incident / Health</option>
                                        <option value="bottle" {{ request('log_type') == 'bottle' ? 'selected' : '' }}>Bottle</option>
                                        <option value="medication" {{ request('log_type') == 'medication' ? 'selected' : '' }}>Medication</option>
                                        <option value="other" {{ request('log_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" class="form-control" placeholder="Search child name..." value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                                        @if (request()->hasAny(['date', 'child_id', 'log_type', 'search']))
                                            <a href="{{ route('admin.child-daily-logs.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-x-circle"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- All Logs Table --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-list-columns-reverse me-2 text-primary"></i>Daily Records Stream &bull; {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                            </h5>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                Showing <strong>{{ $logs->total() }}</strong> total log entries
                            </span>
                        </div>
                        <div class="card-body p-0">
                            @if ($logs->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="fw-bold mt-2 text-secondary">No daily logs found matching your filter criteria.</h6>
                                    <p class="text-muted small">Select a child from the cards above to open their merged daily feed and record new logs.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light text-muted small text-uppercase">
                                            <tr>
                                                <th>Child</th>
                                                <th>Type</th>
                                                <th>Time / Duration</th>
                                                <th>Details & Summary</th>
                                                <th>Staff</th>
                                                <th class="text-end pe-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($logs as $log)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $log->child_id, 'date' => $log->log_date->toDateString()]) }}" class="fw-bold text-dark text-decoration-none">
                                                            {{ $log->child ? $log->child->full_name : '—' }}
                                                        </a>
                                                        <div class="text-muted small">{{ $log->log_date->format('M d, Y') }}</div>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $log->type_badge_class }} fw-bold px-2 py-1">
                                                            <i class="bi {{ $log->type_icon }} me-1"></i>{{ $log->formatted_type }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($log->start_time)
                                                            <span class="fw-semibold text-dark">{{ $log->formatted_start_time }}</span>
                                                            @if ($log->end_time)
                                                                <span class="text-muted">&ndash; {{ $log->formatted_end_time }}</span>
                                                            @endif
                                                            @if ($log->duration_minutes)
                                                                <div class="text-muted small">({{ $log->formatted_duration }})</div>
                                                            @endif
                                                        @else
                                                            <span class="text-muted small">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (in_array($log->log_type, ['meal', 'bottle']))
                                                            <div class="small">
                                                                @if ($log->meal_type)
                                                                    <span class="badge bg-light text-dark border">{{ ucfirst($log->meal_type) }}</span>
                                                                @endif
                                                                @if ($log->amount_eaten)
                                                                    <strong>Ate:</strong> {{ $log->amount_eaten }}
                                                                @endif
                                                                @if ($log->items_served)
                                                                    &bull; <em>{{ $log->items_served }}</em>
                                                                @endif
                                                            </div>
                                                        @elseif ($log->log_type === 'activity')
                                                            <div class="small">
                                                                @if ($log->activityOccurrence && $log->activityOccurrence->activity)
                                                                    <span class="badge bg-primary text-white">{{ $log->activityOccurrence->activity->title }}</span>
                                                                @endif
                                                                @if ($log->is_completed)
                                                                    <span class="badge bg-success-subtle text-success">Completed</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        @if ($log->notes)
                                                            <div class="text-secondary small text-truncate" style="max-width: 320px;" title="{{ $log->notes }}">
                                                                {{ $log->notes }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="text-dark small">{{ $log->staff ? $log->staff->full_name : 'Staff' }}</span>
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $log->child_id, 'date' => $log->log_date->toDateString()]) }}" class="btn btn-outline-primary btn-sm px-2 py-1" title="Open Child Timeline">
                                                            <i class="bi bi-clock-history me-1"></i>Timeline
                                                        </a>
                                                        <form method="POST" action="{{ route('admin.child-daily-logs.destroy', $log->id) }}" class="d-inline" onsubmit="return confirm('Delete this log entry?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-1" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3">
                                    {{ $logs->links() }}
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
