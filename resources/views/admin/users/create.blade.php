<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create a new user account, assign Spatie role, and build profile.">
    <title>Create User | Daycare System</title>

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

        /* Card */
        .card-form {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .card-form .card-header {
            background: #fafafa;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 1.75rem;
        }

        /* Form Labels & Controls */
        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 0.35rem;
        }
        .form-label .required {
            color: #ef4444;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1.5px solid #e5e7eb;
            padding: 0.65rem 0.9rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        /* Role Selector Cards */
        .role-option-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.25s ease;
            background: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }
        .role-option-card:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }
        .role-option-card.selected-admin {
            border-color: #ec4899;
            background: #fdf2f8;
        }
        .role-option-card.selected-staff {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .role-option-card.selected-parent {
            border-color: #10b981;
            background: #ecfdf5;
        }
        .role-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .role-icon.admin { background: #fce7f3; color: #be185d; }
        .role-icon.staff { background: #dbeafe; color: #1d4ed8; }
        .role-icon.parent { background: #d1fae5; color: #047857; }

        /* Section Dividers */
        .section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e1b4b;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed #e5e7eb;
        }
        .section-title i { color: #6366f1; }

        /* Transaction Banner */
        .tx-info-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        /* Buttons */
        .btn-submit {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #fff;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
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
                            <h3 class="mb-0 fw-bold" style="color: #1e1b4b;">Create New User</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                                <li class="breadcrumb-item active">Create</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    {{-- Form Card --}}
                    <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">
                            {{-- Left Column: Account Credentials & Role Selection --}}
                            <div class="col-12 col-lg-5">
                                <div class="card-form h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-lock text-primary me-2"></i> Account Credentials</h5>
                                    </div>
                                    <div class="card-body p-4">

                                        {{-- Role Selection Cards --}}
                                        <div class="mb-4">
                                            <label class="form-label d-block">Select User Role <span class="required">*</span></label>
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label class="role-option-card" id="cardRoleParent" for="roleParent">
                                                        <input class="form-check-input visually-hidden" type="radio" name="role" id="roleParent" value="parent" {{ old('role', 'parent') === 'parent' ? 'checked' : '' }} onchange="onRoleChanged(this.value)">
                                                        <div class="role-icon parent"><i class="bi bi-heart-fill"></i></div>
                                                        <div>
                                                            <div class="fw-bold text-dark">Parent / Guardian</div>
                                                            <div class="text-muted small">Can view child activities, logs & invoices</div>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div class="col-12">
                                                    <label class="role-option-card" id="cardRoleStaff" for="roleStaff">
                                                        <input class="form-check-input visually-hidden" type="radio" name="role" id="roleStaff" value="staff" {{ old('role') === 'staff' ? 'checked' : '' }} onchange="onRoleChanged(this.value)">
                                                        <div class="role-icon staff"><i class="bi bi-person-badge-fill"></i></div>
                                                        <div>
                                                            <div class="fw-bold text-dark">Staff Member</div>
                                                            <div class="text-muted small">Teacher or Assistant for daily daycare logs</div>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div class="col-12">
                                                    <label class="role-option-card" id="cardRoleAdmin" for="roleAdmin">
                                                        <input class="form-check-input visually-hidden" type="radio" name="role" id="roleAdmin" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }} onchange="onRoleChanged(this.value)">
                                                        <div class="role-icon admin"><i class="bi bi-shield-shaded"></i></div>
                                                        <div>
                                                            <div class="fw-bold text-dark">Administrator</div>
                                                            <div class="text-muted small">Full access to manage all system features</div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                            @error('role')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Email Address --}}
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. user@daycare.com" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Password --}}
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password <span class="required">*</span></label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="At least 8 characters" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Confirm Password --}}
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password <span class="required">*</span></label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-type password" required>
                                        </div>

                                        {{-- Account Status --}}
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold text-dark" for="is_active">Active Account</label>
                                            <div class="text-muted small">Active users can log into the system immediately</div>
                                        </div>

                                        {{-- Transaction Info Callout --}}
                                        <div class="tx-info-card mt-4">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <i class="bi bi-shield-check text-primary"></i>
                                                <strong class="text-dark small">Atomic Database Transaction</strong>
                                            </div>
                                            <div class="text-muted small" style="line-height: 1.4;">
                                                The user credentials, Spatie role assignment, and profile record are committed together in a single database transaction.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Profile Information (Adaptive based on Role) --}}
                            <div class="col-12 col-lg-7">
                                <div class="card-form h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-vcard text-primary me-2"></i> Profile Information</h5>
                                    </div>
                                    <div class="card-body p-4">

                                        <div class="section-title">
                                            <i class="bi bi-person-fill"></i> Personal Identity
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-12 col-sm-6">
                                                <label for="first_name" class="form-label">First Name <span class="required">*</span></label>
                                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="First name" required>
                                                @error('first_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label for="last_name" class="form-label">Last Name <span class="required">*</span></label>
                                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Last name" required>
                                                @error('last_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label for="image" class="form-label">Profile Image</label>
                                                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text text-muted small">Allowed: jpg, png, gif, svg. Max size: 2MB.</div>
                                            </div>
                                        </div>

                                        {{-- Parent-Specific Profile Section --}}
                                        <div id="parentFieldsSection" class="role-fields-container">
                                            <div class="section-title">
                                                <i class="bi bi-heart-fill text-success"></i> Parent / Guardian Details
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-12 col-sm-6">
                                                    <label for="mobile" class="form-label">Mobile / Phone Number</label>
                                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile') }}" placeholder="e.g. 01711-000001">
                                                    @error('mobile')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="parent_nid" class="form-label">National ID (NID)</label>
                                                    <input type="text" class="form-control @error('nid') is-invalid @enderror" id="parent_nid" name="nid" value="{{ old('nid') }}" placeholder="e.g. 1234567890">
                                                    @error('nid')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="occupation" class="form-label">Occupation</label>
                                                    <input type="text" class="form-control" id="occupation" name="occupation" value="{{ old('occupation') }}" placeholder="e.g. Engineer, Teacher, Doctor">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="city" class="form-label">City</label>
                                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}" placeholder="e.g. Dhaka">
                                                </div>
                                                <div class="col-12">
                                                    <label for="address" class="form-label">Residential Address</label>
                                                    <textarea class="form-control" id="address" name="address" rows="3" placeholder="Full residential street address">{{ old('address') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Staff-Specific Profile Section --}}
                                        <div id="staffFieldsSection" class="role-fields-container" style="display: none;">
                                            <div class="section-title">
                                                <i class="bi bi-person-badge-fill text-primary"></i> Staff Member Details
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-12 col-sm-6">
                                                    <label for="staff_role" class="form-label">Staff Designation / Role</label>
                                                    <select class="form-select @error('staff_role') is-invalid @enderror" id="staff_role" name="staff_role">
                                                        <option value="teacher" {{ old('staff_role', 'teacher') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                                        <option value="assistant" {{ old('staff_role') === 'assistant' ? 'selected' : '' }}>Assistant</option>
                                                        <option value="admin" {{ old('staff_role') === 'admin' ? 'selected' : '' }}>Admin Staff</option>
                                                    </select>
                                                    @error('staff_role')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="staff_nid" class="form-label">National ID (NID)</label>
                                                    <input type="text" class="form-control" id="staff_nid" name="nid" value="{{ old('nid') }}" placeholder="e.g. 9876543210">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="hire_date" class="form-label">Hire Date</label>
                                                    <input type="date" class="form-control" id="hire_date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}">
                                                </div>
                                                <div class="col-12">
                                                    <label for="note" class="form-label">Staff Notes / Bio</label>
                                                    <textarea class="form-control" id="note" name="note" rows="3" placeholder="Additional notes about this staff member">{{ old('note') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Admin-Specific Profile Section --}}
                                        <div id="adminFieldsSection" class="role-fields-container" style="display: none;">
                                            <div class="section-title">
                                                <i class="bi bi-shield-shaded text-pink"></i> Administrator Details
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-12 col-sm-6">
                                                    <label for="admin_nid" class="form-label">National ID (NID)</label>
                                                    <input type="text" class="form-control" id="admin_nid" name="nid" value="{{ old('nid') }}" placeholder="e.g. 9876543210">
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label for="admin_hire_date" class="form-label">Appointment / Hire Date</label>
                                                    <input type="date" class="form-control" id="admin_hire_date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}">
                                                </div>
                                                <div class="col-12">
                                                    <label for="admin_note" class="form-label">Administrative Notes</label>
                                                    <textarea class="form-control" id="admin_note" name="note" rows="3" placeholder="Administrative duties or notes">{{ old('note', 'System Administrator') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="d-flex align-items-center justify-content-end gap-3 mt-5 pt-3 border-top">
                                            <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4 py-2 rounded-3 text-secondary fw-semibold">Cancel</a>
                                            <button type="submit" class="btn btn-submit d-inline-flex align-items-center gap-2">
                                                <i class="bi bi-person-check-fill"></i> Create User & Assign Role
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">Daycare Management System</div>
            <strong>Copyright &copy; {{ date('Y') }}</strong> All rights reserved.
        </footer>
    </div>

    <script>
        function onRoleChanged(role) {
            // Update role card highlights
            document.querySelectorAll('.role-option-card').forEach(card => {
                card.classList.remove('selected-parent', 'selected-staff', 'selected-admin');
            });

            const parentCard = document.getElementById('cardRoleParent');
            const staffCard = document.getElementById('cardRoleStaff');
            const adminCard = document.getElementById('cardRoleAdmin');

            const parentSection = document.getElementById('parentFieldsSection');
            const staffSection = document.getElementById('staffFieldsSection');
            const adminSection = document.getElementById('adminFieldsSection');

            // Hide all profile sections
            parentSection.style.display = 'none';
            staffSection.style.display = 'none';
            adminSection.style.display = 'none';

            // Enable/disable inputs in hidden sections so duplicate name attributes like 'nid' do not collide
            toggleInputs(parentSection, false);
            toggleInputs(staffSection, false);
            toggleInputs(adminSection, false);

            if (role === 'parent') {
                parentCard.classList.add('selected-parent');
                parentSection.style.display = 'block';
                toggleInputs(parentSection, true);
            } else if (role === 'staff') {
                staffCard.classList.add('selected-staff');
                staffSection.style.display = 'block';
                toggleInputs(staffSection, true);
            } else if (role === 'admin') {
                adminCard.classList.add('selected-admin');
                adminSection.style.display = 'block';
                toggleInputs(adminSection, true);
            }
        }

        function toggleInputs(container, enable) {
            container.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = !enable;
            });
        }

        // Initialize state on load
        document.addEventListener('DOMContentLoaded', () => {
            const selectedRadio = document.querySelector('input[name="role"]:checked');
            if (selectedRadio) {
                onRoleChanged(selectedRadio.value);
            } else {
                onRoleChanged('parent');
            }
        });
    </script>
</body>
</html>
