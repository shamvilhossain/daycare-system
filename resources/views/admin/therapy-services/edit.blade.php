<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Therapy Service | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a></li></ul>
            </div>
        </nav>
        @include('partials.sidebar')

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6"><h3 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Therapy Service</h3></div>
                        <div class="col-sm-6 text-end">
                            <a href="{{ route('admin.therapy-services.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Catalog</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong><i class="bi bi-exclamation-octagon-fill me-2"></i>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.therapy-services.update', $therapyService) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-lg-8">
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0"><i class="bi bi-clipboard2-pulse-fill me-2 text-primary"></i>Service Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label fw-semibold">Service Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $therapyService->name) }}" required>
                                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Therapy Type <span class="text-danger">*</span></label>
                                                <select name="therapy_type" class="form-select @error('therapy_type') is-invalid @enderror" required>
                                                    <option value="slt" {{ old('therapy_type', $therapyService->therapy_type) == 'slt' ? 'selected' : '' }}>SLT (Speech & Language)</option>
                                                    <option value="aba" {{ old('therapy_type', $therapyService->therapy_type) == 'aba' ? 'selected' : '' }}>ABA (Applied Behavior Analysis)</option>
                                                    <option value="ot" {{ old('therapy_type', $therapyService->therapy_type) == 'ot' ? 'selected' : '' }}>OT (Occupational Therapy)</option>
                                                </select>
                                                @error('therapy_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Description</label>
                                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $therapyService->description) }}</textarea>
                                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Duration (minutes) <span class="text-danger">*</span></label>
                                                <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes', $therapyService->duration_minutes) }}" min="15" max="480" required>
                                                @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Session Rate (৳) <span class="text-danger">*</span></label>
                                                <input type="number" name="session_rate" step="0.01" class="form-control @error('session_rate') is-invalid @enderror" value="{{ old('session_rate', $therapyService->session_rate) }}" min="0" required>
                                                @error('session_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Status</label>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $therapyService->is_active) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="isActive">Active</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mb-4">
                                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle-fill me-1"></i> Update Service</button>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-info me-2"></i>Service Stats</h6></div>
                                    <div class="card-body small text-muted">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2 d-flex justify-content-between"><span>Total Sessions:</span> <strong class="text-dark">{{ $therapyService->sessions()->count() }}</strong></li>
                                            <li class="mb-2 d-flex justify-content-between"><span>Completed:</span> <strong class="text-success">{{ $therapyService->sessions()->where('status', 'completed')->count() }}</strong></li>
                                            <li class="d-flex justify-content-between"><span>Created:</span> <strong class="text-dark">{{ $therapyService->created_at->format('M d, Y') }}</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
