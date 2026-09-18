<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $child->full_name }} — Child Profile & Safety Tags | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 50%, #06b6d4 100%);
            border-radius: 12px; padding: 0.75rem 1.25rem; color: #fff; margin-bottom: 0.75rem;
            position: relative; overflow: hidden;
            box-shadow: 0 4px 15px rgba(14,165,233,0.15);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -35px; right: -25px;
            width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.15rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.82rem; opacity: 0.88; position: relative; z-index: 1; margin: 0; }
        .profile-photo {
            width: 90px; height: 90px; border-radius: 16px; object-fit: cover;
            border: 3px solid #e0f2fe; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .profile-avatar-placeholder {
            width: 90px; height: 90px; border-radius: 16px;
            background: #e0e7ff; color: #4f46e5; display: flex;
            align-items: center; justify-content: center; font-weight: 700; font-size: 2rem;
            border: 3px solid #e0f2fe;
        }
        .qr-card {
            border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s ease;
            background: #ffffff;
        }
        .qr-card:hover {
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
            border-color: #cbd5e1;
        }
        .qr-image-wrapper {
            background: #f8fafc; border-radius: 8px; padding: 0.5rem;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1px solid #f1f5f9;
        }
        .qr-image-wrapper img {
            width: 120px; height: 120px; object-fit: contain;
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
            <div class="app-content py-3">
                <div class="container-fluid">

                    {{-- Compact Banner --}}
                    <div class="page-banner d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="bi bi-person-badge me-2"></i>{{ $child->full_name }}</h2>
                            <p>Child Profile &bull; Lost Child Safety QR Tag Management</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.children.edit', $child) }}" class="btn btn-sm btn-light fw-semibold text-primary">
                                <i class="bi bi-pencil-square me-1"></i> Edit Child
                            </a>
                            <a href="{{ route('admin.children.index') }}" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>

                    {{-- Alerts --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>There were issues with your submission:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row g-3">
                        {{-- Left Column: Child Information --}}
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm rounded-3 mb-3">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        @if ($child->photo_url)
                                            <img src="{{ Storage::url($child->photo_url) }}" alt="{{ $child->full_name }}" class="profile-photo">
                                        @else
                                            <div class="profile-avatar-placeholder">
                                                {{ strtoupper(substr($child->first_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <h4 class="h5 fw-bold mb-1">{{ $child->full_name }}</h4>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge {{ $child->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $child->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                <span class="text-muted small">Age: {{ $child->age }}</span>
                                            </div>
                                            <small class="text-secondary d-block">DOB: {{ \Carbon\Carbon::parse($child->date_of_birth)->format('M d, Y') }}</small>
                                        </div>
                                    </div>

                                    <hr class="my-2 text-muted opacity-25">

                                    {{-- Medical / Allergies --}}
                                    <div class="mb-3">
                                        <div class="fw-semibold small text-secondary mb-1"><i class="bi bi-shield-exclamation text-warning me-1"></i> Allergies</div>
                                        <p class="small mb-2 {{ $child->allergies ? 'text-danger fw-semibold' : 'text-muted' }}">
                                            {{ $child->allergies ?: 'None recorded' }}
                                        </p>

                                        <div class="fw-semibold small text-secondary mb-1"><i class="bi bi-clipboard2-pulse text-info me-1"></i> Medical Notes</div>
                                        <p class="small mb-0 text-muted">
                                            {{ $child->medical_notes ?: 'None recorded' }}
                                        </p>
                                    </div>

                                    <hr class="my-2 text-muted opacity-25">

                                    {{-- Emergency Contact --}}
                                    <div class="mb-3">
                                        <div class="fw-semibold small text-secondary mb-1"><i class="bi bi-telephone-alert text-danger me-1"></i> Emergency Contact</div>
                                        @if ($child->ec_name || $child->ec_phone)
                                            <div class="small">
                                                <span class="fw-semibold">{{ $child->ec_name }}</span>
                                                @if ($child->ec_relationship)
                                                    <span class="text-muted">({{ $child->ec_relationship }})</span>
                                                @endif
                                            </div>
                                            <div class="small text-muted">{{ $child->ec_phone ?: 'No phone' }}</div>
                                            @if ($child->ec_authorized_pickup)
                                                <span class="badge bg-info-subtle text-info border border-info-subtle mt-1">Authorized for Pickup</span>
                                            @endif
                                        @else
                                            <span class="text-muted small">No emergency contact specified.</span>
                                        @endif
                                    </div>

                                    <hr class="my-2 text-muted opacity-25">

                                    {{-- Parents --}}
                                    <div>
                                        <div class="fw-semibold small text-secondary mb-2"><i class="bi bi-people text-primary me-1"></i> Guardians / Parents</div>
                                        @forelse ($child->parents as $parent)
                                            <div class="p-2 mb-2 bg-light rounded-2 border">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-semibold small">{{ $parent->first_name }} {{ $parent->last_name }}</span>
                                                    @if ($parent->pivot->is_primary)
                                                        <span class="badge bg-primary">Primary</span>
                                                    @endif
                                                </div>
                                                <div class="small text-muted">
                                                    <i class="bi bi-phone me-1"></i> {{ $parent->mobile ?: 'No mobile' }}
                                                </div>
                                                @if ($parent->email)
                                                    <div class="small text-muted">
                                                        <i class="bi bi-envelope me-1"></i> {{ $parent->email }}
                                                    </div>
                                                @endif
                                                @if ($parent->pivot->relationship)
                                                    <div class="badge bg-secondary-subtle text-secondary small mt-1">
                                                        {{ ucfirst(str_replace('_', ' ', $parent->pivot->relationship)) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <p class="text-muted small mb-0">No parents linked to this child.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Safety QR Tags & Found Reports --}}
                        <div class="col-lg-8">
                            {{-- Generate Safety Tag Card --}}
                            <div class="card border-0 shadow-sm rounded-3 mb-3">
                                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h5 class="card-title fw-bold mb-0 text-dark d-flex align-items-center">
                                        <i class="bi bi-qr-code text-success me-2 fs-5"></i> Lost Child Safety QR Tags
                                    </h5>
                                </div>
                                <div class="card-body p-3">
                                    <p class="text-muted small mb-3">
                                        Generate a water-resistant QR safety tag for this child’s backpack, jacket, or wristband. When scanned by anyone with a smartphone, it displays a mobile-friendly emergency card with a one-tap <strong>Call Guardian</strong> button and GPS location sharing.
                                    </p>

                                    {{-- Generation Form --}}
                                    <form action="{{ route('admin.children.generate-safety-tag', $child) }}" method="POST" class="row g-2 align-items-end mb-4 bg-light p-3 rounded-3 border">
                                        @csrf
                                        <div class="col-md-7">
                                            <label for="label" class="form-label small fw-semibold text-secondary mb-1">Tag Label / Placement (Optional)</label>
                                            <input type="text" class="form-control form-control-sm" id="label" name="label" placeholder="e.g. Backpack Tag, Field Trip Lanyard, Shoe Tag">
                                        </div>
                                        <div class="col-md-5">
                                            <button type="submit" class="btn btn-sm btn-success w-100 fw-semibold py-2">
                                                <i class="bi bi-plus-circle me-1"></i> Generate New Safety QR Tag
                                            </button>
                                        </div>
                                    </form>

                                    {{-- Existing Tags List --}}
                                    <h6 class="fw-bold text-secondary mb-3 small text-uppercase">Existing Safety Tags ({{ $child->safetyTags->count() }})</h6>

                                    @forelse ($child->safetyTags as $tag)
                                        <div class="qr-card p-3 mb-3 {{ !$tag->is_active ? 'opacity-75 bg-light' : '' }}">
                                            <div class="row align-items-center">
                                                <div class="col-auto text-center">
                                                    <div class="qr-image-wrapper">
                                                        @if ($tag->qr_path && Storage::disk('public')->exists($tag->qr_path))
                                                            <img src="{{ Storage::url($tag->qr_path) }}" alt="QR Code" class="img-fluid">
                                                        @else
                                                            <div class="p-3 text-muted small text-center" style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="bi bi-qr-code text-secondary fs-1"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <h6 class="fw-bold mb-0 text-dark">{{ $tag->label ?: 'Safety Tag' }}</h6>
                                                        <span class="badge {{ $tag->is_active ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $tag->is_active ? 'Active' : 'Deactivated' }}
                                                        </span>
                                                    </div>
                                                    <div class="small text-muted mb-2">
                                                        <span>Token: <code class="text-secondary">{{ substr($tag->token, 0, 10) }}...{{ substr($tag->token, -6) }}</code></span>
                                                        <span class="mx-1">&bull;</span>
                                                        <span>Created: {{ $tag->created_at->format('M d, Y h:i A') }}</span>
                                                    </div>

                                                    {{-- Action Buttons --}}
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @if ($tag->qr_path && Storage::disk('public')->exists($tag->qr_path))
                                                            <a href="{{ Storage::url($tag->qr_path) }}" download="child-safety-tag-{{ Str::slug($child->first_name) }}-{{ $tag->token }}.png" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-download me-1"></i> Download for Printing
                                                            </a>
                                                        @endif

                                                        <a href="{{ route('safety.card', $tag->token) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-box-arrow-up-right me-1"></i> View Public Card
                                                        </a>

                                                        @if ($tag->is_active)
                                                            <form action="{{ route('admin.children.deactivate-safety-tag', $tag) }}" method="POST" onsubmit="return confirm('Are you sure you want to deactivate this safety tag? The public QR code will stop functioning.');" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="bi bi-slash-circle me-1"></i> Deactivate
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>

                                                    {{-- Found Reports on this Tag --}}
                                                    @if ($tag->foundReports->isNotEmpty())
                                                        <div class="mt-3 p-2 bg-warning-subtle rounded border border-warning-subtle">
                                                            <div class="fw-semibold small text-dark d-flex align-items-center mb-1">
                                                                <i class="bi bi-exclamation-octagon-fill text-warning me-1"></i> Found Child Reports ({{ $tag->foundReports->count() }})
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered mb-0 bg-white small">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Date/Time</th>
                                                                            <th>Reporter</th>
                                                                            <th>Phone</th>
                                                                            <th>Location / Coordinates</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($tag->foundReports as $report)
                                                                            <tr>
                                                                                <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                                                                                <td>{{ $report->reporter_name ?: 'Anonymous' }}</td>
                                                                                <td>
                                                                                    @if ($report->reporter_phone)
                                                                                        <a href="tel:{{ $report->reporter_phone }}">{{ $report->reporter_phone }}</a>
                                                                                    @else
                                                                                        <span class="text-muted">—</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @if ($report->latitude && $report->longitude)
                                                                                        <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank" class="btn btn-xs btn-outline-success py-0 px-1">
                                                                                            <i class="bi bi-geo-alt-fill"></i> View Map
                                                                                        </a>
                                                                                        <span class="text-muted" style="font-size: 0.75rem;">({{ number_format($report->latitude, 4) }}, {{ number_format($report->longitude, 4) }})</span>
                                                                                    @elseif ($report->message)
                                                                                        {{ Str::limit($report->message, 30) }}
                                                                                    @else
                                                                                        <span class="text-muted">No GPS</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    <span class="badge {{ $report->status === 'resolved' ? 'bg-success' : ($report->status === 'notified' ? 'bg-info' : 'bg-warning text-dark') }}">
                                                                                        {{ ucfirst($report->status) }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 bg-light rounded-3 border">
                                            <i class="bi bi-qr-code-scan text-muted fs-1 mb-2 d-block"></i>
                                            <p class="text-muted small mb-0">No safety tags have been generated for this child yet.</p>
                                            <p class="text-secondary small">Use the form above to generate the first QR safety tag.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>
