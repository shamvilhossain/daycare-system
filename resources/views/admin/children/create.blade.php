<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Child | Daycare System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .parent-row { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem; margin-bottom: 0.75rem; }
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
                        <div class="col-sm-6"><h3 class="mb-0">Add New Child</h3></div>
                        <div class="col-sm-6 text-end"><a href="{{ route('admin.children.index') }}" class="btn btn-secondary">Back to Children</a></div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <form action="{{ route('admin.children.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

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

                        {{-- Child Info --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header"><h5 class="mb-0"><i class="bi bi-person-fill me-2"></i>Child Information</h5></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}" required>
                                        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Allergies</label>
                                        <textarea name="allergies" class="form-control" rows="2">{{ old('allergies') }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Medical Notes</label>
                                        <textarea name="medical_notes" class="form-control" rows="2">{{ old('medical_notes') }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Photo</label>
                                        <input type="file" name="photo" id="photoInput" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/gif,image/webp">
                                        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        <div id="photoPreview" class="mt-2" style="display:none;">
                                            <img id="photoPreviewImg" src="" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #e5e7eb;" alt="Preview">
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
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
                                        <input type="text" name="ec_name" class="form-control" value="{{ old('ec_name') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Relationship</label>
                                        <input type="text" name="ec_relationship" class="form-control" value="{{ old('ec_relationship') }}" placeholder="e.g. Grandmother, Uncle">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="ec_phone" class="form-control" value="{{ old('ec_phone') }}">
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="ec_authorized_pickup" id="ecPickup">
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
                                {{-- JS will add rows here --}}
                            </div>
                        </div>

                        <div class="text-end mb-4">
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Create Child</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('parentContainer');
            const addBtn = document.getElementById('addParentBtn');
            let idx = 0;

            @php
                $parentOptionsJson = $parents->map(fn($p) => ['id' => $p->id, 'name' => $p->full_name]);
            @endphp
            const parentOptions = @json($parentOptionsJson);

            addBtn.addEventListener('click', function() {
                const row = document.createElement('div');
                row.className = 'parent-row';
                let options = '<option value="">Select Parent...</option>';
                parentOptions.forEach(p => { options += `<option value="${p.id}">${p.name}</option>`; });

                row.innerHTML = `
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <select name="parent_ids[${idx}]" class="form-select form-select-sm" required>${options}</select>
                        </div>
                        <div class="col-md-3">
                            <select name="relationships[${idx}]" class="form-select form-select-sm">
                                <option value="">Relationship...</option>
                                <option value="mother">Mother</option>
                                <option value="father">Father</option>
                                <option value="step_parent">Step Parent</option>
                                <option value="grandparent">Grandparent</option>
                                <option value="legal_guardian">Legal Guardian</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_primary[${idx}]"><label class="form-check-label">Primary</label></div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="can_pickup[${idx}]"><label class="form-check-label">Pickup</label></div>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-parent-btn"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>`;
                container.appendChild(row);
                idx++;
            });

            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-parent-btn')) {
                    e.target.closest('.parent-row').remove();
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
                            document.getElementById('photoPreview').style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</body>
</html>
