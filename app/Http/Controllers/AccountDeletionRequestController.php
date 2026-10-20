<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use App\Models\User;
use App\Services\AccountDeletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

/**
 * Admin: review and process account-deletion requests.
 */
class AccountDeletionRequestController extends Controller
{
    public function __construct(private AccountDeletionService $service)
    {
    }

    public function index(Request $request): View
    {
        $query = AccountDeletionRequest::with(['user.parentProfile.children', 'processedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('requester_name', 'like', "%{$search}%");
            });
        }

        $requests = $query
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'pending'   => AccountDeletionRequest::where('status', 'pending')->count(),
            'completed' => AccountDeletionRequest::where('status', 'completed')->count(),
            'rejected'  => AccountDeletionRequest::where('status', 'rejected')->count(),
            'overdue'   => AccountDeletionRequest::where('status', 'pending')
                ->where('created_at', '<', now()->subDays(config('parentapp.deletion_days')))
                ->count(),
        ];

        // For unmatched web requests, suggest the account with the same email (mobile didn't match).
        $possibleMatches = User::with('parentProfile')
            ->whereIn('email', $requests->getCollection()->whereNull('user_id')->pluck('email')->filter())
            ->get()
            ->keyBy(fn ($u) => strtolower($u->email));

        return view('admin.account-deletion-requests.index', compact('requests', 'stats', 'possibleMatches'));
    }

    public function complete(Request $request, AccountDeletionRequest $accountDeletionRequest): RedirectResponse
    {
        abort_unless($accountDeletionRequest->status === 'pending', 422, 'This request has already been processed.');

        $data = $request->validate([
            'admin_notes'  => ['nullable', 'string', 'max:1000'],
            'link_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        // Web request that was not auto-matched: admin verified the requester and links the account.
        if (!$accountDeletionRequest->user_id && !empty($data['link_user_id'])) {
            $linked = User::find($data['link_user_id']);
            if (strcasecmp($linked->email, $accountDeletionRequest->email) !== 0) {
                return back()->with('error', 'The linked account email does not match the request.');
            }
            $accountDeletionRequest->update(['user_id' => $linked->id]);
            $accountDeletionRequest->load('user');
        }

        try {
            $this->service->complete($accountDeletionRequest, $request->user(), $data['admin_notes'] ?? null);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $accountDeletionRequest->user_id
            ? "Request #{$accountDeletionRequest->id} completed — the parent account has been anonymised and deactivated."
            : "Request #{$accountDeletionRequest->id} marked as completed (no linked account).");
    }

    public function reject(Request $request, AccountDeletionRequest $accountDeletionRequest): RedirectResponse
    {
        abort_unless($accountDeletionRequest->status === 'pending', 422, 'This request has already been processed.');

        $data = $request->validate(['admin_notes' => ['required', 'string', 'max:1000']]);

        $this->service->reject($accountDeletionRequest, $request->user(), $data['admin_notes']);

        return back()->with('success', "Request #{$accountDeletionRequest->id} rejected.");
    }
}
