<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment #{{ $enrollment->id }} | Daycare System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .detail-avatar {
            width: 72px; height: 72px; border-radius: 50%; object-fit: cover;
            border: 3px solid #e5e7eb;
        }
        .detail-avatar-placeholder {
            width: 72px; height: 72px; border-radius: 50%;
            background: #e0e7ff; color: #4f46e5; display: flex;
            align-items: center; justify-content: center; font-weight: 700; font-size: 1.5rem;
        }
        .info-group {
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 0.65rem;
            margin-bottom: 0.65rem;
        }
        .info-group:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }
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
                        <li class="nav-header">MAIN</li>
                        <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link"><i class="nav-icon bi bi-grid-1x2-fill"></i><p>Dashboard</p></a></li>
                        <li class="nav-header">MANAGEMENT</li>
                        <li class="nav-item"><a href="{{ route('admin.children.index') }}" class="nav-link"><i class="nav-icon bi bi-people-fill"></i><p>Children</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-person-badge-fill"></i><p>Staff</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-calendar-event-fill"></i><p>Activities</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.enrollments.index') }}" class="nav-link active"><i class="nav-icon bi bi-clipboard-check-fill"></i><p>Enrollments</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-credit-card-2-front-fill"></i><p>Payments</p></a></li>
                        <li class="nav-header">ADMIN</li>
                        <li class="nav-item"><a href="{{ route('admin.programs.index') }}" class="nav-link"><i class="nav-icon bi bi-book-half"></i><p>Programs</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.users.index') }}" class="nav-link"><i class="nav-icon bi bi-person-lines-fill"></i><p>Users & Accounts</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.role-permissions.index') }}" class="nav-link"><i class="nav-icon bi bi-shield-lock-fill"></i><p>Role Permissions</p></a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0">
                                <i class="bi bi-file-earmark-person-fill me-2 text-primary"></i>Enrollment #{{ $enrollment->id }}
                            </h3>
                        </div>
                        <div class="col-sm-6 text-end">
                            <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="btn btn-primary me-2">
                                <i class="bi bi-pencil me-1"></i> Edit Enrollment
                            </a>
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{-- Status Banner & Quick Workflow Actions --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-5 fw-bold text-muted">Status:</span>
                                <span class="badge fs-6 px-3 py-2 {{ $enrollment->status_badge_class }}">
                                    {{ $enrollment->status_label }}
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex gap-2">
                                @if($enrollment->status === 'pending')
                                    <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Approve Application</button>
                                    </form>
                                    <button type="button" class="btn btn-danger" onclick="openRejectModal('{{ route('admin.enrollments.reject', $enrollment) }}', '{{ addslashes($enrollment->child->full_name ?? '') }}')">
                                        <i class="bi bi-x-lg me-1"></i>Reject
                                    </button>
                                @elseif($enrollment->status === 'active')
                                    <form action="{{ route('admin.enrollments.graduate', $enrollment) }}" method="POST" onsubmit="return confirm('Mark this child as graduated from this program?');">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-info text-white"><i class="bi bi-mortarboard me-1"></i>Graduate Child</button>
                                    </form>
                                    <form action="{{ route('admin.enrollments.withdraw', $enrollment) }}" method="POST" onsubmit="return confirm('Mark this child enrollment as withdrawn?');">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-right me-1"></i>Withdraw</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Child Information --}}
                        <div class="col-lg-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="bi bi-person-fill text-primary me-2"></i>Enrolled Child</h5>
                                    @if($enrollment->child)
                                        <a href="{{ route('admin.children.edit', $enrollment->child) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil me-1"></i>Edit Child
                                        </a>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if($enrollment->child)
                                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                                            @if($enrollment->child->photo_url)
                                                <img src="{{ asset('storage/' . $enrollment->child->photo_url) }}" class="detail-avatar" alt="">
                                            @else
                                                <div class="detail-avatar-placeholder">
                                                    {{ strtoupper(substr($enrollment->child->first_name, 0, 1) . substr($enrollment->child->last_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h4 class="mb-0 fw-bold">{{ $enrollment->child->full_name }}</h4>
                                                <span class="badge {{ $enrollment->child->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $enrollment->child->is_active ? 'Active Profile' : 'Inactive Profile' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="info-group d-flex justify-content-between">
                                            <span class="text-muted fw-semibold">Date of Birth</span>
                                            <span class="fw-medium">{{ $enrollment->child->date_of_birth ? $enrollment->child->date_of_birth->format('M d, Y') : '—' }} ({{ $enrollment->child->formatted_age }})</span>
                                        </div>

                                        <div class="info-group d-flex justify-content-between">
                                            <span class="text-muted fw-semibold">Allergies</span>
                                            <span class="fw-medium text-danger">{{ $enrollment->child->allergies ?: 'None specified' }}</span>
                                        </div>

                                        <div class="info-group d-flex justify-content-between">
                                            <span class="text-muted fw-semibold">Emergency Contact</span>
                                            <span class="fw-medium">
                                                {{ $enrollment->child->ec_name ?: '—' }} 
                                                @if($enrollment->child->ec_phone) ({{ $enrollment->child->ec_phone }}) @endif
                                            </span>
                                        </div>

                                        <div class="info-group">
                                            <span class="text-muted fw-semibold d-block mb-1">Linked Parents / Guardians</span>
                                            @forelse($enrollment->child->parents as $p)
                                                <span class="badge bg-light text-dark border me-1">{{ $p->full_name }} ({{ ucfirst($p->pivot->relationship ?? 'Parent') }})</span>
                                            @empty
                                                <span class="text-muted small">No parents linked.</span>
                                            @endforelse
                                        </div>
                                    @else
                                        <div class="text-muted">Child record not found.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Program Information --}}
                        <div class="col-lg-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="bi bi-book-half text-success me-2"></i>Program Details</h5>
                                    @if($enrollment->program)
                                        <a href="{{ route('admin.programs.edit', $enrollment->program) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-pencil me-1"></i>Edit Program
                                        </a>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if($enrollment->program)
                                        <div class="mb-3 pb-3 border-bottom">
                                            <h4 class="mb-1 fw-bold">{{ $enrollment->program->name }}</h4>
                                            <span class="badge bg-secondary">{{ $enrollment->program->service_type_label }}</span>
                                            <span class="badge bg-light text-dark border ms-1">{{ ucfirst($enrollment->program->billing_model) }} Billing</span>
                                        </div>

                                        <div class="info-group d-flex justify-content-between">
                                            <span class="text-muted fw-semibold">Age Requirement</span>
                                            <span class="fw-medium">{{ $enrollment->program->age_range_label }}</span>
                                        </div>

                                        <div class="info-group d-flex justify-content-between">
                                            <span class="text-muted fw-semibold">Capacity</span>
                                            <span class="fw-medium">{{ $enrollment->program->active_enrollments_count }} / {{ $enrollment->program->capacity }} slots filled</span>
                                        </div>

                                        @if($enrollment->program->monthly_fee)
                                            <div class="info-group d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Monthly Fee</span>
                                                <span class="fw-medium">${{ number_format($enrollment->program->monthly_fee, 2) }}</span>
                                            </div>
                                        @endif

                                        @if($enrollment->program->day_start_time && $enrollment->program->day_end_time)
                                            <div class="info-group d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Daily Schedule</span>
                                                <span class="fw-medium">{{ $enrollment->program->day_start_time }} &mdash; {{ $enrollment->program->day_end_time }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-muted">Program record not found.</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Enrollment Audit & Timeline --}}
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="bi bi-clock-history text-info me-2"></i>Enrollment Schedule & Audit</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="p-3 bg-light rounded">
                                                <span class="text-muted small d-block">SERVICE TYPE</span>
                                                <strong class="text-dark">{{ $enrollment->service_type_label }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="p-3 bg-light rounded">
                                                <span class="text-muted small d-block">START DATE</span>
                                                <strong class="text-dark">{{ $enrollment->start_date ? $enrollment->start_date->format('M d, Y') : '—' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="p-3 bg-light rounded">
                                                <span class="text-muted small d-block">END DATE</span>
                                                <strong class="text-dark">{{ $enrollment->end_date ? $enrollment->end_date->format('M d, Y') : 'Ongoing' }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="p-3 bg-light rounded">
                                                <span class="text-muted small d-block">CREATED BY</span>
                                                <strong class="text-dark">{{ $enrollment->createdBy->name ?? 'System' }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    @if($enrollment->notes)
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <span class="text-muted small fw-semibold d-block mb-1">NOTES / REMARKS:</span>
                                            <div class="text-dark" style="white-space: pre-line;">{{ $enrollment->notes }}</div>
                                        </div>
                                    @endif

                                    @if($enrollment->approved_at)
                                        <div class="mt-3 text-muted small">
                                            <i class="bi bi-shield-check text-success me-1"></i>
                                            Approved by <strong>{{ $enrollment->approvedBy->name ?? 'Admin' }}</strong> on {{ $enrollment->approved_at->format('M d, Y \a\t H:i') }}.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Reject Enrollment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to reject this enrollment application?</p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Reason for Rejection (Optional)</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Specify reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(actionUrl) {
            document.getElementById('rejectForm').action = actionUrl;
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }
    </script>
</body>
</html>
