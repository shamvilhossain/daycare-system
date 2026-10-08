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
            'name'           => 'required|string|max:255',
            'service_type'   => 'required|in:full_day,half_day,after_school,drop_in',
            'billing_model'  => 'required|in:monthly,daily,hourly',
            'min_age_months' => 'nullable|integer|min:0',
            'max_age_months' => [
                'nullable',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null && $request->filled('min_age_months') && (int)$value < (int)$request->input('min_age_months')) {
                        $fail('The max age must be greater than or equal to min age.');
                    }
                },
            ],
            'capacity'       => 'required|integer|min:1',
            'monthly_fee'    => 'nullable|numeric|min:0',
            'daily_rate'     => 'nullable|numeric|min:0',
            'hourly_rate'    => 'nullable|numeric|min:0',
            'day_start_time' => 'nullable|date_format:H:i,H:i:s',
            'day_end_time'   => [
                'nullable',
                'date_format:H:i,H:i:s',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->filled('day_start_time')) {
                        $start = strtotime($request->input('day_start_time'));
                        $end = strtotime($value);
                        if ($start !== false && $end !== false && $end <= $start) {
                            $fail('The end time must be after the start time.');
                        }
                    }
                },
            ],
            'is_active'      => 'nullable',
        ]);
        
        $validated['is_active'] = $request->boolean('is_active');

        try {
            DB::transaction(function () use ($validated) {
                Program::create($validated);
            });

            return redirect()->route('admin.programs.index')
                ->with('success', 'Program created successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Failed to create program: ' . $e->getMessage());
        }
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
            'name'           => 'required|string|max:255',
            'service_type'   => 'required|in:full_day,half_day,after_school,drop_in',
            'billing_model'  => 'required|in:monthly,daily,hourly',
            'min_age_months' => 'nullable|integer|min:0',
            'max_age_months' => [
                'nullable',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value !== null && $request->filled('min_age_months') && (int)$value < (int)$request->input('min_age_months')) {
                        $fail('The max age must be greater than or equal to min age.');
                    }
                },
            ],
            'capacity'       => 'required|integer|min:1',
            'monthly_fee'    => 'nullable|numeric|min:0',
            'daily_rate'     => 'nullable|numeric|min:0',
            'hourly_rate'    => 'nullable|numeric|min:0',
            'day_start_time' => 'nullable|date_format:H:i,H:i:s',
            'day_end_time'   => [
                'nullable',
                'date_format:H:i,H:i:s',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->filled('day_start_time')) {
                        $start = strtotime($request->input('day_start_time'));
                        $end = strtotime($value);
                        if ($start !== false && $end !== false && $end <= $start) {
                            $fail('The end time must be after the start time.');
                        }
                    }
                },
            ],
            'is_active'      => 'nullable',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        try {
            DB::transaction(function () use ($program, $validated) {
                $program->update($validated);
            });

            return redirect()->route('admin.programs.index')
                ->with('success', 'Program updated successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'Failed to update program: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Program $program)
    {
        if ($program->enrollments()->exists()) {
            return redirect()->route('admin.programs.index')
                ->with('error', 'Cannot delete program because it has enrolled children.');
        }

        try {
            DB::transaction(function () use ($program) {
                $program->delete();
            });

            return redirect()->route('admin.programs.index')
                ->with('success', 'Program deleted successfully.');
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('admin.programs.index')
                ->with('error', 'Failed to delete program: ' . $e->getMessage());
        }
    }
}
