<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Details | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .detail-label { font-weight: 600; color: #6b7280; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.15rem; }
        .detail-value { font-size: 1rem; color: #1f2937; }
        .therapy-badge-slt { background: #dbeafe; color: #1e40af; }
        .therapy-badge-aba { background: #fef3c7; color: #92400e; }
        .therapy-badge-ot  { background: #d1fae5; color: #065f46; }
        .therapist-avatar-lg {
            width: 56px; height: 56px; border-radius: 50%;
            background: #ede9fe; color: #7c3aed; display: flex;
            align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem;
        }
        .page-banner {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #c084fc 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 25px rgba(124,58,237,0.18);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -50px; right: -30px;
            width: 170px; height: 170px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a></li></ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="user-footer">
                                <form method="POST" action="{{ route('logout') }}">@csrf
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
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="page-banner">
                        <div class="d-flex justify-content-between align-items-center">
                            <div style="position:relative;z-index:1;">
                                <h2 class="mb-1" style="font-size:1.45rem;font-weight:700;">
                                    <i class="bi bi-heart-pulse-fill me-2"></i>Session #{{ $therapySession->id }}
                                </h2>
                                <p class="mb-0" style="font-size:0.9rem;opacity:0.88;">
                                    {{ $therapySession->child->full_name ?? 'Unknown Child' }} · {{ $therapySession->service->name ?? 'Unknown Service' }}
                                </p>
                            </div>
                            <div class="d-flex gap-2" style="position:relative;z-index:1;">
                                <a href="{{ route('admin.therapy-sessions.edit', $therapySession) }}" class="btn btn-light fw-bold shadow-sm">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <a href="{{ route('admin.therapy-sessions.index') }}" class="btn btn-outline-light">
                                    <i class="bi bi-arrow-left me-1"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row g-3">
                        {{-- Main Info --}}
                        <div class="col-lg-8">
                            {{-- Session Details --}}
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Session Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="detail-label">Session Date</div>
                                            <div class="detail-value">
                                                <i class="bi bi-calendar-event me-1 text-muted"></i>{{ \Carbon\Carbon::parse($therapySession->session_date)->format('l, M d, Y') }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="detail-label">Time Slot</div>
                                            <div class="detail-value">
                                                <i class="bi bi-clock me-1 text-muted"></i>{{ \Carbon\Carbon::parse($therapySession->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($therapySession->end_time)->format('h:i A') }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="detail-label">Status</div>
                                            @php
                                                $statusConfig = match($therapySession->status) {
                                                    'scheduled' => ['class' => 'bg-info-subtle text-info border-info-subtle', 'icon' => 'bi-calendar-check', 'label' => 'Scheduled'],
                                                    'completed' => ['class' => 'bg-success-subtle text-success border-success-subtle', 'icon' => 'bi-check2-all', 'label' => 'Completed'],
                                                    'cancelled' => ['class' => 'bg-warning-subtle text-warning border-warning-subtle', 'icon' => 'bi-x-circle', 'label' => 'Cancelled'],
                                                    'no_show'   => ['class' => 'bg-danger-subtle text-danger border-danger-subtle', 'icon' => 'bi-person-slash', 'label' => 'No Show'],
                                                    default     => ['class' => 'bg-secondary-subtle text-secondary', 'icon' => 'bi-question-circle', 'label' => ucfirst($therapySession->status)],
                                                };
                                            @endphp
                                            <span class="badge {{ $statusConfig['class'] }} border fs-6">
                                                <i class="bi {{ $statusConfig['icon'] }} me-1"></i>{{ $statusConfig['label'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Child Info --}}
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="bi bi-person-fill me-2 text-success"></i>Child Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="detail-label">Name</div>
                                            <div class="detail-value fw-semibold">{{ $therapySession->child->full_name ?? 'Unknown' }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="detail-label">Age</div>
                                            <div class="detail-value">{{ $therapySession->child ? $therapySession->child->formatted_age : '—' }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="detail-label">Date of Birth</div>
                                            <div class="detail-value">{{ $therapySession->child && $therapySession->child->date_of_birth ? $therapySession->child->date_of_birth->format('M d, Y') : '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Notes --}}
                            @if($therapySession->notes)
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="bi bi-journal-text me-2 text-secondary"></i>Session Notes</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0" style="white-space: pre-line;">{{ $therapySession->notes }}</p>
                                </div>
                            </div>
                            @endif

                            {{-- Quick Status Update --}}
                            @if($therapySession->status === 'scheduled')
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2 text-warning"></i>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex gap-2">
                                        <form action="{{ route('admin.therapy-sessions.update-status', $therapySession) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-success"><i class="bi bi-check2-all me-1"></i>Mark Completed</button>
                                        </form>
                                        <form action="{{ route('admin.therapy-sessions.update-status', $therapySession) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-warning"><i class="bi bi-x-circle me-1"></i>Cancel Session</button>
                                        </form>
                                        <form action="{{ route('admin.therapy-sessions.update-status', $therapySession) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="no_show">
                                            <button type="submit" class="btn btn-danger"><i class="bi bi-person-slash me-1"></i>No Show</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Sidebar --}}
                        <div class="col-lg-4">
                            {{-- Therapist Card --}}
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge-fill me-2 text-primary"></i>Therapist</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="therapist-avatar-lg mx-auto mb-2">
                                        {{ $therapySession->therapist->initials ?? 'TH' }}
                                    </div>
                                    <h6 class="fw-bold mb-1">{{ $therapySession->therapist->full_name ?? 'Unknown' }}</h6>
                                    @if($therapySession->therapist && $therapySession->therapist->specialization)
                                        <span class="badge therapy-badge-{{ $therapySession->therapist->specialization }} border">
                                            {{ $therapySession->therapist->specialization_label }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Service Card --}}
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-heart-pulse me-2 text-danger"></i>Therapy Service</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <div class="detail-label">Service</div>
                                        <div class="detail-value fw-semibold">{{ $therapySession->service->name ?? 'Unknown' }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="detail-label">Type</div>
                                        <span class="badge therapy-badge-{{ $therapySession->service->therapy_type ?? '' }} border">
                                            {{ strtoupper($therapySession->service->therapy_type ?? '—') }}
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <div class="detail-label">Standard Duration</div>
                                        <div class="detail-value">{{ $therapySession->service->duration_minutes ?? '—' }} minutes</div>
                                    </div>
                                    <div>
                                        <div class="detail-label">Session Rate</div>
                                        <div class="detail-value fw-bold text-success">৳{{ number_format($therapySession->service->session_rate ?? 0, 2) }}</div>
                                    </div>
                                    @if($therapySession->service && $therapySession->service->description)
                                        <hr>
                                        <p class="small text-muted mb-0">{{ $therapySession->service->description }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Booking Info --}}
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-info"></i>Booking Info</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <div class="detail-label">Booked By</div>
                                        <div class="detail-value">{{ $therapySession->bookedBy->name ?? 'Unknown' }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="detail-label">Created</div>
                                        <div class="detail-value small">{{ $therapySession->created_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <div>
                                        <div class="detail-label">Last Updated</div>
                                        <div class="detail-value small">{{ $therapySession->updated_at->format('M d, Y h:i A') }}</div>
                                    </div>
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
