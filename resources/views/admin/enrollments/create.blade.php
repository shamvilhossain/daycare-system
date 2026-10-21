<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Enrollment | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .info-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; }
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
                            <h3 class="mb-0"><i class="bi bi-clipboard-plus me-2"></i>New Enrollment</h3>
                        </div>
                        <div class="col-sm-6 text-end">
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back to Enrollments
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

                    <form action="{{ route('admin.enrollments.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            {{-- Main Form Details --}}
                            <div class="col-lg-8">
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0"><i class="bi bi-person-check-fill me-2 text-primary"></i>Enrollment Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            {{-- Child Selection --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Child <span class="text-danger">*</span></label>
                                                <select name="child_id" id="childSelect" class="form-select @error('child_id') is-invalid @enderror" required>
                                                    <option value="">Select Child...</option>
                                                    @foreach($children as $child)
                                                        <option value="{{ $child->id }}" 
                                                            data-dob="{{ $child->date_of_birth ? $child->date_of_birth->format('Y-m-d') : '' }}"
                                                            data-age="{{ $child->formatted_age }}"
                                                            data-age-months="{{ $child->age_in_months }}"
                                                            {{ old('child_id', $selectedChildId) == $child->id ? 'selected' : '' }}>
                                                            {{ $child->full_name }} (DOB: {{ $child->date_of_birth ? $child->date_of_birth->format('M d, Y') : 'N/A' }} - {{ $child->formatted_age }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('child_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- Program Selection --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Program <span class="text-danger">*</span></label>
                                                <select name="program_id" id="programSelect" class="form-select @error('program_id') is-invalid @enderror" required>
                                                    <option value="">Select Program...</option>
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->id }}"
                                                            data-service-type="{{ $program->service_type }}"
                                                            data-min-age="{{ $program->min_age_months ?? 0 }}"
                                                            data-max-age="{{ $program->max_age_months ?? 999 }}"
                                                            data-age-range="{{ $program->age_range_label }}"
                                                            data-capacity="{{ $program->capacity }}"
                                                            data-active-count="{{ $program->active_count ?? 0 }}"
                                                            data-monthly-fee="{{ $program->monthly_fee }}"
                                                            data-billing-model="{{ $program->billing_model }}"
                                                            {{ old('program_id', $selectedProgramId) == $program->id ? 'selected' : '' }}>
                                                            {{ $program->name }} ({{ $program->age_range_label }} | Cap: {{ $program->active_count ?? 0 }}/{{ $program->capacity }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- Service Type --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Service Type <span class="text-danger">*</span></label>
                                                <select name="service_type" id="serviceTypeSelect" class="form-select @error('service_type') is-invalid @enderror" required>
                                                    <option value="full_day" {{ old('service_type') == 'full_day' ? 'selected' : '' }}>Full Day</option>
                                                    <option value="half_day" {{ old('service_type') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                                    <option value="after_school" {{ old('service_type') == 'after_school' ? 'selected' : '' }}>After School</option>
                                                    <option value="drop_in" {{ old('service_type') == 'drop_in' ? 'selected' : '' }}>Drop-In</option>
                                                </select>
                                                @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- Initial Status --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                                <select name="status" id="statusSelect" class="form-select @error('status') is-invalid @enderror" required>
                                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active (Approved immediately)</option>
                                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending (Requires approval)</option>
                                                    <option value="withdrawn" {{ old('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                                                    <option value="graduated" {{ old('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                                </select>
                                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- Start Date --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                                                <input type="date" name="start_date" id="startDateInput" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- End Date --}}
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">End Date (Optional)</label>
                                                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                                                <small class="text-muted">Leave blank if open-ended/ongoing.</small>
                                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>

                                            {{-- Notes --}}
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Notes / Special Instructions</label>
                                                <textarea name="notes" class="form-control" rows="3" placeholder="Enter any notes, special requirements, or enrollment comments...">{{ old('notes') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Admission fee (one-time, first enrolment only) --}}
                                <div class="card shadow-sm border-0 mb-3" id="admissionCard" style="display:none;">
                                    <div class="card-header bg-white">
                                        <h5 class="mb-0"><i class="bi bi-receipt-cutoff me-2 text-success"></i>Admission Fee</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="admissionText" class="small"></div>
                                        <div id="admissionWaiver" style="display:none;" class="mt-3">
                                            <div class="form-check">
                                                <input type="hidden" name="waive_admission_fee" value="0">
                                                <input class="form-check-input" type="checkbox" name="waive_admission_fee" value="1" id="waiveCheck" {{ old('waive_admission_fee') ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="waiveCheck">Waive the admission fee for this child</label>
                                            </div>
                                            <div id="waiverReasonBox" class="mt-2" style="display:none;">
                                                <input type="text" name="admission_waiver_reason" maxlength="255" class="form-control @error('admission_waiver_reason') is-invalid @enderror"
                                                       value="{{ old('admission_waiver_reason') }}" placeholder="Reason, e.g. sibling discount, staff child, scholarship">
                                                @error('admission_waiver_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mb-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-check-circle-fill me-1"></i> Confirm & Enroll
                                    </button>
                                </div>
                            </div>

                            {{-- Sidebar Live Check & Rules Info --}}
                            <div class="col-lg-4">
                                <div class="card shadow-sm border-0 mb-3">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check text-success me-2"></i>Eligibility & Capacity Check</h6>
                                    </div>
                                    <div class="card-body" id="eligibilityFeedback">
                                        <div class="text-muted small text-center py-3" id="feedbackPrompt">
                                            <i class="bi bi-info-circle fs-3 d-block mb-1 text-primary"></i>
                                            Select a child and a program to preview age eligibility and capacity status.
                                        </div>

                                        <div id="feedbackContent" style="display:none;">
                                            <div class="mb-3 p-2 rounded" id="ageFeedbackBox">
                                                <div class="d-flex align-items-center">
                                                    <i id="ageIcon" class="bi bi-check-circle-fill me-2 fs-5"></i>
                                                    <div>
                                                        <span class="fw-semibold small d-block">Age Eligibility</span>
                                                        <span id="ageFeedbackText" class="small"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3 p-2 rounded" id="capFeedbackBox">
                                                <div class="d-flex align-items-center">
                                                    <i id="capIcon" class="bi bi-check-circle-fill me-2 fs-5"></i>
                                                    <div>
                                                        <span class="fw-semibold small d-block">Program Capacity</span>
                                                        <span id="capFeedbackText" class="small"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Daycare Business Rules Notice --}}
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0 fw-bold"><i class="bi bi-lightbulb text-warning me-2"></i>Business Rules</h6>
                                    </div>
                                    <div class="card-body small text-muted">
                                        <ul class="ps-3 mb-0">
                                            <li class="mb-2"><strong>Age Eligibility:</strong> The child's age on the start date is evaluated against program minimum and maximum age limits.</li>
                                            <li class="mb-2"><strong>Capacity:</strong> Active and pending enrollments count towards program capacity.</li>
                                            <li class="mb-2"><strong>Active Status:</strong> Setting status to <em>Active</em> will automatically record you as the approver.</li>
                                            <li class="mb-2"><strong>Duplicates:</strong> Children cannot have multiple concurrent active enrollments in the same program.</li>
                                            <li><strong>Admission fee:</strong> charged once per child, on the first enrolment — an admission invoice (fee + first month) is created; approve the enrolment once it is paid.</li>
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
            const childSelect = document.getElementById('childSelect');
            const programSelect = document.getElementById('programSelect');
            const serviceTypeSelect = document.getElementById('serviceTypeSelect');
            const startDateInput = document.getElementById('startDateInput');
            const feedbackPrompt = document.getElementById('feedbackPrompt');
            const feedbackContent = document.getElementById('feedbackContent');
            const ageFeedbackBox = document.getElementById('ageFeedbackBox');
            const ageFeedbackText = document.getElementById('ageFeedbackText');
            const ageIcon = document.getElementById('ageIcon');
            const capFeedbackBox = document.getElementById('capFeedbackBox');
            const capFeedbackText = document.getElementById('capFeedbackText');
            const capIcon = document.getElementById('capIcon');

            function calculateMonths(dobString, startDateString) {
                if (!dobString || !startDateString) return null;
                const dob = new Date(dobString);
                const start = new Date(startDateString);
                let months = (start.getFullYear() - dob.getFullYear()) * 12;
                months += start.getMonth() - dob.getMonth();
                if (start.getDate() < dob.getDate()) months--;
                return Math.max(0, months);
            }

            function updatePreview() {
                const childOption = childSelect.options[childSelect.selectedIndex];
                const programOption = programSelect.options[programSelect.selectedIndex];

                if (!childSelect.value || !programSelect.value) {
                    feedbackPrompt.style.display = 'block';
                    feedbackContent.style.display = 'none';
                    return;
                }

                feedbackPrompt.style.display = 'none';
                feedbackContent.style.display = 'block';

                // Auto-select program's default service type if applicable
                const programServiceType = programOption.getAttribute('data-service-type');
                if (programServiceType && !serviceTypeSelect.dataset.userChanged) {
                    serviceTypeSelect.value = programServiceType;
                }

                const dob = childOption.getAttribute('data-dob');
                const startDate = startDateInput.value || new Date().toISOString().split('T')[0];
                const childMonths = calculateMonths(dob, startDate);

                const minAge = parseInt(programOption.getAttribute('data-min-age')) || 0;
                const maxAge = parseInt(programOption.getAttribute('data-max-age')) || 999;
                const ageRange = programOption.getAttribute('data-age-range');

                // Age Check
                let ageValid = true;
                let ageMsg = `Child is ~${childMonths} months old. Required: ${ageRange}.`;

                if (childMonths < minAge) {
                    ageValid = false;
                    ageMsg = `Too young (${childMonths} mos vs min ${minAge} mos required).`;
                } else if (childMonths > maxAge) {
                    ageValid = false;
                    ageMsg = `Exceeds max age (${childMonths} mos vs max ${maxAge} mos).`;
                }

                if (ageValid) {
                    ageFeedbackBox.className = 'mb-3 p-2 rounded bg-success-subtle text-success border border-success-subtle';
                    ageIcon.className = 'bi bi-check-circle-fill me-2 fs-5';
                } else {
                    ageFeedbackBox.className = 'mb-3 p-2 rounded bg-danger-subtle text-danger border border-danger-subtle';
                    ageIcon.className = 'bi bi-x-circle-fill me-2 fs-5';
                }
                ageFeedbackText.textContent = ageMsg;

                // Capacity Check
                const capacity = parseInt(programOption.getAttribute('data-capacity')) || 0;
                const activeCount = parseInt(programOption.getAttribute('data-active-count')) || 0;
                const capValid = activeCount < capacity;

                if (capValid) {
                    capFeedbackBox.className = 'mb-3 p-2 rounded bg-success-subtle text-success border border-success-subtle';
                    capIcon.className = 'bi bi-check-circle-fill me-2 fs-5';
                    capFeedbackText.textContent = `Available: ${capacity - activeCount} spots left (${activeCount}/${capacity} enrolled).`;
                } else {
                    capFeedbackBox.className = 'mb-3 p-2 rounded bg-danger-subtle text-danger border border-danger-subtle';
                    capIcon.className = 'bi bi-exclamation-triangle-fill me-2 fs-5';
                    capFeedbackText.textContent = `Program is full! (${activeCount}/${capacity} capacity reached).`;
                }
            }

            // ---- Admission fee panel ----
            const admission = @json($admission);
            const admissionFee = {{ (float) $admissionFee }};
            const statusSelect = document.getElementById('statusSelect');
            const admissionCard = document.getElementById('admissionCard');
            const admissionText = document.getElementById('admissionText');
            const admissionWaiver = document.getElementById('admissionWaiver');
            const waiveCheck = document.getElementById('waiveCheck');
            const waiverReasonBox = document.getElementById('waiverReasonBox');
            const statusTouched = {{ old('status') ? 'true' : 'false' }};
            let userSetStatus = statusTouched;
            statusSelect.addEventListener('change', () => { userSetStatus = true; });
            const tk = n => '৳' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            function updateAdmission() {
                const info = admission[childSelect.value];
                if (!info || info.status === 'off') { admissionCard.style.display = 'none'; return; }
                admissionCard.style.display = 'block';
                admissionWaiver.style.display = 'none';

                if (info.status !== 'due') {
                    const what = {
                        paid: 'already charged and paid on ' + esc(info.invoice),
                        unpaid: 'already invoiced on ' + esc(info.invoice) + ' (not paid yet)',
                        waived: 'waived' + (info.reason ? ' — ' + esc(info.reason) : ''),
                    }[info.status];
                    admissionText.innerHTML = '<div class="alert alert-secondary mb-0 py-2"><i class="bi bi-check2-circle me-1"></i>Admission fee ' + what
                        + '. <strong>No admission fee</strong> on this enrolment (it is charged once per child).</div>';
                    return;
                }

                const opt = programSelect.options[programSelect.selectedIndex];
                const monthly = programSelect.value && opt.dataset.billingModel === 'monthly' ? parseFloat(opt.dataset.monthlyFee || 0) : 0;
                const waived = waiveCheck.checked;
                const lines = [];
                if (!waived) lines.push(['Admission fee (one-time)', admissionFee]);
                if (monthly > 0) lines.push(['First month tuition (' + esc(opt.textContent.split('(')[0].trim()) + ')', monthly]);
                const total = lines.reduce((s, l) => s + l[1], 0);

                let html = '';
                if (!info.has_parent) {
                    html += '<div class="alert alert-danger py-2"><i class="bi bi-person-x me-1"></i>This child has no parent linked — link a parent first (Children → Edit) so the admission invoice can be billed.</div>';
                }
                html += '<p class="mb-2"><i class="bi bi-stars text-warning me-1"></i><strong>First enrolment for this child.</strong> An admission invoice will be created for the parent:</p>';
                html += lines.length
                    ? '<table class="table table-sm mb-2">' + lines.map(l => '<tr><td>' + l[0] + '</td><td class="text-end">' + tk(l[1]) + '</td></tr>').join('')
                        + '<tr class="fw-bold"><td>Total</td><td class="text-end">' + tk(total) + '</td></tr></table>'
                    : '<p class="text-muted mb-2">Nothing to bill (fee waived and the programme has no monthly fee).</p>';
                html += '<p class="text-muted mb-0">Send the payment link from the invoice, then approve the enrolment once it is paid.</p>';
                admissionText.innerHTML = html;
                admissionWaiver.style.display = 'block';

                // Seat is confirmed after payment → start as Pending unless the user picked a status
                if (!userSetStatus) statusSelect.value = 'pending';
            }

            waiveCheck.addEventListener('change', () => { waiverReasonBox.style.display = waiveCheck.checked ? 'block' : 'none'; updateAdmission(); });
            waiverReasonBox.style.display = waiveCheck.checked ? 'block' : 'none';
            childSelect.addEventListener('change', updateAdmission);
            programSelect.addEventListener('change', updateAdmission);
            updateAdmission();

            childSelect.addEventListener('change', updatePreview);
            programSelect.addEventListener('change', updatePreview);
            startDateInput.addEventListener('change', updatePreview);
            serviceTypeSelect.addEventListener('change', () => { serviceTypeSelect.dataset.userChanged = 'true'; });

            updatePreview();
        });
    </script>
</body>
</html>
