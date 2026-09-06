<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activity->name }} | KinderCare</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-card {
            background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
            border-radius: 16px; color: #fff; padding: 2rem; margin-bottom: 1.5rem;
        }
        .info-pill {
            background: rgba(255, 255, 255, 0.18);
            border-radius: 20px; padding: 6px 14px; display: inline-flex; align-items: center; gap: 6px;
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
                        <li class="nav-header">MANAGEMENT</li>
                        <li class="nav-item">
                            <a href="{{ route('admin.activities.index') }}" class="nav-link active">
                                <i class="nav-icon bi bi-palette-fill"></i>
                                <p>Activity Catalog</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.activity-occurrences.index') }}" class="nav-link">
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
                    {{-- Hero Card --}}
                    <div class="hero-card shadow-sm">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div>
                                <span class="badge bg-light text-primary mb-2 px-3 py-2 fw-semibold">{{ $activity->category_label }}</span>
                                <h2 class="fw-bold mb-2">{{ $activity->name }}</h2>
                                <div class="d-flex flex-wrap gap-2 text-white-50 small mt-2">
                                    <span class="info-pill"><i class="bi bi-clock"></i> {{ $activity->duration_label }}</span>
                                    <span class="info-pill"><i class="bi bi-check2-circle"></i> {{ $activity->is_active ? 'Active' : 'Inactive' }}</span>
                                    <span class="info-pill"><i class="bi bi-calendar-check"></i> {{ $occurrences->total() }} recorded sessions</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.activity-occurrences.create', ['activity_id' => $activity->id]) }}" class="btn btn-light text-primary fw-semibold">
                                    <i class="bi bi-calendar-plus me-1"></i> Schedule Session
                                </a>
                                @role('admin')
                                <a href="{{ route('admin.activities.edit', $activity) }}" class="btn btn-outline-light">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                @endrole
                                <a href="{{ route('admin.activities.index') }}" class="btn btn-outline-light">
                                    <i class="bi bi-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Activity Details --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-0 pt-3">
                                    <h5 class="fw-bold mb-0"><i class="bi bi-card-text text-primary me-2"></i>Educational Goals & Description</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-secondary mb-0" style="line-height: 1.7;">
                                        {{ $activity->description ?: 'No detailed description provided for this activity.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-0 pt-3">
                                    <h5 class="fw-bold mb-0"><i class="bi bi-box-seam text-warning me-2"></i>Materials & Supplies Needed</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-secondary mb-0" style="line-height: 1.7;">
                                        {{ $activity->materials_needed ?: 'No specific materials recorded for this activity.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Schedule / Occurrence History --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-info me-2"></i>Session History & Scheduled Occurrences</h5>
                            <span class="badge bg-light text-secondary border">{{ $occurrences->total() }} total</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Program</th>
                                        <th>Conducted By</th>
                                        <th>Status</th>
                                        <th>Children Logged</th>
                                        <th>Observations</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($occurrences as $occ)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold">{{ $occ->occurrence_date ? $occ->occurrence_date->format('M d, Y') : '—' }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $occ->time_range }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $occ->program ? $occ->program->name : 'All Programs' }}</span>
                                            </td>
                                            <td>
                                                <i class="bi bi-person text-muted me-1"></i> {{ $occ->staff ? $occ->staff->user->name : 'Staff' }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $occ->status_badge_class }}">{{ $occ->status_label }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">{{ $occ->child_daily_logs_count }} children</span>
                                            </td>
                                            <td>
                                                <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">
                                                    {{ $occ->observations ?: '—' }}
                                                </small>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.activity-occurrences.show', $occ) }}" class="btn btn-sm btn-outline-primary">
                                                    View Session <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                No sessions have been scheduled yet for this activity.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($occurrences->hasPages())
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-end">
                                {{ $occurrences->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
