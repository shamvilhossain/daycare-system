<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ParentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Base for parent portal controllers.
 *
 * Everything is resolved by the ParentChildAccess middleware from the logged-in
 * parent's own profile, so queries built from these helpers are scoped by construction.
 */
abstract class ParentController extends Controller
{
    protected function parentProfile(Request $request): ParentProfile
    {
        return $request->attributes->get('parentProfile');
    }

    /** @return Collection<int, Child> */
    protected function parentChildren(Request $request): Collection
    {
        return $request->attributes->get('parentChildren');
    }

    protected function activeChild(Request $request): ?Child
    {
        return $request->attributes->get('activeChild');
    }
}
