<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $announcement->title }} | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #8b5cf6 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -50px; right: -30px;
            width: 170px; height: 170px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner::after {
            content: ''; position: absolute; bottom: -30px; left: 50%;
            width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.05);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.45rem; font-weight: 700; margin-bottom: 0.25rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.9rem; opacity: 0.88; position: relative; z-index: 1; margin: 0; }
        .content-box {
            font-size: 1.05rem;
            line-height: 1.75;
            color: #1e293b;
            background: #f8fafc;
            border-left: 4px solid #6366f1;
            padding: 1.5rem 1.75rem;
            border-radius: 0 12px 12px 0;
            white-space: pre-line;
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
                    <div class="page-banner d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="bi bi-megaphone-fill me-2"></i>Announcement Details</h2>
                            <p>View broadcast information, schedule, audience, and ticker status</p>
                        </div>
                        <div class="d-flex gap-2" style="position:relative;z-index:1;">
                            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-light fw-bold shadow-sm">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-light fw-bold shadow-sm">
                                <i class="bi bi-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-4">
                        {{-- Main Content Card --}}
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                    <span class="badge bg-indigo-subtle text-indigo px-3 py-1 fw-bold" style="background:#ede9fe;color:#6366f1;">
                                        ID #{{ $announcement->id }}
                                    </span>
                                    <div>{!! $announcement->status_badge !!}</div>
                                </div>
                                <div class="card-body p-4">
                                    <h3 class="fw-bold text-dark mb-3">{{ $announcement->title }}</h3>

                                    <div class="d-flex flex-wrap align-items-center gap-3 mb-4 text-muted small">
                                        <div>
                                            <i class="bi bi-person-circle me-1"></i>
                                            Created by: <strong class="text-dark">{{ $announcement->author_name }}</strong>
                                        </div>
                                        <div>&bull;</div>
                                        <div>
                                            <i class="bi bi-calendar-event me-1"></i>
                                            Published: <strong class="text-dark">{{ $announcement->published_at ? $announcement->published_at->format('M d, Y h:i A') : 'Immediate' }}</strong>
                                        </div>
                                        @if($announcement->expires_at)
                                            <div>&bull;</div>
                                            <div>
                                                <i class="bi bi-clock-history me-1"></i>
                                                Expires: <strong class="text-dark">{{ $announcement->expires_at->format('M d, Y h:i A') }}</strong>
                                            </div>
                                        @endif
                                    </div>

                                    <h6 class="text-uppercase text-muted fw-bold small mb-2">Announcement Message</h6>
                                    <div class="content-box">
                                        {{ $announcement->content }}
                                    </div>
                                </div>
                                <div class="card-footer bg-light border-top p-3 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i> Back to Announcements
                                    </a>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-primary" style="background-color: #6366f1; border-color: #6366f1;">
                                            <i class="bi bi-pencil me-1"></i> Edit Announcement
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Metadata Side Panel --}}
                        <div class="col-lg-4">
                            {{-- Welcome Page Visibility Card --}}
                            @php
                                $showsOnWelcome = in_array($announcement->audience, ['parents', 'all']) 
                                    && $announcement->expires_at 
                                    && $announcement->expires_at->isFuture()
                                    && (!$announcement->published_at || $announcement->published_at->isPast());
                            @endphp
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-display me-2 text-indigo" style="color:#6366f1;"></i>Welcome Page Ticker Status</h6>
                                </div>
                                <div class="card-body p-3">
                                    @if ($showsOnWelcome)
                                        <div class="alert alert-success d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-check-circle-fill fs-5"></i>
                                            <div>
                                                <strong>Live on Welcome Page</strong>
                                                <div class="small">Scrolling in the marquee ticker for visitors and parents.</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-secondary d-flex align-items-center gap-2 mb-2">
                                            <i class="bi bi-info-circle-fill fs-5"></i>
                                            <div>
                                                <strong>Not Live on Ticker</strong>
                                                <div class="small">
                                                    @if(!in_array($announcement->audience, ['parents', 'all']))
                                                        Audience is set to Staff only.
                                                    @elseif(!$announcement->expires_at || $announcement->expires_at->isPast())
                                                        Expired or no future expiry date set.
                                                    @elseif($announcement->published_at && $announcement->published_at->isFuture())
                                                        Scheduled for future publication.
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <a href="/" target="_blank" class="btn btn-sm btn-outline-primary w-100 mt-2">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Open Welcome Page
                                    </a>
                                </div>
                            </div>

                            {{-- Target Audience Card --}}
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people me-2"></i>Audience & Targeting</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <span class="text-muted small">Target Audience</span>
                                        <div>{!! $announcement->audience_badge !!}</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                        <span class="text-muted small">Current Status</span>
                                        <div>{!! $announcement->status_badge !!}</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-2">
                                        <span class="text-muted small">Database ID</span>
                                        <span class="fw-bold font-monospace small">#{{ $announcement->id }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Audit / Timestamps Card --}}
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock me-2"></i>Audit Information</h6>
                                </div>
                                <div class="card-body p-3 small">
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Staff ID (Creator)</span>
                                        <span class="fw-medium font-monospace">{{ $announcement->staff_id }} ({{ $announcement->author_name }})</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Created At</span>
                                        <span class="fw-medium">{{ $announcement->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-2">
                                        <span class="text-muted">Last Updated</span>
                                        <span class="fw-medium">{{ $announcement->updated_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Delete Confirmation Modal --}}
                    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered text-start">
                            <div class="modal-content">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Announcement</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body py-3">
                                    Are you sure you want to permanently delete <strong>"{{ $announcement->title }}"</strong>? This action cannot be undone.
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
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
