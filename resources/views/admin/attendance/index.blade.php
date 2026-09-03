<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Desk | Daycare System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #059669 0%, #10b981 50%, #14b8a6 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 25px rgba(16,185,129,0.2);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -50px; right: -30px;
            width: 170px; height: 170px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.45rem; font-weight: 700; margin-bottom: 0.25rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.9rem; opacity: 0.9; position: relative; z-index: 1; margin: 0; }
        .child-avatar {
            width: 42px; height: 42px; border-radius: 50%; object-fit: cover;
            border: 2px solid #e5e7eb;
        }
        .child-avatar-placeholder {
            width: 42px; height: 42px; border-radius: 50%;
            background: #d1fae5; color: #047857; display: flex;
            align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;
        }
        .stat-card {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }
        .status-pill {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.28rem 0.65rem;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .roster-table tbody tr {
            transition: background 0.15s ease;
        }
        .roster-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .date-btn {
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.82rem;
        }
        .bulk-bar {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            display: none;
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
                        <span class="nav-link text-muted fw-medium">Daily Operational Desk &bull; Attendance</span>
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
                            <a href="{{ route('admin.attendance.index') }}" class="nav-link active">
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
                            <h2><i class="bi bi-clock-history me-2"></i>Daily Attendance Desk</h2>
                            <p>Check in and check out enrolled children, track stay durations, and log arrivals in real-time</p>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                            <button type="button" class="btn btn-light fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#manualAttendanceModal">
                                <i class="bi bi-plus-lg me-1"></i> Manual Entry
                            </button>
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
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Quick Date & Program Filters --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body py-3">
                            <form method="GET" action="{{ route('admin.attendance.index') }}" class="row g-2 align-items-center" id="filterForm">
                                <div class="col-lg-4 col-md-6 d-flex align-items-center gap-2">
                                    <label class="form-label mb-0 fw-semibold text-muted text-nowrap"><i class="bi bi-calendar3 me-1"></i>Date:</label>
                                    <input type="date" name="date" class="form-control form-control-sm fw-bold text-dark" value="{{ $date }}" onchange="document.getElementById('filterForm').submit()">
                                    @php
                                        $carbonDate = \Carbon\Carbon::parse($date);
                                        $prevDate = $carbonDate->copy()->subDay()->toDateString();
                                        $nextDate = $carbonDate->copy()->addDay()->toDateString();
                                        $todayDate = \Carbon\Carbon::today()->toDateString();
                                    @endphp
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.attendance.index', array_merge(request()->query(), ['date' => $prevDate])) }}" class="btn btn-outline-secondary" title="Previous Day"><i class="bi bi-chevron-left"></i></a>
                                        <a href="{{ route('admin.attendance.index', array_merge(request()->query(), ['date' => $todayDate])) }}" class="btn btn-outline-secondary {{ $date === $todayDate ? 'active fw-bold' : '' }}" title="Today">Today</a>
                                        <a href="{{ route('admin.attendance.index', array_merge(request()->query(), ['date' => $nextDate])) }}" class="btn btn-outline-secondary" title="Next Day"><i class="bi bi-chevron-right"></i></a>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <select name="program_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                                        <option value="">All Active Programs</option>
                                        @foreach ($programs as $prog)
                                            <option value="{{ $prog->id }}" {{ $programId == $prog->id ? 'selected' : '' }}>
                                                {{ $prog->name }} ({{ $prog->service_type_label }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-4">
                                    <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                                        <option value="">All Statuses</option>
                                        <option value="present" {{ $statusFilter === 'present' ? 'selected' : '' }}>Present (On Premises)</option>
                                        <option value="late" {{ $statusFilter === 'late' ? 'selected' : '' }}>Late</option>
                                        <option value="absent" {{ $statusFilter === 'absent' ? 'selected' : '' }}>Absent</option>
                                        <option value="excused" {{ $statusFilter === 'excused' ? 'selected' : '' }}>Excused</option>
                                        <option value="not_recorded" {{ $statusFilter === 'not_recorded' ? 'selected' : '' }}>Not Checked In</option>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-8">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" class="form-control" placeholder="Search child name..." value="{{ $search }}">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                                        @if ($search || $programId || $statusFilter || $date !== $todayDate)
                                            <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-x-circle"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Live Stat Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card bg-white p-3 shadow-sm border-start border-4 border-primary">
                                <div class="text-muted small text-uppercase fw-bold">Enrolled</div>
                                <div class="fs-4 fw-bolder text-dark">{{ $stats['total_enrolled'] }}</div>
                                <div class="small text-muted">Expected today</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card bg-white p-3 shadow-sm border-start border-4 border-success">
                                <div class="text-muted small text-uppercase fw-bold">In Facility</div>
                                <div class="fs-4 fw-bolder text-success">{{ $stats['currently_in'] }}</div>
                                <div class="small text-success">Checked in now</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card bg-white p-3 shadow-sm border-start border-4 border-info">
                                <div class="text-muted small text-uppercase fw-bold">Checked Out</div>
                                <div class="fs-4 fw-bolder text-info">{{ $stats['checked_out'] }}</div>
                                <div class="small text-muted">Picked up</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card bg-white p-3 shadow-sm border-start border-4 border-warning">
                                <div class="text-muted small text-uppercase fw-bold">Late Arrivals</div>
                                <div class="fs-4 fw-bolder text-warning">{{ $stats['late'] }}</div>
                                <div class="small text-muted">Arrived late</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card bg-white p-3 shadow-sm border-start border-4 border-danger">
                                <div class="text-muted small text-uppercase fw-bold">Absent / Exc.</div>
                                <div class="fs-4 fw-bolder text-danger">{{ $stats['absent'] + $stats['excused'] }}</div>
                                <div class="small text-muted">{{ $stats['absent'] }} abs &bull; {{ $stats['excused'] }} exc</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card bg-white p-3 shadow-sm border-start border-4 border-secondary">
                                <div class="text-muted small text-uppercase fw-bold">Not Checked In</div>
                                <div class="fs-4 fw-bolder text-secondary">{{ $stats['not_checked_in'] }}</div>
                                <div class="small text-muted">Awaiting check-in</div>
                            </div>
                        </div>
                    </div>

                    {{-- Bulk Actions Bar (Revealed when checkboxes are checked) --}}
                    <div id="bulkBar" class="bulk-bar mb-3">
                        <form method="POST" action="{{ route('admin.attendance.bulk') }}" id="bulkForm" class="d-flex flex-wrap align-items-center gap-3">
                            @csrf
                            <input type="hidden" name="attendance_date" value="{{ $date }}">
                            <input type="hidden" name="action" id="bulkActionInput" value="check_in">
                            <span class="fw-bold text-dark"><span id="selectedCount">0</span> children selected</span>
                            <div class="vr"></div>
                            @if ($programId)
                                <input type="hidden" name="program_id" value="{{ $programId }}">
                            @else
                                <div class="d-inline-block">
                                    <select name="program_id" class="form-select form-select-sm" style="width: auto;">
                                        <option value="">Select Program for Check-In...</option>
                                        @foreach ($programs as $prog)
                                            <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <button type="button" class="btn btn-success btn-sm fw-bold" onclick="submitBulk('check_in')">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Bulk Check-In
                            </button>
                            <button type="button" class="btn btn-primary btn-sm fw-bold" onclick="submitBulk('check_out')">
                                <i class="bi bi-box-arrow-right me-1"></i> Bulk Check-Out
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm ms-auto" onclick="clearBulkSelection()">Cancel</button>
                        </form>
                    </div>

                    {{-- Operational Roster Table --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-people-fill text-success me-2"></i>Daily Roster &bull; {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                            </h5>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                Total in List: <strong>{{ count($roster) }}</strong>
                            </span>
                        </div>
                        <div class="card-body p-0">
                            @if (count($roster) === 0)
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="fw-bold mt-2 text-secondary">No enrolled children found for this date/filter.</h6>
                                    <p class="text-muted small">Try selecting another date, clearing filters, or adding a manual attendance entry.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 roster-table">
                                        <thead class="table-light text-muted small text-uppercase">
                                            <tr>
                                                <th style="width: 40px;" class="text-center">
                                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                                                </th>
                                                <th>Child</th>
                                                <th>Program</th>
                                                <th>Status</th>
                                                <th>Check-In</th>
                                                <th>Check-Out</th>
                                                <th>Duration</th>
                                                <th>Notes</th>
                                                <th class="text-end pe-3">Operational Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roster as $item)
                                                @php
                                                    $child = $item['child'];
                                                    $program = $item['program'];
                                                    $att = $item['attendance'];
                                                    $status = $item['status'];
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        @if ($att && $att->is_checked_in)
                                                            <input type="checkbox" class="form-check-input roster-check" data-type="attendance" name="attendance_ids[]" form="bulkForm" value="{{ $att->id }}" onchange="updateBulkBar()">
                                                        @elseif (!$att || in_array($status, ['not_recorded', 'absent', 'excused']))
                                                            <input type="checkbox" class="form-check-input roster-check" data-type="child" name="child_ids[]" form="bulkForm" value="{{ $child->id }}" onchange="updateBulkBar()">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            @if ($child->photo_url ?? false)
                                                                <img src="{{ $child->photo_url }}" class="child-avatar" alt="{{ $child->full_name }}">
                                                            @else
                                                                <div class="child-avatar-placeholder">
                                                                    {{ strtoupper(substr($child->first_name ?? 'C', 0, 1) . substr($child->last_name ?? 'H', 0, 1)) }}
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $child->id, 'date' => $date]) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                                                    {{ $child->full_name }}
                                                                </a>
                                                                <div class="text-muted small">
                                                                    {{ $child->formatted_age }} &bull;
                                                                    <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $child->id, 'date' => $date]) }}" class="text-success text-decoration-none">
                                                                        <i class="bi bi-journal-text me-1"></i>Daily Log
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if ($program)
                                                            <span class="badge bg-light text-dark border">{{ $program->name }}</span>
                                                        @else
                                                            <span class="text-muted small">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($status === 'present')
                                                            @if ($att && $att->check_out_time)
                                                                <span class="status-pill bg-info-subtle text-info-emphasis border border-info">
                                                                    <i class="bi bi-check-all"></i> Checked Out
                                                                </span>
                                                            @else
                                                                <span class="status-pill bg-success-subtle text-success border border-success">
                                                                    <i class="bi bi-dot" style="font-size: 1.2rem; line-height: 0;"></i> Present
                                                                </span>
                                                            @endif
                                                        @elseif ($status === 'late')
                                                            @if ($att && $att->check_out_time)
                                                                <span class="status-pill bg-info-subtle text-info-emphasis border border-info">
                                                                    <i class="bi bi-check-all"></i> Late (Checked Out)
                                                                </span>
                                                            @else
                                                                <span class="status-pill bg-warning-subtle text-warning-emphasis border border-warning">
                                                                    <i class="bi bi-clock-history"></i> Late
                                                                </span>
                                                            @endif
                                                        @elseif ($status === 'absent')
                                                            <span class="status-pill bg-danger-subtle text-danger border border-danger">
                                                                <i class="bi bi-x-circle"></i> Absent
                                                            </span>
                                                        @elseif ($status === 'excused')
                                                            <span class="status-pill bg-primary-subtle text-primary border border-primary">
                                                                <i class="bi bi-info-circle"></i> Excused
                                                            </span>
                                                        @else
                                                            <span class="status-pill bg-light text-secondary border">
                                                                <i class="bi bi-dash"></i> Not Recorded
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($att && $att->check_in_time)
                                                            <span class="fw-semibold text-dark">{{ $att->formatted_check_in_time }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($att && $att->check_out_time)
                                                            <span class="fw-semibold text-dark">{{ $att->formatted_check_out_time }}</span>
                                                        @elseif ($att && $att->is_checked_in)
                                                            <span class="badge bg-warning-subtle text-warning-emphasis">Active</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($att)
                                                            <small class="text-muted fw-medium">{{ $att->formatted_duration }}</small>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($att && $att->notes)
                                                            <span class="text-muted small text-truncate d-inline-block" style="max-width: 140px;" title="{{ $att->notes }}">
                                                                {{ $att->notes }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted small">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <div class="d-inline-flex gap-1 align-items-center">
                                                            @if (!$att || (!$att->check_in_time && !in_array($status, ['present', 'late'])))
                                                                {{-- 1-Click Check In --}}
                                                                <form method="POST" action="{{ route('admin.attendance.check-in') }}" class="d-inline">
                                                                    @csrf
                                                                    <input type="hidden" name="child_id" value="{{ $child->id }}">
                                                                    <input type="hidden" name="program_id" value="{{ $program ? $program->id : '' }}">
                                                                    <input type="hidden" name="attendance_date" value="{{ $date }}">
                                                                    <button type="submit" class="btn btn-success btn-sm px-2 py-1 fw-semibold shadow-sm" title="Quick Check-In Now">
                                                                        <i class="bi bi-box-arrow-in-right me-1"></i> Check In
                                                                    </button>
                                                                </form>

                                                                {{-- Quick Mark Absent --}}
                                                                <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1" onclick="openAbsentModal({{ $child->id }}, {{ $program ? $program->id : 'null' }}, '{{ addslashes($child->full_name) }}')" title="Mark Absent">
                                                                    <i class="bi bi-x-circle"></i>
                                                                </button>
                                                            @elseif ($att && $att->is_checked_in)
                                                                {{-- 1-Click Check Out --}}
                                                                <form method="POST" action="{{ route('admin.attendance.check-out', $att->id) }}" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-primary btn-sm px-2 py-1 fw-semibold shadow-sm" title="Quick Check-Out Now">
                                                                        <i class="bi bi-box-arrow-right me-1"></i> Check Out
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            {{-- Edit Modal Trigger (if record exists) --}}
                                                            @if ($att)
                                                                <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-1" onclick="openEditModal({{ $att->id }}, '{{ addslashes($child->full_name) }}', '{{ $att->status }}', '{{ $att->check_in_time }}', '{{ $att->check_out_time }}', '{{ addslashes($att->notes ?? '') }}')" title="Edit Record">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <form method="POST" action="{{ route('admin.attendance.destroy', $att->id) }}" class="d-inline" onsubmit="return confirm('Remove attendance record for {{ addslashes($child->full_name) }}?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-1" title="Delete Attendance">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- MODAL: Mark Absent --}}
    <div class="modal fade" id="absentModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.attendance.mark-absent') }}" class="modal-content">
                @csrf
                <input type="hidden" name="child_id" id="absentChildId">
                <input type="hidden" name="program_id" id="absentProgramId">
                <input type="hidden" name="attendance_date" value="{{ $date }}">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-x-circle me-2"></i>Mark Absent &bull; <span id="absentChildName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Absence Type</label>
                        <select name="status" class="form-select">
                            <option value="absent">Unexcused / Absent</option>
                            <option value="excused">Excused (Illness, Family Vacation, Doctor)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes / Reason (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="e.g. Fever, parent called in advance"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">Save Absence</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Edit Attendance --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="editAttendanceForm" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i>Edit Attendance &bull; <span id="editChildName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" id="editStatus" class="form-select" required>
                            <option value="present">Present</option>
                            <option value="late">Late</option>
                            <option value="absent">Absent</option>
                            <option value="excused">Excused</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Check-In Time</label>
                            <input type="time" name="check_in_time" id="editCheckInTime" class="form-control" step="1">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Check-Out Time</label>
                            <input type="time" name="check_out_time" id="editCheckOutTime" class="form-control" step="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" id="editNotes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Update Record</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Manual Attendance Entry --}}
    <div class="modal fade" id="manualAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.attendance.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle me-2"></i>Add / Record Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Child</label>
                        <select name="child_id" class="form-select" required>
                            <option value="">Select Child...</option>
                            @foreach ($allChildren as $c)
                                <option value="{{ $c->id }}">{{ $c->full_name }} ({{ $c->formatted_age }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Program</label>
                        <select name="program_id" class="form-select" required>
                            <option value="">Select Program...</option>
                            @foreach ($programs as $prog)
                                <option value="{{ $prog->id }}" {{ $programId == $prog->id ? 'selected' : '' }}>
                                    {{ $prog->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Date</label>
                            <input type="date" name="attendance_date" class="form-control" value="{{ $date }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="present" selected>Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                                <option value="excused">Excused</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Check-In Time</label>
                            <input type="time" name="check_in_time" class="form-control" value="{{ \Carbon\Carbon::now()->format('H:i') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Check-Out Time</label>
                            <input type="time" name="check_out_time" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold">Save Attendance</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAbsentModal(childId, programId, childName) {
            document.getElementById('absentChildId').value = childId;
            document.getElementById('absentProgramId').value = programId || '';
            document.getElementById('absentChildName').innerText = childName;
            new bootstrap.Modal(document.getElementById('absentModal')).show();
        }

        function openEditModal(attendanceId, childName, status, inTime, outTime, notes) {
            document.getElementById('editChildName').innerText = childName;
            document.getElementById('editAttendanceForm').action = '/admin/attendance/' + attendanceId;
            document.getElementById('editStatus').value = status;
            document.getElementById('editCheckInTime').value = inTime ? inTime.substring(0, 8) : '';
            document.getElementById('editCheckOutTime').value = outTime ? outTime.substring(0, 8) : '';
            document.getElementById('editNotes').value = notes;
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        function toggleSelectAll(master) {
            const checks = document.querySelectorAll('.roster-check');
            checks.forEach(c => c.checked = master.checked);
            updateBulkBar();
        }

        function updateBulkBar() {
            const checked = document.querySelectorAll('.roster-check:checked');
            const bulkBar = document.getElementById('bulkBar');
            const countSpan = document.getElementById('selectedCount');

            if (checked.length > 0) {
                bulkBar.style.display = 'block';
                countSpan.innerText = checked.length;
            } else {
                bulkBar.style.display = 'none';
                const selectAll = document.getElementById('selectAllCheckbox');
                if (selectAll) selectAll.checked = false;
            }
        }

        function submitBulk(action) {
            document.getElementById('bulkActionInput').value = action;
            document.getElementById('bulkForm').submit();
        }

        function clearBulkSelection() {
            document.querySelectorAll('.roster-check').forEach(c => c.checked = false);
            const selectAll = document.getElementById('selectAllCheckbox');
            if (selectAll) selectAll.checked = false;
            updateBulkBar();
        }
    </script>
</body>
</html>
