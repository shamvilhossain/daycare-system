<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Therapy Package | KinderCare</title>
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
                        <div class="col-sm-6"><h3 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Purchase Therapy Package</h3></div>
                        <div class="col-sm-6 text-end">
                            <a href="{{ route('admin.child-therapy-packages.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i> Back to Packages</a>
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

                    <form action="{{ route('admin.child-therapy-packages.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-lg-8">
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0"><i class="bi bi-box-seam-fill me-2 text-primary"></i>Purchase Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Child <span class="text-danger">*</span></label>
                                                <select name="child_id" class="form-select @error('child_id') is-invalid @enderror" required>
                                                    <option value="">Select Child...</option>
                                                    @foreach($children as $child)
                                                        <option value="{{ $child->id }}" {{ old('child_id') == $child->id ? 'selected' : '' }}>
                                                            {{ $child->full_name }} ({{ $child->formatted_age }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('child_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Therapy Package <span class="text-danger">*</span></label>
                                                <select name="therapy_package_id" id="packageSelect" class="form-select @error('therapy_package_id') is-invalid @enderror" required>
                                                    <option value="">Select Package...</option>
                                                    @foreach($packages as $pkg)
                                                        <option value="{{ $pkg->id }}"
                                                            data-price="{{ $pkg->price }}"
                                                            data-items="{{ $pkg->items->map(fn($i) => $i->service->name . ' × ' . $i->session_count)->implode(', ') }}"
                                                            {{ old('therapy_package_id') == $pkg->id ? 'selected' : '' }}>
                                                            {{ $pkg->name }} (৳{{ number_format($pkg->price, 2) }} · {{ $pkg->total_sessions }} sessions)
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('therapy_package_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Purchase Price (৳) <span class="text-danger">*</span></label>
                                                <input type="number" name="purchase_price" id="purchasePrice" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror" value="{{ old('purchase_price') }}" min="0" placeholder="0.00" required>
                                                @error('purchase_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                <small class="text-muted">Can differ from catalog price (discounts, etc.)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Purchase Date <span class="text-danger">*</span></label>
                                                <input type="date" name="purchased_at" class="form-control @error('purchased_at') is-invalid @enderror" value="{{ old('purchased_at', date('Y-m-d')) }}" required>
                                                @error('purchased_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Expiry Date</label>
                                                <input type="date" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at') }}">
                                                @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                                <small class="text-muted">Leave blank for no expiry</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mb-4">
                                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-cart-check-fill me-1"></i> Purchase Package</button>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                {{-- Package Preview --}}
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-eye text-info me-2"></i>Package Preview</h6></div>
                                    <div class="card-body" id="previewBody">
                                        <div class="text-muted small text-center py-3" id="previewPrompt">
                                            <i class="bi bi-box-seam fs-3 d-block mb-1 text-secondary"></i>
                                            Select a package to preview its contents.
                                        </div>
                                        <div id="previewContent" style="display:none;">
                                            <p class="small text-muted mb-1"><strong>Included services:</strong></p>
                                            <p class="small" id="previewItems"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-lightbulb text-warning me-2"></i>Notes</h6></div>
                                    <div class="card-body small text-muted">
                                        <ul class="ps-3 mb-0">
                                            <li class="mb-2"><strong>Purchase price</strong> is editable — use for discounts or negotiated rates.</li>
                                            <li class="mb-2">Package composition is <strong>snapshotted</strong> at purchase time — later catalog changes won't affect this purchase.</li>
                                            <li>Session balance is computed live from completed sessions.</li>
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
            const packageSelect = document.getElementById('packageSelect');
            const priceInput = document.getElementById('purchasePrice');
            const previewPrompt = document.getElementById('previewPrompt');
            const previewContent = document.getElementById('previewContent');
            const previewItems = document.getElementById('previewItems');

            packageSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                if (!this.value) {
                    previewPrompt.style.display = 'block';
                    previewContent.style.display = 'none';
                    return;
                }

                const price = option.getAttribute('data-price');
                const items = option.getAttribute('data-items');

                // Auto-fill price from catalog
                if (!priceInput.value || priceInput.value === '0') {
                    priceInput.value = parseFloat(price).toFixed(2);
                }

                previewPrompt.style.display = 'none';
                previewContent.style.display = 'block';
                previewItems.textContent = items || 'No services configured';
            });

            // Trigger on load if old value exists
            if (packageSelect.value) {
                packageSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>
</html>
