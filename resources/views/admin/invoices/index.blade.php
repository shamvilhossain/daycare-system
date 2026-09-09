<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices & Payments | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #065f46 0%, #059669 50%, #10b981 100%);
            border-radius: 16px; padding: 1.75rem 2rem; color: #fff; margin-bottom: 1.5rem;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 25px rgba(5,150,105,0.18);
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
        .invoice-number { font-weight: 600; color: #065f46; font-family: 'Inter', monospace; }
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
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand"><a href="/" class="brand-link"><span class="brand-text font-weight-light">KinderCare</span></a></div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-header">MAIN</li>
                        <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link"><i class="nav-icon bi bi-grid-1x2-fill"></i><p>Dashboard</p></a></li>
                        <li class="nav-header">DAILY OPERATIONS</li>
                        <li class="nav-item"><a href="{{ route('admin.attendance.index') }}" class="nav-link"><i class="nav-icon bi bi-check2-circle"></i><p>Attendance Desk</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.child-daily-logs.index') }}" class="nav-link"><i class="nav-icon bi bi-journal-text"></i><p>Daily Child Logs</p></a></li>
                        <li class="nav-header">MANAGEMENT</li>
                        <li class="nav-item"><a href="{{ route('admin.children.index') }}" class="nav-link"><i class="nav-icon bi bi-people-fill"></i><p>Children</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-person-badge-fill"></i><p>Staff</p></a></li>
                        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon bi bi-calendar-event-fill"></i><p>Activities</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.enrollments.index') }}" class="nav-link"><i class="nav-icon bi bi-clipboard-check-fill"></i><p>Enrollments</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.invoices.index') }}" class="nav-link active"><i class="nav-icon bi bi-receipt-cutoff"></i><p>Invoices & Payments</p></a></li>
                        <li class="nav-item"><a href="{{ route('admin.announcements.index') }}" class="nav-link"><i class="nav-icon bi bi-megaphone-fill"></i><p>Announcements</p></a></li>
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
                    <div class="page-banner d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="bi bi-receipt-cutoff me-2"></i>Invoices & Payments</h2>
                            <p>Create invoices, track payments, and manage billing for enrolled children</p>
                        </div>
                        <a href="{{ route('admin.invoices.create') }}" class="btn btn-light fw-bold shadow-sm" style="position:relative;z-index:1;">
                            <i class="bi bi-plus-lg me-1"></i> New Invoice
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

                    {{-- Stats Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-xl col-md-4 col-sm-6">
                            <div class="card stat-card border-0 shadow-sm h-100">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-primary-subtle text-primary"><i class="bi bi-receipt"></i></div>
                                    <div>
                                        <div class="text-muted small fw-semibold">TOTAL</div>
                                        <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl col-md-4 col-sm-6">
                            <div class="card stat-card border-0 shadow-sm h-100">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-warning-subtle text-warning"><i class="bi bi-file-earmark-text"></i></div>
                                    <div>
                                        <div class="text-muted small fw-semibold">DRAFT</div>
                                        <div class="fs-4 fw-bold">{{ $stats['draft'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl col-md-4 col-sm-6">
                            <div class="card stat-card border-0 shadow-sm h-100">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-success-subtle text-success"><i class="bi bi-check-circle"></i></div>
                                    <div>
                                        <div class="text-muted small fw-semibold">PAID</div>
                                        <div class="fs-4 fw-bold">{{ $stats['paid'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl col-md-4 col-sm-6">
                            <div class="card stat-card border-0 shadow-sm h-100">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-danger-subtle text-danger"><i class="bi bi-exclamation-circle"></i></div>
                                    <div>
                                        <div class="text-muted small fw-semibold">OVERDUE</div>
                                        <div class="fs-4 fw-bold">{{ $stats['overdue'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl col-md-4 col-sm-6">
                            <div class="card stat-card border-0 shadow-sm h-100">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-secondary-subtle text-secondary"><i class="bi bi-x-circle"></i></div>
                                    <div>
                                        <div class="text-muted small fw-semibold">CANCELLED</div>
                                        <div class="fs-4 fw-bold">{{ $stats['cancelled'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.invoices.index') }}" class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">Search</label>
                                    <input type="text" name="search" class="form-control" placeholder="Invoice #, parent, child name..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-semibold">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        @foreach(['draft','paid','overdue','cancelled'] as $s)
                                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-semibold">From Date</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-semibold">To Date</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Invoices Table --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">Invoice #</th>
                                            <th>Parent</th>
                                            <th>Child</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-end">Paid</th>
                                            <th class="text-end">Balance</th>
                                            <th>Status</th>
                                            <th>Invoice Date</th>
                                            <th>Due Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoices as $invoice)
                                            <tr>
                                                <td class="ps-3">
                                                    <span class="invoice-number">{{ $invoice->invoice_number }}</span>
                                                </td>
                                                <td>{{ $invoice->parent->full_name ?? '—' }}</td>
                                                <td>{{ $invoice->child->full_name ?? '—' }}</td>
                                                <td class="text-end fw-semibold">৳{{ number_format($invoice->total_amount, 2) }}</td>
                                                <td class="text-end text-success fw-semibold">৳{{ number_format($invoice->paid_total, 2) }}</td>
                                                <td class="text-end {{ $invoice->balance_due > 0 ? 'text-danger' : 'text-success' }} fw-semibold">
                                                    ৳{{ number_format($invoice->balance_due, 2) }}
                                                </td>
                                                <td>
                                                    <span class="badge {{ $invoice->status_badge_class }}">{{ $invoice->status_label }}</span>
                                                </td>
                                                <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                                <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-5 text-muted">
                                                    <i class="bi bi-receipt fs-1 d-block mb-2 opacity-25"></i>
                                                    No invoices found. <a href="{{ route('admin.invoices.create') }}">Create one</a>.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if($invoices->hasPages())
                            <div class="card-footer bg-white border-top d-flex justify-content-center py-3">
                                {{ $invoices->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
