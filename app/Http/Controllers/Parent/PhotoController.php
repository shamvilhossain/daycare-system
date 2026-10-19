<?php

namespace App\Http\Controllers\Parent;

use App\Models\ActivityMedia;
use App\Models\Child;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhotoController extends ParentController
{
    public function __invoke(Request $request): View
    {
        $child = $this->activeChild($request);

        $media = $child
            ? self::mediaQueryFor($child)
                ->with(['activityOccurrence.activity', 'activityOccurrence.program'])
                ->join('activity_occurrences', 'activity_occurrences.id', '=', 'activity_media.activity_occurrence_id')
                ->orderByDesc('activity_occurrences.occurrence_date')
                ->orderByDesc('activity_media.id')
                ->select('activity_media.*')
                ->paginate(18)
            : null;

        return view('parent.photos', [
            'media'   => $media,
            'grouped' => $media
                ? $media->getCollection()->groupBy(fn ($m) => $m->activityOccurrence->occurrence_date->toDateString())
                : collect(),
        ]);
    }

    /**
     * Media from activity occurrences of the programs the child is/was enrolled in,
     * limited to the dates the child was actually enrolled (no photos from before
     * enrollment or after withdrawal/graduation).
     */
    public static function mediaQueryFor(Child $child): Builder
    {
        $enrollments = $child->enrollments()
            ->whereIn('status', ['active', 'withdrawn', 'graduated'])
            ->get(['program_id', 'start_date', 'end_date']);

        $query = ActivityMedia::query();

        if ($enrollments->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('activityOccurrence', function (Builder $q) use ($enrollments) {
            $q->where('status', '!=', 'cancelled')
                ->where(function (Builder $w) use ($enrollments) {
                    foreach ($enrollments as $enrollment) {
                        $w->orWhere(function (Builder $x) use ($enrollment) {
                            $x->where('program_id', $enrollment->program_id)
                                ->whereDate('occurrence_date', '>=', $enrollment->start_date);

                            if ($enrollment->end_date) {
                                $x->whereDate('occurrence_date', '<=', $enrollment->end_date);
                            }
                        });
                    }
                });
        });
    }
}
