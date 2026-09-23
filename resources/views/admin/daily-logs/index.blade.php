<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Child Logs | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            border-radius: 12px; padding: 0.75rem 1.25rem; color: #fff; margin-bottom: 0.75rem;
            position: relative; overflow: hidden;
            box-shadow: 0 4px 15px rgba(99,102,241,0.15);
        }
        .page-banner::before {
            content: ''; position: absolute; top: -35px; right: -25px;
            width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.08);
            pointer-events: none;
        }
        .page-banner h2 { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.15rem; position: relative; z-index: 1; }
        .page-banner p { font-size: 0.82rem; opacity: 0.88; position: relative; z-index: 1; margin: 0; }
        .child-pill {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.65rem 1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.15s ease;
            text-decoration: none;
            color: #1e293b;
        }
        .child-pill:hover {
            transform: translateY(-2px);
            border-color: #8b5cf6;
            box-shadow: 0 6px 16px rgba(139,92,246,0.12);
            color: #6366f1;
        }
        .child-mini-avatar {
            width: 34px; height: 34px; border-radius: 50%; object-fit: cover;
            border: 2px solid #e2e8f0;
        }
        .child-mini-placeholder {
            width: 34px; height: 34px; border-radius: 50%;
            background: #ede9fe; color: #6d28d9; display: flex;
            align-items: center; justify-content: center; font-weight: 700; font-size: 0.78rem;
        }
    </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <span class="nav-link text-muted fw-medium">Daily Operations &bull; Child Activity, Meal & Nap Logs</span>
                    </li>
                </ul>
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
            <div class="app-content-header pt-2 pb-0">
                <div class="container-fluid">
                    <div class="page-banner d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <h2><i class="bi bi-journal-text me-2"></i>Daily Operational Child Logs</h2>
                            <p>Track merged daily routines — naps, meals, learning activities, diaper changes, and health incidents</p>
                        </div>
                        <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                            <a href="{{ route('admin.attendance.index', ['date' => $date]) }}" class="btn btn-light btn-sm fw-bold shadow-sm px-3">
                                <i class="bi bi-clock-history me-1"></i> Attendance Desk
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    {{-- Alert Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Quick Child Jump Selector --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-person-lines-fill text-indigo me-2" style="color: #6366f1;"></i>
                                Individual Merged Daily Feeds &bull; Select a child to view their timeline
                            </h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($children as $c)
                                    <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $c->id, 'date' => $date]) }}" class="child-pill">
                                        @if ($c->photo_url ?? false)
                                            <img src="{{ $c->photo_url }}" class="child-mini-avatar" alt="{{ $c->full_name }}">
                                        @else
                                            <div class="child-mini-placeholder">
                                                {{ strtoupper(substr($c->first_name, 0, 1) . substr($c->last_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.88rem;">{{ $c->full_name }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $c->formatted_age }}</div>
                                        </div>
                                        <i class="bi bi-chevron-right ms-1 text-muted" style="font-size: 0.75rem;"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Search & Filter Form --}}
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body py-3">
                            <form method="GET" action="{{ route('admin.child-daily-logs.index') }}" class="row g-2 align-items-center" id="logsFilterForm">
                                <div class="col-lg-3 col-md-6 d-flex align-items-center gap-2">
                                    <label class="form-label mb-0 fw-semibold text-muted text-nowrap"><i class="bi bi-calendar3 me-1"></i>Date:</label>
                                    <input type="date" name="date" class="form-control form-control-sm fw-bold" value="{{ request('date', $date) }}" onchange="document.getElementById('logsFilterForm').submit()">
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <select name="child_id" class="form-select form-select-sm" onchange="document.getElementById('logsFilterForm').submit()">
                                        <option value="">All Children</option>
                                        @foreach ($children as $ch)
                                            <option value="{{ $ch->id }}" {{ request('child_id') == $ch->id ? 'selected' : '' }}>
                                                {{ $ch->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <select name="log_type" class="form-select form-select-sm" onchange="document.getElementById('logsFilterForm').submit()">
                                        <option value="">All Log Types</option>
                                        <option value="nap" {{ request('log_type') == 'nap' ? 'selected' : '' }}>Nap & Sleep</option>
                                        <option value="meal" {{ request('log_type') == 'meal' ? 'selected' : '' }}>Meal & Feeding</option>
                                        <option value="activity" {{ request('log_type') == 'activity' ? 'selected' : '' }}>Learning Activity</option>
                                        <option value="diaper_change" {{ request('log_type') == 'diaper_change' ? 'selected' : '' }}>Diaper Change</option>
                                        <option value="incident" {{ request('log_type') == 'incident' ? 'selected' : '' }}>Incident / Health</option>
                                        <option value="bottle" {{ request('log_type') == 'bottle' ? 'selected' : '' }}>Bottle</option>
                                        <option value="medication" {{ request('log_type') == 'medication' ? 'selected' : '' }}>Medication</option>
                                        <option value="other" {{ request('log_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" class="form-control" placeholder="Search child name..." value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                                        @if (request()->hasAny(['date', 'child_id', 'log_type', 'search']))
                                            <a href="{{ route('admin.child-daily-logs.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="bi bi-x-circle"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- All Logs Table --}}
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-list-columns-reverse me-2 text-primary"></i>Daily Records Stream &bull; {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                            </h5>
                            <div>
                                <button type="button" class="btn btn-primary btn-sm px-3 py-1 me-2" title="Log Activity" onclick="openLogModal('activity')">
                                    <i class="bi bi-plus-circle me-1"></i>Log Activity
                                </button>
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    Showing <strong>{{ $logs->total() }}</strong> total log entries
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if ($logs->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-journal-x text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="fw-bold mt-2 text-secondary">No daily logs found matching your filter criteria.</h6>
                                    <p class="text-muted small">Select a child from the cards above to open their merged daily feed and record new logs.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light text-muted small text-uppercase">
                                            <tr>
                                                <th>Child</th>
                                                <th>Type</th>
                                                <th>Time / Duration</th>
                                                <th>Details & Summary</th>
                                                <th class="text-end pe-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($logs as $log)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $log->child_id, 'date' => $log->log_date->toDateString()]) }}" class="fw-bold text-dark text-decoration-none">
                                                            {{ $log->child ? $log->child->full_name : '—' }}
                                                        </a>
                                                        <div class="text-muted small">{{ $log->log_date->format('M d, Y') }}</div>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $log->type_badge_class }} fw-bold px-2 py-1">
                                                            <i class="bi {{ $log->type_icon }} me-1"></i>{{ $log->formatted_type }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($log->start_time)
                                                            <span class="fw-semibold text-dark">{{ $log->formatted_start_time }}</span>
                                                            @if ($log->end_time)
                                                                <span class="text-muted">&ndash; {{ $log->formatted_end_time }}</span>
                                                            @endif
                                                            @if ($log->duration_minutes)
                                                                <div class="text-muted small">({{ $log->formatted_duration }})</div>
                                                            @endif
                                                        @else
                                                            <span class="text-muted small">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (in_array($log->log_type, ['meal', 'bottle']))
                                                            <div class="small">
                                                                @if ($log->meal_type)
                                                                    <span class="badge bg-light text-dark border">{{ ucfirst($log->meal_type) }}</span>
                                                                @endif
                                                                @if ($log->amount_eaten)
                                                                    <strong>Ate:</strong> {{ $log->amount_eaten }}
                                                                @endif
                                                                @if ($log->items_served)
                                                                    &bull; <em>{{ $log->items_served }}</em>
                                                                @endif
                                                            </div>
                                                        @elseif ($log->log_type === 'activity')
                                                            <div class="small">
                                                                @if ($log->activityOccurrence && $log->activityOccurrence->activity)
                                                                    <span class="badge bg-primary text-white">{{ $log->activityOccurrence->activity->title }}</span>
                                                                @endif
                                                                @if ($log->is_completed)
                                                                    <span class="badge bg-success-subtle text-success">Completed</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        @if ($log->notes)
                                                            <div class="text-secondary small text-truncate" style="max-width: 320px;" title="{{ $log->notes }}">
                                                                {{ $log->notes }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <a href="{{ route('admin.child-daily-logs.child-day', ['child' => $log->child_id, 'date' => $log->log_date->toDateString()]) }}" class="btn btn-outline-primary btn-sm px-2 py-1" title="Open Child Timeline">
                                                            <i class="bi bi-clock-history me-1"></i>Timeline
                                                        </a>
                                                        <form method="POST" action="{{ route('admin.child-daily-logs.destroy', $log->id) }}" class="d-inline" onsubmit="return confirm('Delete this log entry?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm px-2 py-1" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-3">
                                    {{ $logs->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- UNIVERSAL QUICK LOG MODAL --}}
    <div class="modal fade" id="universalLogModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.child-daily-logs.store') }}" class="modal-content" id="logForm">
                @csrf
                <input type="hidden" name="log_date" value="{{ $date ?: \Carbon\Carbon::today()->toDateString() }}">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark" id="logModalTitle">
                        <i class="bi bi-journal-plus me-2"></i>Record Entry
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Child Selection --}}
                    <div class="mb-3" id="childSelectionSection">
                        <label class="form-label fw-semibold">Select Child</label>
                        <select name="child_id" id="modalChildIdSelect" class="form-select" required>
                            <option value="">-- Select Child --</option>
                            @foreach ($presentChildren as $child)
                                <option value="{{ $child->id }}">{{ $child->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Log Type --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Entry Type</label>
                        <select name="log_type" id="modalLogType" class="form-select" onchange="toggleLogFields(this.value)" required>
                            <option value="nap">Nap & Sleep</option>
                            <option value="meal">Meal & Nutrition</option>
                            <option value="bottle">Bottle Feeding</option>
                            <option value="activity">Learning Activity</option>
                            <option value="diaper_change">Diaper / Restroom</option>
                            <option value="incident">Incident / Health</option>
                            <option value="medication">Medication Administered</option>
                            <option value="special_program">Special Program</option>
                            <option value="other">General Note</option>
                        </select>
                    </div>

                    {{-- Time range --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Start Time</label>
                            <input type="time" name="start_time" id="modalStartTime" class="form-control" value="{{ \Carbon\Carbon::now()->format('H:i') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" id="endTimeLabel">End Time</label>
                            <input type="time" name="end_time" id="modalEndTime" class="form-control">
                        </div>
                    </div>

                    {{-- Meal-specific section --}}
                    <div id="mealSection" style="display: none;">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Meal Category</label>
                                <select name="meal_type" id="modalMealType" class="form-select">
                                    <option value="breakfast">Breakfast</option>
                                    <option value="lunch" selected>Lunch</option>
                                    <option value="snack">Snack</option>
                                    <option value="bottle">Bottle</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Appetite / Quality</label>
                                <select name="quality" id="modalQuality" class="form-select">
                                    <option value="good" selected>Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                    <option value="refused">Refused</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Amount Eaten</label>
                                <input type="text" name="amount_eaten" id="modalAmountEaten" class="form-control" placeholder="e.g. All, Most, Half, 6 oz">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Items Served</label>
                                <input type="text" name="items_served" id="modalItemsServed" class="form-control" placeholder="e.g. Pasta, apple slices">
                            </div>
                        </div>
                    </div>

                    {{-- Activity-specific section --}}
                    <div id="activitySection" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Scheduled Activity (Optional)</label>
                            <select name="activity_occurrence_id" id="modalActivityOccurrence" class="form-select">
                                <option value="">Select Scheduled Activity (or leave empty)...</option>
                                @foreach ($occurrences ?? [] as $occ)
                                    <option value="{{ $occ->id }}">
                                        {{ $occ->activity->title }} ({{ $occ->start_time ? substr($occ->start_time, 0, 5) : 'Anytime' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_completed" value="1" id="modalIsCompleted" class="form-check-input" checked>
                            <label class="form-check-label fw-semibold" for="modalIsCompleted">Child completed / actively participated in this activity</label>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" id="notesLabel">Notes / Observations</label>
                        <textarea name="notes" id="modalNotes" class="form-control" rows="3" placeholder="Describe how the child behaved, sleep soundly, comments..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="modalSubmitBtn">Save Entry</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleLogFields(type) {
            const mealSection = document.getElementById('mealSection');
            const activitySection = document.getElementById('activitySection');
            const notesLabel = document.getElementById('notesLabel');
            const endTimeLabel = document.getElementById('endTimeLabel');

            if (type === 'meal' || type === 'bottle') {
                mealSection.style.display = 'block';
                activitySection.style.display = 'none';
                notesLabel.innerText = 'Meal Notes (Optional)';
            } else if (type === 'activity') {
                mealSection.style.display = 'none';
                activitySection.style.display = 'block';
                notesLabel.innerText = 'Activity Notes & Engagement';
            } else {
                mealSection.style.display = 'none';
                activitySection.style.display = 'none';
                if (type === 'incident') {
                    notesLabel.innerText = 'Incident Description & Immediate Care *';
                } else if (type === 'nap') {
                    notesLabel.innerText = 'Sleep Observations (Optional)';
                } else {
                    notesLabel.innerText = 'Notes / Details';
                }
            }
        }

        function openLogModal(type, childId = null) {
            document.getElementById('logForm').action = "{{ route('admin.child-daily-logs.store') }}";
            // Remove PUT method spoofing if present
            const methodSpoof = document.getElementById('methodSpoofInput');
            if (methodSpoof) methodSpoof.remove();

            const childSelect = document.getElementById('modalChildIdSelect');
            const childSection = document.getElementById('childSelectionSection');
            
            if (childId) {
                childSelect.value = childId;
                childSection.style.display = 'none';
            } else {
                childSelect.value = '';
                childSection.style.display = 'block';
            }

            document.getElementById('logModalTitle').innerHTML = '<i class="bi bi-journal-plus me-2"></i>Record ' + type.charAt(0).toUpperCase() + type.slice(1).replace('_', ' ');
            document.getElementById('modalSubmitBtn').innerText = 'Save Entry';
            document.getElementById('modalLogType').value = type;
            document.getElementById('modalStartTime').value = new Date().toTimeString().substring(0, 5);
            document.getElementById('modalEndTime').value = '';
            document.getElementById('modalNotes').value = '';
            document.getElementById('modalAmountEaten').value = '';
            document.getElementById('modalItemsServed').value = '';

            toggleLogFields(type);
            new bootstrap.Modal(document.getElementById('universalLogModal')).show();
        }
    </script>
</body>
</html>
