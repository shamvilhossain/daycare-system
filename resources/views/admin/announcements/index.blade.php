<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | KinderCare</title>
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
        .stat-card {
            border-radius: 12px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }
        .stat-card .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }
        .table th { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; font-weight: 600; }
        .table td { vertical-align: middle; }
        .announcement-title { font-weight: 600; color: #1e293b; text-decoration: none; }
        .announcement-title:hover { color: #6366f1; }
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
                            <h2><i class="bi bi-megaphone-fill me-2"></i>Announcements</h2>
                            <p>Broadcast updates to parents, staff, or the entire center with live ticker on the welcome page</p>
                        </div>
                        <a href="{{ route('admin.announcements.create') }}" class="btn btn-light fw-bold shadow-sm" style="position:relative;z-index:1;">
                            <i class="bi bi-plus-lg me-1"></i> New Announcement
                        </a>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{-- Alert Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Stats Overview --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-lg-3">
                            <div class="card stat-card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-indigo-subtle text-indigo" style="background: #ede9fe; color: #6366f1;">
                                        <i class="bi bi-megaphone-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ $stats['total'] }}</div>
                                        <div class="text-muted small">Total Announcements</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card stat-card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-success-subtle text-success">
                                        <i class="bi bi-broadcast"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-success">{{ $stats['active'] }}</div>
                                        <div class="text-muted small">Active Now</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card stat-card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-primary-subtle text-primary">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-primary">{{ $stats['parents'] }}</div>
                                        <div class="text-muted small">For Parents</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="card stat-card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-warning-subtle text-warning">
                                        <i class="bi bi-person-badge-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-warning">{{ $stats['staff'] }}</div>
                                        <div class="text-muted small">Staff Only</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters Card --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.announcements.index') }}" class="row g-2 align-items-center">
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by title or content..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="audience" class="form-select">
                                        <option value="">All Audiences</option>
                                        <option value="all" {{ request('audience') == 'all' ? 'selected' : '' }}>Audience: All (Public)</option>
                                        <option value="parents" {{ request('audience') == 'parents' ? 'selected' : '' }}>Audience: Parents</option>
                                        <option value="staff" {{ request('audience') == 'staff' ? 'selected' : '' }}>Audience: Staff</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Now</option>
                                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1" style="background-color: #6366f1; border-color: #6366f1;">
                                        <i class="bi bi-funnel-fill me-1"></i> Filter
                                    </button>
                                    @if(request()->hasAny(['search', 'audience', 'status']))
                                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Table Card --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3 py-3">Title & Summary</th>
                                            <th class="py-3">Audience</th>
                                            <th class="py-3">Created By</th>
                                            <th class="py-3">Published Date</th>
                                            <th class="py-3">Expiration Date</th>
                                            <th class="py-3">Status</th>
                                            <th class="text-end pe-3 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($announcements as $announcement)
                                            <tr>
                                                <td class="ps-3">
                                                    <div>
                                                        <a href="{{ route('admin.announcements.show', $announcement) }}" class="announcement-title">
                                                            {{ $announcement->title }}
                                                        </a>
                                                        <div class="text-muted small text-truncate" style="max-width: 380px;">
                                                            {{ Str::limit($announcement->content, 90) }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {!! $announcement->audience_badge !!}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                            {{ strtoupper(substr($announcement->author_name, 0, 1)) }}
                                                        </div>
                                                        <span class="small fw-medium">{{ $announcement->author_name }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="small text-muted">
                                                        <i class="bi bi-calendar-event me-1"></i>
                                                        {{ $announcement->published_at ? $announcement->published_at->format('M d, Y h:i A') : 'Immediate' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($announcement->expires_at)
                                                        <span class="small {{ $announcement->expires_at->isPast() ? 'text-danger' : 'text-muted' }}">
                                                            <i class="bi bi-clock-history me-1"></i>
                                                            {{ $announcement->expires_at->format('M d, Y h:i A') }}
                                                        </span>
                                                    @else
                                                        <span class="small text-muted fst-italic">Never expires</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {!! $announcement->status_badge !!}
                                                </td>
                                                <td class="text-end pe-3">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.announcements.show', $announcement) }}" class="btn btn-outline-secondary" title="View details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-outline-secondary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $announcement->id }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>

                                                    {{-- Delete Confirmation Modal --}}
                                                    <div class="modal fade" id="deleteModal{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
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
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="bi bi-megaphone display-4 d-block mb-3 opacity-25"></i>
                                                        <h5>No announcements found</h5>
                                                        <p class="small mb-3">Try clearing your search filters or publish a new announcement.</p>
                                                        <a href="{{ route('admin.announcements.create') }}" class="btn btn-sm btn-primary" style="background-color: #6366f1; border-color: #6366f1;">
                                                            <i class="bi bi-plus-lg me-1"></i> Create First Announcement
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($announcements->hasPages())
                            <div class="card-footer bg-white border-0 py-3">
                                {{ $announcements->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
