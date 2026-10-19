<?php

namespace App\Http\Controllers\Parent;

use App\Models\ActivityMedia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TodayController extends ParentController
{
    public function __invoke(Request $request): View
    {
        $child = $this->activeChild($request);

        $date = Carbon::today();
        if ($request->filled('date')) {
            try {
                $date = Carbon::parse($request->input('date'))->startOfDay();
            } catch (\Throwable) {
                // keep today
            }
        }
        if ($date->isFuture()) {
            $date = Carbon::today();
        }

        $attendance = null;
        $logs = collect();
        $photoCount = 0;

        if ($child) {
            $attendance = $child->attendances()
                ->with('program')
                ->whereDate('attendance_date', $date)
                ->latest('id')
                ->first();

            $logs = $child->dailyLogs()
                ->with(['staff', 'activityOccurrence.activity'])
                ->forDate($date)
                ->chronological()
                ->get();

            $photoCount = PhotoController::mediaQueryFor($child)
                ->whereHas('activityOccurrence', fn ($q) => $q->whereDate('occurrence_date', $date))
                ->count();
        }

        $meals = $logs->whereIn('log_type', ['meal', 'bottle']);
        $naps  = $logs->where('log_type', 'nap');
        $other = $logs->whereNotIn('log_type', ['meal', 'bottle', 'nap']);

        return view('parent.today', [
            'date'       => $date,
            'isToday'    => $date->isToday(),
            'attendance' => $attendance,
            'meals'      => $meals,
            'naps'       => $naps,
            'otherLogs'  => $other,
            'photoCount' => $photoCount,
        ]);
    }
}
