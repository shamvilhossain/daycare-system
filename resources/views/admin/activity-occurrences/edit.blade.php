<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Session | KinderCare</title>
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
            <div class="sidebar-brand"><a href="/" class="brand-link"><span class="brand-text font-weight-light">KinderCare</span></a></div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-header">DAILY OPERATIONS</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.activity-occurrences.index') }}" class="nav-link active">
                                <i class="nav-icon bi bi-calendar-check"></i>
                                <p>Daily Schedule</p>
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
                        <div class="col-lg-9">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h3 class="mb-0 fw-bold">Edit Activity Session</h3>
                                    <p class="text-muted small">Update schedule parameters and staff observations</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.activity-occurrences.show', $activityOccurrence) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye me-1"></i> View Session
                                    </a>
                                    <a href="{{ route('admin.activity-occurrences.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i> Back
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
                                    <form action="{{ route('admin.activity-occurrences.update', $activityOccurrence) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="row g-3">
                                            {{-- Activity selection --}}
                                            <div class="col-md-6">
                                                <label for="activity_id" class="form-label fw-semibold">Activity <span class="text-danger">*</span></label>
                                                <select class="form-select @error('activity_id') is-invalid @enderror" id="activity_id" name="activity_id" required>
                                                    @foreach($activities as $act)
                                                        <option value="{{ $act->id }}" {{ (string)old('activity_id', $activityOccurrence->activity_id) === (string)$act->id ? 'selected' : '' }}>
                                                            {{ $act->name }} ({{ $act->category_label }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('activity_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Program selection --}}
                                            <div class="col-md-6">
                                                <label for="program_id" class="form-label fw-semibold">Classroom / Program <span class="text-danger">*</span></label>
                                                <select class="form-select @error('program_id') is-invalid @enderror" id="program_id" name="program_id" required>
                                                    @foreach($programs as $prog)
                                                        <option value="{{ $prog->id }}" {{ (string)old('program_id', $activityOccurrence->program_id) === (string)$prog->id ? 'selected' : '' }}>
                                                            {{ $prog->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('program_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Staff lead --}}
                                            <div class="col-md-6">
                                                <label for="staff_id" class="form-label fw-semibold">Lead Staff Member <span class="text-danger">*</span></label>
                                                <select class="form-select @error('staff_id') is-invalid @enderror" id="staff_id" name="staff_id" required>
                                                    @foreach($staffMembers as $staff)
                                                        <option value="{{ $staff->id }}" {{ (string)old('staff_id', $activityOccurrence->staff_id) === (string)$staff->id ? 'selected' : '' }}>
                                                            {{ $staff->user ? $staff->user->name : "Staff #{$staff->id}" }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('staff_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Occurrence Date --}}
                                            <div class="col-md-6">
                                                <label for="occurrence_date" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control @error('occurrence_date') is-invalid @enderror" id="occurrence_date" name="occurrence_date" value="{{ old('occurrence_date', $activityOccurrence->occurrence_date ? $activityOccurrence->occurrence_date->format('Y-m-d') : '') }}" required>
                                                @error('occurrence_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Time Range --}}
                                            <div class="col-md-4">
                                                <label for="start_time" class="form-label fw-semibold">Start Time</label>
                                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $activityOccurrence->start_time ? substr($activityOccurrence->start_time, 0, 5) : '') }}">
                                                @error('start_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label for="end_time" class="form-label fw-semibold">End Time</label>
                                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $activityOccurrence->end_time ? substr($activityOccurrence->end_time, 0, 5) : '') }}">
                                                @error('end_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                    <option value="planned" {{ old('status', $activityOccurrence->status) === 'planned' ? 'selected' : '' }}>Planned</option>
                                                    <option value="completed" {{ old('status', $activityOccurrence->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="partial" {{ old('status', $activityOccurrence->status) === 'partial' ? 'selected' : '' }}>Partial</option>
                                                    <option value="cancelled" {{ old('status', $activityOccurrence->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Materials Used --}}
                                            <div class="col-12">
                                                <label for="materials_used" class="form-label fw-semibold">Materials Used</label>
                                                <input type="text" class="form-control @error('materials_used') is-invalid @enderror" id="materials_used" name="materials_used" value="{{ old('materials_used', $activityOccurrence->materials_used) }}">
                                                @error('materials_used')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Observations --}}
                                            <div class="col-12">
                                                <label for="observations" class="form-label fw-semibold">Staff Observations / Group Progress</label>
                                                <textarea class="form-control @error('observations') is-invalid @enderror" id="observations" name="observations" rows="4">{{ old('observations', $activityOccurrence->observations) }}</textarea>
                                                @error('observations')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 pt-3 border-top d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.activity-occurrences.show', $activityOccurrence) }}" class="btn btn-light px-4">Cancel</a>
                                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> Update Session</button>
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
