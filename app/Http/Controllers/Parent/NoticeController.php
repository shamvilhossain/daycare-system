<?php

namespace App\Http\Controllers\Parent;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoticeController extends ParentController
{
    public function __invoke(Request $request): View
    {
        $notices = Announcement::active()
            ->whereIn('audience', ['parents', 'all'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate(15);

        return view('parent.notices', compact('notices'));
    }
}
