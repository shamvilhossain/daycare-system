<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivityOccurrence\StoreActivityOccurrenceRequest;
use App\Http\Requests\ActivityOccurrence\UpdateActivityOccurrenceRequest;
use App\Models\Activity;
use App\Models\ActivityMedia;
use App\Models\ActivityOccurrence;
use App\Models\Program;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ActivityOccurrenceController extends Controller
{
    /**
     * Display a listing of activity occurrences (Daily Schedule & Operational Log).
     */
    public function index(Request $request): View|JsonResponse
    {
        $date = $request->query('date', Carbon::today()->toDateString());
        $programId = $request->query('program_id');
        $activityId = $request->query('activity_id');
        $staffId = $request->query('staff_id');
        $status = $request->query('status');

        $query = ActivityOccurrence::with(['activity', 'program', 'staff', 'media'])
            ->withCount('childDailyLogs');

        // Apply date filter unless explicitly set to 'all'
        if ($date !== 'all') {
            $query->whereDate('occurrence_date', $date);
        }

        if ($programId) {
            $query->where('program_id', $programId);
        }

        if ($activityId) {
            $query->where('activity_id', $activityId);
        }

        if ($staffId) {
            $query->where('staff_id', $staffId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $occurrences = $query->orderBy('start_time', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Calculate statistics for the active scope
        $statsBaseQuery = ActivityOccurrence::query();
        if ($date !== 'all') {
            $statsBaseQuery->whereDate('occurrence_date', $date);
        }
        if ($programId) {
            $statsBaseQuery->where('program_id', $programId);
        }

        $stats = [
            'total'     => (clone $statsBaseQuery)->count(),
            'planned'   => (clone $statsBaseQuery)->where('status', 'planned')->count(),
            'completed' => (clone $statsBaseQuery)->where('status', 'completed')->count(),
            'partial'   => (clone $statsBaseQuery)->where('status', 'partial')->count(),
            'cancelled' => (clone $statsBaseQuery)->where('status', 'cancelled')->count(),
        ];

        // Options for filter selects and modals
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $activities = Activity::where('is_active', true)->orderBy('name')->get();
        $staffMembers = Staff::orderBy('first_name')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'date'        => $date,
                'stats'       => $stats,
                'occurrences' => $occurrences,
            ]);
        }

        return view('admin.activity-occurrences.index', compact(
            'occurrences',
            'stats',
            'programs',
            'activities',
            'staffMembers',
            'date',
            'programId',
            'activityId',
            'staffId',
            'status'
        ));
    }

    /**
     * Show the form for scheduling a new activity occurrence.
     */
    public function create(Request $request): View
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $activities = Activity::where('is_active', true)->orderBy('name')->get();
        $staffMembers = Staff::orderBy('first_name')->get();

        $defaultDate = $request->query('date', Carbon::today()->toDateString());
        $selectedActivityId = $request->query('activity_id');
        $selectedProgramId = $request->query('program_id');

        return view('admin.activity-occurrences.create', compact(
            'programs',
            'activities',
            'staffMembers',
            'defaultDate',
            'selectedActivityId',
            'selectedProgramId'
        ));
    }

    /**
     * Store a newly scheduled activity occurrence.
     */
    public function store(StoreActivityOccurrenceRequest $request): RedirectResponse|JsonResponse
    {
        $occurrence = ActivityOccurrence::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Activity scheduled successfully.',
                'occurrence' => $occurrence->load(['activity', 'program', 'staff']),
            ], 201);
        }

        return redirect()->route('admin.activity-occurrences.index', ['date' => $occurrence->occurrence_date->toDateString()])
            ->with('success', "Activity '{$occurrence->activity->name}' scheduled successfully.");
    }

    /**
     * Display the specified activity occurrence details with media and participating children logs.
     */
    public function show(ActivityOccurrence $activityOccurrence): View|JsonResponse
    {
        $activityOccurrence->load([
            'activity',
            'program',
            'staff',
            'media',
            'childDailyLogs.child',
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success'    => true,
                'occurrence' => $activityOccurrence,
            ]);
        }

        return view('admin.activity-occurrences.show', compact('activityOccurrence'));
    }

    /**
     * Show the form for editing the specified activity occurrence.
     */
    public function edit(ActivityOccurrence $activityOccurrence): View
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        $activities = Activity::orderBy('name')->get();
        $staffMembers = Staff::orderBy('first_name')->get();

        return view('admin.activity-occurrences.edit', compact(
            'activityOccurrence',
            'programs',
            'activities',
            'staffMembers'
        ));
    }

    /**
     * Update the specified activity occurrence in storage.
     */
    public function update(UpdateActivityOccurrenceRequest $request, ActivityOccurrence $activityOccurrence): RedirectResponse|JsonResponse
    {
        $activityOccurrence->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Activity occurrence updated successfully.',
                'occurrence' => $activityOccurrence->load(['activity', 'program', 'staff']),
            ]);
        }

        return redirect()->route('admin.activity-occurrences.show', $activityOccurrence)
            ->with('success', 'Activity session updated successfully.');
    }

    /**
     * Quick status and observation updater.
     */
    public function updateStatus(Request $request, ActivityOccurrence $activityOccurrence): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status'         => 'required|in:planned,completed,partial,cancelled',
            'materials_used' => 'nullable|string|max:255',
            'observations'   => 'nullable|string|max:3000',
        ]);

        $activityOccurrence->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => "Status updated to '{$activityOccurrence->status_label}'.",
                'occurrence' => $activityOccurrence,
            ]);
        }

        return back()->with('success', "Activity marked as {$activityOccurrence->status_label}.");
    }

    /**
     * Upload photo or video media for an activity occurrence.
     */
    public function uploadMedia(Request $request, ActivityOccurrence $activityOccurrence): RedirectResponse|JsonResponse
    {
        $request->validate([
            'file'       => 'required|file|mimes:jpeg,jpg,png,gif,webp,mp4,mov,avi|max:20480',
            'caption'    => 'nullable|string|max:255',
            'media_type' => 'nullable|in:photo,video',
        ]);

        $file = $request->file('file');
        $mime = $file->getMimeType();
        $mediaType = str_starts_with($mime, 'video/') ? 'video' : 'photo';

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('uploads/activities', $filename, 'public');

        $media = ActivityMedia::create([
            'activity_occurrence_id' => $activityOccurrence->id,
            'file_url'               => 'storage/' . $path,
            'media_type'             => $request->input('media_type', $mediaType),
            'caption'                => $request->input('caption'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Media uploaded successfully.',
                'media'   => $media,
            ], 201);
        }

        return back()->with('success', 'Photo/Video uploaded successfully.');
    }

    /**
     * Delete an activity media item.
     */
    public function destroyMedia(ActivityMedia $activityMedia): RedirectResponse|JsonResponse
    {
        // Try removing physical file if stored in storage
        if (str_starts_with($activityMedia->file_url, 'storage/')) {
            $relativePath = str_replace('storage/', '', $activityMedia->file_url);
            Storage::disk('public')->delete($relativePath);
        }

        $activityMedia->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Media item deleted.',
            ]);
        }

        return back()->with('success', 'Media item removed successfully.');
    }

    /**
     * Remove the specified activity occurrence from storage.
     */
    public function destroy(ActivityOccurrence $activityOccurrence): RedirectResponse|JsonResponse
    {
        $date = $activityOccurrence->occurrence_date ? $activityOccurrence->occurrence_date->toDateString() : null;
        
        // Remove attached media files
        foreach ($activityOccurrence->media as $item) {
            if (str_starts_with($item->file_url, 'storage/')) {
                $relativePath = str_replace('storage/', '', $item->file_url);
                Storage::disk('public')->delete($relativePath);
            }
            $item->delete();
        }

        $activityOccurrence->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Activity occurrence removed successfully.',
            ]);
        }

        return redirect()->route('admin.activity-occurrences.index', ['date' => $date ?? 'all'])
            ->with('success', 'Activity session removed successfully.');
    }
}
