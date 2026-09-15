<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing & Revenue Report | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .page-banner {
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 50%, #14b8a6 100%);
            border-radius: 16px; 
            padding: 1.75rem 2rem; 
            color: #fff; 
            margin-bottom: 1.5rem;
            position: relative; 
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(13, 148, 136, 0.2);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -50px; right: -30px;
            width: 170px; height: 170px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner::after {
            content: ''; position: absolute; bottom: -30px; left: 45%;
            width: 130px; height: 130px; border-radius: 50%; background: rgba(255,255,255,0.05);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.35rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.92rem; opacity: 0.9; position: relative; z-index: 1; margin: 0; }

        /* Executive Summary Top Row Ribbon */
        .summary-ribbon {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            padding: 0.75rem 1.25rem;
            margin-top: 1.25rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1.5rem;
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }
        .summary-ribbon-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .summary-ribbon-item .label {
            opacity: 0.85;
            font-weight: 500;
        }
        .summary-ribbon-item .value {
            font-weight: 700;
            font-size: 1.05rem;
        }
        .summary-ribbon-separator {
            opacity: 0.4;
            font-weight: 300;
        }

        /* Metric Cards */
        .stat-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
            position: relative;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.07);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .bg-purple-subtle {
            background-color: #f3e8ff !important;
        }
        .text-purple-emphasis {
            color: #6b21a8 !important;
        }
        .border-purple-subtle {
            border-color: #e9d5ff !important;
        }

        /* Report Table Styling */
        .report-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        }
        .table th {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            font-weight: 700;
            padding: 1rem 0.85rem;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        .table td {
            vertical-align: middle;
            padding: 0.9rem 0.85rem;
            font-size: 0.88rem;
        }
        .invoice-link {
            font-family: 'Inter', monospace;
            font-weight: 700;
            color: #0d9488;
            text-decoration: none;
        }
        .invoice-link:hover {
            color: #0f766e;
            text-decoration: underline;
        }

        /* Filter Card */
        .filter-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.03);
            background: #ffffff;
        }
        .quick-date-btn {
            font-size: 0.76rem;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
        }

        /* Print Styles */
        @media print {
            .app-sidebar, .app-header, .filter-card, .btn-no-print, .pagination, .no-print {
                display: none !important;
            }
            .app-main, .app-content {
                margin: 0 !important;
                padding: 0 !important;
            }
            .page-banner {
                background: #0f766e !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .stat-card {
                border: 1px solid #e2e8f0 !important;
                box-shadow: none !important;
            }
            body {
                background: #fff !important;
            }
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body no-print">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>
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
                    <div class="page-banner d-flex flex-column justify-content-between">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <h2><i class="bi bi-cash-stack me-2"></i>Billing / Revenue Report</h2>
                                <p>Comprehensive revenue breakdown: What's invoiced vs. collected across daycare, therapy, and mixed programs</p>
                            </div>
                            <div class="d-flex gap-2 no-print">
                                <button type="button" onclick="window.print()" class="btn btn-light fw-bold shadow-sm">
                                    <i class="bi bi-printer me-1"></i> Print Report
                                </button>
                                <a href="{{ route('admin.reports.billing-revenue', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-warning fw-bold shadow-sm text-dark">
                                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
                                </a>
                            </div>
                        </div>

                        {{-- Prominent Summary Row at Top --}}
                        <div class="summary-ribbon">
                            <div class="summary-ribbon-item">
                                <span class="label"><i class="bi bi-receipt me-1"></i>Total Invoiced:</span>
                                <span class="value">৳{{ number_format($summary['total_invoiced'], 2) }}</span>
                            </div>
                            <span class="summary-ribbon-separator">·</span>
                            <div class="summary-ribbon-item">
                                <span class="label"><i class="bi bi-check2-circle me-1"></i>Total Collected:</span>
                                <span class="value text-warning-light">৳{{ number_format($summary['total_collected'], 2) }}</span>
                            </div>
                            <span class="summary-ribbon-separator">·</span>
                            <div class="summary-ribbon-item">
                                <span class="label"><i class="bi bi-hourglass-split me-1"></i>Total Outstanding:</span>
                                <span class="value">৳{{ number_format($summary['total_outstanding'], 2) }}</span>
                            </div>
                            <span class="summary-ribbon-separator">·</span>
                            <div class="summary-ribbon-item">
                                <span class="label"><i class="bi bi-pie-chart me-1"></i>Collection Rate:</span>
                                <span class="value">{{ number_format($summary['collection_rate'], 1) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{-- Key Metric Cards Row --}}
                    <div class="row g-3 mb-4">
                        {{-- Total Invoiced --}}
                        <div class="col-xl-3 col-sm-6">
                            <div class="card stat-card h-100">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-primary-subtle text-primary">
                                        <i class="bi bi-receipt-cutoff"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-muted small fw-semibold text-uppercase">Total Invoiced</div>
                                        <div class="fs-4 fw-bold text-dark">৳{{ number_format($summary['total_invoiced'], 2) }}</div>
                                        <div class="small text-muted">
                                            <span>{{ $summary['total_count'] - $summary['cancelled_count'] }} active invoices</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total Collected --}}
                        <div class="col-xl-3 col-sm-6">
                            <div class="card stat-card h-100">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-success-subtle text-success">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-muted small fw-semibold text-uppercase">Total Collected</div>
                                        <div class="fs-4 fw-bold text-success">৳{{ number_format($summary['total_collected'], 2) }}</div>
                                        <div class="small text-muted">
                                            <span>{{ $summary['paid_count'] }} fully settled</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Total Outstanding --}}
                        <div class="col-xl-3 col-sm-6">
                            <div class="card stat-card h-100">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-danger-subtle text-danger">
                                        <i class="bi bi-exclamation-octagon-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-muted small fw-semibold text-uppercase">Total Outstanding</div>
                                        <div class="fs-4 fw-bold {{ $summary['total_outstanding'] > 0 ? 'text-danger' : 'text-success' }}">
                                            ৳{{ number_format($summary['total_outstanding'], 2) }}
                                        </div>
                                        <div class="small text-muted">
                                            <span>{{ $summary['overdue_count'] }} overdue · {{ $summary['draft_count'] }} pending</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Collection Rate % --}}
                        <div class="col-xl-3 col-sm-6">
                            <div class="card stat-card h-100">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="stat-icon bg-info-subtle text-info">
                                        <i class="bi bi-pie-chart-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-muted small fw-semibold text-uppercase">Collection Rate</div>
                                        <div class="fs-4 fw-bold text-dark">{{ number_format($summary['collection_rate'], 1) }}%</div>
                                        <div class="progress mt-1" style="height: 6px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, $summary['collection_rate']) }}%" aria-valuenow="{{ $summary['collection_rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filters Card --}}
                    <div class="card filter-card mb-4 no-print">
                        <div class="card-body p-3 p-md-4">
                            <form method="GET" action="{{ route('admin.reports.billing-revenue') }}" id="reportFilterForm">
                                <div class="row g-3 align-items-end">
                                    {{-- Search --}}
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label small fw-semibold text-secondary">Search</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Invoice #, parent, child..." value="{{ request('search') }}">
                                        </div>
                                    </div>

                                    {{-- Date Range: From --}}
                                    <div class="col-lg-2 col-md-3 col-sm-6">
                                        <label class="form-label small fw-semibold text-secondary">From Date</label>
                                        <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                                    </div>

                                    {{-- Date Range: To --}}
                                    <div class="col-lg-2 col-md-3 col-sm-6">
                                        <label class="form-label small fw-semibold text-secondary">To Date</label>
                                        <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                                    </div>

                                    {{-- Type Filter (Daycare / Therapy / Mixed) --}}
                                    <div class="col-lg-2 col-md-4 col-sm-6">
                                        <label class="form-label small fw-semibold text-secondary">Invoice Type</label>
                                        <select name="type" class="form-select">
                                            <option value="">All Types</option>
                                            <option value="daycare" {{ request('type') === 'daycare' ? 'selected' : '' }}>Daycare</option>
                                            <option value="therapy" {{ request('type') === 'therapy' ? 'selected' : '' }}>Therapy</option>
                                            <option value="mixed" {{ request('type') === 'mixed' ? 'selected' : '' }}>Mixed</option>
                                        </select>
                                    </div>

                                    {{-- Status Filter --}}
                                    <div class="col-lg-1 col-md-4 col-sm-6">
                                        <label class="form-label small fw-semibold text-secondary">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">All</option>
                                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="col-lg-2 col-md-4 d-flex gap-2">
                                        <button type="submit" class="btn btn-teal text-white flex-grow-1" style="background-color: #0d9488;">
                                            <i class="bi bi-funnel-fill me-1"></i> Apply
                                        </button>
                                        <a href="{{ route('admin.reports.billing-revenue') }}" class="btn btn-outline-secondary" title="Reset Filters">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </a>
                                    </div>
                                </div>

                                {{-- Quick Date Presets --}}
                                <div class="mt-3 pt-2 border-top d-flex flex-wrap align-items-center gap-2">
                                    <span class="small text-muted fw-semibold me-1"><i class="bi bi-clock me-1"></i>Quick Ranges:</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-btn" onclick="setDateRange('today')">Today</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-btn" onclick="setDateRange('this_month')">This Month</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-btn" onclick="setDateRange('last_month')">Last Month</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-btn" onclick="setDateRange('this_year')">This Year</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date-btn" onclick="setDateRange('clear')">Clear Dates</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Report Table Card --}}
                    <div class="card report-card overflow-hidden">
                        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
                            <div>
                                <h5 class="card-title fw-bold mb-0 text-dark">
                                    <i class="bi bi-table me-2 text-teal" style="color: #0d9488;"></i>Detailed Invoiced vs. Collected
                                </h5>
                                <span class="small text-muted">Showing {{ $invoices->firstItem() ?? 0 }} to {{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }} records</span>
                            </div>
                            <div class="small text-muted d-flex align-items-center gap-3">
                                <span><span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">Daycare</span> Standard</span>
                                <span><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle me-1">Therapy</span> Sessions</span>
                                <span><span class="badge bg-purple-subtle text-purple-emphasis border border-purple-subtle me-1">Mixed</span> Combined</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3">Invoice #</th>
                                        <th>Family</th>
                                        <th>Type</th>
                                        <th>Invoice Date</th>
                                        <th>Due Date</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center">Status</th>
                                        <th>Paid Date</th>
                                        <th class="text-center no-print" style="width: 80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                        <tr>
                                            {{-- 1. Invoice # --}}
                                            <td class="ps-3">
                                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="invoice-link" title="View invoice details">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>

                                            {{-- 2. Family --}}
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    {{ $invoice->parent->full_name ?? '—' }}
                                                </div>
                                                @if($invoice->child)
                                                    <div class="text-muted small">
                                                        <i class="bi bi-person me-1"></i>{{ $invoice->child->full_name }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- 3. Type (daycare/therapy/mixed) --}}
                                            <td>
                                                <span class="badge {{ $invoice->invoice_type_badge_class }} px-2 py-1">
                                                    @if($invoice->invoice_type === 'therapy')
                                                        <i class="bi bi-heart-pulse me-1"></i>
                                                    @elseif($invoice->invoice_type === 'mixed')
                                                        <i class="bi bi-layers me-1"></i>
                                                    @else
                                                        <i class="bi bi-sun me-1"></i>
                                                    @endif
                                                    {{ $invoice->invoice_type_label }}
                                                </span>
                                            </td>

                                            {{-- 4. Invoice Date --}}
                                            <td class="text-secondary">
                                                {{ $invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '—' }}
                                            </td>

                                            {{-- 5. Due Date --}}
                                            <td>
                                                <span class="{{ $invoice->status === 'overdue' ? 'text-danger fw-semibold' : 'text-secondary' }}">
                                                    {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '—' }}
                                                </span>
                                            </td>

                                            {{-- 6. Amount --}}
                                            <td class="text-end fw-bold">
                                                <span>৳{{ number_format($invoice->total_amount, 2) }}</span>
                                                @if($invoice->paid_total > 0 && $invoice->paid_total < $invoice->total_amount)
                                                    <div class="small text-success fw-normal">
                                                        Paid: ৳{{ number_format($invoice->paid_total, 2) }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- 7. Status --}}
                                            <td class="text-center">
                                                <span class="badge {{ $invoice->status_badge_class }} px-2 py-1">
                                                    {{ $invoice->status_label }}
                                                </span>
                                            </td>

                                            {{-- 8. Paid Date (if paid) --}}
                                            <td>
                                                @if($invoice->paid_date)
                                                    <span class="text-success fw-medium">
                                                        <i class="bi bi-check2-circle me-1"></i>{{ $invoice->paid_date->format('M d, Y') }}
                                                    </span>
                                                @elseif($invoice->status === 'paid')
                                                    <span class="text-success fw-medium">
                                                        <i class="bi bi-check2-circle me-1"></i>Paid
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            {{-- Actions --}}
                                            <td class="text-center no-print">
                                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-teal text-secondary border" title="View Full Invoice">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="text-muted py-3">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                                    <div class="fw-semibold fs-6">No billing records found matching your filters</div>
                                                    <p class="small text-muted mb-3">Try adjusting your date range, type selection, or status filters.</p>
                                                    <a href="{{ route('admin.reports.billing-revenue') }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset All Filters
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination & Footer Summary --}}
                        @if($invoices->hasPages())
                            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3 border-top no-print">
                                <div class="small text-muted">
                                    Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} results
                                </div>
                                <div>
                                    {{ $invoices->links('pagination::bootstrap-5') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Quick Date Range Helper JS --}}
    <script>
        function setDateRange(range) {
            const today = new Date();
            const fromInput = document.getElementById('from_date');
            const toInput = document.getElementById('to_date');

            function formatDate(d) {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            if (range === 'today') {
                fromInput.value = formatDate(today);
                toInput.value = formatDate(today);
            } else if (range === 'this_month') {
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                fromInput.value = formatDate(firstDay);
                toInput.value = formatDate(lastDay);
            } else if (range === 'last_month') {
                const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
                fromInput.value = formatDate(firstDay);
                toInput.value = formatDate(lastDay);
            } else if (range === 'this_year') {
                const firstDay = new Date(today.getFullYear(), 0, 1);
                const lastDay = new Date(today.getFullYear(), 11, 31);
                fromInput.value = formatDate(firstDay);
                toInput.value = formatDate(lastDay);
            } else if (range === 'clear') {
                fromInput.value = '';
                toInput.value = '';
            }
        }
    </script>
</body>
</html>
