<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Edit Staff Member — update profile details, role, department, and account information.">
    <title>Edit Staff Member | KinderCare</title>

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

        .form-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #374151;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f3f4f6;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .required-star { color: #ef4444; }

        .current-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
        }
        .current-avatar-placeholder {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.35rem;
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
                        <span class="nav-link text-muted" style="font-size:0.9rem;">Edit Staff Member</span>
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
        @include('partials.sidebar')

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="page-banner d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="bi bi-pencil-square me-2"></i>Edit Staff Member: {{ $staff->full_name }}</h2>
                            <p>Update role, department assignment, contact information, and security credentials.</p>
                        </div>
                        <div class="d-flex gap-2" style="position:relative;z-index:2;">
                            <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-light fw-bold shadow-sm">
                                <i class="bi bi-eye me-1"></i> View Profile
                            </a>
                            <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-light fw-semibold">
                                <i class="bi bi-arrow-left me-1"></i> Back to Directory
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please resolve the following errors:</h6>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.staff.update', $staff) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Left Column: Personal & Employment Details --}}
                            <div class="col-lg-8">

                                {{-- Personal Information --}}
                                <div class="card card-custom mb-4">
                                    <div class="card-body p-4">
                                        <div class="form-section-title">
                                            <i class="bi bi-person-circle text-primary"></i> Personal Information
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">First Name <span class="required-star">*</span></label>
                                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $staff->first_name) }}" required>
                                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Last Name <span class="required-star">*</span></label>
                                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $staff->last_name) }}" required>
                                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">National ID (NID)</label>
                                                <input type="text" name="nid" class="form-control @error('nid') is-invalid @enderror" value="{{ old('nid', $staff->nid) }}">
                                                @error('nid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Date of Birth</label>
                                                <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $staff->date_of_birth?->format('Y-m-d')) }}">
                                                @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Employment & Assignments --}}
                                <div class="card card-custom mb-4">
                                    <div class="card-body p-4">
                                        <div class="form-section-title">
                                            <i class="bi bi-briefcase-fill text-primary"></i> Role & Department Assignment
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Department <span class="required-star">*</span></label>
                                                <select name="department" id="department_select" class="form-select @error('department') is-invalid @enderror" required>
                                                    <option value="daycare" {{ old('department', $staff->department) === 'daycare' ? 'selected' : '' }}>Daycare</option>
                                                    <option value="therapy" {{ old('department', $staff->department) === 'therapy' ? 'selected' : '' }}>Therapy</option>
                                                </select>
                                                @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                <div class="form-text">Therapy staff specialize in child developmental therapy.</div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Staff Role <span class="required-star">*</span></label>
                                                <select name="role" id="role_select" class="form-select @error('role') is-invalid @enderror" required>
                                                    <option value="teacher" {{ old('role', $staff->role) === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                                    <option value="assistant" {{ old('role', $staff->role) === 'assistant' ? 'selected' : '' }}>Assistant</option>
                                                    <option value="therapist" {{ old('role', $staff->role) === 'therapist' ? 'selected' : '' }}>Therapist</option>
                                                    <option value="admin" {{ old('role', $staff->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                                </select>
                                                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6" id="specialization_container" style="display: {{ old('department', $staff->department) === 'therapy' || old('role', $staff->role) === 'therapist' ? 'block' : 'none' }};">
                                                <label class="form-label fw-semibold">Therapy Specialization</label>
                                                <select name="specialization" id="specialization_select" class="form-select @error('specialization') is-invalid @enderror">
                                                    <option value="">-- Select Specialization --</option>
                                                    <option value="slt" {{ old('specialization', $staff->specialization) === 'slt' ? 'selected' : '' }}>Speech & Language Therapy (SLT)</option>
                                                    <option value="aba" {{ old('specialization', $staff->specialization) === 'aba' ? 'selected' : '' }}>Applied Behavior Analysis (ABA)</option>
                                                    <option value="ot" {{ old('specialization', $staff->specialization) === 'ot' ? 'selected' : '' }}>Occupational Therapy (OT)</option>
                                                </select>
                                                @error('specialization') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Hire Date</label>
                                                <input type="date" name="hire_date" class="form-control @error('hire_date') is-invalid @enderror" value="{{ old('hire_date', $staff->hire_date?->format('Y-m-d')) }}">
                                                @error('hire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Notes & Qualifications</label>
                                                <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror">{{ old('note', $staff->note) }}</textarea>
                                                @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- Right Column: User Credentials, Avatar, & Status --}}
                            <div class="col-lg-4">

                                {{-- User Account Credentials --}}
                                <div class="card card-custom mb-4">
                                    <div class="card-body p-4">
                                        <div class="form-section-title">
                                            <i class="bi bi-shield-lock-fill text-primary"></i> Account Credentials
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Email Address <span class="required-star">*</span></label>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $staff->user?->email) }}" required>
                                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">New Password</label>
                                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current">
                                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <div class="form-text">Fill only if you want to reset password.</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Confirm New Password</label>
                                            <input type="password" name="password_confirmation" class="form-control" placeholder="Re-type new password">
                                        </div>
                                    </div>
                                </div>

                                {{-- Profile Image & Status --}}
                                <div class="card card-custom mb-4">
                                    <div class="card-body p-4">
                                        <div class="form-section-title">
                                            <i class="bi bi-image text-primary"></i> Photo & Status
                                        </div>

                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            @if ($staff->photo_url)
                                                <img src="{{ $staff->photo_url }}" class="current-avatar" alt="{{ $staff->full_name }}">
                                            @else
                                                <div class="current-avatar-placeholder">
                                                    {{ $staff->initials }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold small text-dark">Current Photo</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">Upload new file below to replace.</div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Replace Photo</label>
                                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <div class="form-text">Max 2MB. JPG, PNG, or WebP.</div>
                                        </div>

                                        <div class="form-check form-switch mt-3">
                                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $staff->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="is_active">
                                                Active Staff Member
                                            </label>
                                            <div class="form-text">Inactive staff cannot log in or be assigned to activities.</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                                        <i class="bi bi-check2-circle me-1"></i> Update Staff Member
                                    </button>
                                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="app-footer text-muted py-3 px-4 border-top small bg-body">
            <div class="float-end d-none d-sm-inline">KinderCare Management System</div>
            <strong>Copyright &copy; {{ date('Y') }} KinderCare.</strong> All rights reserved.
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deptSelect = document.getElementById('department_select');
            const roleSelect = document.getElementById('role_select');
            const specContainer = document.getElementById('specialization_container');

            function checkSpecializationVisibility() {
                if (deptSelect.value === 'therapy' || roleSelect.value === 'therapist') {
                    specContainer.style.display = 'block';
                } else {
                    specContainer.style.display = 'none';
                }
            }

            deptSelect.addEventListener('change', function() {
                if (this.value === 'therapy' && roleSelect.value !== 'therapist') {
                    roleSelect.value = 'therapist';
                } else if (this.value === 'daycare' && roleSelect.value === 'therapist') {
                    roleSelect.value = 'teacher';
                }
                checkSpecializationVisibility();
            });

            roleSelect.addEventListener('change', function() {
                if (this.value === 'therapist') {
                    deptSelect.value = 'therapy';
                }
                checkSpecializationVisibility();
            });

            checkSpecializationVisibility();
        });
    </script>
</body>
</html>
