<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionController extends Controller
{
    /**
     * Display roles with their permissions as a checkbox matrix.
     */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        // Group permissions by module prefix (e.g. "children", "staff", "invoices")
        $grouped = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('admin.role-permissions', compact('roles', 'permissions', 'grouped'));
    }

    /**
     * Store a newly created role with optional permissions.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => strtolower(trim($validated['name'])),
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions(
                Permission::whereIn('id', $validated['permissions'])->get()
            );
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('success', "Role \"{$role->name}\" created successfully.");
    }

    /**
     * Update the permissions for a given role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $permissionIds = $validated['permissions'] ?? [];

        $role->syncPermissions(
            Permission::whereIn('id', $permissionIds)->get()
        );

        // Clear Spatie cache so changes take effect immediately
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('success', "Permissions for \"{$role->name}\" updated successfully.");
    }

    /**
     * Delete a role (prevent deleting core roles).
     */
    public function destroy(Role $role)
    {
        $protected = ['admin', 'staff', 'parent'];

        if (in_array($role->name, $protected)) {
            return back()->with('error', "The \"{$role->name}\" role is protected and cannot be deleted.");
        }

        $roleName = $role->name;
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('success', "Role \"{$roleName}\" deleted successfully.");
    }
}
