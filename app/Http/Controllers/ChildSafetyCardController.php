<?php

namespace App\Http\Controllers;

use App\Jobs\SendFoundChildAlertJob;
use App\Models\ChildFoundReport;
use App\Models\ChildSafetyTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChildSafetyCardController extends Controller
{
    /**
     * Display the public safety card for a lost child.
     */
    public function show(string $token)
    {
        $tag = ChildSafetyTag::where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $child = $tag->child;
        $child->load('parents');

        $primaryParent = $child->parentProfile;
        $guardianPhone = $primaryParent?->phone ?? $child->ec_phone;

        return view('safety.card', compact('tag', 'child', 'primaryParent', 'guardianPhone'));
    }

    /**
     * Handle the report when someone finds the child.
     */
    public function reportFound(Request $request, string $token)
    {
        $tag = ChildSafetyTag::where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $validated = $request->validate([
            'reporter_name'  => 'nullable|string|max:255',
            'reporter_phone' => 'nullable|string|max:50',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
            'message'        => 'nullable|string|max:1000',
        ]);

        $report = DB::transaction(function () use ($tag, $validated) {
            return ChildFoundReport::create([
                'child_safety_tag_id' => $tag->id,
                'reporter_name'       => $validated['reporter_name'] ?? null,
                'reporter_phone'      => $validated['reporter_phone'] ?? null,
                'latitude'            => $validated['latitude'] ?? null,
                'longitude'           => $validated['longitude'] ?? null,
                'message'             => $validated['message'] ?? null,
                'status'              => 'new',
            ]);
        });

        SendFoundChildAlertJob::dispatch($report);

        return back()->with('status', 'Alert sent! The guardian and daycare staff have been notified.');
    }
}
