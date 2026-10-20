<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Items | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #065f46 0%, #059669 50%, #10b981 100%);
            border-radius: 16px; padding: 1.25rem 1.5rem; color: #fff; margin-bottom: 1rem;
            position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(5,150,105,0.18);
        }
        .page-banner::before { content: ''; position: absolute; top: -50px; right: -30px; width: 170px; height: 170px; border-radius: 50%; background: rgba(255,255,255,0.08); pointer-events: none; }
        .page-banner h2 { font-size: 1.3rem; font-weight: 700; margin-bottom: .2rem; position: relative; z-index: 1; }
        .page-banner p { font-size: .88rem; opacity: .9; margin: 0; position: relative; z-index: 1; }
        .soft-card { border: 0; border-radius: 14px; box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 6px 24px -12px rgba(15,23,42,.12); }
        .stat { display: flex; align-items: center; gap: .75rem; }
        .stat .ico { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; font-size: 1.1rem; }
        .table thead th { font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: #64748b; font-weight: 700; background: #f8fafc; border-bottom: 0; white-space: nowrap; }
        .table td { vertical-align: middle; }
        tr.is-hidden td { opacity: .6; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a></li></ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><span class="d-none d-md-inline">{{ Auth::user()->name }}</span></a>
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

        @include('partials.sidebar')

        <main class="app-main">
            <div class="app-content pt-3">
                <div class="container-fluid">
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb small mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}" class="text-decoration-none">Invoices</a></li>
                            <li class="breadcrumb-item active">Item catalog</li>
                        </ol>
                    </nav>

                    <div class="page-banner d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h2><i class="bi bi-tags-fill me-2"></i>Invoice Item Catalog</h2>
                            <p>The names offered in the Description dropdown when creating an invoice. New names typed on an invoice are added here automatically.</p>
                        </div>
                        <a href="{{ route('admin.invoices.create') }}" class="btn btn-light btn-sm fw-bold shadow-sm px-3" style="position:relative;z-index:1">
                            <i class="bi bi-plus-lg me-1"></i>New Invoice
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm">
                            @foreach ($errors->all() as $error)<div><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</div>@endforeach
                        </div>
                    @endif

                    <div class="row g-3 mb-3">
                        @foreach ([['Total items', $stats['total'], 'bi-tags', 'bg-primary-subtle text-primary'], ['In dropdown', $stats['active'], 'bi-eye', 'bg-success-subtle text-success'], ['Hidden', $stats['hidden'], 'bi-eye-slash', 'bg-secondary-subtle text-secondary']] as [$label, $value, $icon, $class])
                            <div class="col-md-4">
                                <div class="card soft-card"><div class="card-body stat">
                                    <div class="ico {{ $class }}"><i class="bi {{ $icon }}"></i></div>
                                    <div><div class="fs-4 fw-bold">{{ $value }}</div><div class="small text-muted">{{ $label }}</div></div>
                                </div></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row g-3">
                        {{-- Add item --}}
                        <div class="col-lg-4">
                            <div class="card soft-card">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle text-success me-1"></i>Add an item</h6>
                                    <form method="POST" action="{{ route('admin.invoice-item-presets.store') }}">
                                        @csrf
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Name</label>
                                            <input type="text" name="name" class="form-control" maxlength="255" required placeholder="e.g., Monthly Tuition" value="{{ old('name') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Default amount (optional)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">৳</span>
                                                <input type="number" name="default_amount" class="form-control" step="0.01" min="0" value="{{ old('default_amount') }}" placeholder="0.00">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-lg me-1"></i>Add to catalog</button>
                                    </form>
                                    <div class="small text-muted mt-3">
                                        <i class="bi bi-info-circle me-1"></i>A trailing month like “– May 2023” is removed from names automatically, so monthly charges share one entry.
                                        Editing or removing an item never changes existing invoices.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Catalog --}}
                        <div class="col-lg-8">
                            <div class="card soft-card">
                                <div class="card-body pb-2">
                                    <form method="GET" class="row g-2">
                                        <div class="col-sm-7">
                                            <input type="text" name="search" class="form-control" placeholder="Search items…" value="{{ request('search') }}">
                                        </div>
                                        <div class="col-sm-3">
                                            <select name="status" class="form-select">
                                                <option value="">All</option>
                                                <option value="active" @selected(request('status') === 'active')>In dropdown</option>
                                                <option value="hidden" @selected(request('status') === 'hidden')>Hidden</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2 d-grid"><button class="btn btn-outline-primary"><i class="bi bi-funnel"></i></button></div>
                                    </form>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-3">Name</th>
                                                <th style="width:150px">Default amount</th>
                                                <th class="text-center">Used</th>
                                                <th>Last used</th>
                                                <th class="text-end pe-3">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($presets as $preset)
                                                <tr class="{{ $preset->is_active ? '' : 'is-hidden' }}">
                                                    <td class="ps-3">
                                                        <input type="text" name="name" form="edit{{ $preset->id }}" class="form-control form-control-sm" value="{{ $preset->name }}" maxlength="255" required>
                                                        @unless ($preset->is_active)<span class="badge bg-secondary-subtle text-secondary mt-1">Hidden</span>@endunless
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">৳</span>
                                                            <input type="number" name="default_amount" form="edit{{ $preset->id }}" class="form-control" step="0.01" min="0" value="{{ $preset->default_amount }}">
                                                        </div>
                                                    </td>
                                                    <td class="text-center"><span class="badge bg-light text-dark border">{{ $preset->usage_count }}×</span></td>
                                                    <td class="small text-muted">{{ $preset->last_used_at?->format('M d, Y') ?? '—' }}</td>
                                                    <td class="text-end pe-3 text-nowrap">
                                                        <form id="edit{{ $preset->id }}" method="POST" action="{{ route('admin.invoice-item-presets.update', $preset) }}" class="d-inline">
                                                            @csrf @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Save changes"><i class="bi bi-check-lg"></i></button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.invoice-item-presets.toggle', $preset) }}" class="d-inline">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ $preset->is_active ? 'Hide from dropdown' : 'Show in dropdown' }}">
                                                                <i class="bi {{ $preset->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.invoice-item-presets.destroy', $preset) }}" class="d-inline" onsubmit="return confirm('Remove “{{ addslashes($preset->name) }}” from the catalog? Existing invoices are not affected.');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-tags fs-2 d-block mb-2 opacity-25"></i>No items yet.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if ($presets->hasPages())
                                    <div class="card-footer bg-white border-0">{{ $presets->links() }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
