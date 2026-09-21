<?php

namespace App\Http\Controllers\Traits;

use App\Models\Child;
use Illuminate\Support\Facades\Auth;

/**
 * Provides parent-role scoping helpers for controllers that deal with child-related data.
 *
 * When the logged-in user has the 'parent' role, queries should be restricted
 * to only their own children. For admin/staff users, no scoping is applied.
 */
trait ScopesForParent
{
    /**
     * Get the child IDs belonging to the currently logged-in parent.
     * Returns null for non-parent users (no scoping needed).
     */
    protected function getParentChildIds(): ?array
    {
        $user = Auth::user();

        if (!$user->hasRole('parent')) {
            return null;
        }

        $parentProfile = $user->parentProfile;

        if (!$parentProfile) {
            return [];
        }

        return $parentProfile->children()->pluck('children.id')->toArray();
    }

    /**
     * Get the parent_id from the parent_profiles table for the currently logged-in parent.
     * Returns null for non-parent users.
     */
    protected function getParentProfileId(): ?int
    {
        $user = Auth::user();

        if (!$user->hasRole('parent')) {
            return null;
        }

        return $user->parentProfile?->id;
    }

    /**
     * Check if the logged-in user is a parent.
     */
    protected function isParent(): bool
    {
        return Auth::user()->hasRole('parent');
    }

    /**
     * Abort 403 if the logged-in parent does not own the given child.
     */
    protected function authorizeParentAccessToChild(Child $child): void
    {
        $childIds = $this->getParentChildIds();

        if ($childIds !== null && !in_array($child->id, $childIds)) {
            abort(403, 'You do not have access to this child\'s records.');
        }
    }

    /**
     * Abort 403 if the logged-in parent does not own the child associated with a model.
     * The model must have a child_id attribute.
     */
    protected function authorizeParentAccessToChildRecord($model): void
    {
        $childIds = $this->getParentChildIds();

        if ($childIds !== null && !in_array($model->child_id, $childIds)) {
            abort(403, 'You do not have access to this record.');
        }
    }
}
