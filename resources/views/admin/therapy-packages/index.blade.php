<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Therapy Package Catalog | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #fcd34d 100%);
            border-radius: 12px; padding: 0.75rem 1.25rem; color: #fff; margin-bottom: 0.75rem;
            position: relative; overflow: hidden;
            box-shadow: 0 4px 15px rgba(217,119,6,0.15);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -35px; right: -25px;
            width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.15rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.82rem; opacity: 0.88; position: relative; z-index: 1; margin: 0; }
        .therapy-badge-slt { background: #dbeafe; color: #1e40af; }
        .therapy-badge-aba { background: #fef3c7; color: #92400e; }
        .therapy-badge-ot  { background: #d1fae5; color: #065f46; }
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
            <div class="app-content-header pt-2 pb-0">
                <div class="container-fluid">
                    <div class="page-banner d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h2><i class="bi bi-gift me-2"></i>Therapy Package Catalog</h2>
                            <p>Bundle therapy services into purchasable packages with custom pricing</p>
                        </div>
                        <a href="{{ route('admin.therapy-packages.create') }}" class="btn btn-light btn-sm fw-bold shadow-sm px-3" style="position:relative;z-index:1;">
                            <i class="bi bi-plus-lg me-1"></i> New Package
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
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Search & Filter --}}
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body py-2">
                            <form method="GET" action="{{ route('admin.therapy-packages.index') }}" class="row g-2 align-items-center">
                                <div class="col-md-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search packages..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">All Statuses</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-funnel me-1"></i>Filter</button>
                                    <a href="{{ route('admin.therapy-packages.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Package Name</th>
                                            <th>Services Included</th>
                                            <th>Total Sessions</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($packages as $package)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $package->name }}</div>
                                                </td>
                                                <td>
                                                    @foreach($package->items as $item)
                                                        <div class="small">
                                                            <span class="badge therapy-badge-{{ $item->service->therapy_type ?? 'slt' }} border me-1" style="font-size:0.65rem;">
                                                                {{ strtoupper($item->service->therapy_type ?? '?') }}
                                                            </span>
                                                            {{ $item->service->name ?? 'Unknown' }}
                                                            <span class="text-muted">× {{ $item->session_count }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if($package->items->isEmpty())
                                                        <span class="text-muted small">No services</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-semibold text-dark">{{ $package->total_sessions }}</span>
                                                    <span class="text-muted small">sessions</span>
                                                </td>
                                                <td><span class="fw-semibold text-dark">৳{{ number_format($package->price, 2) }}</span></td>
                                                <td>
                                                    @if($package->is_active)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.therapy-packages.edit', $package) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('admin.therapy-packages.destroy', $package) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this therapy package?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    <i class="bi bi-gift fs-1 d-block mb-2 text-secondary"></i>
                                                    No therapy packages found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            {{ $packages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
