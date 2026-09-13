<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Therapy Session | KinderCare</title>
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
        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a></li></ul>
            </div>
        </nav>

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0"><i class="bi bi-heart-pulse me-2"></i>New Therapy Session</h3>
                        </div>
                        <div class="col-sm-6 text-end">
                            <a href="{{ route('admin.therapy-sessions.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back to Sessions
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong><i class="bi bi-exclamation-octagon-fill me-2"></i>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.therapy-sessions.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            {{-- Main Form --}}
                            <div class="col-lg-8">
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0"><i class="bi bi-heart-pulse-fill me-2 text-primary"></i>Session Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            {{-- Child --}}
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

                                            {{-- Therapy Service --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Therapy Service <span class="text-danger">*</span></label>
                                                <select name="therapy_service_id" id="therapyServiceSelect" class="form-select @error('therapy_service_id') is-invalid @enderror" required>
                                                    <option value="">Select Service...</option>
                                                    @foreach($services as $service)
                                                        <option value="{{ $service->id }}"
                                                            data-therapy-type="{{ $service->therapy_type }}"
                                                            data-duration="{{ $service->duration_minutes }}"
                                                            data-rate="{{ $service->session_rate }}"
                                                            {{ old('therapy_service_id') == $service->id ? 'selected' : '' }}>
                                                            {{ $service->name }} ({{ strtoupper($service->therapy_type) }} · {{ $service->duration_minutes }}min · ৳{{ number_format($service->session_rate, 2) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('therapy_service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- Therapist --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Therapist <span class="text-danger">*</span></label>
                                                <select name="staff_id" id="therapistSelect" class="form-select @error('staff_id') is-invalid @enderror" required>
                                                    <option value="">Select Therapist...</option>
                                                    @foreach($therapists as $therapist)
                                                        <option value="{{ $therapist->id }}"
                                                            data-specialization="{{ $therapist->specialization }}"
                                                            {{ old('staff_id') == $therapist->id ? 'selected' : '' }}>
                                                            {{ $therapist->full_name }} ({{ $therapist->specialization_short ?? 'N/A' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('staff_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- Status --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                                    <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    <option value="no_show" {{ old('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                                                </select>
                                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- Session Date --}}
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Session Date <span class="text-danger">*</span></label>
                                                <input type="date" name="session_date" class="form-control @error('session_date') is-invalid @enderror" value="{{ old('session_date', date('Y-m-d')) }}" required>
                                                @error('session_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- Start Time --}}
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                                                <input type="time" name="start_time" id="startTimeInput" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', '09:00') }}" required>
                                                @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- End Time --}}
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">End Time <span class="text-danger">*</span></label>
                                                <input type="time" name="end_time" id="endTimeInput" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', '10:00') }}" required>
                                                @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- Notes --}}
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Session Notes</label>
                                                <textarea name="notes" class="form-control" rows="3" placeholder="Enter any session notes, goals, or observations...">{{ old('notes') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mb-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-check-circle-fill me-1"></i> Create Session
                                    </button>
                                </div>
                            </div>

                            {{-- Sidebar: Validation Preview --}}
                            <div class="col-lg-4">
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check text-success me-2"></i>Validation Preview</h6>
                                    </div>
                                    <div class="card-body" id="validationFeedback">
                                        <div class="text-muted small text-center py-3" id="feedbackPrompt">
                                            <i class="bi bi-info-circle fs-3 d-block mb-1 text-primary"></i>
                                            Select a service and therapist to preview specialization matching.
                                        </div>

                                        <div id="feedbackContent" style="display:none;">
                                            <div class="mb-3 p-2 rounded" id="specFeedbackBox">
                                                <div class="d-flex align-items-center">
                                                    <i id="specIcon" class="bi bi-check-circle-fill me-2 fs-5"></i>
                                                    <div>
                                                        <span class="fw-semibold small d-block">Specialization Match</span>
                                                        <span id="specFeedbackText" class="small"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3 p-2 rounded" id="timeFeedbackBox">
                                                <div class="d-flex align-items-center">
                                                    <i id="timeIcon" class="bi bi-clock-fill me-2 fs-5"></i>
                                                    <div>
                                                        <span class="fw-semibold small d-block">Session Duration</span>
                                                        <span id="timeFeedbackText" class="small"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Business Rules Notice --}}
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0 fw-bold"><i class="bi bi-lightbulb text-warning me-2"></i>Booking Rules</h6>
                                    </div>
                                    <div class="card-body small text-muted">
                                        <ul class="ps-3 mb-0">
                                            <li class="mb-2"><strong>Specialization:</strong> The therapist's specialization must match the therapy service type (SLT/ABA/OT).</li>
                                            <li class="mb-2"><strong>Overlap:</strong> A therapist cannot have overlapping sessions on the same date and time.</li>
                                            <li class="mb-2"><strong>Time:</strong> End time must be after start time.</li>
                                            <li><strong>Auto-fill:</strong> Selecting a service auto-calculates the end time based on its duration.</li>
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
            const serviceSelect = document.getElementById('therapyServiceSelect');
            const therapistSelect = document.getElementById('therapistSelect');
            const startTimeInput = document.getElementById('startTimeInput');
            const endTimeInput = document.getElementById('endTimeInput');
            const feedbackPrompt = document.getElementById('feedbackPrompt');
            const feedbackContent = document.getElementById('feedbackContent');
            const specFeedbackBox = document.getElementById('specFeedbackBox');
            const specFeedbackText = document.getElementById('specFeedbackText');
            const specIcon = document.getElementById('specIcon');
            const timeFeedbackBox = document.getElementById('timeFeedbackBox');
            const timeFeedbackText = document.getElementById('timeFeedbackText');
            const timeIcon = document.getElementById('timeIcon');

            function updatePreview() {
                const serviceOption = serviceSelect.options[serviceSelect.selectedIndex];
                const therapistOption = therapistSelect.options[therapistSelect.selectedIndex];

                if (!serviceSelect.value || !therapistSelect.value) {
                    feedbackPrompt.style.display = 'block';
                    feedbackContent.style.display = 'none';
                    return;
                }

                feedbackPrompt.style.display = 'none';
                feedbackContent.style.display = 'block';

                // Specialization check
                const serviceType = serviceOption.getAttribute('data-therapy-type');
                const therapistSpec = therapistOption.getAttribute('data-specialization');
                const specMatch = serviceType === therapistSpec;

                if (specMatch) {
                    specFeedbackBox.className = 'mb-3 p-2 rounded bg-success-subtle text-success border border-success-subtle';
                    specIcon.className = 'bi bi-check-circle-fill me-2 fs-5';
                    specFeedbackText.textContent = `Match! Therapist (${therapistSpec.toUpperCase()}) matches service (${serviceType.toUpperCase()}).`;
                } else {
                    specFeedbackBox.className = 'mb-3 p-2 rounded bg-danger-subtle text-danger border border-danger-subtle';
                    specIcon.className = 'bi bi-x-circle-fill me-2 fs-5';
                    specFeedbackText.textContent = `Mismatch! Therapist is ${(therapistSpec || 'N/A').toUpperCase()} but service requires ${serviceType.toUpperCase()}.`;
                }

                // Duration check
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;
                if (startTime && endTime) {
                    const start = new Date(`2000-01-01T${startTime}`);
                    const end = new Date(`2000-01-01T${endTime}`);
                    const diffMin = (end - start) / 60000;
                    if (diffMin > 0) {
                        timeFeedbackBox.className = 'mb-3 p-2 rounded bg-info-subtle text-info border border-info-subtle';
                        timeIcon.className = 'bi bi-clock-fill me-2 fs-5';
                        timeFeedbackText.textContent = `Duration: ${diffMin} minutes (${startTime} – ${endTime}).`;
                    } else {
                        timeFeedbackBox.className = 'mb-3 p-2 rounded bg-danger-subtle text-danger border border-danger-subtle';
                        timeIcon.className = 'bi bi-exclamation-triangle-fill me-2 fs-5';
                        timeFeedbackText.textContent = 'End time must be after start time.';
                    }
                }
            }

            // Auto-fill end time when service changes
            serviceSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const duration = parseInt(option.getAttribute('data-duration')) || 60;
                const startTime = startTimeInput.value;

                if (startTime) {
                    const start = new Date(`2000-01-01T${startTime}`);
                    start.setMinutes(start.getMinutes() + duration);
                    const h = String(start.getHours()).padStart(2, '0');
                    const m = String(start.getMinutes()).padStart(2, '0');
                    endTimeInput.value = `${h}:${m}`;
                }

                updatePreview();
            });

            therapistSelect.addEventListener('change', updatePreview);
            startTimeInput.addEventListener('change', function() {
                // Recalculate end time
                const option = serviceSelect.options[serviceSelect.selectedIndex];
                if (option && option.value) {
                    const duration = parseInt(option.getAttribute('data-duration')) || 60;
                    const start = new Date(`2000-01-01T${this.value}`);
                    start.setMinutes(start.getMinutes() + duration);
                    const h = String(start.getHours()).padStart(2, '0');
                    const m = String(start.getMinutes()).padStart(2, '0');
                    endTimeInput.value = `${h}:${m}`;
                }
                updatePreview();
            });
            endTimeInput.addEventListener('change', updatePreview);

            updatePreview();
        });
    </script>
</body>
</html>
