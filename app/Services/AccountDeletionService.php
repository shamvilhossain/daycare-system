<?php

namespace App\Services;

use App\Models\AccountDeletionRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Account & data deletion for parent accounts (Google Play requirement).
 *
 * Parent accounts are anonymised rather than hard-deleted: invoices and payments
 * reference the parent profile and must be kept for the daycare's financial records,
 * and child records belong to the daycare's care records.
 */
class AccountDeletionService
{
    /**
     * Record a deletion request. Web requests are matched to an account only when
     * both the email and the parent's mobile number match.
     */
    public function submit(array $data, string $source, ?User $user = null): AccountDeletionRequest
    {
        $email = strtolower(trim($data['email'] ?? $user?->email ?? ''));
        $mobile = isset($data['mobile']) ? preg_replace('/[\s\-]/', '', (string) $data['mobile']) : null;

        if (!$user && $email !== '') {
            $candidate = User::where('email', $email)->with('parentProfile')->first();
            if ($candidate?->parentProfile && $mobile && $this->sameMobile($candidate->parentProfile->mobile, $mobile)) {
                $user = $candidate;
            }
        }

        // One open request per account is enough.
        if ($user) {
            $existing = AccountDeletionRequest::pending()->where('user_id', $user->id)->first();
            if ($existing) {
                return $existing;
            }
        }

        return AccountDeletionRequest::create([
            'user_id'        => $user?->id,
            'email'          => $user?->email ?? $email,
            'mobile'         => $mobile ?: $user?->parentProfile?->mobile,
            'requester_name' => $data['requester_name'] ?? $user?->parentProfile?->full_name,
            'reason'         => $data['reason'] ?? null,
            'source'         => $source,
            'status'         => 'pending',
        ]);
    }

    /**
     * Complete a request: anonymise the linked parent account (if any) and close the request.
     *
     * @throws RuntimeException when the linked account is not a plain parent account
     */
    public function complete(AccountDeletionRequest $request, User $admin, ?string $notes = null): void
    {
        DB::transaction(function () use ($request, $admin, $notes) {
            $user = $request->user;

            if ($user) {
                $this->anonymiseParent($user);

                // Close any other open requests for the same account.
                AccountDeletionRequest::pending()
                    ->where('user_id', $user->id)
                    ->where('id', '!=', $request->id)
                    ->update([
                        'status'       => 'completed',
                        'admin_notes'  => 'Closed together with request #' . $request->id,
                        'processed_by' => $admin->id,
                        'processed_at' => now(),
                    ]);
            }

            $request->update([
                'status'       => 'completed',
                'admin_notes'  => $notes,
                'processed_by' => $admin->id,
                'processed_at' => now(),
            ]);
        });
    }

    public function reject(AccountDeletionRequest $request, User $admin, ?string $notes = null): void
    {
        $request->update([
            'status'       => 'rejected',
            'admin_notes'  => $notes,
            'processed_by' => $admin->id,
            'processed_at' => now(),
        ]);
    }

    /**
     * Remove personal data from a parent account and make it unusable.
     *
     * Kept: the (anonymised) parent profile row, because invoices/payments reference it.
     * Removed: name, contact details, NID, address, photo, login, roles, sessions,
     * and the links to children.
     */
    public function anonymiseParent(User $user): void
    {
        if ($user->is_super_admin || $user->hasRole(['admin', 'staff']) || $user->staffProfile) {
            throw new RuntimeException('Only parent accounts can be deleted through this flow. Manage staff/admin accounts from Users & Accounts.');
        }

        $oldEmail = $user->email;
        $profile = $user->parentProfile;

        if ($profile) {
            if ($profile->image && !Str::startsWith($profile->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete(Str::after($profile->image, 'storage/'));
            }

            $profile->children()->detach();

            $profile->update([
                'first_name' => 'Deleted',
                'last_name'  => 'Parent #' . $profile->id,
                'nid'        => null,
                'mobile'     => '',
                'occupation' => null,
                'city'       => null,
                'address'    => null,
                'image'      => null,
            ]);
        }

        $user->syncRoles([]);
        $user->forceFill([
            'email'             => 'deleted-user-' . $user->id . '-' . Str::lower(Str::random(8)) . '@deleted.invalid',
            'password'          => Str::random(64),
            'is_active'         => false,
            'remember_token'    => null,
            'email_verified_at' => null,
        ])->save();

        DB::table('sessions')->where('user_id', $user->id)->delete();
        DB::table('password_reset_tokens')->where('email', $oldEmail)->delete();
    }

    private function sameMobile(?string $a, ?string $b): bool
    {
        $normalise = fn (?string $m) => substr(preg_replace('/\D/', '', (string) $m), -10);

        return $normalise($a) !== '' && $normalise($a) === $normalise($b);
    }
}
