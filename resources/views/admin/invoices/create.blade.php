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
        .therapy-session-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .therapy-session-item:hover {
            border-color: #6366f1;
            background-color: #f5f3ff;
        }
        .therapy-session-item.selected {
            border-color: #6366f1;
            background-color: #eef2ff;
            box-shadow: 0 0 0 2px rgba(99,102,241,0.15);
        }
        .therapy-badge {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.7rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.5px; padding: 2px 8px; border-radius: 6px;
        }
        .therapy-badge.slt { background: #dbeafe; color: #1e40af; }
        .therapy-badge.aba { background: #fce7f3; color: #9d174d; }
        .therapy-badge.ot  { background: #d1fae5; color: #065f46; }
        .therapy-toggle-wrapper {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            border: 1px solid #c7d2fe;
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }
        .sessions-loading {
            text-align: center; padding: 2rem; color: #94a3b8;
        }
        .no-sessions-msg {
            text-align: center; padding: 2rem; color: #94a3b8;
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

                                {{-- Therapy Sessions Section --}}
                                <div class="card border-0 shadow-sm mt-4" id="therapyCard" style="display:none;">
                                    <div class="card-header bg-white">
                                        <div class="therapy-toggle-wrapper d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-indigo-700">
                                                    <i class="bi bi-heart-pulse me-2"></i>Include Therapy Sessions
                                                </h6>
                                                <small class="text-muted">Optionally bill completed therapy sessions on this invoice</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="includeTherapyToggle" style="width:3rem;height:1.5rem;cursor:pointer;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body" id="therapySessionsBody" style="display:none;">
                                        <div class="sessions-loading" id="therapyLoading" style="display:none;">
                                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                            Loading billable sessions...
                                        </div>
                                        <div id="therapySessionsList"></div>
                                        <div class="no-sessions-msg" id="noSessionsMsg" style="display:none;">
                                            <i class="bi bi-check-circle fs-3 d-block mb-2 opacity-25"></i>
                                            No unbilled completed sessions found for this child.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Line Items --}}
                            <div class="col-lg-7">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><i class="bi bi-list-check text-success me-2"></i>Line Items <small class="text-muted fw-normal">(Daycare, Fees, etc.)</small></h5>
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
                                                    {{-- Manual items start empty — admin adds as needed --}}
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center py-3" id="noManualItemsMsg">
                                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Click "Add Item" to add daycare charges, or toggle therapy sessions on the left.</small>
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
        const therapyCard = document.getElementById('therapyCard');
        const therapyToggle = document.getElementById('includeTherapyToggle');
        const therapySessionsBody = document.getElementById('therapySessionsBody');
        const therapyLoading = document.getElementById('therapyLoading');
        const therapySessionsList = document.getElementById('therapySessionsList');
        const noSessionsMsg = document.getElementById('noSessionsMsg');
        const noManualItemsMsg = document.getElementById('noManualItemsMsg');

        let itemIndex = 0;
        let therapySessionsData = [];
        let selectedSessionIds = new Set();

        // Load children when parent changes
        parentSelect.addEventListener('change', function () {
            const parentId = this.value;
            childSelect.innerHTML = '<option value="">— Loading... —</option>';

            // Reset therapy section
            therapyCard.style.display = 'none';
            therapyToggle.checked = false;
            therapySessionsBody.style.display = 'none';
            therapySessionsList.innerHTML = '';
            selectedSessionIds.clear();
            recalcTotal();

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

        // When child changes, show therapy card and reset
        childSelect.addEventListener('change', function () {
            const childId = this.value;

            // Reset therapy state
            therapyToggle.checked = false;
            therapySessionsBody.style.display = 'none';
            therapySessionsList.innerHTML = '';
            selectedSessionIds.clear();
            therapySessionsData = [];
            recalcTotal();

            if (childId) {
                therapyCard.style.display = 'block';
            } else {
                therapyCard.style.display = 'none';
            }
        });

        // Therapy toggle
        therapyToggle.addEventListener('change', function () {
            if (this.checked) {
                therapySessionsBody.style.display = 'block';
                loadTherapySessions();
            } else {
                therapySessionsBody.style.display = 'none';
                // Clear selections when toggling off
                selectedSessionIds.clear();
                removeTherapyHiddenInputs();
                recalcTotal();
            }
        });

        // Load billable therapy sessions via AJAX
        function loadTherapySessions() {
            const childId = childSelect.value;
            if (!childId) return;

            therapyLoading.style.display = 'block';
            noSessionsMsg.style.display = 'none';
            therapySessionsList.innerHTML = '';

            fetch(`{{ route('admin.invoices.get-therapy-sessions') }}?child_id=${childId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(r => r.json())
            .then(sessions => {
                therapyLoading.style.display = 'none';
                therapySessionsData = sessions;

                if (sessions.length === 0) {
                    noSessionsMsg.style.display = 'block';
                    return;
                }

                sessions.forEach(session => {
                    const typeClass = session.therapy_type.toLowerCase();
                    const div = document.createElement('div');
                    div.className = 'therapy-session-item d-flex align-items-center';
                    div.dataset.sessionId = session.id;
                    div.dataset.rate = session.raw_rate;
                    div.innerHTML = `
                        <div class="form-check me-3">
                            <input class="form-check-input therapy-session-checkbox" type="checkbox"
                                   value="${session.id}" id="therapy_${session.id}" style="width:1.2rem;height:1.2rem;cursor:pointer;">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-semibold">${session.service_name}</span>
                                <span class="therapy-badge ${typeClass}">${session.therapy_type}</span>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>${session.session_date}
                                <span class="mx-1">·</span>
                                <i class="bi bi-person me-1"></i>${session.therapist}
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-dark">৳${session.session_rate}</span>
                        </div>
                    `;

                    // Click anywhere on the card to toggle
                    div.addEventListener('click', function (e) {
                        if (e.target.closest('.form-check-input')) return; // let checkbox handle itself
                        const cb = this.querySelector('.therapy-session-checkbox');
                        cb.checked = !cb.checked;
                        cb.dispatchEvent(new Event('change'));
                    });

                    const checkbox = div.querySelector('.therapy-session-checkbox');
                    checkbox.addEventListener('change', function () {
                        if (this.checked) {
                            selectedSessionIds.add(session.id);
                            div.classList.add('selected');
                        } else {
                            selectedSessionIds.delete(session.id);
                            div.classList.remove('selected');
                        }
                        updateTherapyHiddenInputs();
                        recalcTotal();
                    });

                    therapySessionsList.appendChild(div);
                });

                // Add "Select All" button
                if (sessions.length > 1) {
                    const selectAllDiv = document.createElement('div');
                    selectAllDiv.className = 'text-end mt-2';
                    selectAllDiv.innerHTML = `
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllTherapyBtn">
                            <i class="bi bi-check-all me-1"></i>Select All
                        </button>
                    `;
                    therapySessionsList.prepend(selectAllDiv);

                    document.getElementById('selectAllTherapyBtn').addEventListener('click', function () {
                        const allChecked = selectedSessionIds.size === sessions.length;
                        document.querySelectorAll('.therapy-session-checkbox').forEach(cb => {
                            cb.checked = !allChecked;
                            cb.dispatchEvent(new Event('change'));
                        });
                        this.innerHTML = allChecked
                            ? '<i class="bi bi-check-all me-1"></i>Select All'
                            : '<i class="bi bi-x-lg me-1"></i>Deselect All';
                    });
                }
            })
            .catch(() => {
                therapyLoading.style.display = 'none';
                noSessionsMsg.style.display = 'block';
                noSessionsMsg.innerHTML = '<i class="bi bi-exclamation-triangle fs-3 d-block mb-2 text-warning opacity-50"></i>Failed to load therapy sessions.';
            });
        }

        // Manage hidden inputs for selected therapy session IDs
        function updateTherapyHiddenInputs() {
            removeTherapyHiddenInputs();
            selectedSessionIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'therapy_session_ids[]';
                input.value = id;
                input.className = 'therapy-hidden-input';
                document.getElementById('invoiceForm').appendChild(input);
            });
        }

        function removeTherapyHiddenInputs() {
            document.querySelectorAll('.therapy-hidden-input').forEach(el => el.remove());
        }

        // Add manual line item
        addItemBtn.addEventListener('click', function () {
            noManualItemsMsg.style.display = 'none';
            const row = document.createElement('tr');
            row.classList.add('item-row');
            row.innerHTML = `
                <td class="ps-3">
                    <input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="e.g., Monthly Tuition" required>
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
            row.querySelector('.item-amount').addEventListener('input', recalcTotal);
        });

        // Remove line item (delegated)
        itemsBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-item-btn');
            if (btn) {
                btn.closest('tr').remove();
                recalcTotal();
                // Show helper msg if no manual items
                if (itemsBody.querySelectorAll('.item-row').length === 0) {
                    noManualItemsMsg.style.display = 'block';
                }
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

            // Sum manual items
            document.querySelectorAll('.item-amount').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            // Sum selected therapy sessions
            selectedSessionIds.forEach(id => {
                const session = therapySessionsData.find(s => s.id === id);
                if (session) {
                    total += session.raw_rate;
                }
            });

            invoiceTotal.textContent = '৳' + total.toFixed(2);
        }

        // Trigger parent change if old value is set (validation failure)
        if (parentSelect.value) {
            parentSelect.dispatchEvent(new Event('change'));
        }
    });
    </script>
</body>
</html>
