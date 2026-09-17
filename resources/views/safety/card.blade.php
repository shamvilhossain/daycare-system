<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Safety Tag - {{ $child->first_name }}</title>
    <!-- Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
            min-height: 100vh;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.25rem 0.75rem;
        }
        .safety-card {
            max-width: 480px;
            width: 100%;
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .safety-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            padding: 1.5rem 1.25rem;
            text-align: center;
        }
        .child-avatar-ring {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border: 3px solid #ffffff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }
        .btn-call {
            background: #10b981;
            border-color: #10b981;
            font-size: 1.15rem;
            font-weight: 700;
            padding: 0.9rem 1.25rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }
        .btn-call:hover, .btn-call:focus {
            background: #059669;
            border-color: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }
        .card-body-section {
            padding: 1.5rem 1.25rem;
        }
        .badge-safety {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .location-badge {
            font-size: 0.82rem;
            color: #047857;
            background: #d1fae5;
            padding: 0.35rem 0.65rem;
            border-radius: 0.5rem;
            display: none;
        }
    </style>
</head>
<body>

<div class="safety-card">
    <div class="safety-header">
        <div class="child-avatar-ring">
            <i class="bi bi-person-fill"></i>
        </div>
        <h1 class="h3 fw-bold mb-1">{{ $child->first_name }}</h1>
        <div class="badge-safety mt-1">
            <i class="bi bi-shield-check"></i> Daycare Safety Protected
        </div>
        <p class="small text-white-50 mt-2 mb-0">
            If you have found this child, please call the guardian right away!
        </p>
    </div>

    <div class="card-body-section">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <ul class="mb-0 small ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Quick Call Actions -->
        <div class="d-grid gap-2 mb-4">
            @if ($guardianPhone)
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $guardianPhone) }}" class="btn btn-success btn-call text-white shadow-sm text-center text-decoration-none">
                    <i class="bi bi-telephone-fill me-2"></i> Call Guardian
                    <span class="d-block small fw-normal opacity-90">{{ $guardianPhone }}</span>
                </a>
            @else
                <div class="alert alert-warning text-center mb-2">
                    <i class="bi bi-exclamation-triangle me-1"></i> Guardian phone not on file. Please send a report below.
                </div>
            @endif

            @if ($child->ec_phone && $child->ec_phone !== $guardianPhone)
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $child->ec_phone) }}" class="btn btn-outline-secondary py-2 fw-semibold text-center text-decoration-none">
                    <i class="bi bi-telephone-outbound me-2"></i> Call Emergency Contact: {{ $child->ec_name ?: 'Contact' }}
                    <span class="d-block small text-muted">{{ $child->ec_phone }}</span>
                </a>
            @endif
        </div>

        <hr class="my-4 text-muted opacity-25">

        <!-- Report Found Form -->
        <div class="bg-light p-3 rounded-3 border">
            <h2 class="h6 fw-bold mb-2 text-dark d-flex align-items-center">
                <i class="bi bi-geo-alt-fill text-danger me-2"></i> Report Child Found
            </h2>
            <p class="text-muted small mb-3">
                Send your phone number &amp; GPS location to immediately alert the guardians and daycare administration.
            </p>

            <form action="{{ route('safety.report', $tag->token) }}" method="POST" id="foundReportForm">
                @csrf
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                <div class="mb-3">
                    <label for="reporter_name" class="form-label small fw-semibold text-secondary mb-1">Your Name (Optional)</label>
                    <input type="text" class="form-control form-control-sm" id="reporter_name" name="reporter_name" value="{{ old('reporter_name') }}" placeholder="e.g. Good Samaritan / Park Guard">
                </div>

                <div class="mb-3">
                    <label for="reporter_phone" class="form-label small fw-semibold text-secondary mb-1">Your Contact Phone (Optional)</label>
                    <input type="tel" class="form-control form-control-sm" id="reporter_phone" name="reporter_phone" value="{{ old('reporter_phone') }}" placeholder="e.g. +1 555-0199">
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label small fw-semibold text-secondary mb-1">Message / Current Location Details</label>
                    <textarea class="form-control form-control-sm" id="message" name="message" rows="2" placeholder="e.g. Safe with staff at Sunshine Park cafe">{{ old('message') }}</textarea>
                </div>

                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnShareLocation">
                            <i class="bi bi-crosshair me-1"></i> Attach GPS Location
                        </button>
                        <span class="location-badge" id="locationStatus">
                            <i class="bi bi-check2-circle me-1"></i> Location attached
                        </span>
                    </div>
                    <small class="text-muted d-block mt-1" id="locationHelperText" style="font-size: 0.78rem;">
                        Allows guardians to view a Google Maps pin of where the child was found.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                    <i class="bi bi-send-fill me-1"></i> Send Found Alert
                </button>
            </form>
        </div>

        <div class="mt-4 text-center">
            <p class="text-muted mb-0" style="font-size: 0.78rem;">
                <i class="bi bi-lock-fill text-secondary me-1"></i>
                Child privacy protected. Only first name and emergency numbers are shown.
            </p>
        </div>
    </div>
</div>

<script>
    const btnShareLocation = document.getElementById('btnShareLocation');
    const locationStatus = document.getElementById('locationStatus');
    const locationHelperText = document.getElementById('locationHelperText');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    function acquireLocation(callback) {
        if (!navigator.geolocation) {
            if (locationHelperText) locationHelperText.innerText = 'Geolocation is not supported by your browser.';
            if (callback) callback();
            return;
        }

        btnShareLocation.disabled = true;
        btnShareLocation.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Locating...';

        navigator.geolocation.getCurrentPosition(
            function (position) {
                latInput.value = position.coords.latitude;
                lngInput.value = position.coords.longitude;
                btnShareLocation.style.display = 'none';
                locationStatus.style.display = 'inline-flex';
                if (locationHelperText) locationHelperText.innerText = 'GPS coordinates attached: ' + position.coords.latitude.toFixed(4) + ', ' + position.coords.longitude.toFixed(4);
                if (callback) callback();
            },
            function (err) {
                btnShareLocation.disabled = false;
                btnShareLocation.innerHTML = '<i class="bi bi-crosshair me-1"></i> Retry Location';
                if (locationHelperText) locationHelperText.innerText = 'Could not retrieve GPS location (' + err.message + '). You can still submit the form.';
                if (callback) callback();
            },
            { timeout: 8000, enableHighAccuracy: true }
        );
    }

    btnShareLocation.addEventListener('click', function () {
        acquireLocation();
    });

    // If coordinates were already supplied from previous submission, show badge
    if (latInput.value && lngInput.value) {
        btnShareLocation.style.display = 'none';
        locationStatus.style.display = 'inline-flex';
    }
</script>
</body>
</html>
