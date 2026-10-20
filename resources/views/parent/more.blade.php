@extends('parent.layouts.app')

@section('title', 'More')

@section('content')
    <div class="pp-card d-flex align-items-center gap-3">
        <span class="pp-icon-bubble"><i class="bi bi-person-circle"></i></span>
        <div style="min-width:0">
            <div class="fw-bold">{{ $parentProfile->full_name }}</div>
            <div class="small text-muted text-truncate">{{ auth()->user()->email }}</div>
        </div>
    </div>

    {{-- Install app (shown by parent.js only when the browser allows it / on iOS Safari) --}}
    <div class="pp-card d-none" data-pp-install-card>
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('icons/icon-192.png') }}" alt="" width="48" height="48" class="rounded-3 flex-shrink-0">
            <div class="flex-grow-1">
                <div class="fw-bold">Install the KinderCare app</div>
                <div class="small text-muted">Open it from your home screen, full-screen.</div>
            </div>
        </div>
        <button type="button" class="btn btn-primary pp-btn-lg w-100 mt-3 d-none" data-pp-install>
            <i class="bi bi-download me-1"></i> Install app
        </button>
        <div class="small mt-3 d-none" data-pp-ios-install>
            On iPhone: tap <i class="bi bi-box-arrow-up"></i> <strong>Share</strong>, then
            <strong>Add to Home Screen</strong>.
        </div>
    </div>

    <div class="pp-card pp-menu py-1">
        <a href="{{ route('parent.notices') }}">
            <span class="pp-icon-bubble"><i class="bi bi-megaphone"></i></span>
            Notices
            @if ($noticeCount > 0)
                <span class="badge rounded-pill bg-primary ms-auto">{{ $noticeCount }}</span>
                <i class="bi bi-chevron-right ms-0"></i>
            @else
                <i class="bi bi-chevron-right"></i>
            @endif
        </a>
        @if ($activeChild)
            <a href="{{ route('parent.profile') }}">
                <span class="pp-icon-bubble"><i class="bi bi-person-vcard"></i></span>
                {{ $activeChild->first_name }}'s profile
                <i class="bi bi-chevron-right"></i>
            </a>
        @endif
        <a href="{{ route('dashboard') }}">
            <span class="pp-icon-bubble"><i class="bi bi-display"></i></span>
            Open full web portal
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>

    <div class="pp-card pp-menu py-1">
        <a href="{{ route('privacy-policy') }}">
            <span class="pp-icon-bubble"><i class="bi bi-shield-check"></i></span>
            Privacy policy
            <i class="bi bi-chevron-right"></i>
        </a>
        <a href="{{ route('account-deletion.show') }}" class="text-danger">
            <span class="pp-icon-bubble bg-danger-subtle text-danger"><i class="bi bi-person-x"></i></span>
            Delete account
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>

    <form method="POST" action="{{ route('logout') }}" data-pp-logout>
        @csrf
        <button type="submit" class="btn btn-outline-danger pp-btn-lg w-100">
            <i class="bi bi-box-arrow-right me-1"></i> Log out
        </button>
    </form>
@endsection
