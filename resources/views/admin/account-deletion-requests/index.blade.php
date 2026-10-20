<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Deletion Requests | KinderCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-banner {
            background: linear-gradient(135deg, #b91c1c 0%, #e11d48 50%, #8b5cf6 100%);
            border-radius: 12px; padding: 0.75rem 1.25rem; color: #fff; margin-bottom: 0.75rem;
            position: relative; overflow: hidden;
            box-shadow: 0 4px 15px rgba(225, 29, 72, 0.15);
        }
        .page-banner h2 { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.15rem; }
        .page-banner p { font-size: 0.82rem; opacity: 0.88; margin: 0; }
        .stat-card { border-radius: 12px; }
        .stat-card .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
        }
        .table th { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; font-weight: 600; }
        .table td { vertical-align: middle; }
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

        <main class="app-main">
            <div class="app-content-header pt-2 pb-0">
                <div class="container-fluid">
                    <div class="page-banner d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h2><i class="bi bi-person-x-fill me-2"></i>Account Deletion Requests</h2>
                            <p>Requests from parents to delete their account (required by Google Play). Process within {{ config('parentapp.deletion_days') }} days.</p>
                        </div>
                        <a href="{{ route('account-deletion.show') }}" target="_blank" class="btn btn-light btn-sm fw-bold shadow-sm px-3">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Public page
                        </a>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                        </div>
                    @endif

                    {{-- Stats --}}
                    <div class="row g-3 mb-4">
                        @foreach ([
                            ['Pending', $stats['pending'], 'bi-hourglass-split', 'bg-warning-subtle text-warning'],
                            ['Overdue', $stats['overdue'], 'bi-alarm', 'bg-danger-subtle text-danger'],
                            ['Completed', $stats['completed'], 'bi-check2-circle', 'bg-success-subtle text-success'],
                            ['Rejected', $stats['rejected'], 'bi-x-circle', 'bg-secondary-subtle text-secondary'],
                        ] as [$label, $value, $icon, $class])
                            <div class="col-6 col-lg-3">
                                <div class="card stat-card border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <div class="stat-icon {{ $class }}"><i class="bi {{ $icon }}"></i></div>
                                        <div>
                                            <div class="fs-4 fw-bold text-dark">{{ $value }}</div>
                                            <div class="text-muted small">{{ $label }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Filters --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.account-deletion-requests.index') }}" class="row g-2 align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email or mobile..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        @foreach (['pending' => 'Pending', 'completed' => 'Completed', 'rejected' => 'Rejected'] as $value => $label)
                                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
                                    @if (request()->hasAny(['search', 'status']))
                                        <a href="{{ route('admin.account-deletion-requests.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3 py-3">#</th>
                                            <th class="py-3">Requester</th>
                                            <th class="py-3">Linked account</th>
                                            <th class="py-3">Source</th>
                                            <th class="py-3">Requested</th>
                                            <th class="py-3">Status</th>
                                            <th class="text-end pe-3 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($requests as $req)
                                            @php
                                                $dueBy = $req->created_at->copy()->addDays(config('parentapp.deletion_days'));
                                                $match = !$req->user_id ? $possibleMatches->get(strtolower($req->email)) : null;
                                            @endphp
                                            <tr>
                                                <td class="ps-3 text-muted">{{ $req->id }}</td>
                                                <td>
                                                    <div class="fw-semibold">{{ $req->requester_name ?: '—' }}</div>
                                                    <div class="small text-muted">{{ $req->email }}</div>
                                                    @if ($req->mobile)<div class="small text-muted">{{ $req->mobile }}</div>@endif
                                                    @if ($req->reason)
                                                        <div class="small fst-italic text-muted mt-1" style="max-width: 320px;">“{{ Str::limit($req->reason, 120) }}”</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($req->user)
                                                        <div class="small fw-semibold">{{ $req->user->name }}</div>
                                                        <div class="small text-muted">User #{{ $req->user->id }} · {{ $req->user->is_active ? 'Active' : 'Inactive' }}</div>
                                                        @if ($req->user->parentProfile?->children?->isNotEmpty())
                                                            <div class="small text-muted">Children: {{ $req->user->parentProfile->children->pluck('full_name')->implode(', ') }}</div>
                                                        @endif
                                                    @elseif ($match)
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Not verified</span>
                                                        <div class="small text-muted mt-1">
                                                            Email matches {{ $match->name }} (#{{ $match->id }}), mobile on file: {{ $match->parentProfile?->mobile ?: '—' }}
                                                        </div>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">No matching account</span>
                                                    @endif
                                                </td>
                                                <td><span class="small">{{ $req->source_label }}</span></td>
                                                <td>
                                                    <div class="small">{{ $req->created_at->format('M d, Y h:i A') }}</div>
                                                    @if ($req->status === 'pending')
                                                        <div class="small {{ $dueBy->isPast() ? 'text-danger fw-semibold' : 'text-muted' }}">
                                                            Due by {{ $dueBy->format('M d, Y') }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $req->status_badge_class }} px-2 py-1">{{ $req->status_label }}</span>
                                                    @if ($req->processed_at)
                                                        <div class="small text-muted mt-1">
                                                            {{ $req->processed_at->format('M d, Y') }}@if ($req->processedBy) · {{ $req->processedBy->name }}@endif
                                                        </div>
                                                    @endif
                                                    @if ($req->admin_notes)
                                                        <div class="small text-muted" style="max-width: 220px;">{{ Str::limit($req->admin_notes, 80) }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-3">
                                                    @if ($req->status === 'pending')
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#completeModal{{ $req->id }}">
                                                                <i class="bi bi-person-x me-1"></i> Delete
                                                            </button>
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                                                Reject
                                                            </button>
                                                        </div>

                                                        {{-- Complete modal --}}
                                                        <div class="modal fade" id="completeModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered text-start">
                                                                <form method="POST" action="{{ route('admin.account-deletion-requests.complete', $req) }}" class="modal-content">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <div class="modal-header border-0 pb-0">
                                                                        <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Delete account</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        @if ($req->user)
                                                                            <p class="mb-2">This will permanently <strong>anonymise and deactivate</strong> the account of <strong>{{ $req->user->name }}</strong>:</p>
                                                                            <ul class="small">
                                                                                <li>Removes name, email, mobile, NID, address and photo</li>
                                                                                <li>Removes sign-in access, roles and active sessions</li>
                                                                                <li>Unlinks the account from their children</li>
                                                                                <li>Keeps invoices/payments and child care records (without the parent's details)</li>
                                                                            </ul>
                                                                        @elseif ($match)
                                                                            <p class="small mb-2">
                                                                                This web request was <strong>not verified automatically</strong> (the mobile number did not match).
                                                                                Only link it after confirming the requester is the account holder, e.g. by calling the number on file.
                                                                            </p>
                                                                            <div class="form-check mb-2">
                                                                                <input class="form-check-input" type="checkbox" name="link_user_id" value="{{ $match->id }}" id="link{{ $req->id }}">
                                                                                <label class="form-check-label small" for="link{{ $req->id }}">
                                                                                    I verified the requester — delete account of {{ $match->name }} (#{{ $match->id }})
                                                                                </label>
                                                                            </div>
                                                                            <p class="small text-muted mb-2">Leave unticked to close the request without deleting any account.</p>
                                                                        @else
                                                                            <p class="small mb-2">No account matches this request. Completing it only closes the request.</p>
                                                                        @endif
                                                                        <label class="form-label small fw-semibold">Notes (optional)</label>
                                                                        <textarea name="admin_notes" rows="2" class="form-control" maxlength="1000"></textarea>
                                                                    </div>
                                                                    <div class="modal-footer border-0 pt-0">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-danger">{{ $req->user || $match ? 'Confirm' : 'Close request' }}</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>

                                                        {{-- Reject modal --}}
                                                        <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered text-start">
                                                                <form method="POST" action="{{ route('admin.account-deletion-requests.reject', $req) }}" class="modal-content">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <div class="modal-header border-0 pb-0">
                                                                        <h5 class="modal-title">Reject request</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p class="small text-muted">Use this only for requests that could not be verified, are duplicates, or were withdrawn by the parent.</p>
                                                                        <label class="form-label small fw-semibold">Reason (required)</label>
                                                                        <textarea name="admin_notes" rows="2" class="form-control" required maxlength="1000"></textarea>
                                                                    </div>
                                                                    <div class="modal-footer border-0 pt-0">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-dark">Reject</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5 text-muted">
                                                    <i class="bi bi-person-check display-4 d-block mb-3 opacity-25"></i>
                                                    <h5>No deletion requests</h5>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($requests->hasPages())
                            <div class="card-footer bg-white border-0 py-3">{{ $requests->links() }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
