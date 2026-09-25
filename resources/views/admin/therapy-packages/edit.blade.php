<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Therapy Package | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .therapy-badge-slt { background: #dbeafe; color: #1e40af; }
        .therapy-badge-aba { background: #fef3c7; color: #92400e; }
        .therapy-badge-ot  { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a></li></ul>
            </div>
        </nav>
        @include('partials.sidebar')

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6"><h3 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Therapy Package</h3></div>
                        <div class="col-sm-6 text-end">
                            <a href="{{ route('admin.therapy-packages.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Packages</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong><i class="bi bi-exclamation-octagon-fill me-2"></i>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.therapy-packages.update', $therapyPackage) }}" method="POST" id="packageForm">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-lg-8">
                                {{-- Package Info --}}
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0"><i class="bi bi-gift-fill me-2 text-primary"></i>Package Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label fw-semibold">Package Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $therapyPackage->name) }}" required>
                                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Package Price (৳) <span class="text-danger">*</span></label>
                                                <input type="number" name="price" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $therapyPackage->price) }}" min="0" required>
                                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Status</label>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $therapyPackage->is_active) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="isActive">Active</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Package Items --}}
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><i class="bi bi-list-check me-2 text-success"></i>Included Services</h5>
                                        <button type="button" class="btn btn-outline-success btn-sm" id="addItemBtn"><i class="bi bi-plus-lg me-1"></i>Add Service</button>
                                    </div>
                                    <div class="card-body">
                                        <div id="itemsContainer"></div>
                                        <div id="noItemsMsg" class="text-center text-muted py-3" style="display:none;">
                                            <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                            Click "Add Service" to include therapy services in this package.
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mb-4">
                                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle-fill me-1"></i> Update Package</button>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-info me-2"></i>Package Stats</h6></div>
                                    <div class="card-body small text-muted">
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2 d-flex justify-content-between"><span>Total Purchases:</span> <strong class="text-dark">{{ $therapyPackage->childPurchases()->count() }}</strong></li>
                                            <li class="mb-2 d-flex justify-content-between"><span>Active Purchases:</span> <strong class="text-success">{{ $therapyPackage->childPurchases()->where('status', 'active')->count() }}</strong></li>
                                            <li class="d-flex justify-content-between"><span>Created:</span> <strong class="text-dark">{{ $therapyPackage->created_at->format('M d, Y') }}</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const services = @json($services);
            const container = document.getElementById('itemsContainer');
            const noItemsMsg = document.getElementById('noItemsMsg');
            let itemIndex = 0;

            function updateNoItemsMsg() {
                noItemsMsg.style.display = container.children.length === 0 ? 'block' : 'none';
            }

            function addItem(serviceId = '', sessionCount = 1) {
                const row = document.createElement('div');
                row.className = 'row g-2 mb-2 align-items-end item-row';
                row.innerHTML = `
                    <div class="col-md-7">
                        <label class="form-label fw-semibold small">Service <span class="text-danger">*</span></label>
                        <select name="items[${itemIndex}][therapy_service_id]" class="form-select form-select-sm" required>
                            <option value="">Select Service...</option>
                            ${services.map(s => `<option value="${s.id}" ${s.id == serviceId ? 'selected' : ''}>${s.name} (${s.therapy_type.toUpperCase()} · ${s.duration_minutes}min · ৳${parseFloat(s.session_rate).toFixed(2)})</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Sessions <span class="text-danger">*</span></label>
                        <input type="number" name="items[${itemIndex}][session_count]" class="form-control form-control-sm" value="${sessionCount}" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-item-btn"><i class="bi bi-trash"></i></button>
                    </div>
                `;
                container.appendChild(row);
                itemIndex++;
                updateNoItemsMsg();

                row.querySelector('.remove-item-btn').addEventListener('click', function() {
                    row.remove();
                    updateNoItemsMsg();
                });
            }

            document.getElementById('addItemBtn').addEventListener('click', () => addItem());

            // Load existing items
            @if(old('items'))
                @foreach(old('items') as $i => $item)
                    addItem('{{ $item['therapy_service_id'] ?? '' }}', {{ $item['session_count'] ?? 1 }});
                @endforeach
            @else
                @foreach($therapyPackage->items as $item)
                    addItem('{{ $item->therapy_service_id }}', {{ $item->session_count }});
                @endforeach
            @endif

            updateNoItemsMsg();
        });
    </script>
</body>
</html>
