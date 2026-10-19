@extends('parent.layouts.app')

@section('title', 'Therapy')

@php
    $statusClasses = [
        'scheduled' => 'bg-primary-subtle text-primary',
        'completed' => 'bg-success-subtle text-success',
        'cancelled' => 'bg-secondary-subtle text-secondary',
        'no_show'   => 'bg-warning-subtle text-warning-emphasis',
    ];
    $fmtTime = fn ($t) => $t ? \Carbon\Carbon::parse($t)->format('g:i A') : '';
@endphp

@section('content')
    <div class="pp-page-title">Therapy</div>
    <div class="pp-page-sub">Sessions and packages for {{ $activeChild->first_name }}.</div>

    {{-- Packages --}}
    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-box2-heart"></i> Active packages</div>
        @forelse ($packages as $pkg)
            <div class="pp-list-item flex-column">
                <div class="d-flex justify-content-between w-100">
                    <span class="fw-bold">{{ $pkg->package?->name ?? 'Therapy package' }}</span>
                    @if ($pkg->is_expired)
                        <span class="badge bg-danger-subtle text-danger rounded-pill">Expired</span>
                    @elseif ($pkg->expires_at)
                        <span class="small text-muted">Expires {{ $pkg->expires_at->format('j M Y') }}</span>
                    @endif
                </div>
                @foreach ($pkg->breakdown as $row)
                    @php $pct = $row['included'] > 0 ? round(($row['included'] - $row['remaining']) / $row['included'] * 100) : 0; @endphp
                    <div class="w-100 mt-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $row['service']?->name ?? 'Service' }}</span>
                            <span class="fw-bold">{{ $row['remaining'] }} of {{ $row['included'] }} left</span>
                        </div>
                        <div class="progress" style="height:8px" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: {{ $pct }}%; background: var(--pp-primary)"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="text-muted">No active therapy packages.</div>
        @endforelse
    </div>

    {{-- Upcoming --}}
    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-calendar-event"></i> Upcoming sessions</div>
        @forelse ($upcoming as $session)
            <div class="pp-list-item">
                <div class="text-center flex-shrink-0" style="width:48px">
                    <div class="small text-muted text-uppercase fw-bold">{{ $session->session_date->format('M') }}</div>
                    <div class="fs-4 fw-bold lh-1">{{ $session->session_date->format('j') }}</div>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold">{{ $session->service?->name ?? 'Therapy session' }}</div>
                    <div class="small text-muted">
                        {{ $session->session_date->format('l') }} · {{ $fmtTime($session->start_time) }} – {{ $fmtTime($session->end_time) }}
                    </div>
                    @if ($session->therapist)
                        <div class="small"><i class="bi bi-person me-1"></i>{{ $session->therapist->full_name }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-muted">No upcoming sessions.</div>
        @endforelse
    </div>

    {{-- Recent --}}
    @if ($recent->isNotEmpty())
        <div class="pp-card">
            <div class="pp-card-title"><i class="bi bi-clock-history"></i> Recent sessions</div>
            @foreach ($recent as $session)
                <div class="pp-list-item">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">{{ $session->service?->name ?? 'Therapy session' }}</span>
                            <span class="badge rounded-pill {{ $statusClasses[$session->status] ?? 'bg-light text-dark' }}">
                                {{ ucwords(str_replace('_', ' ', $session->status)) }}
                            </span>
                        </div>
                        <div class="small text-muted">
                            {{ $session->session_date->format('D, j M') }} · {{ $fmtTime($session->start_time) }}
                            @if ($session->therapist) · {{ $session->therapist->full_name }} @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
