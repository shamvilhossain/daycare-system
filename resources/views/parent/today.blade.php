@extends('parent.layouts.app')

@section('title', 'Today')

@section('content')
    {{-- Date pager --}}
    <div class="pp-datebar">
        <a href="{{ route('parent.today', ['date' => $date->copy()->subDay()->toDateString()]) }}" class="btn btn-light border" aria-label="Previous day">
            <i class="bi bi-chevron-left"></i>
        </a>
        <div class="text-center">
            <div class="pp-page-title mb-0">{{ $isToday ? 'Today' : $date->format('l') }}</div>
            <div class="text-muted small">{{ $date->format('j F Y') }}</div>
        </div>
        @if ($isToday)
            <span class="btn btn-light border disabled" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
        @else
            <a href="{{ route('parent.today', ['date' => $date->copy()->addDay()->toDateString()]) }}" class="btn btn-light border" aria-label="Next day">
                <i class="bi bi-chevron-right"></i>
            </a>
        @endif
    </div>

    {{-- Attendance --}}
    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-door-open"></i> Attendance</div>
        @if ($attendance)
            <div class="d-flex gap-2 mb-2">
                <div class="pp-stat">
                    <div class="pp-stat-value">{{ $attendance->formatted_check_in_time ?? '—' }}</div>
                    <div class="pp-stat-label">Check-in</div>
                </div>
                <div class="pp-stat">
                    <div class="pp-stat-value">{{ $attendance->formatted_check_out_time ?? '—' }}</div>
                    <div class="pp-stat-label">Check-out</div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center small">
                <span class="badge {{ $attendance->status_badge_class }} rounded-pill px-3 py-2">{{ $attendance->status_label }}</span>
                <span class="text-muted">
                    @if ($attendance->is_checked_in)
                        <i class="bi bi-circle-fill text-success me-1" style="font-size:.5rem"></i>At daycare now
                    @elseif ($attendance->is_checked_out)
                        {{ $attendance->formatted_duration }} at daycare
                    @endif
                </span>
            </div>
            @if ($attendance->notes)
                <div class="mt-2 small text-muted"><i class="bi bi-chat-left-text me-1"></i>{{ $attendance->notes }}</div>
            @endif
        @else
            <div class="text-muted">No attendance recorded {{ $isToday ? 'yet today' : 'for this day' }}.</div>
        @endif
    </div>

    {{-- Meals --}}
    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-egg-fried"></i> Meals</div>
        @forelse ($meals as $log)
            <div class="pp-list-item">
                <span class="pp-icon-bubble"><i class="bi {{ $log->type_icon }}"></i></span>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">{{ $log->meal_type ? ucfirst($log->meal_type) : $log->formatted_type }}</span>
                        <span class="text-muted small">{{ $log->formatted_start_time }}</span>
                    </div>
                    @if ($log->items_served)
                        <div class="small">{{ $log->items_served }}</div>
                    @endif
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        @if ($log->amount_eaten)
                            <span class="pp-chip">Ate: {{ $log->amount_eaten }}</span>
                        @endif
                        @if ($log->quality)
                            <span class="badge {{ $log->quality_badge_class }} rounded-pill">{{ ucfirst($log->quality) }}</span>
                        @endif
                    </div>
                    @if ($log->notes)
                        <div class="small text-muted mt-1">{{ $log->notes }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-muted">No meals logged.</div>
        @endforelse
    </div>

    {{-- Nap --}}
    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-moon-stars"></i> Nap</div>
        @forelse ($naps as $log)
            <div class="pp-list-item">
                <span class="pp-icon-bubble"><i class="bi bi-moon-stars-fill"></i></span>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">
                            {{ $log->formatted_start_time ?? '—' }}
                            @if ($log->formatted_end_time) – {{ $log->formatted_end_time }} @endif
                        </span>
                        <span class="text-muted small">{{ $log->formatted_duration }}</span>
                    </div>
                    @if ($log->quality)
                        <span class="badge {{ $log->quality_badge_class }} rounded-pill mt-1">{{ ucfirst($log->quality) }}</span>
                    @endif
                    @if ($log->notes)
                        <div class="small text-muted mt-1">{{ $log->notes }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-muted">No nap logged.</div>
        @endforelse
    </div>

    {{-- Other updates --}}
    @if ($otherLogs->isNotEmpty())
        <div class="pp-card">
            <div class="pp-card-title"><i class="bi bi-journal-text"></i> Other updates</div>
            @foreach ($otherLogs as $log)
                <div class="pp-list-item">
                    <span class="pp-icon-bubble {{ in_array($log->log_type, ['incident', 'medication']) ? 'bg-danger-subtle text-danger' : '' }}">
                        <i class="bi {{ $log->type_icon }}"></i>
                    </span>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">
                                {{ $log->log_type === 'activity' && $log->activityOccurrence?->activity ? $log->activityOccurrence->activity->name : $log->formatted_type }}
                            </span>
                            <span class="text-muted small">{{ $log->formatted_start_time }}</span>
                        </div>
                        @if ($log->notes)
                            <div class="small text-muted">{{ $log->notes }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Photos shortcut --}}
    <a href="{{ route('parent.photos') }}" class="pp-card d-flex align-items-center gap-3">
        <span class="pp-icon-bubble"><i class="bi bi-images"></i></span>
        <div class="flex-grow-1">
            <div class="fw-bold">{{ $photoCount }} {{ Str::plural('photo', $photoCount) }} from this day</div>
            <div class="small text-muted">See all class photos</div>
        </div>
        <i class="bi bi-chevron-right text-muted"></i>
    </a>
@endsection
