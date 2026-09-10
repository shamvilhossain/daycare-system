<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Calendar Report - {{ $currentMonth->format('F Y') }} | KinderCare</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #8b5cf6 100%);
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
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
        }

        /* Monthly Calendar Grid Styles */
        .calendar-table {
            table-layout: fixed;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }
        .calendar-header-cell {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 8px;
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
        }
        .calendar-day-cell {
            height: 140px;
            vertical-align: top;
            padding: 8px;
            border-right: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            background: #fff;
            position: relative;
            transition: background 0.15s;
        }
        .calendar-day-cell:nth-child(7) { border-right: none; }
        .calendar-day-cell.other-month {
            background-color: #fafafa;
            opacity: 0.55;
        }
        .calendar-day-cell.is-today {
            background-color: #f5f3ff;
            border: 2px solid #8b5cf6 !important;
            border-radius: 4px;
        }
        .calendar-day-cell.is-weekend {
            background-color: #fafbfc;
        }
        .calendar-day-cell:hover:not(.other-month) {
            background-color: #f8fafc;
        }
        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .day-number {
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .is-today .day-number {
            background: #6366f1;
            color: #fff;
        }
        .day-count-badge {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #64748b;
        }
        .activities-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
            max-height: 98px;
            overflow-y: auto;
        }
        .activity-chip {
            font-size: 0.72rem;
            padding: 3px 6px;
            border-radius: 6px;
            line-height: 1.25;
            cursor: pointer;
            text-decoration: none;
            display: block;
            border-left: 3px solid transparent;
            background: #f8fafc;
            color: #1e293b;
            transition: transform 0.1s, box-shadow 0.1s;
        }
        .activity-chip:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .chip-completed {
            border-left-color: #10b981;
            background: #f0fdf4;
            color: #065f46;
        }
        .chip-planned {
            border-left-color: #3b82f6;
            background: #eff6ff;
            color: #1e40af;
        }
        .chip-partial {
            border-left-color: #f59e0b;
            background: #fffbeb;
            color: #92400e;
        }
        .chip-cancelled {
            border-left-color: #ef4444;
            background: #fef2f2;
            color: #991b1b;
            text-decoration: line-through;
        }
        .activity-time {
            font-size: 0.65rem;
            color: #64748b;
            font-weight: 500;
        }
        .category-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 3px;
        }

        @media print {
            .app-sidebar, .app-header, .page-banner, .filter-card, .btn-print, .no-print {
                display: none !important;
            }
            .app-main { margin: 0 !important; padding: 0 !important; }
            .calendar-day-cell { height: 110px !important; }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body no-print">
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
        <aside class="app-sidebar bg-body-secondary shadow no-print" data-bs-theme="dark">
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
                            <a href="{{ route('admin.reports.activity-calendar') }}" class="nav-link active">
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
                            <div class="badge bg-white text-indigo mb-2 px-3 py-1 fw-bold">Reports Section</div>
                            <h2><i class="bi bi-calendar-range-fill me-2"></i>Monthly Activity Calendar Report</h2>
                            <p class="mb-0 text-white-50">Overview of curriculum activities that occurred each day across daycare programs</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap no-print">
                            <button onclick="window.print()" class="btn btn-outline-light">
                                <i class="bi bi-printer me-1"></i> Print Report
                            </button>
                            <a href="{{ route('admin.activity-occurrences.create', ['date' => $currentMonth->toDateString()]) }}" class="btn btn-light text-primary fw-semibold">
                                <i class="bi bi-calendar-plus me-1"></i> Schedule Session
                            </a>
                        </div>
                    </div>

                    {{-- Month Navigator Bar --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                {{-- Month switcher --}}
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ request()->fullUrlWithQuery(['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="btn btn-outline-secondary btn-sm" title="Previous Month">
                                        <i class="bi bi-chevron-left"></i> {{ $prevMonth->format('M') }}
                                    </a>

                                    <h4 class="mb-0 fw-bold text-dark px-2">
                                        {{ $currentMonth->format('F Y') }}
                                    </h4>

                                    <a href="{{ request()->fullUrlWithQuery(['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="btn btn-outline-secondary btn-sm" title="Next Month">
                                        {{ $nextMonth->format('M') }} <i class="bi bi-chevron-right"></i>
                                    </a>

                                    @php $today = \Carbon\Carbon::today(); @endphp
                                    @if($month !== $today->month || $year !== $today->year)
                                        <a href="{{ request()->fullUrlWithQuery(['year' => $today->year, 'month' => $today->month]) }}" class="btn btn-sm btn-outline-primary ms-2">
                                            Today's Month
                                        </a>
                                    @endif
                                </div>

                                {{-- Year and Month Select Dropdowns --}}
                                <form method="GET" action="{{ route('admin.reports.activity-calendar') }}" class="d-flex align-items-center gap-2">
                                    @if($programId) <input type="hidden" name="program_id" value="{{ $programId }}"> @endif
                                    @if($category) <input type="hidden" name="category" value="{{ $category }}"> @endif
                                    @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
                                    @if($activityId) <input type="hidden" name="activity_id" value="{{ $activityId }}"> @endif

                                    <select name="month" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                                        @for($m = 1; $m <= 12; $m++)
                                            @php $mDate = \Carbon\Carbon::create(null, $m, 1); @endphp
                                            <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                                                {{ $mDate->format('F') }}
                                            </option>
                                        @endfor
                                    </select>

                                    <select name="year" class="form-select form-select-sm" style="width: 100px;" onchange="this.form.submit()">
                                        @for($y = $year - 2; $y <= $year + 2; $y++)
                                            <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Monthly KPI Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card p-3">
                                <div class="text-muted small text-uppercase fw-semibold">Total Sessions</div>
                                <div class="fs-4 fw-bold text-dark mt-1">{{ $stats['total'] }}</div>
                                <div class="small text-muted mt-1">{{ $stats['days_active'] }} days active</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card p-3">
                                <div class="text-muted small text-uppercase fw-semibold">Completed</div>
                                <div class="fs-4 fw-bold text-success mt-1">{{ $stats['completed'] }}</div>
                                <div class="small text-success mt-1">
                                    {{ $stats['total'] > 0 ? round(($stats['completed'] / $stats['total']) * 100) : 0 }}% completion
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card p-3">
                                <div class="text-muted small text-uppercase fw-semibold">Planned Ahead</div>
                                <div class="fs-4 fw-bold text-info mt-1">{{ $stats['planned'] }}</div>
                                <div class="small text-info mt-1">Upcoming sessions</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card p-3">
                                <div class="text-muted small text-uppercase fw-semibold">Top Learning Domain</div>
                                <div class="fs-6 fw-bold text-primary mt-2 text-truncate" title="{{ $stats['top_category'] }}">{{ $stats['top_category'] }}</div>
                                <div class="small text-muted mt-1">Most focused</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card p-3">
                                <div class="text-muted small text-uppercase fw-semibold">Top Program</div>
                                <div class="fs-6 fw-bold text-dark mt-2 text-truncate" title="{{ $stats['top_program'] }}">{{ $stats['top_program'] }}</div>
                                <div class="small text-muted mt-1">Highest engagement</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="card stat-card p-3">
                                <div class="text-muted small text-uppercase fw-semibold">Children Logs</div>
                                <div class="fs-4 fw-bold text-purple mt-1" style="color: #7c3aed;">{{ $stats['total_children_engaged'] }}</div>
                                <div class="small text-muted mt-1">Participations recorded</div>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Form --}}
                    <div class="card border-0 shadow-sm mb-4 filter-card no-print">
                        <div class="card-body p-3">
                            <form method="GET" action="{{ route('admin.reports.activity-calendar') }}" class="row g-2 align-items-center">
                                <input type="hidden" name="month" value="{{ $month }}">
                                <input type="hidden" name="year" value="{{ $year }}">

                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1 fw-semibold">Classroom / Program</label>
                                    <select name="program_id" class="form-select form-select-sm">
                                        <option value="">All Programs</option>
                                        @foreach($programs as $prog)
                                            <option value="{{ $prog->id }}" {{ (string)$programId === (string)$prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1 fw-semibold">Domain / Category</label>
                                    <select name="category" class="form-select form-select-sm">
                                        <option value="">All Domains</option>
                                        @foreach($categories as $key => $label)
                                            <option value="{{ $key }}" {{ $category === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label small text-muted mb-1 fw-semibold">Session Status</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">All Statuses</option>
                                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="planned" {{ $status === 'planned' ? 'selected' : '' }}>Planned</option>
                                        <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label small text-muted mb-1 fw-semibold">Activity Template</label>
                                    <select name="activity_id" class="form-select form-select-sm">
                                        <option value="">All Activities</option>
                                        @foreach($activities as $act)
                                            <option value="{{ $act->id }}" {{ (string)$activityId === (string)$act->id ? 'selected' : '' }}>{{ $act->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex align-items-end gap-2 pt-3">
                                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel"></i> Apply Filter</button>
                                    @if($programId || $category || $status || $activityId)
                                        <a href="{{ route('admin.reports.activity-calendar', ['month' => $month, 'year' => $year]) }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Monthly Activity Calendar Grid --}}
                    <div class="card border-0 shadow-sm overflow-hidden mb-4">
                        <div class="table-responsive">
                            <table class="calendar-table">
                                <thead>
                                    <tr>
                                        <th class="calendar-header-cell text-danger-emphasis">Sun</th>
                                        <th class="calendar-header-cell">Mon</th>
                                        <th class="calendar-header-cell">Tue</th>
                                        <th class="calendar-header-cell">Wed</th>
                                        <th class="calendar-header-cell">Thu</th>
                                        <th class="calendar-header-cell">Fri</th>
                                        <th class="calendar-header-cell text-danger-emphasis">Sat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $dayChunks = array_chunk($calendarDays, 7); @endphp
                                    @foreach($dayChunks as $week)
                                        <tr>
                                            @foreach($week as $day)
                                                @php
                                                    $cellClasses = [];
                                                    if (!$day['is_current_month']) $cellClasses[] = 'other-month';
                                                    if ($day['is_today']) $cellClasses[] = 'is-today';
                                                    if ($day['is_weekend']) $cellClasses[] = 'is-weekend';
                                                @endphp
                                                <td class="calendar-day-cell {{ implode(' ', $cellClasses) }}" onclick="openDayModal('{{ $day['date'] }}')">
                                                    <div class="day-header">
                                                        <span class="day-number">{{ $day['day_number'] }}</span>
                                                        @if($day['occurrences_count'] > 0)
                                                            <span class="day-count-badge" title="{{ $day['occurrences_count'] }} activities">
                                                                {{ $day['occurrences_count'] }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="activities-container">
                                                        @foreach($day['occurrences'] as $occ)
                                                            @php
                                                                $chipClass = match($occ->status) {
                                                                    'completed' => 'chip-completed',
                                                                    'planned'   => 'chip-planned',
                                                                    'partial'   => 'chip-partial',
                                                                    'cancelled' => 'chip-cancelled',
                                                                    default     => '',
                                                                };
                                                            @endphp
                                                            <div class="activity-chip {{ $chipClass }}" 
                                                                 title="{{ $occ->activity ? $occ->activity->name : 'Activity' }} ({{ $occ->time_range }}) - {{ $occ->status_label }}"
                                                                 onclick="event.stopPropagation(); openDetailModal({{ $occ->id }});">
                                                                <div class="fw-semibold text-truncate">
                                                                    <span class="category-dot" style="background-color: {{ $occ->activity ? $occ->activity->category_color : '#6b7280' }};"></span>
                                                                    {{ $occ->activity ? $occ->activity->name : 'Activity' }}
                                                                </div>
                                                                <div class="activity-time d-flex justify-content-between align-items-center">
                                                                    <span>{{ $occ->formatted_start_time }}</span>
                                                                    <span class="text-truncate" style="max-width: 55px;">{{ $occ->program ? $occ->program->name : '' }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Legend & Quick Overview --}}
                    <div class="card border-0 shadow-sm p-3 no-print">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3 flex-wrap small">
                                <span class="fw-semibold text-muted text-uppercase">Status Legend:</span>
                                <span><span class="badge bg-success-subtle text-success border border-success-subtle me-1">●</span> Completed</span>
                                <span><span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">●</span> Planned</span>
                                <span><span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle me-1">●</span> Partial</span>
                                <span><span class="badge bg-danger-subtle text-danger border border-danger-subtle me-1">●</span> Cancelled</span>
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-info-circle me-1"></i> Tip: Click any activity badge or day cell to see full staff notes and participating children.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Activity Occurrence Detail Modal --}}
    <div class="modal fade" id="occDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <span id="modalCategoryBadge" class="badge px-3 py-1 mb-2">Category</span>
                        <h4 id="modalActivityName" class="modal-title fw-bold text-dark">Activity Title</h4>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6 col-md-3">
                            <div class="p-2 bg-light rounded">
                                <div class="text-muted small">Date</div>
                                <div id="modalDate" class="fw-semibold text-dark">—</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-2 bg-light rounded">
                                <div class="text-muted small">Time Range</div>
                                <div id="modalTimeRange" class="fw-semibold text-dark">—</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-2 bg-light rounded">
                                <div class="text-muted small">Classroom</div>
                                <div id="modalProgram" class="fw-semibold text-dark">—</div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-2 bg-light rounded">
                                <div class="text-muted small">Lead Staff</div>
                                <div id="modalStaff" class="fw-semibold text-dark">—</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold text-muted small text-uppercase">Status</label>
                        <div id="modalStatusBadge" class="mt-1"></div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold text-muted small text-uppercase">Staff Observations & Feedback</label>
                        <div id="modalObservations" class="p-3 bg-light rounded text-secondary" style="line-height: 1.6;">—</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-semibold text-muted small text-uppercase">Materials Used</label>
                        <div id="modalMaterials" class="p-2 bg-light rounded text-dark small">—</div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <div class="small text-muted">
                            <i class="bi bi-people me-1"></i> <span id="modalChildrenCount">0</span> Children recorded
                            &nbsp;·&nbsp;
                            <i class="bi bi-camera me-1"></i> <span id="modalMediaCount">0</span> Media attached
                        </div>
                        <a id="modalViewFullLink" href="#" class="btn btn-primary btn-sm">
                            Open Full Session Dossier <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pass Occurrences JSON for interactive client modals --}}
    <script>
        const occurrencesData = @json($occurrencesByDate);

        function openDetailModal(occurrenceId) {
            let found = null;
            for (const date in occurrencesData) {
                const list = occurrencesData[date];
                const match = list.find(o => o.id === occurrenceId);
                if (match) {
                    found = match;
                    break;
                }
            }

            if (!found) return;

            document.getElementById('modalActivityName').innerText = found.activity ? found.activity.name : 'Activity';
            document.getElementById('modalCategoryBadge').innerText = found.activity ? found.activity.category_label : 'Category';
            document.getElementById('modalCategoryBadge').style.backgroundColor = found.activity ? found.activity.category_color : '#6366f1';
            document.getElementById('modalCategoryBadge').style.color = '#fff';

            document.getElementById('modalDate').innerText = found.occurrence_date ? new Date(found.occurrence_date).toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' }) : '—';
            document.getElementById('modalTimeRange').innerText = (found.start_time ? found.start_time.substring(0,5) : '') + (found.end_time ? ' - ' + found.end_time.substring(0,5) : '');
            document.getElementById('modalProgram').innerText = found.program ? found.program.name : 'General';
            document.getElementById('modalStaff').innerText = found.staff && found.staff.user ? found.staff.user.name : ('Staff #' + found.staff_id);

            // Status
            let statusBadge = `<span class="badge ${found.status === 'completed' ? 'bg-success' : (found.status === 'planned' ? 'bg-primary' : (found.status === 'partial' ? 'bg-warning' : 'bg-danger'))} text-uppercase">${found.status}</span>`;
            document.getElementById('modalStatusBadge').innerHTML = statusBadge;

            document.getElementById('modalObservations').innerText = found.observations || 'No staff observations recorded yet.';
            document.getElementById('modalMaterials').innerText = found.materials_used || 'Standard curriculum supplies.';
            document.getElementById('modalChildrenCount').innerText = found.child_daily_logs_count || 0;
            document.getElementById('modalMediaCount').innerText = (found.media ? found.media.length : 0);

            document.getElementById('modalViewFullLink').href = `/admin/activity-occurrences/${found.id}`;

            const modal = new bootstrap.Modal(document.getElementById('occDetailModal'));
            modal.show();
        }

        function openDayModal(dateStr) {
            const list = occurrencesData[dateStr];
            if (list && list.length === 1) {
                openDetailModal(list[0].id);
            } else if (list && list.length > 1) {
                // If multiple, open the first one with option to view
                openDetailModal(list[0].id);
            } else {
                // Empty day - quick redirect to schedule an activity on this date
                if (confirm(`No activities logged for ${dateStr}. Would you like to schedule an activity for this date?`)) {
                    window.location.href = `/admin/activity-occurrences/create?date=${dateStr}`;
                }
            }
        }
    </script>
</body>
</html>
