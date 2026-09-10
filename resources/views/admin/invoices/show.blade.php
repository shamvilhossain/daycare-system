<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
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
        .invoice-number-hero {
            font-size: 1.1rem; font-weight: 700; color: #065f46;
            font-family: 'Inter', monospace; letter-spacing: 0.5px;
        }
        .summary-card {
            border-radius: 12px;
            transition: transform 0.15s ease;
        }
        .summary-card:hover {
            transform: translateY(-2px);
        }
        .payment-history-badge {
            width: 32px; height: 32px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
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
                        <li class="nav-item"><a href="{{ route('admin.staff.index') }}" class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}"><i class="nav-icon bi bi-person-badge-fill"></i><p>Staff</p></a></li>
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
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0">
                                <i class="bi bi-receipt text-success me-2"></i>
                                <span class="invoice-number-hero">{{ $invoice->invoice_number }}</span>
                            </h3>
                        </div>
                        <div class="col-sm-6 text-end">
                            @if($invoice->status === 'draft')
                                <button type="button" class="btn btn-success me-1" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    <i class="bi bi-cash-stack me-1"></i>Record Payment
                                </button>
                                <form action="{{ route('admin.invoices.cancel', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this invoice?');">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger me-1">
                                        <i class="bi bi-x-circle me-1"></i>Cancel Invoice
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{-- Alerts --}}
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

                    {{-- Status & Summary Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="card summary-card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <span class="text-muted small fw-semibold d-block mb-1">STATUS</span>
                                    <span class="badge fs-6 px-3 py-2 {{ $invoice->status_badge_class }}">{{ $invoice->status_label }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card summary-card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <span class="text-muted small fw-semibold d-block mb-1">TOTAL AMOUNT</span>
                                    <span class="fs-4 fw-bold text-dark">৳{{ number_format($invoice->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card summary-card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <span class="text-muted small fw-semibold d-block mb-1">TOTAL PAID</span>
                                    <span class="fs-4 fw-bold text-success">৳{{ number_format($invoice->paid_total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card summary-card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <span class="text-muted small fw-semibold d-block mb-1">BALANCE DUE</span>
                                    <span class="fs-4 fw-bold {{ $invoice->balance_due > 0 ? 'text-danger' : 'text-success' }}">
                                        ৳{{ number_format($invoice->balance_due, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        {{-- Parent Info --}}
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="bi bi-person-fill text-primary me-2"></i>Parent / Guardian</h5>
                                </div>
                                <div class="card-body">
                                    @if($invoice->parent)
                                        <div class="info-group d-flex justify-content-between">
                                            <span class="text-muted fw-semibold">Name</span>
                                            <span class="fw-medium">{{ $invoice->parent->full_name }}</span>
                                        </div>
                                        <div class="info-group d-flex justify-content-between">
                                            <span class="text-muted fw-semibold">Mobile</span>
                                            <span class="fw-medium">{{ $invoice->parent->mobile ?? '—' }}</span>
                                        </div>
                                        <div class="info-group d-flex justify-content-between">
                                            <span class="text-muted fw-semibold">Email</span>
                                            <span class="fw-medium">{{ $invoice->parent->user->email ?? '—' }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Parent record not found.</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Child & Date Info --}}
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="bi bi-info-circle-fill text-info me-2"></i>Invoice Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="info-group d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">Child</span>
                                        <span class="fw-medium">{{ $invoice->child->full_name ?? '—' }}</span>
                                    </div>
                                    <div class="info-group d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">Invoice Date</span>
                                        <span class="fw-medium">{{ $invoice->invoice_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="info-group d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">Due Date</span>
                                        <span class="fw-medium">{{ $invoice->due_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="info-group d-flex justify-content-between">
                                        <span class="text-muted fw-semibold">Created</span>
                                        <span class="fw-medium">{{ $invoice->created_at->format('M d, Y \\a\\t H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Line Items --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-list-check text-success me-2"></i>Line Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">#</th>
                                            <th>Description</th>
                                            <th class="text-end pe-3">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->items as $i => $item)
                                            <tr>
                                                <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                                <td class="fw-medium">{{ $item->description }}</td>
                                                <td class="text-end pe-3 fw-semibold">৳{{ number_format($item->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="2" class="ps-3 fw-bold text-end">Total</td>
                                            <td class="text-end pe-3 fw-bold fs-5 text-dark">৳{{ number_format($invoice->total_amount, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Payment History --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-clock-history text-warning me-2"></i>Payment History</h5>
                            @if($invoice->status === 'draft' && $invoice->balance_due > 0)
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    <i class="bi bi-plus-lg me-1"></i>Record Payment
                                </button>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3">#</th>
                                            <th>Date</th>
                                            <th>Method</th>
                                            <th>Transaction ID</th>
                                            <th class="text-end">Amount</th>
                                            <th>Received By</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoice->payments as $pi => $payment)
                                            <tr>
                                                <td class="ps-3 text-muted">{{ $pi + 1 }}</td>
                                                <td>{{ $payment->paid_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">
                                                        {{ $payment->payment_method_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($payment->transaction_id)
                                                        <code class="text-dark">{{ $payment->transaction_id }}</code>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-end fw-semibold text-success">৳{{ number_format($payment->paid_amount, 2) }}</td>
                                                <td>{{ $payment->receivedBy->name ?? '—' }}</td>
                                                <td class="text-muted small">{{ $payment->remarks ?? '—' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="bi bi-cash-coin fs-3 d-block mb-2 opacity-25"></i>
                                                    No payments recorded yet.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    {{-- Record Payment Modal --}}
    @if($invoice->status === 'draft')
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.invoices.add-payment', $invoice) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-cash-stack text-success me-2"></i>Record Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Balance Due: <strong>৳{{ number_format($invoice->balance_due, 2) }}</strong>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Paid Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" name="paid_amount" class="form-control" step="0.01" min="0.01"
                                           max="{{ $invoice->balance_due }}" value="{{ $invoice->balance_due }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="paymentMethodSelect" class="form-select" required>
                                    <option value="cash" selected>Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="transactionIdGroup" style="display:none;">
                                <label class="form-label fw-semibold">Transaction ID</label>
                                <input type="text" name="transaction_id" id="transactionIdInput" class="form-control" placeholder="Enter transaction reference">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="paid_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Penalty Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" name="penalty_amount" class="form-control" step="0.01" min="0" value="0">
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2" placeholder="Optional notes about this payment..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>Confirm Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const methodSelect = document.getElementById('paymentMethodSelect');
        const txnGroup = document.getElementById('transactionIdGroup');
        const txnInput = document.getElementById('transactionIdInput');

        if (methodSelect) {
            methodSelect.addEventListener('change', function () {
                if (this.value === 'cash') {
                    txnGroup.style.display = 'none';
                    txnInput.value = '';
                } else {
                    txnGroup.style.display = 'block';
                }
            });
        }

        // Show payment modal if there were validation errors
        @if($errors->any())
            const paymentModal = document.getElementById('paymentModal');
            if (paymentModal) {
                const modal = new bootstrap.Modal(paymentModal);
                modal.show();
            }
        @endif
    });
    </script>
</body>
</html>
