<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Schedule & Log | KinderCare</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #14b8a6 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
        }
        .page-banner h2 { font-size: 1.45rem; font-weight: 700; }
        .stat-card {
            border: none; border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-icon {
            width: 46px; height: 46px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
        }
        .date-btn {
            font-size: 0.85rem; font-weight: 500; border-radius: 8px;
        }
        .occurrence-row:hover { background-color: #f8fafc; }
        .badge-planned { background-color: #e0f2fe; color: #0369a1; }
        .badge-completed { background-color: #dcfce7; color: #15803d; }
        .badge-partial { background-color: #fef3c7; color: #b45309; }
        .badge-cancelled { background-color: #fee2e2; color: #b91c1c; }
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
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-footer">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
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
                <a href="/" class="brand-link">
                    <span class="brand-text font-weight-light">KinderCare</span>
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
                            <a href="{{ route('admin.child-daily-logs.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-journal-text"></i>
                                <p>Daily Child Logs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.activity-occurrences.index') }}" class="nav-link active">
                                <i class="nav-icon bi bi-calendar-check"></i>
                                <p>Daily Schedule & Log</p>
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
                            <a href="{{ route('admin.activities.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-palette-fill"></i>
                                <p>Activity Catalog</p>
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
                        <li class="nav-header">REPORTS</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.reports.activity-calendar') }}" class="nav-link">
                                <i class="nav-icon bi bi-calendar3"></i>
                                <p>Activity Calendar</p>
                            </a>
                        </li>
                        @endrole
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header py-4">
                <div class="container-fluid">
                    {{-- Banner --}}
                    <div class="page-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h2><i class="bi bi-calendar-check me-2"></i>Daily Activity Schedule & Observations</h2>
                            <p class="mb-0 text-white-50">Operational timeline of planned group activities, staff observations, and media records</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.activity-calendar') }}" class="btn btn-light text-success fw-semibold">
                                <i class="bi bi-calendar3 me-1"></i> Monthly Report
                            </a>
                            <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-light">
                                <i class="bi bi-palette me-1"></i> Activity Catalog
                            </a>
                            <a href="{{ route('admin.activity-occurrences.create', ['date' => $date !== 'all' ? $date : now()->toDateString()]) }}" class="btn btn-light text-success fw-semibold">
                                <i class="bi bi-plus-circle me-1"></i> Schedule Activity
                            </a>
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Date Quick Navigation Bar --}}
                    @php
                        $today = \Carbon\Carbon::today()->toDateString();
                        $yesterday = \Carbon\Carbon::yesterday()->toDateString();
                        $tomorrow = \Carbon\Carbon::tomorrow()->toDateString();
                    @endphp
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-semibold text-muted small text-uppercase me-2"><i class="bi bi-calendar-event me-1"></i> Date:</span>
                                    <a href="{{ request()->fullUrlWithQuery(['date' => $yesterday]) }}" class="btn btn-sm {{ $date === $yesterday ? 'btn-success' : 'btn-outline-secondary' }} date-btn">
                                        Yesterday
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['date' => $today]) }}" class="btn btn-sm {{ $date === $today ? 'btn-success' : 'btn-outline-secondary' }} date-btn">
                                        Today
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['date' => $tomorrow]) }}" class="btn btn-sm {{ $date === $tomorrow ? 'btn-success' : 'btn-outline-secondary' }} date-btn">
                                        Tomorrow
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['date' => 'all']) }}" class="btn btn-sm {{ $date === 'all' ? 'btn-success' : 'btn-outline-secondary' }} date-btn">
                                        All Dates
                                    </a>
                                </div>

                                <form method="GET" action="{{ route('admin.activity-occurrences.index') }}" class="d-flex align-items-center gap-2">
                                    <input type="date" name="date" class="form-control form-control-sm" value="{{ $date !== 'all' ? $date : $today }}" onchange="this.form.submit()">
                                    @if(request('program_id')) <input type="hidden" name="program_id" value="{{ request('program_id') }}"> @endif
                                    @if(request('activity_id')) <input type="hidden" name="activity_id" value="{{ request('activity_id') }}"> @endif
                                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                                    @if(request('staff_id')) <input type="hidden" name="staff_id" value="{{ request('staff_id') }}"> @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Summary --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3 col-xl">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold">Total Sessions</div>
                                        <div class="fs-4 fw-bold text-dark">{{ $stats['total'] }}</div>
                                    </div>
                                    <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-collection"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold">Planned</div>
                                        <div class="fs-4 fw-bold text-info">{{ $stats['planned'] }}</div>
                                    </div>
                                    <div class="stat-icon bg-info-subtle text-info"><i class="bi bi-clock"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold">Completed</div>
                                        <div class="fs-4 fw-bold text-success">{{ $stats['completed'] }}</div>
                                    </div>
                                    <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-check2-all"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold">Partial</div>
                                        <div class="fs-4 fw-bold text-warning">{{ $stats['partial'] }}</div>
                                    </div>
                                    <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-xl">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold">Cancelled</div>
                                        <div class="fs-4 fw-bold text-danger">{{ $stats['cancelled'] }}</div>
                                    </div>
                                    <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-x-circle"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters Form --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-3">
                            <form method="GET" action="{{ route('admin.activity-occurrences.index') }}" class="row g-2 align-items-center">
                                <input type="hidden" name="date" value="{{ $date }}">

                                <div class="col-md-3">
                                    <select name="program_id" class="form-select">
                                        <option value="">All Programs</option>
                                        @foreach($programs as $prog)
                                            <option value="{{ $prog->id }}" {{ (string)$programId === (string)$prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <select name="activity_id" class="form-select">
                                        <option value="">All Activities</option>
                                        @foreach($activities as $act)
                                            <option value="{{ $act->id }}" {{ (string)$activityId === (string)$act->id ? 'selected' : '' }}>{{ $act->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        <option value="planned" {{ $status === 'planned' ? 'selected' : '' }}>Planned</option>
                                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select name="staff_id" class="form-select">
                                        <option value="">All Staff</option>
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->id }}" {{ (string)$staffId === (string)$staff->id ? 'selected' : '' }}>
                                                {{ $staff->user ? $staff->user->name : "Staff #{$staff->id}" }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
                                    @if($programId || $activityId || $status || $staffId)
                                        <a href="{{ route('admin.activity-occurrences.index', ['date' => $date]) }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Activity Occurrences Table --}}
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 14%;">Time / Date</th>
                                        <th style="width: 22%;">Activity & Category</th>
                                        <th style="width: 15%;">Program</th>
                                        <th style="width: 14%;">Lead Staff</th>
                                        <th style="width: 11%;">Status</th>
                                        <th style="width: 12%;">Participation</th>
                                        <th style="width: 12%;" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($occurrences as $occ)
                                        <tr class="occurrence-row">
                                            <td>
                                                <div class="fw-bold text-dark"><i class="bi bi-clock me-1 text-muted"></i>{{ $occ->time_range }}</div>
                                                <small class="text-muted">{{ $occ->occurrence_date ? $occ->occurrence_date->format('M d, Y') : '' }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-primary">{{ $occ->activity ? $occ->activity->name : 'Activity deleted' }}</div>
                                                <div class="small text-muted">
                                                    <span class="badge bg-light text-secondary border me-1">{{ $occ->activity ? $occ->activity->category_label : 'General' }}</span>
                                                    @if($occ->media->count() > 0)
                                                        <span class="badge bg-secondary-subtle text-secondary" title="{{ $occ->media->count() }} media attachments"><i class="bi bi-camera"></i> {{ $occ->media->count() }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $occ->program ? $occ->program->name : 'All Programs' }}</span>
                                            </td>
                                            <td>
                                                <i class="bi bi-person text-muted me-1"></i>
                                                <span class="small">{{ $occ->staff && $occ->staff->user ? $occ->staff->user->name : "Staff #{$occ->staff_id}" }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass = match($occ->status) {
                                                        'planned'   => 'badge-planned',
                                                        'completed' => 'badge-completed',
                                                        'partial'   => 'badge-partial',
                                                        'cancelled' => 'badge-cancelled',
                                                        default     => 'bg-secondary',
                                                    };
                                                @endphp
                                                <button type="button" class="btn btn-sm badge {{ $badgeClass }} border-0 px-2 py-1" data-bs-toggle="modal" data-bs-target="#statusModal{{ $occ->id }}" title="Click to update status & observations">
                                                    {{ $occ->status_label }} <i class="bi bi-pencil-square ms-1"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">
                                                    <i class="bi bi-people me-1"></i> {{ $occ->child_daily_logs_count }} logged
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.activity-occurrences.show', $occ) }}" class="btn btn-outline-info" title="View Details, Media, & Children">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.activity-occurrences.edit', $occ) }}" class="btn btn-outline-primary" title="Edit Session">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteOccModal{{ $occ->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                {{-- Status Update Modal --}}
                                                <div class="modal fade" id="statusModal{{ $occ->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered text-start">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.activity-occurrences.update-status', $occ) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold">Update Session Status</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Status</label>
                                                                        <select name="status" class="form-select" required>
                                                                            <option value="planned" {{ $occ->status === 'planned' ? 'selected' : '' }}>Planned (Scheduled)</option>
                                                                            <option value="completed" {{ $occ->status === 'completed' ? 'selected' : '' }}>Completed (Fully Ran)</option>
                                                                            <option value="partial" {{ $occ->status === 'partial' ? 'selected' : '' }}>Partial (Cut short)</option>
                                                                            <option value="cancelled" {{ $occ->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Actual Materials Used</label>
                                                                        <input type="text" name="materials_used" class="form-control" value="{{ $occ->materials_used }}" placeholder="e.g., Red & Blue finger paints, paper plates">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-semibold">Staff Observations / Notes</label>
                                                                        <textarea name="observations" class="form-control" rows="3" placeholder="How did the group activity go? Children engagement notes...">{{ $occ->observations }}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Delete Modal --}}
                                                <div class="modal fade" id="deleteOccModal{{ $occ->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered text-start">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Delete Session</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to remove this scheduled session for <strong>{{ $occ->activity ? $occ->activity->name : 'Activity' }}</strong> on {{ $occ->occurrence_date ? $occ->occurrence_date->format('M d, Y') : '' }}?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="{{ route('admin.activity-occurrences.destroy', $occ) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                                No activities scheduled for this date or filter.
                                                <div class="mt-2">
                                                    <a href="{{ route('admin.activity-occurrences.create', ['date' => $date !== 'all' ? $date : now()->toDateString()]) }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-plus-lg me-1"></i> Schedule First Activity
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($occurrences->hasPages())
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-end">
                                {{ $occurrences->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
