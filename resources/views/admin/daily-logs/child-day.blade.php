<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $child->full_name }} &bull; Daily Operational Log | Daycare System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #8b5cf6 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 25px rgba(79,70,229,0.22);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -50px; right: -30px;
            width: 170px; height: 170px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .child-header-avatar {
            width: 64px; height: 64px; border-radius: 50%; object-fit: cover;
            border: 3px solid rgba(255,255,255,0.3);
        }
        .child-header-placeholder {
            width: 64px; height: 64px; border-radius: 50%;
            background: rgba(255,255,255,0.2); color: #fff; display: flex;
            align-items: center; justify-content: center; font-weight: 800; font-size: 1.35rem;
            border: 3px solid rgba(255,255,255,0.35);
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

        /* Timeline Styling */
        .daily-timeline {
            position: relative;
            padding: 1rem 0 1rem 2.25rem;
        }
        .daily-timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 20px;
            width: 3px;
            background: #e2e8f0;
            border-radius: 2px;
        }
        .timeline-node {
            position: relative;
            margin-bottom: 1.75rem;
        }
        .timeline-node:last-child {
            margin-bottom: 0;
        }
        .timeline-icon-wrap {
            position: absolute;
            left: -2.25rem;
            top: 2px;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            z-index: 2;
        }
        .timeline-card {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .timeline-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .node-nap .timeline-icon-wrap { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
        .node-meal .timeline-icon-wrap, .node-bottle .timeline-icon-wrap { background: linear-gradient(135deg, #10b981, #059669); }
        .node-activity .timeline-icon-wrap { background: linear-gradient(135deg, #0ea5e9, #0284c7); }
        .node-diaper_change .timeline-icon-wrap { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .node-incident .timeline-icon-wrap { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .node-medication .timeline-icon-wrap { background: linear-gradient(135deg, #ec4899, #db2777); }
        .node-special_program .timeline-icon-wrap { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .node-other .timeline-icon-wrap { background: linear-gradient(135deg, #64748b, #475569); }

        .quick-action-btn {
            border-radius: 24px;
            font-weight: 600;
            font-size: 0.82rem;
            padding: 0.45rem 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s ease;
        }
        .quick-action-btn:hover {
            transform: translateY(-1px);
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
                        <span class="nav-link text-muted fw-medium">Daily Operations &bull; Merged Child Timeline</span>
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
                    {{-- Child Header Banner --}}
                    <div class="page-banner d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            @if ($child->photo_url ?? false)
                                <img src="{{ $child->photo_url }}" class="child-header-avatar" alt="{{ $child->full_name }}">
                            @else
                                <div class="child-header-placeholder">
                                    {{ strtoupper(substr($child->first_name, 0, 1) . substr($child->last_name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h2 class="mb-0 fw-bold">{{ $child->full_name }}</h2>
                                    <span class="badge bg-white text-dark fw-bold px-2 py-1">{{ $child->formatted_age }}</span>
                                    @if ($attendance)
                                        @if ($attendance->is_checked_in)
                                            <span class="badge bg-emerald text-white fw-bold px-2 py-1" style="background-color: #10b981;">
                                                <i class="bi bi-box-arrow-in-right me-1"></i> In Facility (since {{ $attendance->formatted_check_in_time }})
                                            </span>
                                        @elseif ($attendance->is_checked_out)
                                            <span class="badge bg-light text-primary fw-bold px-2 py-1">
                                                <i class="bi bi-check-all me-1"></i> Picked Up (Stay: {{ $attendance->formatted_duration }})
                                            </span>
                                        @elseif ($attendance->status === 'absent')
                                            <span class="badge bg-danger text-white fw-bold px-2 py-1">Marked Absent</span>
                                        @endif
                                    @else
                                        <span class="badge bg-white text-secondary fw-semibold px-2 py-1">Not Checked In</span>
                                    @endif
                                </div>
                                <p class="mt-1 mb-0 opacity-90 small">
                                    Enrolled in:
                                    @if ($child->enrollments->where('status', 'active')->first())
                                        <strong>{{ $child->enrollments->where('status', 'active')->first()->program->name }}</strong>
                                    @else
                                        <em>General Roster</em>
                                    @endif
                                    @if ($child->allergies)
                                        &bull; <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill me-1"></i>Allergies: {{ $child->allergies }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Date Switcher in Header --}}
                        <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                            @php
                                $cDate = \Carbon\Carbon::parse($date);
                                $prevD = $cDate->copy()->subDay()->toDateString();
                                $nextD = $cDate->copy()->addDay()->toDateString();
                                $todayD = \Carbon\Carbon::today()->toDateString();
                            @endphp
                            <div class="btn-group btn-group-sm bg-white rounded-3 shadow-sm p-1">
                                <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $child->id, 'date' => $prevD]) }}" class="btn btn-outline-secondary border-0" title="Previous Day"><i class="bi bi-chevron-left"></i></a>
                                <span class="btn btn-light border-0 fw-bold text-dark px-3">{{ $cDate->format('M d, Y') }}</span>
                                <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $child->id, 'date' => $nextD]) }}" class="btn btn-outline-secondary border-0" title="Next Day"><i class="bi bi-chevron-right"></i></a>
                            </div>
                            <a href="{{ route('admin.child-daily-logs.index', ['date' => $date]) }}" class="btn btn-light fw-bold shadow-sm" title="Back to All Children Logs">
                                <i class="bi bi-arrow-left me-1"></i> Overview
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{-- Alerts --}}
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

                    {{-- Quick Log Action Bar --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-plus-circle-fill text-indigo" style="color: #6366f1;"></i>
                                <span>Record Operational Event:</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-purple quick-action-btn" style="border-color: #8b5cf6; color: #7c3aed;" onclick="openLogModal('nap')">
                                    <i class="bi bi-moon-stars-fill text-purple"></i> Log Nap
                                </button>
                                <button type="button" class="btn btn-outline-success quick-action-btn" onclick="openLogModal('meal')">
                                    <i class="bi bi-egg-fried text-success"></i> Log Meal / Snack
                                </button>
                                <button type="button" class="btn btn-outline-primary quick-action-btn" onclick="openLogModal('activity')">
                                    <i class="bi bi-palette-fill text-primary"></i> Log Activity
                                </button>
                                <button type="button" class="btn btn-outline-warning quick-action-btn" style="border-color: #f59e0b; color: #b45309;" onclick="openLogModal('diaper_change')">
                                    <i class="bi bi-droplet-half text-warning"></i> Log Diaper / Potty
                                </button>
                                <button type="button" class="btn btn-outline-danger quick-action-btn" onclick="openLogModal('incident')">
                                    <i class="bi bi-exclamation-triangle-fill text-danger"></i> Log Incident
                                </button>
                                <button type="button" class="btn btn-outline-secondary quick-action-btn" onclick="openLogModal('other')">
                                    <i class="bi bi-three-dots"></i> Other Note
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Merged Daily Aggregate Summary Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="card stat-card bg-white p-3 shadow-sm border-start border-4" style="border-left-color: #8b5cf6 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted small text-uppercase fw-bold">Nap & Rest</span>
                                    <i class="bi bi-moon-stars-fill text-purple" style="color: #8b5cf6;"></i>
                                </div>
                                <div class="fs-4 fw-bolder text-dark">{{ $summary['formatted_sleep_total'] }}</div>
                                <div class="small text-muted">{{ $summary['nap_count'] }} recorded nap{{ $summary['nap_count'] == 1 ? '' : 's' }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card stat-card bg-white p-3 shadow-sm border-start border-4 border-success">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted small text-uppercase fw-bold">Meals & Nutrition</span>
                                    <i class="bi bi-egg-fried text-success"></i>
                                </div>
                                <div class="fs-4 fw-bolder text-success">{{ $summary['meals_count'] }}</div>
                                <div class="small text-muted">Meals / snacks logged</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card stat-card bg-white p-3 shadow-sm border-start border-4 border-info">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted small text-uppercase fw-bold">Activities</span>
                                    <i class="bi bi-palette-fill text-info"></i>
                                </div>
                                <div class="fs-4 fw-bolder text-info">{{ $summary['activities_completed_count'] }}/{{ $summary['activities_count'] }}</div>
                                <div class="small text-muted">{{ $summary['activity_completion_rate'] }}% participation</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card stat-card bg-white p-3 shadow-sm border-start border-4" style="border-left-color: #f59e0b !important;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted small text-uppercase fw-bold">Care & Routine</span>
                                    <i class="bi bi-droplet-half" style="color: #f59e0b;"></i>
                                </div>
                                <div class="fs-4 fw-bolder text-dark">{{ $summary['diaper_changes_count'] }}</div>
                                <div class="small text-muted">{{ $summary['incidents_count'] }} incident{{ $summary['incidents_count'] == 1 ? '' : 's' }} logged</div>
                            </div>
                        </div>
                    </div>

                    {{-- Main Merged Timeline Card --}}
                    <div class="card shadow-sm border-0 mb-5">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-clock-history me-2 text-primary"></i>Merged Daily Routine &bull; {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                            </h5>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                Total Events: <strong>{{ count($logs) }}</strong>
                            </span>
                        </div>

                        <div class="card-body p-4">
                            @if ($logs->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-journal-x text-muted" style="font-size: 3.5rem;"></i>
                                    <h6 class="fw-bold mt-3 text-secondary">No daily operational events logged yet for {{ $child->first_name }} on this date.</h6>
                                    <p class="text-muted small">Use the action buttons above to record meals, naps, activities, diaper changes, or incidents.</p>
                                </div>
                            @else
                                <div class="daily-timeline">
                                    @foreach ($logs as $log)
                                        <div class="timeline-node node-{{ $log->log_type }}">
                                            <div class="timeline-icon-wrap" title="{{ $log->formatted_type }}">
                                                <i class="bi {{ $log->type_icon }}"></i>
                                            </div>

                                            <div class="timeline-card p-3 ms-3">
                                                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-2">
                                                    <div>
                                                        <span class="badge {{ $log->type_badge_class }} fw-bold px-2 py-1 text-uppercase" style="font-size: 0.72rem;">
                                                            {{ $log->formatted_type }}
                                                        </span>
                                                        @if ($log->start_time)
                                                            <span class="text-dark fw-bold ms-2" style="font-size: 0.95rem;">
                                                                {{ $log->formatted_start_time }}
                                                                @if ($log->end_time)
                                                                    &ndash; {{ $log->formatted_end_time }}
                                                                @endif
                                                            </span>
                                                        @else
                                                            <span class="text-muted ms-2 small">Untimed</span>
                                                        @endif
                                                    </div>

                                                    {{-- Actions: Edit / Delete --}}
                                                    <div class="d-flex align-items-center gap-1">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-0" style="font-size: 0.75rem;" onclick="openEditLogModal({{ json_encode($log) }})">
                                                            <i class="bi bi-pencil me-1"></i>Edit
                                                        </button>
                                                        <form method="POST" action="{{ route('admin.child-daily-logs.destroy', $log->id) }}" class="d-inline" onsubmit="return confirm('Delete this {{ $log->formatted_type }} entry?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-0" style="font-size: 0.75rem;">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>

                                                {{-- Specific Content based on Log Type --}}
                                                @if ($log->log_type === 'nap')
                                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                                        <span class="badge bg-purple-subtle text-purple border border-purple px-2 py-1">
                                                            <i class="bi bi-hourglass-split me-1"></i>Sleep Duration: {{ $log->formatted_duration }}
                                                        </span>
                                                    </div>
                                                @elseif (in_array($log->log_type, ['meal', 'bottle']))
                                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                                        @if ($log->meal_type)
                                                            <span class="badge bg-light text-dark border fw-bold text-uppercase" style="font-size: 0.72rem;">{{ ucfirst($log->meal_type) }}</span>
                                                        @endif
                                                        @if ($log->amount_eaten)
                                                            <span class="badge bg-success-subtle text-success border border-success">
                                                                <i class="bi bi-pie-chart-fill me-1"></i>Amount: {{ $log->amount_eaten }}
                                                            </span>
                                                        @endif
                                                        @if ($log->quality)
                                                            <span class="badge {{ $log->quality_badge_class }}">
                                                                Appetite: {{ ucfirst($log->quality) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if ($log->items_served)
                                                        <div class="text-dark small mb-2">
                                                            <strong>Items served:</strong> {{ $log->items_served }}
                                                        </div>
                                                    @endif
                                                @elseif ($log->log_type === 'activity')
                                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                                        @if ($log->activityOccurrence && $log->activityOccurrence->activity)
                                                            <span class="badge bg-primary text-white fw-bold">
                                                                {{ $log->activityOccurrence->activity->title }}
                                                            </span>
                                                        @endif
                                                        @if ($log->is_completed !== null)
                                                            @if ($log->is_completed)
                                                                <span class="badge bg-success-subtle text-success border border-success">
                                                                    <i class="bi bi-check-circle-fill me-1"></i>Completed
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning-subtle text-warning border border-warning">
                                                                    <i class="bi bi-pause-circle me-1"></i>Partial / Participated
                                                                </span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @elseif ($log->log_type === 'incident')
                                                    <div class="alert alert-danger py-2 px-3 mb-2 small">
                                                        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Incident Recorded:</strong> Please review actions taken and ensure parental notification.
                                                    </div>
                                                @endif

                                                {{-- Notes & Staff details --}}
                                                @if ($log->notes)
                                                    <p class="text-secondary small mb-2 bg-light p-2 rounded-2 border">
                                                        {{ $log->notes }}
                                                    </p>
                                                @endif

                                                <div class="text-muted small d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                                    <span>
                                                        <i class="bi bi-person-badge me-1"></i>Staff: <strong>{{ $log->staff ? $log->staff->full_name : 'Daycare Staff' }}</strong>
                                                    </span>
                                                    <span class="text-muted opacity-75">
                                                        Recorded at {{ $log->created_at->format('g:i A') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- UNIVERSAL QUICK LOG MODAL --}}
    <div class="modal fade" id="universalLogModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.child-daily-logs.store') }}" class="modal-content" id="logForm">
                @csrf
                <input type="hidden" name="child_id" value="{{ $child->id }}">
                <input type="hidden" name="log_date" value="{{ $date }}">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="logModalTitle">
                        <i class="bi bi-journal-plus me-2"></i>Record Entry
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Log Type --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Entry Type</label>
                        <select name="log_type" id="modalLogType" class="form-select" onchange="toggleLogFields(this.value)" required>
                            <option value="nap">Nap & Sleep</option>
                            <option value="meal">Meal & Nutrition</option>
                            <option value="bottle">Bottle Feeding</option>
                            <option value="activity">Learning Activity</option>
                            <option value="diaper_change">Diaper / Restroom</option>
                            <option value="incident">Incident / Health</option>
                            <option value="medication">Medication Administered</option>
                            <option value="special_program">Special Program</option>
                            <option value="other">General Note</option>
                        </select>
                    </div>

                    {{-- Time range --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Start Time</label>
                            <input type="time" name="start_time" id="modalStartTime" class="form-control" value="{{ \Carbon\Carbon::now()->format('H:i') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" id="endTimeLabel">End Time</label>
                            <input type="time" name="end_time" id="modalEndTime" class="form-control">
                        </div>
                    </div>

                    {{-- Meal-specific section --}}
                    <div id="mealSection" style="display: none;">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Meal Category</label>
                                <select name="meal_type" id="modalMealType" class="form-select">
                                    <option value="breakfast">Breakfast</option>
                                    <option value="lunch" selected>Lunch</option>
                                    <option value="snack">Snack</option>
                                    <option value="bottle">Bottle</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Appetite / Quality</label>
                                <select name="quality" id="modalQuality" class="form-select">
                                    <option value="good" selected>Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                    <option value="refused">Refused</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Amount Eaten</label>
                                <input type="text" name="amount_eaten" id="modalAmountEaten" class="form-control" placeholder="e.g. All, Most, Half, 6 oz">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Items Served</label>
                                <input type="text" name="items_served" id="modalItemsServed" class="form-control" placeholder="e.g. Pasta, apple slices">
                            </div>
                        </div>
                    </div>

                    {{-- Activity-specific section --}}
                    <div id="activitySection" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Scheduled Activity (Optional)</label>
                            <select name="activity_occurrence_id" id="modalActivityOccurrence" class="form-select">
                                <option value="">Select Scheduled Activity (or leave empty)...</option>
                                @foreach ($occurrences as $occ)
                                    <option value="{{ $occ->id }}">
                                        {{ $occ->activity->title }} ({{ $occ->start_time ? substr($occ->start_time, 0, 5) : 'Anytime' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_completed" value="1" id="modalIsCompleted" class="form-check-input" checked>
                            <label class="form-check-label fw-semibold" for="modalIsCompleted">Child completed / actively participated in this activity</label>
                        </div>
                    </div>

                    {{-- Staff attribution --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Logged By Staff</label>
                        <select name="staff_id" id="modalStaffId" class="form-select">
                            @foreach ($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ (Auth::user()->staffProfile && Auth::user()->staffProfile->id === $staff->id) ? 'selected' : '' }}>
                                    {{ $staff->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" id="notesLabel">Notes / Observations</label>
                        <textarea name="notes" id="modalNotes" class="form-control" rows="3" placeholder="Describe how the child behaved, sleep soundly, comments..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="modalSubmitBtn">Save Entry</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleLogFields(type) {
            const mealSection = document.getElementById('mealSection');
            const activitySection = document.getElementById('activitySection');
            const notesLabel = document.getElementById('notesLabel');
            const endTimeLabel = document.getElementById('endTimeLabel');

            if (type === 'meal' || type === 'bottle') {
                mealSection.style.display = 'block';
                activitySection.style.display = 'none';
                notesLabel.innerText = 'Meal Notes (Optional)';
            } else if (type === 'activity') {
                mealSection.style.display = 'none';
                activitySection.style.display = 'block';
                notesLabel.innerText = 'Activity Notes & Engagement';
            } else {
                mealSection.style.display = 'none';
                activitySection.style.display = 'none';
                if (type === 'incident') {
                    notesLabel.innerText = 'Incident Description & Immediate Care *';
                } else if (type === 'nap') {
                    notesLabel.innerText = 'Sleep Observations (Optional)';
                } else {
                    notesLabel.innerText = 'Notes / Details';
                }
            }
        }

        function openLogModal(type) {
            document.getElementById('logForm').action = "{{ route('admin.child-daily-logs.store') }}";
            // Remove PUT method spoofing if present
            const methodSpoof = document.getElementById('methodSpoofInput');
            if (methodSpoof) methodSpoof.remove();

            document.getElementById('logModalTitle').innerHTML = '<i class="bi bi-journal-plus me-2"></i>Record ' + type.charAt(0).toUpperCase() + type.slice(1).replace('_', ' ');
            document.getElementById('modalSubmitBtn').innerText = 'Save Entry';
            document.getElementById('modalLogType').value = type;
            document.getElementById('modalStartTime').value = new Date().toTimeString().substring(0, 5);
            document.getElementById('modalEndTime').value = '';
            document.getElementById('modalNotes').value = '';
            document.getElementById('modalAmountEaten').value = '';
            document.getElementById('modalItemsServed').value = '';

            toggleLogFields(type);
            new bootstrap.Modal(document.getElementById('universalLogModal')).show();
        }

        function openEditLogModal(log) {
            document.getElementById('logForm').action = '/admin/child-daily-logs/' + log.id;

            // Add PUT method spoofing
            let methodSpoof = document.getElementById('methodSpoofInput');
            if (!methodSpoof) {
                methodSpoof = document.createElement('input');
                methodSpoof.type = 'hidden';
                methodSpoof.name = '_method';
                methodSpoof.id = 'methodSpoofInput';
                methodSpoof.value = 'PUT';
                document.getElementById('logForm').appendChild(methodSpoof);
            }

            document.getElementById('logModalTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit ' + log.log_type.charAt(0).toUpperCase() + log.log_type.slice(1);
            document.getElementById('modalSubmitBtn').innerText = 'Update Entry';
            document.getElementById('modalLogType').value = log.log_type;
            document.getElementById('modalStartTime').value = log.start_time ? log.start_time.substring(0, 5) : '';
            document.getElementById('modalEndTime').value = log.end_time ? log.end_time.substring(0, 5) : '';
            document.getElementById('modalNotes').value = log.notes || '';
            document.getElementById('modalMealType').value = log.meal_type || 'lunch';
            document.getElementById('modalQuality').value = log.quality || 'good';
            document.getElementById('modalAmountEaten').value = log.amount_eaten || '';
            document.getElementById('modalItemsServed').value = log.items_served || '';
            if (log.activity_occurrence_id) {
                document.getElementById('modalActivityOccurrence').value = log.activity_occurrence_id;
            }
            if (log.staff_id) {
                document.getElementById('modalStaffId').value = log.staff_id;
            }
            document.getElementById('modalIsCompleted').checked = !!log.is_completed;

            toggleLogFields(log.log_type);
            new bootstrap.Modal(document.getElementById('universalLogModal')).show();
        }
    </script>
</body>
</html>
