<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChildSwitchController extends ParentController
{
    /**
     * Ownership of {child} is enforced by ParentChildAccess, which also stores it
     * as the active child in the session.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $back = url()->previous();

        return str_starts_with($back, url('/parent'))
            ? redirect($back)
            : redirect()->route('parent.today');
    }
}
