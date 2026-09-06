<?php

namespace App\Http\Controllers;

use App\Http\Requests\Activity\StoreActivityRequest;
use App\Http\Requests\Activity\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Display a listing of activities (master catalog).
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Activity::withCount('occurrences');

        // Search by keyword
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('materials_needed', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        // Filter by active status
        if ($request->filled('status')) {
            $isActive = $request->query('status') === 'active';
            $query->where('is_active', $isActive);
        }

        $activities = $query->orderBy('name')->paginate(12)->withQueryString();

        $categories = [
            'art'          => 'Art',
            'music'        => 'Music',
            'outdoor'      => 'Outdoor',
            'reading'      => 'Reading',
            'motor_skills' => 'Motor Skills',
            'sensory'      => 'Sensory',
            'cognitive'    => 'Cognitive',
            'social'       => 'Social',
            'language'     => 'Language',
            'math'         => 'Math',
            'science'      => 'Science',
            'other'        => 'Other',
        ];

        $stats = [
            'total'        => Activity::count(),
            'active'       => Activity::where('is_active', true)->count(),
            'categories'   => Activity::distinct('category')->count('category'),
            'occurrences'  => Activity::withCount('occurrences')->get()->sum('occurrences_count'),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'activities' => $activities,
                'stats'      => $stats,
            ]);
        }

        return view('admin.activities.index', compact('activities', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new activity.
     */
    public function create(): View
    {
        $categories = [
            'art'          => 'Art',
            'music'        => 'Music',
            'outdoor'      => 'Outdoor',
            'reading'      => 'Reading',
            'motor_skills' => 'Motor Skills',
            'sensory'      => 'Sensory',
            'cognitive'    => 'Cognitive',
            'social'       => 'Social',
            'language'     => 'Language',
            'math'         => 'Math',
            'science'      => 'Science',
            'other'        => 'Other',
        ];

        return view('admin.activities.create', compact('categories'));
    }

    /**
     * Store a newly created activity in storage.
     */
    public function store(StoreActivityRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $activity = Activity::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => "Activity '{$activity->name}' created successfully.",
                'activity' => $activity,
            ], 201);
        }

        return redirect()->route('admin.activities.index')
            ->with('success', "Activity '{$activity->name}' added to catalog successfully.");
    }

    /**
     * Display the specified activity with its scheduled occurrences.
     */
    public function show(Request $request, Activity $activity): View|JsonResponse
    {
        $occurrences = $activity->occurrences()
            ->with(['program', 'staff', 'media'])
            ->withCount('childDailyLogs')
            ->orderByDesc('occurrence_date')
            ->orderByDesc('start_time')
            ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'activity'    => $activity,
                'occurrences' => $occurrences,
            ]);
        }

        return view('admin.activities.show', compact('activity', 'occurrences'));
    }

    /**
     * Show the form for editing the specified activity.
     */
    public function edit(Activity $activity): View
    {
        $categories = [
            'art'          => 'Art',
            'music'        => 'Music',
            'outdoor'      => 'Outdoor',
            'reading'      => 'Reading',
            'motor_skills' => 'Motor Skills',
            'sensory'      => 'Sensory',
            'cognitive'    => 'Cognitive',
            'social'       => 'Social',
            'language'     => 'Language',
            'math'         => 'Math',
            'science'      => 'Science',
            'other'        => 'Other',
        ];

        return view('admin.activities.edit', compact('activity', 'categories'));
    }

    /**
     * Update the specified activity in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $activity->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => "Activity '{$activity->name}' updated successfully.",
                'activity' => $activity,
            ]);
        }

        return redirect()->route('admin.activities.index')
            ->with('success', "Activity '{$activity->name}' updated successfully.");
    }

    /**
     * Toggle the active status of an activity.
     */
    public function toggleStatus(Activity $activity): RedirectResponse|JsonResponse
    {
        $activity->is_active = !$activity->is_active;
        $activity->save();

        $statusText = $activity->is_active ? 'activated' : 'deactivated';

        if (request()->wantsJson()) {
            return response()->json([
                'success'   => true,
                'message'   => "Activity has been {$statusText}.",
                'is_active' => $activity->is_active,
            ]);
        }

        return back()->with('success', "Activity '{$activity->name}' has been {$statusText}.");
    }

    /**
     * Remove the specified activity from storage.
     */
    public function destroy(Activity $activity): RedirectResponse|JsonResponse
    {
        // If activity has occurrences, prevent deletion to preserve audit history
        if ($activity->occurrences()->exists()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete '{$activity->name}' because it has recorded occurrences. Deactivate it instead.",
                ], 422);
            }

            return back()->withErrors([
                'error' => "Cannot delete '{$activity->name}' because it has recorded history/occurrences. You can deactivate it instead."
            ]);
        }

        $name = $activity->name;
        $activity->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Activity '{$name}' deleted successfully.",
            ]);
        }

        return redirect()->route('admin.activities.index')
            ->with('success', "Activity '{$name}' removed from catalog.");
    }
}
