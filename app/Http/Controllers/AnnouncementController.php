<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements with search, filters, and statistics.
     */
    public function index(Request $request)
    {
        $query = Announcement::with(['creator', 'staff']);

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
            'total'     => Announcement::count(),
            'active'    => Announcement::active()->count(),
            'parents'   => Announcement::where('audience', 'parents')->count(),
            'staff'     => Announcement::where('audience', 'staff')->count(),
        ];

        return view('admin.announcements.index', compact('announcements', 'stats'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
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
        $announcement->load(['creator', 'staff']);
        return view('admin.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
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
        DB::transaction(function () use ($announcement) {
            $announcement->delete();
        });

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
