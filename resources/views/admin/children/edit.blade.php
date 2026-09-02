<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Child | Daycare System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .parent-row, .doc-row { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem; margin-bottom: 0.75rem; }
        .current-photo { width: 80px; height: 80px; border-radius: 12px; object-fit: cover; border: 2px solid #e5e7eb; }
        .doc-icon-badge { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a></li></ul>
            </div>
        </nav>

        {{-- Sidebar --}}
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand"><a href="/" class="brand-link"><span class="brand-text font-weight-light">DaycareSystem</span></a></div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-header">MAIN</li>
                        <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link"><i class="nav-icon bi bi-grid-1x2-fill"></i><p>Dashboard</p></a></li>
                        <li class="nav-header">MANAGEMENT</li>
                        <li class="nav-item"><a href="{{ route('admin.children.index') }}" class="nav-link active"><i class="nav-icon bi bi-people-fill"></i><p>Children</p></a></li>
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
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">Edit Child — {{ $child->full_name }}</h3></div>
                        <div class="col-sm-6 text-end"><a href="{{ route('admin.children.index') }}" class="btn btn-secondary">Back to Children</a></div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.children.update', $child) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Child Info --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header"><h5 class="mb-0"><i class="bi bi-person-fill me-2"></i>Child Information</h5></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $child->first_name) }}" required>
                                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $child->last_name) }}" required>
                                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $child->date_of_birth ? $child->date_of_birth->format('Y-m-d') : '') }}" required>
                                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Allergies</label>
                                        <textarea name="allergies" class="form-control" rows="2">{{ old('allergies', $child->allergies) }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Medical Notes</label>
                                        <textarea name="medical_notes" class="form-control" rows="2">{{ old('medical_notes', $child->medical_notes) }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Photo</label>
                                        @if($child->photo_url)
                                            <div class="mb-2" id="currentPhotoWrap">
                                                <img src="{{ asset('storage/' . $child->photo_url) }}" id="photoPreviewImg" class="current-photo" alt="">
                                            </div>
                                        @else
                                            <div class="mb-2" id="currentPhotoWrap" style="display:none;">
                                                <img src="" id="photoPreviewImg" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #e5e7eb;" alt="Preview">
                                            </div>
                                        @endif
                                        <input type="file" name="photo" id="photoInput" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/gif,image/webp">
                                        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ old('is_active', $child->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="isActive">Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Emergency Contact --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header"><h5 class="mb-0"><i class="bi bi-telephone-fill me-2"></i>Emergency Contact</h5></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Contact Name</label>
                                        <input type="text" name="ec_name" class="form-control" value="{{ old('ec_name', $child->ec_name) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Relationship</label>
                                        <input type="text" name="ec_relationship" class="form-control" value="{{ old('ec_relationship', $child->ec_relationship) }}" placeholder="e.g. Grandmother, Uncle">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="ec_phone" class="form-control" value="{{ old('ec_phone', $child->ec_phone) }}">
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="ec_authorized_pickup" id="ecPickup" {{ old('ec_authorized_pickup', $child->ec_authorized_pickup) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ecPickup">Authorized to pick up child</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Guardian Link --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Linked Guardians</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addParentBtn"><i class="bi bi-plus-lg me-1"></i>Add Guardian</button>
                            </div>
                            <div class="card-body" id="parentContainer">
                                {{-- Existing parents rendered by JS --}}
                            </div>
                        </div>

                        {{-- Documents & Attachments --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0"><i class="bi bi-file-earmark-text-fill me-2"></i>Documents & Attachments</h5>
                                    <small class="text-muted">Manage child records, medical clearances, immunization cards, and agreements</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addDocBtn"><i class="bi bi-plus-lg me-1"></i>Attach New Document</button>
                            </div>
                            <div class="card-body">
                                {{-- Existing Documents Table --}}
                                @if($child->documents && $child->documents->count() > 0)
                                    <div class="table-responsive mb-3">
                                        <table class="table table-hover align-middle border rounded">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Document</th>
                                                    <th>Type</th>
                                                    <th>Uploaded</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($child->documents as $doc)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="doc-icon-badge bg-primary-subtle text-primary">
                                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                                </div>
                                                                <div>
                                                                    <span class="fw-semibold text-dark">{{ $doc->name }}</span>
                                                                    <small class="text-muted d-block font-monospace">{{ basename($doc->file_url) }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $doc->doc_type_badge_class }}">{{ $doc->doc_type_label }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted small">{{ $doc->created_at ? $doc->created_at->format('M d, Y') : '—' }}</span>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('admin.documents.download', $doc) }}" class="btn btn-outline-secondary" title="Download Document">
                                                                    <i class="bi bi-download"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-outline-danger" onclick="deleteExistingDoc('{{ route('admin.documents.destroy', $doc) }}', '{{ addslashes($doc->name) }}')" title="Delete Document">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-light border text-center py-3 mb-3 text-muted">
                                        <i class="bi bi-folder2-open fs-3 d-block mb-1 text-secondary"></i>
                                        No existing documents on file for this child.
                                    </div>
                                @endif

                                {{-- New Documents to add --}}
                                <div id="documentContainer">
                                    {{-- JS will add new document rows here --}}
                                </div>
                            </div>
                        </div>

                        <div class="text-end mb-4">
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Update Child</button>
                        </div>
                    </form>

                    {{-- Standalone Document Delete Form --}}
                    <form id="deleteDocForm" method="POST" style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function deleteExistingDoc(deleteUrl, docName) {
            if (confirm(`Are you sure you want to delete "${docName}"?`)) {
                const form = document.getElementById('deleteDocForm');
                form.action = deleteUrl;
                form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Guardians
            const container = document.getElementById('parentContainer');
            const addBtn = document.getElementById('addParentBtn');
            let idx = 0;

            @php
                $allParentsJson = $parents->map(fn($p) => ['id' => $p->id, 'name' => $p->full_name]);
                $linkedParentsJson = $child->parents->map(fn($p) => [
                    'id' => $p->id,
                    'relationship' => $p->pivot->relationship,
                    'is_primary' => $p->pivot->is_primary,
                    'can_pickup' => $p->pivot->can_pickup,
                ]);
            @endphp
            const allParents = @json($allParentsJson);
            const linkedParents = @json($linkedParentsJson);

            function addRow(data = null) {
                const row = document.createElement('div');
                row.className = 'parent-row';
                let options = '<option value="">Select Parent...</option>';
                allParents.forEach(p => {
                    const sel = data && data.id == p.id ? 'selected' : '';
                    options += `<option value="${p.id}" ${sel}>${p.name}</option>`;
                });

                const rels = ['mother','father','step_parent','grandparent','legal_guardian','other'];
                let relOpts = '<option value="">Relationship...</option>';
                rels.forEach(r => {
                    const sel = data && data.relationship == r ? 'selected' : '';
                    relOpts += `<option value="${r}" ${sel}>${r.replace('_',' ').replace(/\b\w/g,l=>l.toUpperCase())}</option>`;
                });

                row.innerHTML = `
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3"><select name="parent_ids[${idx}]" class="form-select form-select-sm" required>${options}</select></div>
                        <div class="col-md-3"><select name="relationships[${idx}]" class="form-select form-select-sm">${relOpts}</select></div>
                        <div class="col-md-2"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_primary[${idx}]" ${data && data.is_primary ? 'checked' : ''}><label class="form-check-label">Primary</label></div></div>
                        <div class="col-md-2"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="can_pickup[${idx}]" ${data && data.can_pickup ? 'checked' : ''}><label class="form-check-label">Pickup</label></div></div>
                        <div class="col-md-2 text-end"><button type="button" class="btn btn-sm btn-outline-danger remove-parent-btn"><i class="bi bi-x-lg"></i></button></div>
                    </div>`;
                container.appendChild(row);
                idx++;
            }

            // Render existing linked parents
            linkedParents.forEach(p => addRow(p));

            addBtn.addEventListener('click', () => addRow());

            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-parent-btn')) {
                    e.target.closest('.parent-row').remove();
                }
            });

            // Documents
            const docContainer = document.getElementById('documentContainer');
            const addDocBtn = document.getElementById('addDocBtn');
            let docIdx = 0;

            addDocBtn.addEventListener('click', function() {
                const row = document.createElement('div');
                row.className = 'doc-row';
                row.innerHTML = `
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label small mb-1 fw-semibold">Document Name</label>
                            <input type="text" name="documents[${docIdx}][name]" class="form-control form-control-sm" placeholder="e.g. Immunization Record" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1 fw-semibold">Document Type</label>
                            <select name="documents[${docIdx}][doc_type]" class="form-select form-select-sm" required>
                                <option value="birth_certificate">Birth Certificate</option>
                                <option value="medical_form">Medical Form / Immunization</option>
                                <option value="custody_agreement">Custody Agreement</option>
                                <option value="other" selected>Other</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1 fw-semibold">Expiry Date</label>
                            <input type="date" name="documents[${docIdx}][expiry_date]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1 fw-semibold">File (PDF, Doc, Image)</label>
                            <input type="file" name="documents[${docIdx}][file]" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" required>
                        </div>
                        <div class="col-md-1 text-end pt-3">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-doc-btn" title="Remove"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>`;
                docContainer.appendChild(row);
                docIdx++;
            });

            docContainer.addEventListener('click', function(e) {
                if (e.target.closest('.remove-doc-btn')) {
                    e.target.closest('.doc-row').remove();
                }
            });

            // Photo preview
            const photoInput = document.getElementById('photoInput');
            if (photoInput) {
                photoInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            document.getElementById('photoPreviewImg').src = e.target.result;
                            document.getElementById('currentPhotoWrap').style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</body>
</html>
