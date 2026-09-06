<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Activity: {{ $activity->name }} | KinderCare</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-card {
            border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);
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
            </div>
        </nav>

        {{-- Sidebar --}}
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="/" class="brand-link">
                    <span class="brand-text font-weight-light">KinderCare</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-header">MANAGEMENT</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.activities.index') }}" class="nav-link active">
                                <i class="nav-icon bi bi-palette-fill"></i>
                                <p>Activity Catalog</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header py-4">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h3 class="mb-0 fw-bold">Edit Activity</h3>
                                    <p class="text-muted small">Update curriculum activity specifications</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.activities.show', $activity) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye me-1"></i> View Details
                                    </a>
                                    <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i> Back to Catalog
                                    </a>
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mb-4">
                                    <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-octagon me-1"></i> Please check form errors</h6>
                                    <ul class="mb-0 small">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <div class="card form-card">
                                <div class="card-body p-4">
                                    <form action="{{ route('admin.activities.update', $activity) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label for="name" class="form-label fw-semibold">Activity Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $activity->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label for="category" class="form-label fw-semibold">Learning Domain / Category <span class="text-danger">*</span></label>
                                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                                    @foreach ($categories as $key => $label)
                                                        <option value="{{ $key }}" {{ old('category', $activity->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                @error('category')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="duration_minutes" class="form-label fw-semibold">Standard Duration (Minutes)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $activity->duration_minutes) }}" min="5" max="480">
                                                    <span class="input-group-text">mins</span>
                                                </div>
                                                @error('duration_minutes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="materials_needed" class="form-label fw-semibold">Materials & Supplies Needed</label>
                                                <input type="text" class="form-control @error('materials_needed') is-invalid @enderror" id="materials_needed" name="materials_needed" value="{{ old('materials_needed', $activity->materials_needed) }}">
                                                @error('materials_needed')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label for="description" class="form-label fw-semibold">Educational Goals & Description</label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $activity->description) }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $activity->is_active) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold" for="is_active">Active in curriculum</label>
                                                </div>
                                            </div>

                                            <div class="col-12 pt-3 border-top d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.activities.index') }}" class="btn btn-light px-4">Cancel</a>
                                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Update Activity</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
