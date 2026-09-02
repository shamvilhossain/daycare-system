<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = Program::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.programs.index', compact('programs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.programs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_type' => 'required|in:full_day,half_day,after_school,drop_in',
            'billing_model' => 'required|in:monthly,daily,hourly',
            'min_age_months' => 'nullable|integer|min:0',
            'max_age_months' => 'nullable|integer|gte:min_age_months',
            'capacity' => 'required|integer|min:1',
            'monthly_fee' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'day_start_time' => 'nullable|date_format:H:i',
            'day_end_time' => 'nullable|date_format:H:i|after:day_start_time',
            'is_active' => 'nullable|boolean'
        ]);
        
        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($validated) {
            Program::create($validated);
        });

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Program $program)
    {
        return view('admin.programs.show', compact('program'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Program $program)
    {
        return view('admin.programs.edit', compact('program'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_type' => 'required|in:full_day,half_day,after_school,drop_in',
            'billing_model' => 'required|in:monthly,daily,hourly',
            'min_age_months' => 'nullable|integer|min:0',
            'max_age_months' => 'nullable|integer|gte:min_age_months',
            'capacity' => 'required|integer|min:1',
            'monthly_fee' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'day_start_time' => 'nullable|date_format:H:i',
            'day_end_time' => 'nullable|date_format:H:i|after:day_start_time',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($program, $validated) {
            $program->update($validated);
        });

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        DB::transaction(function () use ($program) {
            $program->delete();
        });

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program deleted successfully.');
    }
}
