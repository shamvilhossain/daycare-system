<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Staff Management — view and manage teachers, assistants, therapists, and administration staff.">
    <title>Staff Management | KinderCare</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- AdminLTE 4 via Vite --}}
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Page Banner */
        .page-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #7c3aed 100%);
            border-radius: 16px;
            padding: 1.75rem 2rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.18);
        }
        .page-banner::before {
            content: '';
            position: absolute;
            top: -50px; right: -30px;
            width: 170px; height: 170px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.45rem; font-weight: 700; margin-bottom: 0.25rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.9rem; opacity: 0.88; position: relative; z-index: 1; margin: 0; }

        /* Stat Cards */
        .stat-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            transition: all 0.25s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            border-color: #cbd5e1;
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.35rem;
        }
        .stat-icon.total { background: #eef2ff; color: #4f46e5; }
        .stat-icon.daycare { background: #e0f2fe; color: #0284c7; }
        .stat-icon.therapy { background: #f3e8ff; color: #7e22ce; }
        .stat-icon.active { background: #ecfdf5; color: #059669; }

        /* Staff Avatar */
        .staff-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
        }
        .staff-avatar-placeholder {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 2px 5px rgba(99, 102, 241, 0.25);
        }

        /* Role Badges */
        .badge-role-teacher { background-color: #dbeafe; color: #1e40af; }
        .badge-role-assistant { background-color: #ccfbf1; color: #0f766e; }
        .badge-role-therapist { background-color: #f3e8ff; color: #6b21a8; }
        .badge-role-admin { background-color: #fef3c7; color: #92400e; }

        .badge-dept-daycare { background-color: #e0f2fe; color: #0369a1; }
        .badge-dept-therapy { background-color: #ede9fe; color: #5b21b6; }

        .badge-spec { background-color: #fae8ff; color: #86198f; font-weight: 600; }

        .btn-action-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #6b7280;
            transition: all 0.2s;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .btn-action-icon:hover { background: #f3f4f6; color: #111827; }
        .btn-action-icon.danger:hover { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
        .btn-action-icon.success:hover { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }

        /* Card custom */
        .card-custom {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .table > :not(caption) > * > * {
            padding: 0.9rem 1.15rem;
            vertical-align: middle;
        }
        .table thead th {
            background: #f9fafb;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 700;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
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
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <span class="nav-link text-muted" style="font-size:0.9rem;">
                            Staff Management
                        </span>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-muted" style="font-size:0.85rem;">{{ Auth::user()->email }}</span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="page-banner d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="bi bi-person-badge-fill me-2"></i>Staff Directory & Management</h2>
                            <p>Manage daycare teachers, assistants, therapy specialists, and administrative staff members.</p>
                        </div>
                        <a href="{{ route('admin.staff.create') }}" class="btn btn-light fw-bold shadow-sm" style="position:relative;z-index:2;">
                            <i class="bi bi-person-plus-fill me-1"></i> Add Staff Member
                        </a>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    {{-- Feedback Alerts --}}
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

                    {{-- Stat Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="stat-card">
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Total Staff</div>
                                    <div class="fs-4 fw-bold text-dark mt-1">{{ $counts['total'] }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">All registered staff</div>
                                </div>
                                <div class="stat-icon total">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card">
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Daycare Dept</div>
                                    <div class="fs-4 fw-bold text-primary mt-1">{{ $counts['daycare'] }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Teachers & Assistants</div>
                                </div>
                                <div class="stat-icon daycare">
                                    <i class="bi bi-building"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card">
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Therapy Dept</div>
                                    <div class="fs-4 fw-bold text-purple mt-1" style="color: #7e22ce;">{{ $counts['therapy'] }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">SLT, ABA, OT Specialists</div>
                                </div>
                                <div class="stat-icon therapy">
                                    <i class="bi bi-heart-pulse-fill"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card">
                                <div>
                                    <div class="text-muted small fw-semibold text-uppercase">Active Staff</div>
                                    <div class="fs-4 fw-bold text-success mt-1">{{ $counts['active'] }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $counts['inactive'] }} inactive</div>
                                </div>
                                <div class="stat-icon active">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters Card --}}
                    <div class="card card-custom mb-4">
                        <div class="card-body p-3">
                            <form method="GET" action="{{ route('admin.staff.index') }}" class="row g-2 align-items-center">
                                <div class="col-md-3">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search name, email, NID..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select name="department" class="form-select form-select-sm">
                                        <option value="">All Departments</option>
                                        <option value="daycare" {{ request('department') === 'daycare' ? 'selected' : '' }}>Daycare</option>
                                        <option value="therapy" {{ request('department') === 'therapy' ? 'selected' : '' }}>Therapy</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="role" class="form-select form-select-sm">
                                        <option value="">All Roles</option>
                                        <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                        <option value="assistant" {{ request('role') === 'assistant' ? 'selected' : '' }}>Assistant</option>
                                        <option value="therapist" {{ request('role') === 'therapist' ? 'selected' : '' }}>Therapist</option>
                                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="specialization" class="form-select form-select-sm">
                                        <option value="">All Specializations</option>
                                        <option value="slt" {{ request('specialization') === 'slt' ? 'selected' : '' }}>SLT (Speech)</option>
                                        <option value="aba" {{ request('specialization') === 'aba' ? 'selected' : '' }}>ABA (Behavior)</option>
                                        <option value="ot" {{ request('specialization') === 'ot' ? 'selected' : '' }}>OT (Occupational)</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">Status</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                        <i class="bi bi-funnel me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Filters">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Staff Table Card --}}
                    <div class="card card-custom">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;"></th>
                                            <th>Staff Member</th>
                                            <th>Role</th>
                                            <th>Department</th>
                                            <th>Specialization</th>
                                            <th>NID / Identification</th>
                                            <th>Status</th>
                                            <th class="text-end" style="width: 140px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($staffMembers as $staff)
                                            <tr>
                                                <td>
                                                    @if ($staff->photo_url)
                                                        <img src="{{ $staff->photo_url }}" class="staff-avatar" alt="{{ $staff->full_name }}">
                                                    @else
                                                        <div class="staff-avatar-placeholder">
                                                            {{ $staff->initials }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $staff->full_name }}</div>
                                                    <div class="text-muted small">
                                                        <i class="bi bi-envelope me-1"></i>{{ $staff->user?->email ?? 'No email linked' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $roleBadgeClass = match($staff->role) {
                                                            'teacher'   => 'badge-role-teacher',
                                                            'assistant' => 'badge-role-assistant',
                                                            'therapist' => 'badge-role-therapist',
                                                            'admin'     => 'badge-role-admin',
                                                            default     => 'bg-secondary text-white',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $roleBadgeClass }} px-2 py-1">
                                                        {{ $staff->role_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($staff->department === 'therapy')
                                                        <span class="badge badge-dept-therapy px-2 py-1">
                                                            <i class="bi bi-heart-pulse me-1"></i>Therapy
                                                        </span>
                                                    @else
                                                        <span class="badge badge-dept-daycare px-2 py-1">
                                                            <i class="bi bi-sun me-1"></i>Daycare
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($staff->specialization)
                                                        <span class="badge badge-spec px-2 py-1" title="{{ $staff->specialization_label }}">
                                                            {{ $staff->specialization_short }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($staff->nid)
                                                        <code class="text-muted small">{{ $staff->nid }}</code>
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($staff->is_active)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Active
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                            <i class="bi bi-slash-circle me-1"></i>Inactive
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex gap-1">
                                                        {{-- View details --}}
                                                        <a href="{{ route('admin.staff.show', $staff) }}" class="btn-action-icon" title="View Profile">
                                                            <i class="bi bi-eye"></i>
                                                        </a>

                                                        {{-- Edit --}}
                                                        <a href="{{ route('admin.staff.edit', $staff) }}" class="btn-action-icon" title="Edit Staff Member">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>

                                                        {{-- Toggle active status --}}
                                                        <form action="{{ route('admin.staff.toggle-status', $staff) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn-action-icon {{ $staff->is_active ? 'text-warning' : 'success' }}" title="{{ $staff->is_active ? 'Deactivate' : 'Activate' }}">
                                                                <i class="bi {{ $staff->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                                                            </button>
                                                        </form>

                                                        {{-- Delete --}}
                                                        <form action="{{ route('admin.staff.destroy', $staff) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete staff member {{ $staff->full_name }}?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-action-icon danger" title="Delete Staff Member">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5 text-muted">
                                                    <i class="bi bi-person-badge display-6 d-block mb-3 opacity-50"></i>
                                                    <p class="mb-1 fw-semibold">No staff members found matching criteria.</p>
                                                    <p class="small text-muted mb-3">Try adjusting your search filters or add a new staff member.</p>
                                                    <a href="{{ route('admin.staff.create') }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-plus-lg me-1"></i> Add Staff Member
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($staffMembers->hasPages())
                            <div class="card-footer bg-white border-top py-3">
                                {{ $staffMembers->links() }}
                            </div>
                        @endif
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
