<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends ParentController
{
    public function __invoke(Request $request): View
    {
        $child = $this->activeChild($request);

        $enrollments = collect();
        $guardians = collect();

        if ($child) {
            $enrollments = $child->enrollments()
                ->with('program')
                ->whereIn('status', ['active', 'pending'])
                ->latest('start_date')
                ->get();

            $guardians = $child->parents()->with('user')->get();
        }

        return view('parent.profile', compact('enrollments', 'guardians'));
    }
}
