<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Staff Profile — view staff details, assignments, activity sessions, and daily log records.">
    <title>{{ $staff->full_name }} | Staff Profile | KinderCare</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- AdminLTE 4 via Vite --}}
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }

        .page-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #7c3aed 100%);
            border-radius: 16px;
            padding: 1.75rem 2rem;
            color: #fff;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.18);
        }
        .page-banner h2 { font-size: 1.45rem; font-weight: 700; margin-bottom: 0.25rem; }
        .page-banner p { font-size: 0.9rem; opacity: 0.88; margin: 0; }

        .card-custom {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .profile-avatar-lg {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .profile-avatar-placeholder-lg {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 2rem;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .metric-card {
            background: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
        }

        /* Role Badges */
        .badge-role-teacher { background-color: #dbeafe; color: #1e40af; }
        .badge-role-assistant { background-color: #ccfbf1; color: #0f766e; }
        .badge-role-therapist { background-color: #f3e8ff; color: #6b21a8; }
        .badge-role-admin { background-color: #fef3c7; color: #92400e; }

        .badge-dept-daycare { background-color: #e0f2fe; color: #0369a1; }
        .badge-dept-therapy { background-color: #ede9fe; color: #5b21b6; }
        .badge-spec { background-color: #fae8ff; color: #86198f; }

        .info-label {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1f2937;
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
                        <span class="nav-link text-muted" style="font-size:0.9rem;">Staff Profile</span>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-muted" style="font-size:0.85rem;">{{ Auth::user()->email }}</span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link"><i class="bi bi-box-arrow-right"></i> Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- Sidebar --}}
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="{{ route('dashboard') }}" class="brand-link">
                    <i class="bi bi-house-heart-fill brand-image" style="font-size:1.4rem;"></i>
                    <span class="brand-text fw-light"><b>Kinder</b>Care</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-header">MAIN</li>
                        <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link"><i class="nav-icon bi bi-grid-1x2-fill"></i><p>Dashboard</p></a></li>

                        <li class="nav-header">DAILY OPERATIONS</li>
                        <li class="nav-item"><a href="{{ route('admin.attendance.index') }}" class="nav-link"><i class="nav-icon bi bi-check2-circle"></i><p>Attendance Desk</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.child-daily-logs.index') }}" class="nav-link"><i class="nav-icon bi bi-journal-text"></i><p>Daily Child Logs</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.activity-occurrences.index') }}" class="nav-link"><i class="nav-icon bi bi-calendar-check"></i><p>Daily Schedule & Log</p></a></li>

                        <li class="nav-header">MANAGEMENT</li>
                        <li class="nav-item"><a href="{{ route('admin.children.index') }}" class="nav-link"><i class="nav-icon bi bi-people-fill"></i><p>Children</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.staff.index') }}" class="nav-link active"><i class="nav-icon bi bi-person-badge-fill"></i><p>Staff</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.activities.index') }}" class="nav-link"><i class="nav-icon bi bi-palette-fill"></i><p>Activity Catalog</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.enrollments.index') }}" class="nav-link"><i class="nav-icon bi bi-clipboard-check-fill"></i><p>Enrollments</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.invoices.index') }}" class="nav-link"><i class="nav-icon bi bi-receipt-cutoff"></i><p>Invoices & Payments</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.announcements.index') }}" class="nav-link"><i class="nav-icon bi bi-megaphone-fill"></i><p>Announcements</p></a></li>

                        @role('admin')
                        <li class="nav-header">ADMIN</li>
                        <li class="nav-item"><a href="{{ route('admin.programs.index') }}" class="nav-link"><i class="nav-icon bi bi-book-half"></i><p>Programs</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.users.index') }}" class="nav-link"><i class="nav-icon bi bi-person-lines-fill"></i><p>Users & Accounts</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.role-permissions.index') }}" class="nav-link"><i class="nav-icon bi bi-shield-lock-fill"></i><p>Role Permissions</p></a></li>
                        <li class="nav-header">REPORTS</li>
                        <li class="nav-item"><a href="{{ route('admin.reports.activity-calendar') }}" class="nav-link"><i class="nav-icon bi bi-calendar3"></i><p>Activity Calendar</p></a></li>
                        @endrole
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="page-banner d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="bi bi-person-badge me-2"></i>{{ $staff->full_name }}</h2>
                            <p>{{ $staff->role_label }} &bull; {{ $staff->department_label }} Department</p>
                        </div>
                        <div class="d-flex gap-2" style="position:relative;z-index:2;">
                            <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-light fw-bold shadow-sm">
                                <i class="bi bi-pencil me-1"></i> Edit Staff Member
                            </a>
                            <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-light fw-semibold">
                                <i class="bi bi-arrow-left me-1"></i> Staff Directory
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row g-4">
                        {{-- Left Column: Profile Card --}}
                        <div class="col-lg-4">
                            <div class="card card-custom mb-4 text-center p-4">
                                <div class="mb-3 d-flex justify-content-center">
                                    @if ($staff->photo_url)
                                        <img src="{{ $staff->photo_url }}" class="profile-avatar-lg" alt="{{ $staff->full_name }}">
                                    @else
                                        <div class="profile-avatar-placeholder-lg">
                                            {{ $staff->initials }}
                                        </div>
                                    @endif
                                </div>
                                <h4 class="fw-bold mb-1">{{ $staff->full_name }}</h4>
                                <div class="text-muted small mb-3">{{ $staff->user?->email }}</div>

                                <div class="d-flex flex-wrap justify-content-center gap-1 mb-4">
                                    @php
                                        $roleBadgeClass = match($staff->role) {
                                            'teacher'   => 'badge-role-teacher',
                                            'assistant' => 'badge-role-assistant',
                                            'therapist' => 'badge-role-therapist',
                                            'admin'     => 'badge-role-admin',
                                            default     => 'bg-secondary text-white',
                                        };
                                    @endphp
                                    <span class="badge {{ $roleBadgeClass }} px-2 py-1">{{ $staff->role_label }}</span>

                                    @if ($staff->department === 'therapy')
                                        <span class="badge badge-dept-therapy px-2 py-1">Therapy Dept</span>
                                    @else
                                        <span class="badge badge-dept-daycare px-2 py-1">Daycare Dept</span>
                                    @endif

                                    @if ($staff->specialization)
                                        <span class="badge badge-spec px-2 py-1">{{ $staff->specialization_short }}</span>
                                    @endif

                                    @if ($staff->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactive</span>
                                    @endif
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="metric-card">
                                            <div class="text-muted small">Sessions Led</div>
                                            <div class="fs-4 fw-bold text-primary">{{ $staff->activityOccurrences->count() }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric-card">
                                            <div class="text-muted small">Daily Logs</div>
                                            <div class="fs-4 fw-bold text-purple" style="color: #7e22ce;">{{ $staff->childDailyLogs->count() }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <form action="{{ route('admin.staff.toggle-status', $staff) }}" method="POST" class="flex-fill">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $staff->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} w-100 fw-semibold">
                                            <i class="bi {{ $staff->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }} me-1"></i>
                                            {{ $staff->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.staff.destroy', $staff) }}" method="POST" class="flex-fill" onsubmit="return confirm('Are you sure you want to delete staff member {{ $staff->full_name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 fw-semibold">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Information & Activity History --}}
                        <div class="col-lg-8">

                            {{-- Detailed Personal & Employment Info --}}
                            <div class="card card-custom mb-4">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-card-text text-primary me-2"></i>Staff Details & Identification</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="info-label">National ID (NID)</div>
                                            <div class="info-value">{{ $staff->nid ?? 'Not provided' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Date of Birth & Age</div>
                                            <div class="info-value">
                                                @if ($staff->date_of_birth)
                                                    {{ $staff->date_of_birth->format('F d, Y') }} ({{ $staff->date_of_birth->age }} years old)
                                                @else
                                                    Not provided
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Hire Date & Tenure</div>
                                            <div class="info-value">
                                                @if ($staff->hire_date)
                                                    {{ $staff->hire_date->format('F d, Y') }} ({{ $staff->hire_date->diffForHumans() }})
                                                @else
                                                    Not specified
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Therapy Specialization</div>
                                            <div class="info-value">
                                                {{ $staff->specialization_label ?? 'None (Daycare)' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">User Account Role</div>
                                            <div class="info-value">{{ ucfirst($staff->user?->role ?? 'None') }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Member Since</div>
                                            <div class="info-value">{{ $staff->created_at->format('M d, Y') }}</div>
                                        </div>
                                        <div class="col-12">
                                            <div class="info-label">Notes & Qualifications</div>
                                            <div class="p-3 bg-light rounded-3 text-secondary small">
                                                {{ $staff->note ?: 'No specific notes recorded for this staff member.' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Assigned Activity Sessions --}}
                            <div class="card card-custom mb-4">
                                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-calendar-event text-primary me-2"></i>Recent Activity Sessions Led</h6>
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $staff->activityOccurrences->count() }} total</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Activity</th>
                                                    <th>Date & Time</th>
                                                    <th>Status</th>
                                                    <th class="text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($staff->activityOccurrences->take(5) as $occ)
                                                    <tr>
                                                        <td class="fw-semibold">{{ $occ->activity?->title ?? 'Activity #' . $occ->activity_id }}</td>
                                                        <td class="small">{{ $occ->occurrence_date ? \Carbon\Carbon::parse($occ->occurrence_date)->format('M d, Y') : '—' }}</td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ ucfirst($occ->status ?? 'scheduled') }}</span>
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="{{ route('admin.activity-occurrences.show', $occ) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.8rem;">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-3 text-muted small">No activity sessions assigned to this staff member yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="app-footer text-muted py-3 px-4 border-top small bg-body">
            <div class="float-end d-none d-sm-inline">KinderCare Management System</div>
            <strong>Copyright &copy; {{ date('Y') }} KinderCare.</strong> All rights reserved.
        </footer>
    </div>
</body>
</html>
