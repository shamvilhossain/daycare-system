<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the parent's children and the currently selected ("active") child
 * for every /parent/* request.
 *
 * - The parent must have a parent profile.
 * - If the route carries a {child} parameter, it must belong to this parent (403 otherwise)
 *   and becomes the active child.
 * - Otherwise the active child comes from the session, falling back to the first child.
 *
 * Results are placed on the request attributes (parentProfile, parentChildren, activeChild)
 * and shared with all views.
 */
class ParentChildAccess
{
    public const SESSION_KEY = 'parent.active_child_id';

    public function handle(Request $request, Closure $next): Response
    {
        $profile = $request->user()?->parentProfile;

        if (!$profile) {
            abort(403, 'No parent profile is linked to this account.');
        }

        $children = $profile->children()->orderBy('first_name')->get();

        $routeChild = $request->route('child');
        if ($routeChild !== null) {
            $routeChildId = is_object($routeChild) ? $routeChild->getKey() : (int) $routeChild;
            $activeChild = $children->firstWhere('id', $routeChildId);

            if (!$activeChild) {
                abort(403, 'You do not have access to this child\'s records.');
            }
        } else {
            $activeChild = $children->firstWhere('id', (int) $request->session()->get(self::SESSION_KEY))
                ?? $children->first();
        }

        if ($activeChild) {
            $request->session()->put(self::SESSION_KEY, $activeChild->id);
        }

        $request->attributes->set('parentProfile', $profile);
        $request->attributes->set('parentChildren', $children);
        $request->attributes->set('activeChild', $activeChild);

        View::share([
            'parentProfile'  => $profile,
            'parentChildren' => $children,
            'activeChild'    => $activeChild,
        ]);

        return $next($request);
    }
}
