<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Program | Daycare System</title>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a></li></ul>
            </div>
        </nav>

        {{-- Sidebar --}}
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand"><a href="/" class="brand-link"><span class="brand-text font-weight-light">DaycareSystem</span></a></div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-header">ADMIN</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.programs.index') }}" class="nav-link active">
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
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">Edit Program</h3></div>
                        <div class="col-sm-6 text-end"><a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">Back to Programs</a></div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="card shadow-sm border-0">
                        <form action="{{ route('admin.programs.update', $program) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Program Name</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $program->name) }}" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Service Type</label>
                                        <select name="service_type" class="form-select @error('service_type') is-invalid @enderror" required>
                                            <option value="">Select...</option>
                                            <option value="full_day" {{ old('service_type', $program->service_type) == 'full_day' ? 'selected' : '' }}>Full Day</option>
                                            <option value="half_day" {{ old('service_type', $program->service_type) == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                            <option value="after_school" {{ old('service_type', $program->service_type) == 'after_school' ? 'selected' : '' }}>After School</option>
                                            <option value="drop_in" {{ old('service_type', $program->service_type) == 'drop_in' ? 'selected' : '' }}>Drop-In</option>
                                        </select>
                                        @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Billing Model</label>
                                        <select name="billing_model" class="form-select @error('billing_model') is-invalid @enderror" required>
                                            <option value="">Select...</option>
                                            <option value="monthly" {{ old('billing_model', $program->billing_model) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="daily" {{ old('billing_model', $program->billing_model) == 'daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="hourly" {{ old('billing_model', $program->billing_model) == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                        </select>
                                        @error('billing_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Min Age (Months)</label>
                                        <input type="number" name="min_age_months" class="form-control @error('min_age_months') is-invalid @enderror" value="{{ old('min_age_months', $program->min_age_months) }}">
                                        @error('min_age_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Max Age (Months)</label>
                                        <input type="number" name="max_age_months" class="form-control @error('max_age_months') is-invalid @enderror" value="{{ old('max_age_months', $program->max_age_months) }}">
                                        @error('max_age_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Capacity</label>
                                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $program->capacity) }}" required>
                                        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Monthly Fee ($)</label>
                                        <input type="number" step="0.01" name="monthly_fee" class="form-control @error('monthly_fee') is-invalid @enderror" value="{{ old('monthly_fee', $program->monthly_fee) }}">
                                        @error('monthly_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Daily Rate ($)</label>
                                        <input type="number" step="0.01" name="daily_rate" class="form-control @error('daily_rate') is-invalid @enderror" value="{{ old('daily_rate', $program->daily_rate) }}">
                                        @error('daily_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Hourly Rate ($)</label>
                                        <input type="number" step="0.01" name="hourly_rate" class="form-control @error('hourly_rate') is-invalid @enderror" value="{{ old('hourly_rate', $program->hourly_rate) }}">
                                        @error('hourly_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Start Time</label>
                                        <input type="time" name="day_start_time" class="form-control @error('day_start_time') is-invalid @enderror" value="{{ old('day_start_time', $program->day_start_time ? \Carbon\Carbon::parse($program->day_start_time)->format('H:i') : '') }}">
                                        @error('day_start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">End Time</label>
                                        <input type="time" name="day_end_time" class="form-control @error('day_end_time') is-invalid @enderror" value="{{ old('day_end_time', $program->day_end_time ? \Carbon\Carbon::parse($program->day_end_time)->format('H:i') : '') }}">
                                        @error('day_end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4 d-flex align-items-center mt-5">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="isActive">Active Program</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">Update Program</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
