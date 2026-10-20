@extends('legal.layout')

@section('title', 'Delete Account')

@section('content')
    <div class="pp-card">
        <div class="pp-page-title">Delete your {{ $org['name'] }} account</div>
        <div class="pp-page-sub mb-0">
            You can ask us to delete your parent account and personal data at any time. We process requests within
            {{ $deletionDays }} days.
        </div>
    </div>

    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-trash3"></i> What we delete</div>
        <ul class="mb-0 small">
            <li>Your name, email address, mobile number, ID number, address and profile photo</li>
            <li>Your password and ability to sign in (on every device)</li>
            <li>The link between your account and your child</li>
        </ul>
    </div>

    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-archive"></i> What we keep, and why</div>
        <ul class="mb-0 small">
            <li><strong>Invoices and payments</strong>: required by accounting and tax law. They are kept without your contact details.</li>
            <li>
                <strong>Your child’s care records</strong> (attendance, care logs, therapy and incident records): kept by
                the daycare as part of its care records for as long as the law requires. Ask the office if you would like
                these reviewed.
            </li>
            <li>A record of this deletion request, as proof it was handled.</li>
        </ul>
        <div class="small text-muted mt-2">
            See our <a href="{{ route('privacy-policy') }}">privacy policy</a> for details.
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 border-0">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if ($isParent)
        @if ($pending)
            <div class="pp-card text-center">
                <i class="bi bi-hourglass-split fs-1" style="color: var(--pp-primary)"></i>
                <div class="fw-bold mt-2">Your request is being processed</div>
                <div class="small text-muted">
                    Requested {{ $pending->created_at->format('j M Y') }}. Your account will be deleted by
                    {{ $pending->created_at->copy()->addDays($deletionDays)->format('j M Y') }} at the latest.
                    Contact the office if you change your mind.
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('account-deletion.store') }}" class="pp-card">
                @csrf
                <div class="pp-card-title"><i class="bi bi-shield-lock"></i> Confirm it’s you</div>

                <div class="mb-3">
                    <label for="reason" class="form-label small fw-semibold">Reason (optional)</label>
                    <textarea id="reason" name="reason" rows="2" class="form-control" maxlength="1000">{{ old('reason') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold">Your password</label>
                    <input id="password" type="password" name="password" class="form-control form-control-lg" required autocomplete="current-password">
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="confirm" value="1" id="confirm" required>
                    <label class="form-check-label small" for="confirm">
                        I understand my account will be deleted and I will no longer be able to sign in.
                    </label>
                </div>
                <button type="submit" class="btn btn-danger pp-btn-lg w-100">
                    <i class="bi bi-trash3 me-1"></i> Request account deletion
                </button>
            </form>
        @endif
    @elseif ($isOtherUser)
        <div class="pp-card small">
            Staff and administrator accounts are managed by the daycare administrator. Please contact them directly.
        </div>
    @else
        <div class="pp-card small">
            <div class="fw-bold mb-1">Have the app?</div>
            Sign in and go to <strong>More → Delete account</strong>. It’s the quickest way. Or fill in the form below and
            we will verify the request with you before deleting anything.
        </div>

        <form method="POST" action="{{ route('account-deletion.store') }}" class="pp-card">
            @csrf
            <div class="pp-card-title"><i class="bi bi-person-x"></i> Request deletion</div>

            {{-- Honeypot (hidden from people) --}}
            <div class="d-none" aria-hidden="true">
                <label for="website">Website</label>
                <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="requester_name" class="form-label small fw-semibold">Your name</label>
                <input id="requester_name" type="text" name="requester_name" value="{{ old('requester_name') }}" class="form-control form-control-lg" required maxlength="150" autocomplete="name">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold">Account email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" required autocomplete="email">
            </div>
            <div class="mb-3">
                <label for="mobile" class="form-label small fw-semibold">Mobile number on the account</label>
                <input id="mobile" type="tel" name="mobile" value="{{ old('mobile') }}" class="form-control form-control-lg" required maxlength="30" autocomplete="tel" placeholder="01XXXXXXXXX">
            </div>
            <div class="mb-3">
                <label for="guest_reason" class="form-label small fw-semibold">Reason (optional)</label>
                <textarea id="guest_reason" name="reason" rows="2" class="form-control" maxlength="1000">{{ old('reason') }}</textarea>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="confirm" value="1" id="guest_confirm" required>
                <label class="form-check-label small" for="guest_confirm">
                    I understand my account will be deleted and I will no longer be able to sign in.
                </label>
            </div>
            <button type="submit" class="btn btn-danger pp-btn-lg w-100">
                <i class="bi bi-send me-1"></i> Send deletion request
            </button>
        </form>
    @endif
@endsection
