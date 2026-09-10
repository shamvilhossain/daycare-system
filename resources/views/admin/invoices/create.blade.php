<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .page-banner h2 { font-size: 1.45rem; font-weight: 700; margin-bottom: 0.25rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.9rem; opacity: 0.88; position: relative; z-index: 1; margin: 0; }
        .item-row { transition: background-color 0.15s ease; }
        .item-row:hover { background-color: #f8fafc; }
        .total-display {
            font-size: 1.6rem; font-weight: 800; color: #065f46;
            font-family: 'Inter', monospace;
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
                    <div class="page-banner d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="bi bi-plus-circle me-2"></i>Create New Invoice</h2>
                            <p>Generate a new invoice for a parent's child with itemized charges</p>
                        </div>
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-light fw-bold shadow-sm" style="position:relative;z-index:1;">
                            <i class="bi bi-arrow-left me-1"></i> Back to Invoices
                        </a>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.invoices.store') }}" id="invoiceForm">
                        @csrf
                        <div class="row g-4">
                            {{-- Left Column: Parent, Child, Dates --}}
                            <div class="col-lg-5">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0"><i class="bi bi-person-fill text-primary me-2"></i>Invoice Details</h5>
                                    </div>
                                    <div class="card-body">
                                        {{-- Parent Selection --}}
                                        <div class="mb-3">
                                            <label for="parent_id" class="form-label fw-semibold">Parent / Guardian <span class="text-danger">*</span></label>
                                            <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror" required>
                                                <option value="">— Select Parent —</option>
                                                @foreach($parents as $parent)
                                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                        {{ $parent->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        {{-- Child Selection (dependent) --}}
                                        <div class="mb-3">
                                            <label for="child_id" class="form-label fw-semibold">Child <span class="text-danger">*</span></label>
                                            <select name="child_id" id="child_id" class="form-select @error('child_id') is-invalid @enderror" required disabled>
                                                <option value="">— Select parent first —</option>
                                            </select>
                                            @error('child_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <hr class="my-3">

                                        {{-- Invoice Date --}}
                                        <div class="mb-3">
                                            <label for="invoice_date" class="form-label fw-semibold">Invoice Date <span class="text-danger">*</span></label>
                                            <input type="date" name="invoice_date" id="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                            @error('invoice_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        {{-- Due Date --}}
                                        <div class="mb-3">
                                            <label for="due_date" class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
                                            <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                                            @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Line Items --}}
                            <div class="col-lg-7">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><i class="bi bi-list-check text-success me-2"></i>Line Items</h5>
                                        <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                                            <i class="bi bi-plus-lg me-1"></i>Add Item
                                        </button>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table mb-0" id="itemsTable">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="ps-3" style="width:55%">Description</th>
                                                        <th style="width:30%">Amount (৳)</th>
                                                        <th class="text-center" style="width:15%">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemsBody">
                                                    <tr class="item-row">
                                                        <td class="ps-3">
                                                            <input type="text" name="items[0][description]" class="form-control" placeholder="e.g., Monthly Tuition" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[0][amount]" class="form-control item-amount" placeholder="0.00" step="0.01" min="0.01" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" disabled title="At least one item required">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                        <span class="text-muted fw-semibold">Invoice Total</span>
                                        <span class="total-display" id="invoiceTotal">৳0.00</span>
                                    </div>
                                </div>

                                {{-- Submit --}}
                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-lg me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg px-4">
                                        <i class="bi bi-check-lg me-1"></i>Create Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const parentSelect = document.getElementById('parent_id');
        const childSelect = document.getElementById('child_id');
        const itemsBody = document.getElementById('itemsBody');
        const addItemBtn = document.getElementById('addItemBtn');
        const invoiceTotal = document.getElementById('invoiceTotal');

        let itemIndex = 1;

        // Load children when parent changes
        parentSelect.addEventListener('change', function () {
            const parentId = this.value;
            childSelect.innerHTML = '<option value="">— Loading... —</option>';

            if (!parentId) {
                childSelect.innerHTML = '<option value="">— Select parent first —</option>';
                childSelect.disabled = true;
                return;
            }

            fetch(`{{ route('admin.invoices.get-children') }}?parent_id=${parentId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(children => {
                childSelect.innerHTML = '<option value="">— Select Child —</option>';
                children.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = `${c.first_name} ${c.last_name}`;
                    childSelect.appendChild(opt);
                });
                childSelect.disabled = false;
            })
            .catch(() => {
                childSelect.innerHTML = '<option value="">— Failed to load —</option>';
                childSelect.disabled = true;
            });
        });

        // Add line item
        addItemBtn.addEventListener('click', function () {
            const row = document.createElement('tr');
            row.classList.add('item-row');
            row.innerHTML = `
                <td class="ps-3">
                    <input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="e.g., Late Pickup Fee" required>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][amount]" class="form-control item-amount" placeholder="0.00" step="0.01" min="0.01" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" title="Remove item">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            itemsBody.appendChild(row);
            itemIndex++;
            updateRemoveButtons();
            row.querySelector('.item-amount').addEventListener('input', recalcTotal);
        });

        // Remove line item (delegated)
        itemsBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-item-btn');
            if (btn && !btn.disabled) {
                btn.closest('tr').remove();
                updateRemoveButtons();
                recalcTotal();
            }
        });

        // Recalculate total on amount input
        itemsBody.addEventListener('input', function (e) {
            if (e.target.classList.contains('item-amount')) {
                recalcTotal();
            }
        });

        function recalcTotal() {
            let total = 0;
            document.querySelectorAll('.item-amount').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            invoiceTotal.textContent = '৳' + total.toFixed(2);
        }

        function updateRemoveButtons() {
            const rows = itemsBody.querySelectorAll('.item-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-item-btn');
                btn.disabled = rows.length <= 1;
                btn.title = rows.length <= 1 ? 'At least one item required' : 'Remove item';
            });
        }

        // Trigger parent change if old value is set (validation failure)
        if (parentSelect.value) {
            parentSelect.dispatchEvent(new Event('change'));
        }
    });
    </script>
</body>
</html>
