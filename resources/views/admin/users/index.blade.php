<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage users — create users, assign roles, and view profile details.">
    <title>User Management | Daycare System</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- AdminLTE 4 via Vite --}}
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Sidebar */
        .app-sidebar { font-family: 'Inter', sans-serif; }
        .sidebar-brand .brand-text { font-weight: 700; }

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
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem;
        }
        .stat-icon.total { background: #eef2ff; color: #4f46e5; }
        .stat-icon.admin { background: #fdf2f8; color: #db2777; }
        .stat-icon.staff { background: #eff6ff; color: #2563eb; }
        .stat-icon.parent { background: #ecfdf5; color: #059669; }

        /* Card */
        .card-custom {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        /* Avatar Circle */
        .avatar-circle {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }
        .avatar-admin { background: linear-gradient(135deg, #ec4899, #db2777); }
        .avatar-staff { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .avatar-parent { background: linear-gradient(135deg, #10b981, #059669); }

        /* Role Badges */
        .role-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.35rem 0.65rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .role-badge.admin { background: #fce7f3; color: #be185d; border: 1px solid #fbcfe8; }
        .role-badge.staff { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .role-badge.parent { background: #d1fae5; color: #047857; border: 1px solid #a7f3d0; }

        /* Status Badge */
        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .status-dot.active { background: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2); }
        .status-dot.inactive { background: #9ca3af; }

        /* Buttons */
        .btn-create-user {
            background: #fff;
            color: #4f46e5;
            border: none;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.88rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-create-user:hover {
            background: #f8fafc;
            color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        }

        .btn-action-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #6b7280;
            transition: all 0.2s;
            font-size: 0.85rem;
        }
        .btn-action-icon:hover {
            background: #f3f4f6;
            color: #111827;
        }
        .btn-action-icon.danger:hover {
            background: #fef2f2;
            color: #ef4444;
            border-color: #fecaca;
        }

        /* Filter Tabs */
        .filter-btn {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 8px;
            padding: 0.4rem 0.9rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s;
        }
        .filter-btn:hover { background: #f9fafb; color: #111827; }
        .filter-btn.active {
            background: #4f46e5;
            color: #fff;
            border-color: #4f46e5;
        }

        /* Table */
        .table > :not(caption) > * > * {
            padding: 1rem 1.25rem;
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
                            User Management
                        </span>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" title="Notifications"><i class="bi bi-bell"></i></a>
                    </li>
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
                        <li class="nav-header">MANAGEMENT</li>
                        <li class="nav-item"><a href="{{ route('admin.children.index') }}" class="nav-link"><i class="nav-icon bi bi-people-fill"></i><p>Children</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-person-badge-fill"></i><p>Staff</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-calendar-event-fill"></i><p>Activities</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.enrollments.index') }}" class="nav-link"><i class="nav-icon bi bi-clipboard-check-fill"></i><p>Enrollments</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-credit-card-2-front-fill"></i><p>Payments</p></a></li>
                        <li class="nav-header">ADMIN</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.programs.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-book-half"></i>
                                <p>Programs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}" class="nav-link active">
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
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-bar-chart-line-fill"></i><p>Analytics</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-gear-fill"></i><p>Settings</p></a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0 fw-bold" style="color: #1e1b4b;">User Accounts</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    {{-- Flash Alert --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert" id="flashAlert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert" id="flashAlert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Page Banner --}}
                    <div class="page-banner d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <h2><i class="bi bi-person-plus-fill me-2"></i> User & Role Management</h2>
                            <p>Create new system users, assign Spatie roles (Admin, Staff, Parent), and auto-create profiles in an atomic transaction.</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.users.create') }}" class="btn-create-user">
                                <i class="bi bi-plus-lg"></i> Add New User
                            </a>
                        </div>
                    </div>

                    {{-- Metrics Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="stat-card">
                                <div>
                                    <div class="text-muted small fw-semibold mb-1">TOTAL USERS</div>
                                    <div class="h3 mb-0 fw-bold text-dark">{{ $counts['total'] }}</div>
                                </div>
                                <div class="stat-icon total"><i class="bi bi-people-fill"></i></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="stat-card">
                                <div>
                                    <div class="text-muted small fw-semibold mb-1">ADMINISTRATORS</div>
                                    <div class="h3 mb-0 fw-bold text-pink">{{ $counts['admin'] }}</div>
                                </div>
                                <div class="stat-icon admin"><i class="bi bi-shield-shaded"></i></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="stat-card">
                                <div>
                                    <div class="text-muted small fw-semibold mb-1">STAFF MEMBERS</div>
                                    <div class="h3 mb-0 fw-bold text-primary">{{ $counts['staff'] }}</div>
                                </div>
                                <div class="stat-icon staff"><i class="bi bi-person-badge-fill"></i></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="stat-card">
                                <div>
                                    <div class="text-muted small fw-semibold mb-1">PARENTS / GUARDIANS</div>
                                    <div class="h3 mb-0 fw-bold text-success">{{ $counts['parent'] }}</div>
                                </div>
                                <div class="stat-icon parent"><i class="bi bi-heart-fill"></i></div>
                            </div>
                        </div>
                    </div>

                    {{-- Search and Filters --}}
                    <div class="card card-custom mb-4">
                        <div class="card-body p-3">
                            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-center">
                                @if(request('role'))
                                    <input type="hidden" name="role" value="{{ request('role') }}">
                                @endif
                                <div class="col-12 col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Search by name, email, phone, or NID..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-1 flex-wrap">
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), [])) }}" class="filter-btn {{ !request('role') ? 'active' : '' }}">All</a>
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'admin'])) }}" class="filter-btn {{ request('role') === 'admin' ? 'active' : '' }}">Admin</a>
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'staff'])) }}" class="filter-btn {{ request('role') === 'staff' ? 'active' : '' }}">Staff</a>
                                    <a href="{{ route('admin.users.index', array_merge(request()->except('role', 'page'), ['role' => 'parent'])) }}" class="filter-btn {{ request('role') === 'parent' ? 'active' : '' }}">Parent</a>
                                </div>
                                <div class="col-12 col-md-3 d-flex gap-2 justify-content-md-end">
                                    <select name="status" class="form-select form-select-sm" style="max-width: 140px;" onchange="this.form.submit()">
                                        <option value="">All Status</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @if(request('search') || request('role') || request('status') !== null)
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary" title="Clear Filters">
                                            <i class="bi bi-x-circle"></i> Reset
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Users Table --}}
                    <div class="card card-custom">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Role (Spatie)</th>
                                        <th>Profile Details</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    @php
                                                        $initials = strtoupper(substr($user->name, 0, 2));
                                                        $avatarClass = 'avatar-' . $user->role;
                                                    @endphp
                                                    @if($user->profile?->image)
                                                        <img src="{{ Storage::url($user->profile->image) }}" alt="{{ $user->name }}" class="rounded-circle border" width="42" height="42" style="object-fit: cover;">
                                                    @else
                                                        <div class="avatar-circle {{ $avatarClass }}">
                                                            {{ $initials }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                                        <div class="text-muted small">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $spatieRole = $user->roles->first()?->name ?? $user->role;
                                                @endphp
                                                <span class="role-badge {{ $spatieRole }}">
                                                    <i class="bi {{ $spatieRole === 'admin' ? 'bi-shield-shaded' : ($spatieRole === 'staff' ? 'bi-person-badge-fill' : 'bi-heart-fill') }}"></i>
                                                    {{ ucfirst($spatieRole) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($user->parentProfile)
                                                    <div class="small">
                                                        @if($user->parentProfile->mobile)
                                                            <div><i class="bi bi-telephone text-muted me-1"></i> {{ $user->parentProfile->mobile }}</div>
                                                        @endif
                                                        @if($user->parentProfile->city || $user->parentProfile->occupation)
                                                            <div class="text-muted">{{ $user->parentProfile->occupation ?? 'Parent' }} {{ $user->parentProfile->city ? '• '.$user->parentProfile->city : '' }}</div>
                                                        @endif
                                                    </div>
                                                @elseif($user->staffProfile)
                                                    <div class="small">
                                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ ucfirst($user->staffProfile->role ?? 'Staff') }}</span>
                                                        @if($user->staffProfile->hire_date)
                                                            <span class="text-muted ms-1">Hired: {{ \Carbon\Carbon::parse($user->staffProfile->hire_date)->format('M Y') }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                                        <span class="status-dot active"></span> Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">
                                                        <span class="status-dot inactive"></span> Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-muted small">
                                                {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    {{-- Edit Form --}}
                                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-action-icon" title="Edit User">
                                                        <i class="bi bi-pencil-square text-primary"></i>
                                                    </a>

                                                    {{-- Toggle Status Form --}}
                                                    @if(Auth::id() !== $user->id)
                                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn-action-icon" title="{{ $user->is_active ? 'Deactivate User' : 'Activate User' }}">
                                                                <i class="bi {{ $user->is_active ? 'bi-toggle-on text-success' : 'bi-toggle-off text-muted' }}"></i>
                                                            </button>
                                                        </form>

                                                        @if(!$user->is_super_admin)
                                                            <button type="button" class="btn-action-icon danger" onclick="openDeleteModal('{{ $user->id }}', '{{ $user->email }}')" title="Delete User">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-light text-muted border px-2 py-1 small ms-1">You</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="bi bi-people display-6 d-block mb-2 text-secondary"></i>
                                                No users found matching your criteria.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($users->hasPages())
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center py-3">
                                <div class="text-muted small">
                                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                                </div>
                                <div>
                                    {{ $users->links() }}
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">Daycare Management System</div>
            <strong>Copyright &copy; {{ date('Y') }}</strong> All rights reserved.
        </footer>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i> Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="mb-1">Are you sure you want to delete <strong id="deleteUserEmail"></strong>?</p>
                    <p class="text-muted small mb-0">This will permanently delete the user account, associated profile (Parent/Staff), and role assignments in one transaction.</p>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteUserForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-3 px-3">Delete User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(userId, userEmail) {
            document.getElementById('deleteUserEmail').textContent = userEmail;
            document.getElementById('deleteUserForm').action = `/admin/users/${userId}`;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        const fa = document.getElementById('flashAlert');
        if (fa) {
            setTimeout(() => {
                fa.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                fa.style.opacity = '0';
                fa.style.transform = 'translateY(-10px)';
                setTimeout(() => fa.remove(), 500);
            }, 4000);
        }
    </script>
</body>
</html>
