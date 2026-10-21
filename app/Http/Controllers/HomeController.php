<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Program;
use App\Models\TherapyService;
use Illuminate\View\View;

/**
 * Public home page: daycare programs + therapy center, driven by live catalog data.
 */
class HomeController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::whereIn('audience', ['parents', 'all'])
            ->where('expires_at', '>', now())
            ->where(function ($query) {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderByRaw('CASE WHEN published_at IS NULL THEN created_at ELSE published_at END DESC')
            ->get();

        $programs = Program::where('is_active', true)
            ->orderByRaw('COALESCE(min_age_months, 0)')
            ->get()
            ->map(fn (Program $p) => [
                'name'      => $p->name,
                'type'      => $p->service_type_label,
                'ages'      => $this->ageRange($p->min_age_months, $p->max_age_months),
                'from'      => $p->min_age_months !== null ? $this->ageLabel($p->min_age_months) : null,
                'hours'     => $p->day_start_time && $p->day_end_time
                    ? date('g:i A', strtotime($p->day_start_time)) . ' – ' . date('g:i A', strtotime($p->day_end_time))
                    : null,
                'seats'     => $p->capacity ? $p->available_capacity : null,
                'is_full'   => $p->capacity ? $p->is_full : false,
            ]);

        $therapies = TherapyService::where('is_active', true)->orderBy('name')->get()
            ->groupBy('therapy_type')
            ->map(fn ($services, $type) => [
                'type'     => $type,
                'services' => $services,
            ])
            ->values();

        return view('welcome', compact('announcements', 'programs', 'therapies'));
    }

    /** "3 months", "2 years" — whole years from 2 years up (71 months reads as 5 years). */
    private function ageLabel(int $months): string
    {
        return $months < 24 ? $months . ' months' : intdiv($months, 12) . ' years';
    }

    private function ageRange(?int $min, ?int $max): string
    {
        $fmt = fn (int $months) => str_replace([' months', ' years'], [' mo', ' yrs'], $this->ageLabel($months));

        // Both in years → share the unit: "3 – 5 yrs"
        if ($min !== null && $max !== null && $min >= 24) {
            return intdiv($min, 12) . ' – ' . intdiv($max, 12) . ' yrs';
        }

        return match (true) {
            $min !== null && $max !== null => $fmt($min) . ' – ' . $fmt($max),
            $min !== null => 'From ' . $fmt($min),
            $max !== null => 'Up to ' . $fmt($max),
            default => 'All ages',
        };
    }
}
