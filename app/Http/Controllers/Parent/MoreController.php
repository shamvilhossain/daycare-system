<?php

namespace App\Http\Controllers\Parent;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MoreController extends ParentController
{
    public function __invoke(Request $request): View
    {
        $noticeCount = Announcement::active()
            ->whereIn('audience', ['parents', 'all'])
            ->count();

        return view('parent.more', compact('noticeCount'));
    }
}
