<?php

namespace App\Http\Controllers\Parent;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TherapyController extends ParentController
{
    public function __invoke(Request $request): View
    {
        $child = $this->activeChild($request);

        $upcoming = collect();
        $recent = collect();
        $packages = collect();

        if ($child) {
            $upcoming = $child->therapySessions()
                ->with(['service', 'therapist'])
                ->where('status', 'scheduled')
                ->whereDate('session_date', '>=', Carbon::today())
                ->orderBy('session_date')
                ->orderBy('start_time')
                ->limit(20)
                ->get();

            // Session notes are internal staff notes and are intentionally not shown to parents.
            $recent = $child->therapySessions()
                ->with(['service', 'therapist'])
                ->whereIn('status', ['completed', 'cancelled', 'no_show'])
                ->orderByDesc('session_date')
                ->orderByDesc('start_time')
                ->limit(10)
                ->get();

            $packages = $child->therapyPackages()
                ->with(['package', 'items.service'])
                ->withCount(['sessions as completed_sessions_count' => fn ($q) => $q->where('status', 'completed')])
                ->where('status', 'active')
                ->latest('purchased_at')
                ->get()
                ->map(function ($childPackage) {
                    $childPackage->breakdown = $childPackage->items->map(fn ($item) => [
                        'service'   => $item->service,
                        'included'  => (int) $item->sessions_included,
                        'remaining' => $item->service ? $childPackage->remainingFor($item->service) : 0,
                    ]);
                    $childPackage->is_expired = $childPackage->expires_at && $childPackage->expires_at->isPast();

                    return $childPackage;
                });
        }

        return view('parent.therapy', compact('upcoming', 'recent', 'packages'));
    }
}
