<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /** Audiences a parent may see. Staff-only notices are never shown to parents. */
    private const PARENT_AUDIENCES = ['parents', 'all'];

    private function isParentViewer(): bool
    {
        return (bool) auth()->user()?->hasRole('parent');
    }

    /** Creating, editing and deleting require announcements.create (admins always pass via Gate::before). */
    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->can('announcements.create'), 403, 'You are not allowed to manage announcements.');
    }

    /**
     * Display a listing of announcements with search, filters, and statistics.
     */
    public function index(Request $request)
    {
        $query = Announcement::with(['creator', 'staff']);
        $statsBase = Announcement::query();

        // Parents only see live notices addressed to parents or everyone
        if ($this->isParentViewer()) {
            $query->whereIn('audience', self::PARENT_AUDIENCES)->active();
            $statsBase->whereIn('audience', self::PARENT_AUDIENCES)->active();
        }

        // Search by title or content
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by audience
        if ($audience = $request->input('audience')) {
            if (in_array($audience, ['all', 'parents', 'staff'])) {
                $query->where('audience', $audience);
            }
        }

        // Filter by status
        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })->where(function ($q) {
                    $q->whereNull('published_at')->orWhere('published_at', '<=', now());
                });
            } elseif ($status === 'scheduled') {
                $query->where('published_at', '>', now());
            } elseif ($status === 'expired') {
                $query->whereNotNull('expires_at')->where('expires_at', '<=', now());
            }
        }

        $announcements = $query->orderByRaw('CASE WHEN published_at IS NULL THEN created_at ELSE published_at END DESC')
            ->paginate(10)
            ->withQueryString();

        // Statistics
        $stats = [
            'total'     => (clone $statsBase)->count(),
            'active'    => (clone $statsBase)->active()->count(),
            'parents'   => (clone $statsBase)->where('audience', 'parents')->count(),
            'staff'     => (clone $statsBase)->where('audience', 'staff')->count(),
        ];

        return view('admin.announcements.index', compact('announcements', 'stats'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        $this->authorizeManage();

        return view('admin.announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'audience'     => 'required|in:all,parents,staff',
            'published_at' => 'nullable|date',
            'expires_at'   => 'nullable|date|after_or_equal:published_at',
        ]);

        // Default published_at to now() if not supplied
        if (empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // staff_id = created by authenticated user ID (as required)
        $validated['staff_id'] = auth()->id();

        DB::transaction(function () use ($validated) {
            Announcement::create($validated);
        });

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement published successfully.');
    }

    /**
     * Display the specified announcement.
     */
    public function show(Announcement $announcement)
    {
        if ($this->isParentViewer()) {
            $visible = in_array($announcement->audience, self::PARENT_AUDIENCES, true)
                && Announcement::whereKey($announcement->id)->active()->exists();
            abort_unless($visible, 404);
        }

        $announcement->load(['creator', 'staff']);
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement)
    {
        $this->authorizeManage();

        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $this->authorizeManage();

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'audience'     => 'required|in:all,parents,staff',
            'published_at' => 'nullable|date',
            'expires_at'   => 'nullable|date|after_or_equal:published_at',
        ]);

        if (empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        DB::transaction(function () use ($announcement, $validated) {
            $announcement->update($validated);
        });

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $this->authorizeManage();

        DB::transaction(function () use ($announcement) {
            $announcement->delete();
        });

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
