<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage role permissions — create roles and assign Spatie permissions.">
    <title>Role Permissions | Daycare System</title>

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
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 50%, #4c1d95 100%);
            border-radius: 16px;
            padding: 1.75rem 2rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .page-banner::before {
            content: '';
            position: absolute;
            top: -50px; right: -30px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
        }
        .page-banner h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 0.25rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.9rem; opacity: 0.85; position: relative; z-index: 1; margin: 0; }

        /* Card */
        .card { border: 1px solid #e5e7eb; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .card-header { background: transparent; border-bottom: 1px solid #f3f4f6; }
        .card-title i { color: #7c3aed; }

        /* Role Cards Grid */
        .roles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }

        .role-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        .role-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }
        .role-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border-color: transparent;
        }
        .role-card.rc-admin::after { background: linear-gradient(90deg, #ef4444, #f87171); }
        .role-card.rc-staff::after { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .role-card.rc-parent::after { background: linear-gradient(90deg, #10b981, #34d399); }
        .role-card.rc-custom::after { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

        .role-card-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }
        .role-card-icon.ic-admin { background: #fef2f2; color: #ef4444; }
        .role-card-icon.ic-staff { background: #eff6ff; color: #3b82f6; }
        .role-card-icon.ic-parent { background: #ecfdf5; color: #10b981; }
        .role-card-icon.ic-custom { background: #fffbeb; color: #f59e0b; }

        .role-card-name {
            font-size: 1.1rem; font-weight: 700; color: #1e1b4b;
            text-transform: capitalize; margin-bottom: 0.25rem;
        }
        .role-card-meta {
            font-size: 0.8rem; color: #9ca3af;
            display: flex; align-items: center; gap: 6px; margin-bottom: 1rem;
        }
        .role-card-actions { display: flex; gap: 0.5rem; }

        .btn-edit-role {
            flex: 1;
            padding: 0.45rem 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            color: #7c3aed;
            font-size: 0.8rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        .btn-edit-role:hover {
            background: #f3f0ff;
            border-color: #7c3aed;
        }

        .btn-delete-role {
            padding: 0.45rem 0.65rem;
            border: 1px solid #fecaca;
            border-radius: 8px;
            background: #fff;
            color: #ef4444;
            font-size: 0.8rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }
        .btn-delete-role:hover {
            background: #fef2f2;
            border-color: #ef4444;
        }

        /* Create Role Button (in the grid) */
        .role-card-create {
            border: 2px dashed #c4b5fd;
            background: #faf8ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 180px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .role-card-create:hover {
            border-color: #7c3aed;
            background: #f3f0ff;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(124, 58, 237, 0.12);
        }
        .role-card-create::after { display: none; }
        .role-card-create i { font-size: 2rem; color: #7c3aed; margin-bottom: 0.5rem; }
        .role-card-create span { font-size: 0.9rem; font-weight: 600; color: #7c3aed; }

        /* Permission Badge */
        .perm-badge-sm {
            font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 600;
        }
        .perm-badge-sm.view { background: #dbeafe; color: #1d4ed8; }
        .perm-badge-sm.create { background: #d1fae5; color: #047857; }
        .perm-badge-sm.update { background: #fef3c7; color: #b45309; }
        .perm-badge-sm.delete { background: #fee2e2; color: #b91c1c; }
        .perm-badge-sm.manage { background: #ede9fe; color: #6d28d9; }

        /* Modal Styling */
        .modal .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 25px 60px rgba(0,0,0,0.15);
        }
        .modal .modal-header {
            border-bottom: 1px solid #f3f4f6;
            padding: 1.25rem 1.5rem;
        }
        .modal .modal-header .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e1b4b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .modal .modal-body { padding: 1.5rem; }
        .modal .modal-footer {
            border-top: 1px solid #f3f4f6;
            padding: 1rem 1.5rem;
        }

        /* Modal Input */
        .modal-input {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: #1e1b4b;
            background: #fff;
            transition: all 0.3s ease;
            outline: none;
        }
        .modal-input:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
        }
        .modal-input::placeholder { color: #9ca3af; }
        .modal-label {
            font-size: 0.85rem; font-weight: 600; color: #1e1b4b;
            margin-bottom: 0.4rem; display: block;
        }

        /* Permission Checklist (modal) */
        .perm-checklist {
            max-height: 350px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
        }
        .perm-checklist::-webkit-scrollbar { width: 6px; }
        .perm-checklist::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        .perm-module-header {
            background: #f0edf9;
            padding: 0.5rem 0.85rem;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7c3aed;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .perm-check-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0.4rem 0.85rem;
            border-bottom: 1px solid #f9fafb;
            transition: background 0.15s ease;
            cursor: pointer;
        }
        .perm-check-item:hover { background: #faf9ff; }
        .perm-check-item:last-child { border-bottom: none; }
        .perm-check-item input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: #7c3aed;
            cursor: pointer;
            flex-shrink: 0;
        }
        .perm-check-item label {
            font-size: 0.82rem; color: #374151;
            cursor: pointer; flex: 1; margin: 0;
        }

        /* Search */
        .perm-search {
            width: 100%;
            padding: 0.55rem 0.85rem 0.55rem 2.2rem;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.82rem;
            font-family: 'Inter', sans-serif;
            color: #1e1b4b;
            background: #fff;
            outline: none;
            border-radius: 10px 10px 0 0;
        }
        .perm-search::placeholder { color: #9ca3af; }
        .perm-search-wrap { position: relative; }
        .perm-search-wrap > i {
            position: absolute; left: 0.75rem; top: 50%;
            transform: translateY(-50%); color: #9ca3af;
            font-size: 0.85rem; pointer-events: none;
        }

        /* Shortcuts */
        .select-shortcuts {
            display: flex; gap: 6px; margin-bottom: 0.5rem;
        }
        .select-shortcuts button {
            font-size: 0.72rem; padding: 3px 10px;
            border: 1px solid #d1d5db; border-radius: 6px;
            background: #fff; color: #6b7280;
            cursor: pointer; transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }
        .select-shortcuts button:hover {
            background: #f3f0ff; border-color: #7c3aed; color: #7c3aed;
        }

        /* Buttons */
        .btn-primary-custom {
            padding: 0.55rem 1.5rem; border: none; border-radius: 10px;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: #fff; font-size: 0.85rem; font-weight: 600;
            font-family: 'Inter', sans-serif; cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.35);
        }
        .btn-primary-custom:active { transform: translateY(0); }

        .btn-success-custom {
            padding: 0.55rem 1.5rem; border: none; border-radius: 10px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff; font-size: 0.85rem; font-weight: 600;
            font-family: 'Inter', sans-serif; cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
        }

        .btn-danger-custom {
            padding: 0.5rem 1.25rem; border: none; border-radius: 10px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff; font-size: 0.85rem; font-weight: 600;
            font-family: 'Inter', sans-serif; cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-danger-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
        }

        .btn-cancel {
            padding: 0.5rem 1.25rem; border: 1px solid #d1d5db; border-radius: 10px;
            background: #fff; color: #6b7280; font-size: 0.85rem;
            font-weight: 500; font-family: 'Inter', sans-serif;
            cursor: pointer; transition: all 0.2s ease;
        }
        .btn-cancel:hover { background: #f9fafb; border-color: #9ca3af; }

        /* Alerts */
        .alert-custom {
            border-radius: 12px; padding: 0.85rem 1.25rem;
            font-size: 0.875rem; font-weight: 500;
            display: flex; align-items: center; gap: 8px;
            animation: slideDown 0.4s ease;
        }
        .alert-custom.alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; }
        .alert-custom.alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .validation-error { font-size: 0.78rem; color: #ef4444; margin-top: 4px; }

        /* Animation */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeSlideUp 0.5s ease-out backwards; }
        .animate-delay-1 { animation-delay: 0.05s; }
        .animate-delay-2 { animation-delay: 0.1s; }
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
                            Good <span id="timeGreeting">day</span>, <strong>{{ explode('@', Auth::user()->email)[0] }}</strong>
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
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-clipboard-check-fill"></i><p>Enrollments</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-credit-card-2-front-fill"></i><p>Payments</p></a></li>
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
                            <a href="{{ route('admin.role-permissions.index') }}" class="nav-link active">
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
                        <div class="col-sm-6"><h3 class="mb-0">Role Permissions</h3></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="#">Admin</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Role Permissions</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    {{-- Page Banner --}}
                    <div class="page-banner animate-in">
                        <h2><i class="bi bi-shield-lock me-2"></i>Role & Permission Manager</h2>
                        <p>Manage roles and assign permissions. Click on a role to edit its permissions.</p>
                    </div>

                    {{-- Alerts --}}
                    @if (session('success'))
                        <div class="alert-custom alert-success mb-3 animate-in" id="flashAlert">
                            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert-custom alert-error mb-3 animate-in" id="flashAlert">
                            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert-custom alert-error mb-3 animate-in" id="flashAlert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Roles Grid --}}
                    <div class="roles-grid animate-in animate-delay-1">
                        @foreach ($roles as $role)
                            @php
                                $variant = match($role->name) {
                                    'admin' => 'admin',
                                    'staff' => 'staff',
                                    'parent' => 'parent',
                                    default => 'custom',
                                };
                                $icons = [
                                    'admin' => 'bi-shield-fill-check',
                                    'staff' => 'bi-person-workspace',
                                    'parent' => 'bi-people-fill',
                                    'custom' => 'bi-person-gear',
                                ];
                                $isProtected = in_array($role->name, ['admin', 'staff', 'parent']);
                            @endphp
                            <div class="role-card rc-{{ $variant }}">
                                <div class="role-card-icon ic-{{ $variant }}">
                                    <i class="bi {{ $icons[$variant] }}"></i>
                                </div>
                                <div class="role-card-name">{{ $role->name }}</div>
                                <div class="role-card-meta">
                                    <i class="bi bi-key-fill"></i>
                                    {{ $role->permissions->count() }} of {{ $permissions->count() }} permissions
                                </div>
                                <div class="role-card-actions">
                                    <button type="button"
                                            class="btn-edit-role"
                                            onclick="openEditModal({{ $role->id }}, '{{ $role->name }}', {{ $role->permissions->pluck('id') }})">
                                        <i class="bi bi-pencil-square"></i> Edit Permissions
                                    </button>
                                    @unless ($isProtected)
                                        <button type="button"
                                                class="btn-delete-role"
                                                onclick="openDeleteModal({{ $role->id }}, '{{ $role->name }}')">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    @endunless
                                </div>
                            </div>
                        @endforeach

                        {{-- Create Role Card --}}
                        <div class="role-card role-card-create" onclick="openCreateModal()">
                            <i class="bi bi-plus-circle-dotted"></i>
                            <span>Create New Role</span>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">AdminLTE 4</div>
            <strong>&copy; {{ date('Y') }} Daycare System.</strong> All rights reserved.
        </footer>
    </div>

    {{-- ============================================================ --}}
    {{-- CREATE ROLE MODAL --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.role-permissions.store') }}" method="POST" id="createRoleForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle-fill" style="color:#10b981;"></i>
                            Create New Role
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Role Name --}}
                        <div class="mb-3">
                            <label class="modal-label">Role Name</label>
                            <input type="text" name="name" class="modal-input" placeholder="e.g. supervisor, accountant" required>
                            <div style="font-size:0.73rem; color:#9ca3af; margin-top:4px;">
                                <i class="bi bi-info-circle me-1"></i>Auto-converted to lowercase.
                            </div>
                        </div>

                        {{-- Permissions --}}
                        <div>
                            <label class="modal-label">
                                Assign Permissions <span style="font-weight:400; color:#9ca3af;">(optional)</span>
                            </label>
                            <div class="select-shortcuts">
                                <button type="button" onclick="toggleAllIn('#createPermList', true)">
                                    <i class="bi bi-check-all me-1"></i>Select All
                                </button>
                                <button type="button" onclick="toggleAllIn('#createPermList', false)">
                                    <i class="bi bi-x-lg me-1"></i>Clear All
                                </button>
                            </div>
                            <div class="perm-checklist" id="createPermList">
                                <div class="perm-search-wrap">
                                    <i class="bi bi-search"></i>
                                    <input type="text" class="perm-search" placeholder="Filter permissions..." oninput="filterPerms('#createPermList', this.value)">
                                </div>
                                @foreach ($grouped as $module => $modulePermissions)
                                    <div class="perm-module-header" data-module="{{ $module }}">
                                        <i class="bi bi-folder-fill"></i>
                                        {{ str_replace('-', ' ', $module) }}
                                        <span style="font-weight:400; opacity:0.6; margin-left:auto; font-size:0.65rem;">{{ $modulePermissions->count() }}</span>
                                    </div>
                                    @foreach ($modulePermissions as $permission)
                                        <div class="perm-check-item" data-perm-name="{{ $permission->name }}">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="cp-{{ $permission->id }}">
                                            <label for="cp-{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-success-custom">
                            <i class="bi bi-plus-lg"></i> Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- EDIT ROLE PERMISSIONS MODAL --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="editRoleForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square" style="color:#7c3aed;"></i>
                            Edit Permissions — <span id="editRoleName" style="text-transform:capitalize;"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="select-shortcuts">
                            <button type="button" onclick="toggleAllIn('#editPermList', true)">
                                <i class="bi bi-check-all me-1"></i>Select All
                            </button>
                            <button type="button" onclick="toggleAllIn('#editPermList', false)">
                                <i class="bi bi-x-lg me-1"></i>Clear All
                            </button>
                        </div>
                        <div class="perm-checklist" id="editPermList">
                            <div class="perm-search-wrap">
                                <i class="bi bi-search"></i>
                                <input type="text" class="perm-search" placeholder="Filter permissions..." oninput="filterPerms('#editPermList', this.value)">
                            </div>
                            @foreach ($grouped as $module => $modulePermissions)
                                <div class="perm-module-header" data-module="{{ $module }}">
                                    <i class="bi bi-folder-fill"></i>
                                    {{ str_replace('-', ' ', $module) }}
                                    <span style="font-weight:400; opacity:0.6; margin-left:auto; font-size:0.65rem;">{{ $modulePermissions->count() }}</span>
                                </div>
                                @foreach ($modulePermissions as $permission)
                                    <div class="perm-check-item" data-perm-name="{{ $permission->name }}">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="ep-{{ $permission->id }}">
                                        <label for="ep-{{ $permission->id }}">{{ $permission->name }}</label>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-primary-custom">
                            <i class="bi bi-check2-circle"></i> Save Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- DELETE ROLE MODAL --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-size:1rem; font-weight:600;">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Delete Role
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:0.9rem; color:#374151; margin:0;">
                        Are you sure you want to delete <strong id="deleteRoleName"></strong>?
                        All permission assignments for this role will be removed.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteRoleForm" method="POST" style="margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-custom">
                            <i class="bi bi-trash3 me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Time greeting
        (function() {
            const h = new Date().getHours();
            const g = h < 12 ? 'morning' : h < 17 ? 'afternoon' : 'evening';
            const el = document.getElementById('timeGreeting');
            if (el) el.textContent = g;
        })();

        // Helper to safely get Bootstrap Modal instance
        function showModal(modalId) {
            const el = document.getElementById(modalId);
            const bs = window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
            if (bs && bs.Modal) {
                const modal = bs.Modal.getOrCreateInstance(el);
                modal.show();
            } else {
                console.error('Bootstrap JS is not loaded.');
            }
        }

        // Open Create Role modal
        function openCreateModal() {
            const form = document.getElementById('createRoleForm');
            form.reset();
            toggleAllIn('#createPermList', false);
            showModal('createRoleModal');
        }

        // Open Edit Role modal
        function openEditModal(roleId, roleName, assignedPermIds) {
            document.getElementById('editRoleName').textContent = roleName;
            document.getElementById('editRoleForm').action = `/admin/role-permissions/${roleId}`;

            // Reset all checkboxes then check the assigned ones
            document.querySelectorAll('#editPermList input[type="checkbox"]').forEach(cb => {
                cb.checked = assignedPermIds.includes(parseInt(cb.value));
            });

            // Clear search
            const searchInput = document.querySelector('#editPermList .perm-search');
            if (searchInput) { searchInput.value = ''; filterPerms('#editPermList', ''); }

            showModal('editRoleModal');
        }

        // Open Delete modal
        function openDeleteModal(roleId, roleName) {
            document.getElementById('deleteRoleName').textContent = roleName;
            document.getElementById('deleteRoleForm').action = `/admin/role-permissions/${roleId}`;
            showModal('deleteRoleModal');
        }

        // Toggle all checkboxes in a container
        function toggleAllIn(containerSelector, state) {
            document.querySelectorAll(`${containerSelector} input[type="checkbox"]`).forEach(cb => cb.checked = state);
        }

        // Filter permissions in a checklist
        function filterPerms(containerSelector, query) {
            const q = query.toLowerCase().trim();
            const container = document.querySelector(containerSelector);
            const items = container.querySelectorAll('.perm-check-item');
            const headers = container.querySelectorAll('.perm-module-header');
            const visibleModules = new Set();

            items.forEach(item => {
                const name = item.dataset.permName.toLowerCase();
                const visible = !q || name.includes(q);
                item.style.display = visible ? '' : 'none';
                if (visible) {
                    let prev = item.previousElementSibling;
                    while (prev && !prev.classList.contains('perm-module-header')) {
                        prev = prev.previousElementSibling;
                    }
                    if (prev) visibleModules.add(prev.dataset.module);
                }
            });
            headers.forEach(h => {
                h.style.display = visibleModules.has(h.dataset.module) ? '' : 'none';
            });
        }

        // Auto-dismiss flash alert
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
