@extends('parent.layouts.app')

@section('title', 'Profile')

@php
    $relationshipLabel = fn ($r) => ucwords(str_replace('_', ' ', $r ?? 'parent'));
@endphp

@section('content')
    {{-- Child header --}}
    <div class="text-center mb-3">
        @include('parent.partials.avatar', ['child' => $activeChild, 'size' => 'lg'])
        <div class="pp-page-title mt-2 mb-0">{{ $activeChild->full_name }}</div>
        <div class="text-muted small">
            {{ $activeChild->formatted_age }} · Born {{ $activeChild->date_of_birth?->format('j M Y') }}
        </div>
    </div>

    {{-- Program --}}
    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-mortarboard"></i> Program</div>
        @forelse ($enrollments as $enrollment)
            <div class="pp-list-item">
                <div class="flex-grow-1">
                    <div class="fw-bold">{{ $enrollment->program?->name }}</div>
                    <div class="small text-muted">
                        {{ $enrollment->service_type_label }} · Since {{ $enrollment->start_date->format('j M Y') }}
                    </div>
                </div>
                <span class="badge rounded-pill {{ $enrollment->status_badge_class }}">{{ $enrollment->status_label }}</span>
            </div>
        @empty
            <div class="text-muted">Not enrolled in a program.</div>
        @endforelse
    </div>

    {{-- Health --}}
    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-clipboard2-pulse"></i> Health</div>
        <div class="mb-2">
            <div class="small text-muted fw-semibold">Allergies</div>
            <div>{{ $activeChild->allergies ?: 'None recorded' }}</div>
        </div>
        <div>
            <div class="small text-muted fw-semibold">Medical notes</div>
            <div>{{ $activeChild->medical_notes ?: 'None recorded' }}</div>
        </div>
    </div>

    {{-- Pickup & contacts --}}
    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-person-check"></i> Authorised pickup &amp; contacts</div>
        @foreach ($guardians as $guardian)
            <div class="pp-list-item">
                <span class="pp-icon-bubble"><i class="bi bi-person"></i></span>
                <div class="flex-grow-1">
                    <div class="fw-bold">
                        {{ $guardian->full_name }}
                        @if ($guardian->id === $parentProfile->id) <span class="small text-muted fw-normal">(you)</span> @endif
                    </div>
                    <div class="small text-muted">
                        {{ $relationshipLabel($guardian->pivot->relationship) }}
                        @if ($guardian->pivot->is_primary) · Primary @endif
                        @if ($guardian->mobile) · {{ $guardian->mobile }} @endif
                    </div>
                </div>
                @if ($guardian->pivot->can_pickup)
                    <span class="badge bg-success-subtle text-success rounded-pill"><i class="bi bi-check2"></i> Pickup</span>
                @endif
            </div>
        @endforeach

        @if ($activeChild->ec_name)
            <div class="pp-list-item">
                <span class="pp-icon-bubble bg-danger-subtle text-danger"><i class="bi bi-telephone"></i></span>
                <div class="flex-grow-1">
                    <div class="fw-bold">{{ $activeChild->ec_name }}</div>
                    <div class="small text-muted">
                        Emergency contact
                        @if ($activeChild->ec_relationship) · {{ $activeChild->ec_relationship }} @endif
                        @if ($activeChild->ec_phone) · {{ $activeChild->ec_phone }} @endif
                    </div>
                </div>
                @if ($activeChild->ec_authorized_pickup)
                    <span class="badge bg-success-subtle text-success rounded-pill"><i class="bi bi-check2"></i> Pickup</span>
                @endif
            </div>
        @endif

        <div class="small text-muted mt-3">
            <i class="bi bi-info-circle me-1"></i>To change these details, please contact the daycare office.
        </div>
    </div>
@endsection
