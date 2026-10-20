<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use App\Services\AccountDeletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public "Delete your account" page (Google Play requirement).
 * - Logged-in parents confirm with their password (request linked to their account).
 * - Anyone else can submit a request with email + mobile; staff verify it before processing.
 */
class AccountDeletionController extends Controller
{
    public function __construct(private AccountDeletionService $service)
    {
    }

    public function show(Request $request): View
    {
        $user = $request->user();
        $isParent = $user?->hasRole('parent') ?? false;

        return view('legal.account-deletion', [
            'isParent'      => $isParent,
            'isOtherUser'   => $user && !$isParent,
            'pending'       => $isParent
                ? AccountDeletionRequest::pending()->where('user_id', $user->id)->latest()->first()
                : null,
            'org'           => config('parentapp.organization'),
            'deletionDays'  => config('parentapp.deletion_days'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user?->hasRole('parent')) {
            $data = $request->validate([
                'password' => ['required', 'current_password'],
                'reason'   => ['nullable', 'string', 'max:1000'],
                'confirm'  => ['accepted'],
            ], [
                'password.current_password' => 'The password is incorrect.',
                'confirm.accepted'          => 'Please confirm that you understand what will be deleted.',
            ]);

            $this->service->submit(['reason' => $data['reason'] ?? null], 'app', $user);

            return redirect()->route('account-deletion.show')
                ->with('success', 'Your deletion request has been received. We will process it within ' . config('parentapp.deletion_days') . ' days.');
        }

        if ($user) {
            abort(403, 'Staff and admin accounts are managed by the daycare administrator.');
        }

        // Honeypot: bots fill hidden fields. Pretend success.
        if ($request->filled('website')) {
            return redirect()->route('account-deletion.show')->with('success', $this->guestMessage());
        }

        $data = $request->validate([
            'requester_name' => ['required', 'string', 'max:150'],
            'email'          => ['required', 'email', 'max:255'],
            'mobile'         => ['required', 'string', 'max:30'],
            'reason'         => ['nullable', 'string', 'max:1000'],
            'confirm'        => ['accepted'],
        ], [
            'confirm.accepted' => 'Please confirm that you understand what will be deleted.',
        ]);

        $this->service->submit($data, 'web');

        // Same message whether or not an account matched (no account enumeration).
        return redirect()->route('account-deletion.show')->with('success', $this->guestMessage());
    }

    private function guestMessage(): string
    {
        return 'Thank you. If an account matches these details, the daycare will verify the request and delete it within '
            . config('parentapp.deletion_days') . ' days.';
    }
}
