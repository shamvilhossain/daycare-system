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
        /* Description combobox (catalog dropdown + free text) */
        .items-wrap { overflow: visible; }
        .item-combo { position: relative; }
        .item-combo .combo-toggle { border-color: var(--bs-border-color); color: #64748b; }
        .combo-menu {
            display: none; position: absolute; left: 0; right: 0; top: calc(100% + 4px); z-index: 1060;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
            box-shadow: 0 12px 30px -8px rgba(15,23,42,.18); max-height: 280px; overflow-y: auto; padding: 6px;
        }
        .combo-menu.show { display: block; }
        .combo-option {
            display: flex; align-items: center; justify-content: space-between; gap: .75rem;
            padding: .5rem .65rem; border-radius: 8px; cursor: pointer; font-size: .9rem;
        }
        .combo-option:hover, .combo-option.active { background: #ecfdf5; }
        .combo-option .amt { font-size: .8rem; color: #64748b; white-space: nowrap; }
        .combo-option mark { padding: 0; background: #fef08a; border-radius: 2px; }
        .combo-option.add-new { color: #047857; font-weight: 600; border-top: 1px dashed #e2e8f0; margin-top: 4px; }
        .combo-empty { padding: .6rem .65rem; color: #94a3b8; font-size: .85rem; }
        .combo-head { padding: .3rem .65rem .4rem; font-size: .7rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #94a3b8; }
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
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.invoice-item-presets.index') }}" class="btn btn-sm btn-outline-secondary" title="Edit, hide or remove saved item names">
                                                <i class="bi bi-gear me-1"></i>Manage items
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                                                <i class="bi bi-plus-lg me-1"></i>Add Item
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        @error('items')
                                            <div class="alert alert-danger m-3 mb-0 py-2 small">{{ $message }}</div>
                                        @enderror
                                        <div class="items-wrap">
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
                                        <div class="px-3 pb-2 small text-muted" id="itemsHint" style="display:none">
                                            <i class="bi bi-lightbulb me-1"></i>Pick an item from the list or type a new one — new items are saved to the list automatically.
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

        // ---------- Description combobox: pick from the catalog or type a new item ----------
        // Dropdown sources: saved free-text items, therapy packages, single therapy sessions
        const CATALOG = @json($itemCatalog);
        const GROUPS = { preset: 'Saved items', package: 'Therapy packages', service: 'Single therapy sessions' };
        const itemsHint = document.getElementById('itemsHint');
        const escapeHtml = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const fmtAmount = n => '৳' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function highlight(name, q) {
            if (!q) return escapeHtml(name);
            const i = name.toLowerCase().indexOf(q.toLowerCase());
            if (i < 0) return escapeHtml(name);
            return escapeHtml(name.slice(0, i)) + '<mark>' + escapeHtml(name.slice(i, i + q.length)) + '</mark>' + escapeHtml(name.slice(i + q.length));
        }

        function setupCombo(row) {
            const input = row.querySelector('.item-desc');
            const amount = row.querySelector('.item-amount');
            const menu = row.querySelector('.combo-menu');
            const pkgInput = row.querySelector('.item-package-id');
            const svcInput = row.querySelector('.item-service-id');
            const kindLabel = row.querySelector('.item-kind');
            let options = [];
            let active = -1;
            let linkedName = null; // name of the linked package/service, if any

            function render() {
                const q = input.value.trim();
                const ql = q.toLowerCase();
                const matches = CATALOG.filter(p => !ql || p.name.toLowerCase().includes(ql) || (p.detail || '').toLowerCase().includes(ql));
                const exact = CATALOG.some(p => p.name.toLowerCase() === ql);
                options = matches.slice(0, 80);
                if (q && !exact) options.push({ kind: 'new', name: q });

                let html = '';
                let lastKind = null;
                options.forEach((o, i) => {
                    if (o.kind !== 'new' && o.kind !== lastKind) {
                        html += `<div class="combo-head">${GROUPS[o.kind]}</div>`;
                        lastKind = o.kind;
                    }
                    if (o.kind === 'new') {
                        html += `<div class="combo-option add-new ${i === active ? 'active' : ''}" data-i="${i}"><span><i class="bi bi-plus-circle me-1"></i>Add “${escapeHtml(o.name)}” as a new item</span></div>`;
                        return;
                    }
                    const icon = o.kind === 'package' ? 'bi-box2-heart text-info' : (o.kind === 'service' ? 'bi-heart-pulse text-danger' : 'bi-tag text-muted');
                    html += `<div class="combo-option ${i === active ? 'active' : ''}" data-i="${i}">
                        <span><i class="bi ${icon} me-2"></i>${highlight(o.name, q)}${o.detail ? `<div class="small text-muted ms-4">${escapeHtml(o.detail)}</div>` : ''}</span>
                        ${o.amount !== null ? `<span class="amt">${fmtAmount(o.amount)}</span>` : ''}
                    </div>`;
                });
                if (!options.length) html = '<div class="combo-empty">No saved items yet — type a name to add one.</div>';
                menu.innerHTML = html;
            }

            // Link/unlink the row to a therapy package or single session
            function link(o) {
                pkgInput.value = o && o.kind === 'package' ? o.id : '';
                svcInput.value = o && o.kind === 'service' ? o.id : '';
                linkedName = o && (o.kind === 'package' || o.kind === 'service') ? o.name : null;
                if (o && o.kind === 'package') {
                    kindLabel.innerHTML = `<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle"><i class="bi bi-box2-heart me-1"></i>Therapy package</span>
                        <span class="text-muted">Buys ${escapeHtml(o.detail)} for the selected child when the invoice is created.</span>`;
                } else if (o && o.kind === 'service') {
                    kindLabel.innerHTML = `<span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle"><i class="bi bi-heart-pulse me-1"></i>Single therapy session</span>
                        <span class="text-muted">Advance payment for one session. For sessions already held, use “Include therapy sessions” instead.</span>`;
                } else {
                    kindLabel.innerHTML = '';
                }
            }
            row.linkCatalog = link;

            function open() { active = -1; render(); menu.classList.add('show'); }
            function close() { menu.classList.remove('show'); active = -1; }

            function choose(i) {
                const o = options[i];
                if (!o) return;
                input.value = o.name;
                link(o);
                const isTherapy = o.kind === 'package' || o.kind === 'service';
                // Therapy items always take their catalog price; saved items only fill an empty amount
                if (o.amount !== null && o.amount !== undefined && (isTherapy || !amount.value)) {
                    amount.value = o.amount.toFixed(2);
                    recalcTotal();
                }
                close();
                (o.kind !== 'new' && amount.value ? input : amount).focus();
            }

            input.addEventListener('focus', open);
            input.addEventListener('input', () => {
                if (linkedName !== null && input.value !== linkedName) link(null); // edited away from the package/session
                active = -1; render(); menu.classList.add('show');
            });
            input.addEventListener('keydown', e => {
                if (!menu.classList.contains('show') && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) { open(); }
                if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active + 1, options.length - 1); render(); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active - 1, 0); render(); }
                else if (e.key === 'Enter' && menu.classList.contains('show') && active >= 0) { e.preventDefault(); choose(active); }
                else if (e.key === 'Escape' || e.key === 'Tab') { close(); }
            });
            // mousedown (not click) so the input doesn't blur before the choice registers
            menu.addEventListener('mousedown', e => {
                const opt = e.target.closest('.combo-option');
                if (opt) { e.preventDefault(); choose(Number(opt.dataset.i)); }
            });
            row.querySelector('.combo-toggle').addEventListener('click', () => {
                if (menu.classList.contains('show')) { close(); } else { input.focus(); open(); }
            });
            document.addEventListener('mousedown', e => { if (!row.querySelector('.item-combo').contains(e.target)) close(); });
        }

        // Add manual line item
        function addItemRow(description = '', amountValue = '', packageId = '', serviceId = '') {
            noManualItemsMsg.style.display = 'none';
            itemsHint.style.display = 'block';
            const row = document.createElement('tr');
            row.classList.add('item-row');
            row.innerHTML = `
                <td class="ps-3">
                    <div class="item-combo">
                        <div class="input-group">
                            <input type="text" name="items[${itemIndex}][description]" class="form-control item-desc"
                                   placeholder="Select or type an item…" autocomplete="off" maxlength="255" required>
                            <button type="button" class="btn btn-outline-secondary combo-toggle" tabindex="-1" title="Show saved items">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                        <div class="combo-menu"></div>
                        <input type="hidden" name="items[${itemIndex}][therapy_package_id]" class="item-package-id">
                        <input type="hidden" name="items[${itemIndex}][therapy_service_id]" class="item-service-id">
                        <div class="item-kind small mt-1"></div>
                    </div>
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
            row.querySelector('.item-desc').value = description;
            row.querySelector('.item-amount').value = amountValue;
            itemsBody.appendChild(row);
            itemIndex++;
            row.querySelector('.item-amount').addEventListener('input', recalcTotal);
            setupCombo(row);
            // Re-link a restored therapy row (after a validation error)
            const linked = CATALOG.find(c => (packageId && c.kind === 'package' && String(c.id) === String(packageId))
                || (serviceId && c.kind === 'service' && String(c.id) === String(serviceId)));
            if (linked) row.linkCatalog(linked);
            return row;
        }

        addItemBtn.addEventListener('click', function () {
            addItemRow().querySelector('.item-desc').focus();
        });

        // Restore rows after a validation error
        (@json(array_values(old('items', [])))).forEach(it => addItemRow(it.description ?? '', it.amount ?? '', it.therapy_package_id ?? '', it.therapy_service_id ?? ''));
        recalcTotal();

        // Remove line item (delegated)
        itemsBody.addEventListener('click', function (e) {
            const btn = e.target.closest('.remove-item-btn');
            if (btn) {
                btn.closest('tr').remove();
                recalcTotal();
                // Show helper msg if no manual items
                if (itemsBody.querySelectorAll('.item-row').length === 0) {
                    noManualItemsMsg.style.display = 'block';
                    itemsHint.style.display = 'none';
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
