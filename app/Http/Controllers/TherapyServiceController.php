<?php

namespace App\Http\Controllers;

use App\Models\TherapyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TherapyServiceController extends Controller
{
    /**
     * Display a listing of therapy services.
     */
    public function index(Request $request)
    {
        $query = TherapyService::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($type = $request->input('therapy_type')) {
            $query->where('therapy_type', $type);
        }

        if ($request->input('status') !== null && $request->input('status') !== '') {
            $query->where('is_active', $request->input('status'));
        }

        $services = $query->orderBy('name')->paginate(15)->appends($request->query());

        return view('admin.therapy-services.index', compact('services'));
    }

    /**
     * Show the form for creating a new therapy service.
     */
    public function create()
    {
        return view('admin.therapy-services.create');
    }

    /**
     * Store a newly created therapy service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'therapy_type'     => 'required|in:slt,aba,ot',
            'description'      => 'nullable|string|max:2000',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'session_rate'     => 'required|numeric|min:0',
            'is_active'        => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($validated) {
            TherapyService::create($validated);
        });

        return redirect()->route('admin.therapy-services.index')
            ->with('success', 'Therapy service created successfully.');
    }

    /**
     * Show the form for editing a therapy service.
     */
    public function edit(TherapyService $therapyService)
    {
        return view('admin.therapy-services.edit', compact('therapyService'));
    }

    /**
     * Update the specified therapy service.
     */
    public function update(Request $request, TherapyService $therapyService)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'therapy_type'     => 'required|in:slt,aba,ot',
            'description'      => 'nullable|string|max:2000',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'session_rate'     => 'required|numeric|min:0',
            'is_active'        => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($therapyService, $validated) {
            $therapyService->update($validated);
        });

        return redirect()->route('admin.therapy-services.index')
            ->with('success', 'Therapy service updated successfully.');
    }

    /**
     * Remove the specified therapy service.
     */
    public function destroy(TherapyService $therapyService)
    {
        if ($therapyService->sessions()->exists()) {
            return back()->with('error', 'Cannot delete a service that has existing sessions.');
        }

        DB::transaction(function () use ($therapyService) {
            $therapyService->delete();
        });

        return redirect()->route('admin.therapy-services.index')
            ->with('success', 'Therapy service deleted successfully.');
    }
}
