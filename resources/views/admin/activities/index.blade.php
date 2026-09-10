<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Catalog | KinderCare</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
        }
        .page-banner h2 { font-size: 1.45rem; font-weight: 700; }
        .stat-card {
            border: none; border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
        }
        .category-pill {
            font-size: 0.75rem; font-weight: 600;
            padding: 4px 10px; border-radius: 20px;
        }
        .cat-art { background: #fee2e2; color: #b91c1c; }
        .cat-music { background: #fef3c7; color: #b45309; }
        .cat-outdoor { background: #dcfce7; color: #15803d; }
        .cat-reading { background: #e0e7ff; color: #4338ca; }
        .cat-motor_skills { background: #fae8ff; color: #86198f; }
        .cat-sensory { background: #e0f2fe; color: #0369a1; }
        .cat-cognitive { background: #f1f5f9; color: #334155; }
        .cat-other { background: #f3f4f6; color: #4b5563; }
        .table > tbody > tr > td { vertical-align: middle; }
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
                            <a href="{{ route('admin.activity-occurrences.index') }}" class="nav-link">
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
                            <a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-person-badge-fill"></i>
                                <p>Staff</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.activities.index') }}" class="nav-link active">
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
                        <li class="nav-item">
                            <a href="{{ route('admin.invoices.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-receipt-cutoff"></i>
                                <p>Invoices & Payments</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.announcements.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-megaphone-fill"></i>
                                <p>Announcements</p>
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
            <div class="app-content-header">
                <div class="container-fluid">
                    {{-- Banner --}}
                    <div class="page-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h2><i class="bi bi-palette-fill me-2"></i>Activity Catalog</h2>
                            <p class="mb-0 text-white-50">Curriculum catalog, learning domains, and materials reference</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reports.activity-calendar') }}" class="btn btn-outline-light">
                                <i class="bi bi-calendar3 me-1"></i> Calendar Report
                            </a>
                            <a href="{{ route('admin.activity-occurrences.index') }}" class="btn btn-outline-light">
                                <i class="bi bi-calendar-week me-1"></i> Live Activity Schedule
                            </a>
                            @role('admin')
                            <a href="{{ route('admin.activities.create') }}" class="btn btn-light text-primary fw-semibold">
                                <i class="bi bi-plus-lg me-1"></i> Add Activity
                            </a>
                            @endrole
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Stats Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-xl-3">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold">Total Catalog</div>
                                        <div class="fs-4 fw-bold text-dark">{{ $stats['total'] }}</div>
                                    </div>
                                    <div class="stat-icon bg-primary-subtle text-primary">
                                        <i class="bi bi-collection-play"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold">Active Activities</div>
                                        <div class="fs-4 fw-bold text-success">{{ $stats['active'] }}</div>
                                    </div>
                                    <div class="stat-icon bg-success-subtle text-success">
                                        <i class="bi bi-check2-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold">Categories</div>
                                        <div class="fs-4 fw-bold text-warning">{{ $stats['categories'] }}</div>
                                    </div>
                                    <div class="stat-icon bg-warning-subtle text-warning">
                                        <i class="bi bi-tags"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="card stat-card p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-semibold">Times Scheduled</div>
                                        <div class="fs-4 fw-bold text-info">{{ $stats['occurrences'] }}</div>
                                    </div>
                                    <div class="stat-icon bg-info-subtle text-info">
                                        <i class="bi bi-calendar2-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters & Search --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-3">
                            <form method="GET" action="{{ route('admin.activities.index') }}" class="row g-2 align-items-center">
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, description, materials..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="category" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach ($categories as $key => $label)
                                            <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
                                    @if(request()->anyFilled(['search', 'category', 'status']))
                                        <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Activity List Table --}}
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%;">Activity Name</th>
                                        <th style="width: 14%;">Category</th>
                                        <th style="width: 12%;">Duration</th>
                                        <th style="width: 20%;">Materials Needed</th>
                                        <th style="width: 10%;" class="text-center">Scheduled</th>
                                        <th style="width: 9%;" class="text-center">Status</th>
                                        <th style="width: 10%;" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($activities as $act)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $act->name }}</div>
                                                @if($act->description)
                                                    <small class="text-muted d-inline-block text-truncate" style="max-width: 260px;">{{ $act->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $catClass = match($act->category) {
                                                        'art' => 'cat-art',
                                                        'music' => 'cat-music',
                                                        'outdoor' => 'cat-outdoor',
                                                        'reading' => 'cat-reading',
                                                        'motor_skills' => 'cat-motor_skills',
                                                        'sensory' => 'cat-sensory',
                                                        'cognitive' => 'cat-cognitive',
                                                        default => 'cat-other',
                                                    };
                                                @endphp
                                                <span class="category-pill {{ $catClass }}">{{ $act->category_label }}</span>
                                            </td>
                                            <td>
                                                <i class="bi bi-clock text-muted me-1"></i> {{ $act->duration_label }}
                                            </td>
                                            <td>
                                                <span class="small text-muted">{{ $act->materials_needed ?: 'None specified' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">{{ $act->occurrences_count }} times</span>
                                            </td>
                                            <td class="text-center">
                                                @role('admin')
                                                <form action="{{ route('admin.activities.toggle-status', $act) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm badge {{ $act->is_active ? 'bg-success' : 'bg-secondary' }}" style="cursor: pointer;" title="Click to toggle">
                                                        {{ $act->is_active ? 'Active' : 'Inactive' }}
                                                    </button>
                                                </form>
                                                @else
                                                <span class="badge {{ $act->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $act->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                @endrole
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.activities.show', $act) }}" class="btn btn-outline-info" title="View History & Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.activity-occurrences.create', ['activity_id' => $act->id]) }}" class="btn btn-outline-success" title="Schedule this activity">
                                                        <i class="bi bi-calendar-plus"></i>
                                                    </a>
                                                    @role('admin')
                                                    <a href="{{ route('admin.activities.edit', $act) }}" class="btn btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $act->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    @endrole
                                                </div>

                                                @role('admin')
                                                {{-- Delete Modal --}}
                                                <div class="modal fade" id="deleteModal{{ $act->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content text-start">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Delete Activity</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to remove <strong>{{ $act->name }}</strong> from the catalog?
                                                                @if($act->occurrences_count > 0)
                                                                    <div class="alert alert-warning mt-2 mb-0 small">
                                                                        <i class="bi bi-exclamation-triangle"></i> This activity has {{ $act->occurrences_count }} scheduled occurrences and cannot be deleted. Deactivate it instead.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="{{ route('admin.activities.destroy', $act) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger" {{ $act->occurrences_count > 0 ? 'disabled' : '' }}>Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endrole
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                No activities found matching your criteria.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($activities->hasPages())
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-end">
                                {{ $activities->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
