<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activityOccurrence->activity ? $activityOccurrence->activity->name : 'Activity Session' }} | KinderCare</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-banner {
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #14b8a6 100%);
            border-radius: 16px; padding: 2rem; color: #fff; margin-bottom: 1.5rem;
        }
        .info-pill {
            background: rgba(255, 255, 255, 0.18);
            border-radius: 20px; padding: 5px 14px; font-size: 0.85rem;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .media-card {
            border: none; border-radius: 12px; overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            transition: transform 0.2s;
        }
        .media-card:hover { transform: translateY(-3px); }
        .media-preview {
            width: 100%; height: 180px; object-fit: cover; background: #000;
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
                        <li class="nav-item">
                            <a href="{{ route('admin.child-daily-logs.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-journal-text"></i>
                                <p>Daily Child Logs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.activities.index') }}" class="nav-link">
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
                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Hero Banner --}}
                    <div class="hero-banner shadow-sm">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-light text-dark px-3 py-1">{{ $activityOccurrence->program ? $activityOccurrence->program->name : 'General' }}</span>
                                    <span class="badge bg-white text-teal px-3 py-1 fw-bold">{{ $activityOccurrence->status_label }}</span>
                                </div>
                                <h2 class="fw-bold mb-2">{{ $activityOccurrence->activity ? $activityOccurrence->activity->name : 'Activity Session' }}</h2>
                                <div class="d-flex flex-wrap gap-2 text-white-50 mt-3">
                                    <span class="info-pill"><i class="bi bi-calendar3"></i> {{ $activityOccurrence->occurrence_date ? $activityOccurrence->occurrence_date->format('l, F j, Y') : '—' }}</span>
                                    <span class="info-pill"><i class="bi bi-clock"></i> {{ $activityOccurrence->time_range }}</span>
                                    <span class="info-pill"><i class="bi bi-person-badge"></i> Lead: {{ $activityOccurrence->staff && $activityOccurrence->staff->user ? $activityOccurrence->staff->user->name : "Staff #{$activityOccurrence->staff_id}" }}</span>
                                    <span class="info-pill"><i class="bi bi-camera"></i> {{ $activityOccurrence->media->count() }} media</span>
                                    <span class="info-pill"><i class="bi bi-people"></i> {{ $activityOccurrence->childDailyLogs->count() }} children recorded</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-light text-dark fw-semibold" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Add Media
                                </button>
                                <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                    <i class="bi bi-check2-square me-1"></i> Update Status
                                </button>
                                <a href="{{ route('admin.activity-occurrences.edit', $activityOccurrence) }}" class="btn btn-outline-light">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <a href="{{ route('admin.activity-occurrences.index', ['date' => $activityOccurrence->occurrence_date ? $activityOccurrence->occurrence_date->toDateString() : 'all']) }}" class="btn btn-outline-light">
                                    <i class="bi bi-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Session Details row --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-0 pt-3">
                                    <h5 class="fw-bold mb-0"><i class="bi bi-chat-left-quote text-success me-2"></i>Staff Observations & Group Progress</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-secondary mb-0" style="line-height: 1.8;">
                                        {{ $activityOccurrence->observations ?: 'No observations recorded yet. Click "Update Status" to log progress notes.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-0 pt-3">
                                    <h5 class="fw-bold mb-0"><i class="bi bi-box-seam text-warning me-2"></i>Materials Used</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="text-muted small text-uppercase fw-semibold mb-1">Session Materials:</div>
                                        <div class="fw-semibold text-dark">{{ $activityOccurrence->materials_used ?: 'Used standard materials from catalog' }}</div>
                                    </div>
                                    @if($activityOccurrence->activity && $activityOccurrence->activity->materials_needed)
                                        <div>
                                            <div class="text-muted small text-uppercase fw-semibold mb-1">Catalog Suggested:</div>
                                            <div class="small text-muted">{{ $activityOccurrence->activity->materials_needed }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Activity Media Gallery --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0"><i class="bi bi-images text-primary me-2"></i>Session Photos & Videos</h5>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
                                <i class="bi bi-plus-lg me-1"></i> Upload Photo / Video
                            </button>
                        </div>
                        <div class="card-body">
                            @if($activityOccurrence->media->count() > 0)
                                <div class="row g-3">
                                    @foreach($activityOccurrence->media as $mediaItem)
                                        <div class="col-sm-6 col-md-4 col-lg-3">
                                            <div class="card media-card h-100">
                                                @if($mediaItem->media_type === 'video')
                                                    <video src="{{ asset($mediaItem->file_url) }}" controls class="media-preview"></video>
                                                @else
                                                    <img src="{{ asset($mediaItem->file_url) }}" alt="{{ $mediaItem->caption ?? 'Activity photo' }}" class="media-preview">
                                                @endif
                                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                                    <p class="small mb-2 text-dark">{{ $mediaItem->caption ?: 'Activity capture' }}</p>
                                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                        <span class="badge bg-light text-secondary text-uppercase">{{ $mediaItem->media_type }}</span>
                                                        <form action="{{ route('admin.activity-occurrences.destroy-media', $mediaItem) }}" method="POST" onsubmit="return confirm('Remove this media item?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm text-danger p-0 border-0" title="Delete media">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-camera fs-1 d-block mb-2 text-secondary"></i>
                                    No photos or videos attached to this session yet.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Participating Children Logs --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0"><i class="bi bi-people-fill text-info me-2"></i>Participating Children (Daily Logs)</h5>
                            <a href="{{ route('admin.child-daily-logs.index', ['date' => $activityOccurrence->occurrence_date ? $activityOccurrence->occurrence_date->toDateString() : now()->toDateString()]) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-journal-text me-1"></i> Open Daily Child Logs
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Child Name</th>
                                        <th>Participation Status</th>
                                        <th>Child Specific Notes</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activityOccurrence->childDailyLogs as $log)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $log->child ? $log->child->full_name : "Child #{$log->child_id}" }}</div>
                                                <small class="text-muted">{{ $log->child && $log->child->dob ? $log->child->dob->age . ' yrs' : '' }}</small>
                                            </td>
                                            <td>
                                                @if($log->is_completed)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-lg"></i> Completed</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Partial / In progress</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="small text-secondary">{{ $log->notes ?: 'Participated in group activity' }}</span>
                                            </td>
                                            <td class="text-end">
                                                @if($log->child)
                                                    <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $log->child->id, 'date' => $activityOccurrence->occurrence_date ? $activityOccurrence->occurrence_date->toDateString() : now()->toDateString()]) }}" class="btn btn-sm btn-outline-primary">
                                                        Child Timeline <i class="bi bi-arrow-right"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                No child daily logs have been tagged to this session occurrence yet.
                                                <div class="small mt-1">When staff record an 'activity' entry in the Child Daily Log desk, selecting this activity session will automatically link them here.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Update Status Modal --}}
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.activity-occurrences.update-status', $activityOccurrence) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Update Session Status & Observations</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="planned" {{ $activityOccurrence->status === 'planned' ? 'selected' : '' }}>Planned (Scheduled)</option>
                                <option value="completed" {{ $activityOccurrence->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="partial" {{ $activityOccurrence->status === 'partial' ? 'selected' : '' }}>Partial (Cut short)</option>
                                <option value="cancelled" {{ $activityOccurrence->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Materials Used</label>
                            <input type="text" name="materials_used" class="form-control" value="{{ $activityOccurrence->materials_used }}" placeholder="e.g., Red & green paint, paper plates">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Staff Observations</label>
                            <textarea name="observations" class="form-control" rows="4" placeholder="How did the group activity go? Note any highlights or behaviors...">{{ $activityOccurrence->observations }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Updates</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Upload Media Modal --}}
    <div class="modal fade" id="uploadMediaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.activity-occurrences.upload-media', $activityOccurrence) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Upload Session Media</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Photo or Video <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" accept="image/*,video/*" required>
                            <div class="form-text">Supported: JPG, PNG, WEBP, MP4, MOV (max 20MB)</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Caption / Note</label>
                            <input type="text" name="caption" class="form-control" placeholder="e.g., Building towers, group sing along">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
