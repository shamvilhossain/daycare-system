<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Program | KinderCare</title>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #7c3aed 100%);
            border-radius: 12px; padding: 1rem 1.25rem; color: #fff; margin-bottom: 1rem;
            position: relative; overflow: hidden;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.15);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -35px; right: -25px;
            width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.2rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.85rem; opacity: 0.9; position: relative; z-index: 1; margin: 0; }
        .form-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #334155;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
        @include('partials.sidebar')

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header pt-3 pb-0">
                <div class="container-fluid">
                    <div class="page-banner d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h2><i class="bi bi-plus-circle-fill me-2"></i>Add New Program</h2>
                            <p>Configure childcare schedule, service type, age parameters, and rates</p>
                        </div>
                        <a href="{{ route('admin.programs.index') }}" class="btn btn-light btn-sm fw-semibold shadow-sm px-3" style="position:relative;z-index:1;">
                            <i class="bi bi-arrow-left me-1"></i> Back to Programs
                        </a>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <div class="d-flex align-items-center mb-1">
                                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                                <strong>Please fix the following errors:</strong>
                            </div>
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card shadow-sm border-0 mb-4">
                        <form action="{{ route('admin.programs.store') }}" method="POST">
                            @csrf
                            <div class="card-body p-4">
                                {{-- Section 1: General Details --}}
                                <div class="form-section-title">
                                    <i class="bi bi-info-circle text-primary"></i> Program Overview
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Program Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Toddler Morning Discovery" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Service Type <span class="text-danger">*</span></label>
                                        <select name="service_type" class="form-select @error('service_type') is-invalid @enderror" required>
                                            <option value="">Select...</option>
                                            <option value="full_day" {{ old('service_type') == 'full_day' ? 'selected' : '' }}>Full Day</option>
                                            <option value="half_day" {{ old('service_type') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                            <option value="after_school" {{ old('service_type') == 'after_school' ? 'selected' : '' }}>After School</option>
                                            <option value="drop_in" {{ old('service_type') == 'drop_in' ? 'selected' : '' }}>Drop-In</option>
                                        </select>
                                        @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Billing Model <span class="text-danger">*</span></label>
                                        <select name="billing_model" class="form-select @error('billing_model') is-invalid @enderror" required>
                                            <option value="">Select...</option>
                                            <option value="monthly" {{ old('billing_model', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="daily" {{ old('billing_model') == 'daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="hourly" {{ old('billing_model') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                        </select>
                                        @error('billing_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- Section 2: Capacity & Age Range --}}
                                <div class="form-section-title">
                                    <i class="bi bi-people text-primary"></i> Capacity & Age Bracket
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Capacity (Children) <span class="text-danger">*</span></label>
                                        <input type="number" name="capacity" min="1" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', 15) }}" required>
                                        <div class="form-text">Maximum enrolled slots allowed.</div>
                                        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Min Age (Months)</label>
                                        <input type="number" name="min_age_months" min="0" class="form-control @error('min_age_months') is-invalid @enderror" value="{{ old('min_age_months') }}" placeholder="e.g. 12 (1 year)">
                                        <div class="form-text">Leave blank if no minimum age limit.</div>
                                        @error('min_age_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Max Age (Months)</label>
                                        <input type="number" name="max_age_months" min="0" class="form-control @error('max_age_months') is-invalid @enderror" value="{{ old('max_age_months') }}" placeholder="e.g. 36 (3 years)">
                                        <div class="form-text">Leave blank if no maximum age limit.</div>
                                        @error('max_age_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- Section 3: Daily Timing & Schedule --}}
                                <div class="form-section-title">
                                    <i class="bi bi-clock text-primary"></i> Operating Hours
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Daily Start Time</label>
                                        <input type="time" name="day_start_time" class="form-control @error('day_start_time') is-invalid @enderror" value="{{ old('day_start_time') }}">
                                        @error('day_start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Daily End Time</label>
                                        <input type="time" name="day_end_time" class="form-control @error('day_end_time') is-invalid @enderror" value="{{ old('day_end_time') }}">
                                        @error('day_end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- Section 4: Fees & Rates --}}
                                <div class="form-section-title">
                                    <i class="bi bi-currency-dollar text-primary"></i> Pricing & Rates
                                </div>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Monthly Fee ($)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0" name="monthly_fee" class="form-control @error('monthly_fee') is-invalid @enderror" value="{{ old('monthly_fee') }}" placeholder="0.00">
                                        </div>
                                        @error('monthly_fee')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Daily Rate ($)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0" name="daily_rate" class="form-control @error('daily_rate') is-invalid @enderror" value="{{ old('daily_rate') }}" placeholder="0.00">
                                        </div>
                                        @error('daily_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Hourly Rate ($)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0" name="hourly_rate" class="form-control @error('hourly_rate') is-invalid @enderror" value="{{ old('hourly_rate') }}" placeholder="0.00">
                                        </div>
                                        @error('hourly_rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                {{-- Section 5: Program Status --}}
                                <div class="form-section-title">
                                    <i class="bi bi-toggle-on text-primary"></i> Status
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="isActive" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="isActive">Active Program</label>
                                            <div class="form-text">Active programs are available for new child enrollments and attendance logs.</div>
                                        </div>
                                        @error('is_active')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light border-0 py-3 d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.programs.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                    <i class="bi bi-check-lg me-1"></i> Create Program
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
