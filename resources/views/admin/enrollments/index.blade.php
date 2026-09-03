<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollments | Daycare System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #0284c7 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 25px rgba(37,99,235,0.18);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -50px; right: -30px;
            width: 170px; height: 170px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.45rem; font-weight: 700; margin-bottom: 0.25rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.9rem; opacity: 0.88; position: relative; z-index: 1; margin: 0; }
        .child-avatar {
            width: 40px; height: 40px; border-radius: 50%; object-fit: cover;
            border: 2px solid #e5e7eb;
        }
        .child-avatar-placeholder {
            width: 40px; height: 40px; border-radius: 50%;
            background: #e0e7ff; color: #4f46e5; display: flex;
            align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;
        }
        .stat-card {
            border-radius: 12px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }
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
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand"><a href="/" class="brand-link"><span class="brand-text font-weight-light">DaycareSystem</span></a></div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-header">MAIN</li>
                        <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link"><i class="nav-icon bi bi-grid-1x2-fill"></i><p>Dashboard</p></a></li>
                        <li class="nav-header">DAILY OPERATIONS</li>
                        <li class="nav-item"><a href="{{ route('admin.attendance.index') }}" class="nav-link"><i class="nav-icon bi bi-check2-circle"></i><p>Attendance Desk</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.child-daily-logs.index') }}" class="nav-link"><i class="nav-icon bi bi-journal-text"></i><p>Daily Child Logs</p></a></li>
                        <li class="nav-header">MANAGEMENT</li>
                        <li class="nav-item"><a href="{{ route('admin.children.index') }}" class="nav-link"><i class="nav-icon bi bi-people-fill"></i><p>Children</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-person-badge-fill"></i><p>Staff</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-calendar-event-fill"></i><p>Activities</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.enrollments.index') }}" class="nav-link active"><i class="nav-icon bi bi-clipboard-check-fill"></i><p>Enrollments</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-credit-card-2-front-fill"></i><p>Payments</p></a></li>
                        <li class="nav-header">ADMIN</li>
                        <li class="nav-item"><a href="{{ route('admin.programs.index') }}" class="nav-link"><i class="nav-icon bi bi-book-half"></i><p>Programs</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.users.index') }}" class="nav-link"><i class="nav-icon bi bi-person-lines-fill"></i><p>Users & Accounts</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.role-permissions.index') }}" class="nav-link"><i class="nav-icon bi bi-shield-lock-fill"></i><p>Role Permissions</p></a></li>
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
                            <h2><i class="bi bi-clipboard-check-fill me-2"></i>Enrollments</h2>
                            <p>Manage program enrollments, capacity checks, age eligibility, and approval statuses</p>
                        </div>
                        <a href="{{ route('admin.enrollments.create') }}" class="btn btn-light fw-bold shadow-sm" style="position:relative;z-index:1;">
                            <i class="bi bi-plus-lg me-1"></i> New Enrollment
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
                        <div class="col-md-3 col-sm-6">
                            <div class="card stat-card shadow-sm border-0 border-start border-primary border-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small fw-semibold">TOTAL ENROLLMENTS</div>
                                            <div class="fs-4 fw-bold text-dark">{{ $stats['total'] }}</div>
                                        </div>
                                        <div class="p-2 rounded bg-primary-subtle text-primary fs-4"><i class="bi bi-collection-fill"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card stat-card shadow-sm border-0 border-start border-success border-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small fw-semibold">ACTIVE ENROLLED</div>
                                            <div class="fs-4 fw-bold text-success">{{ $stats['active'] }}</div>
                                        </div>
                                        <div class="p-2 rounded bg-success-subtle text-success fs-4"><i class="bi bi-check2-circle"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card stat-card shadow-sm border-0 border-start border-warning border-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small fw-semibold">PENDING APPROVALS</div>
                                            <div class="fs-4 fw-bold text-warning">{{ $stats['pending'] }}</div>
                                        </div>
                                        <div class="p-2 rounded bg-warning-subtle text-warning fs-4"><i class="bi bi-hourglass-split"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card stat-card shadow-sm border-0 border-start border-info border-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-muted small fw-semibold">GRADUATED / COMPLETED</div>
                                            <div class="fs-4 fw-bold text-info">{{ $stats['graduated'] }}</div>
                                        </div>
                                        <div class="p-2 rounded bg-info-subtle text-info fs-4"><i class="bi bi-mortarboard-fill"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Search & Filter --}}
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body py-2">
                            <form method="GET" action="{{ route('admin.enrollments.index') }}" class="row g-2 align-items-center">
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search child or program..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="program_id" class="form-select form-select-sm">
                                        <option value="">All Programs</option>
                                        @foreach($programs as $prog)
                                            <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                                                {{ $prog->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">All Statuses</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                        <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-funnel me-1"></i>Filter</button>
                                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
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
                                            <th>Program & Service</th>
                                            <th>Timeline</th>
                                            <th>Status</th>
                                            <th>Approval Info</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($enrollments as $enrollment)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($enrollment->child && $enrollment->child->photo_url)
                                                            <img src="{{ asset('storage/' . $enrollment->child->photo_url) }}" class="child-avatar" alt="">
                                                        @else
                                                            <div class="child-avatar-placeholder">
                                                                {{ strtoupper(substr($enrollment->child->first_name ?? 'C', 0, 1) . substr($enrollment->child->last_name ?? '', 0, 1)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="fw-semibold text-dark text-decoration-none">
                                                                {{ $enrollment->child->full_name ?? 'Unknown Child' }}
                                                            </a>
                                                            <small class="text-muted d-block">
                                                                Age: {{ $enrollment->child ? $enrollment->child->formatted_age : '—' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-medium text-dark">{{ $enrollment->program->name ?? 'Unknown Program' }}</div>
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                        {{ $enrollment->service_type_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="small">
                                                        <span class="fw-medium text-dark"><i class="bi bi-calendar-event me-1 text-muted"></i>{{ $enrollment->start_date ? $enrollment->start_date->format('M d, Y') : '—' }}</span>
                                                        <span class="text-muted"> &rarr; </span>
                                                        <span class="text-muted">{{ $enrollment->end_date ? $enrollment->end_date->format('M d, Y') : 'Ongoing' }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $enrollment->status_badge_class }}">
                                                        {{ $enrollment->status_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($enrollment->status === 'active' && $enrollment->approved_at)
                                                        <div class="small text-success"><i class="bi bi-check2-all me-1"></i>Approved</div>
                                                        <small class="text-muted d-block">By {{ $enrollment->approvedBy->name ?? 'Admin' }} on {{ $enrollment->approved_at->format('M d, Y') }}</small>
                                                    @elseif($enrollment->status === 'pending')
                                                        <span class="badge bg-warning-subtle text-warning"><i class="bi bi-clock me-1"></i>Awaiting Decision</span>
                                                    @elseif($enrollment->status === 'graduated')
                                                        <span class="small text-info"><i class="bi bi-mortarboard me-1"></i>Completed</span>
                                                    @elseif($enrollment->status === 'withdrawn')
                                                        <span class="small text-muted"><i class="bi bi-box-arrow-right me-1"></i>Withdrawn</span>
                                                    @elseif($enrollment->status === 'rejected')
                                                        <span class="small text-danger"><i class="bi bi-x-circle me-1"></i>Denied</span>
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        {{-- Quick Approve / Reject for Pending --}}
                                                        @if($enrollment->status === 'pending')
                                                            <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST" class="d-inline">
                                                                @csrf @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Approve Enrollment">
                                                                    <i class="bi bi-check-lg"></i>
                                                                </button>
                                                            </form>
                                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="openRejectModal('{{ route('admin.enrollments.reject', $enrollment) }}', '{{ addslashes($enrollment->child->full_name ?? '') }}')" title="Reject Enrollment">
                                                                <i class="bi bi-x-lg"></i>
                                                            </button>
                                                        @endif

                                                        <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this enrollment record?');">
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
                                                    <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                                                    No enrollment records found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            {{ $enrollments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Reject Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to reject the enrollment application for <strong id="rejectChildName"></strong>?</p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Reason for Rejection (Optional)</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Specify reason for denial..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(actionUrl, childName) {
            document.getElementById('rejectForm').action = actionUrl;
            document.getElementById('rejectChildName').textContent = childName;
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }
    </script>
</body>
</html>
