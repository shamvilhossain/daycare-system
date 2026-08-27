<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear Spatie's cached permissions so this seeder is safe to re-run
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define every permission, grouped by module ──────────────
        $permissions = [
            // Children
            'children.view-any', 'children.view', 'children.create',
            'children.update', 'children.delete',

            // Guardian access (guardian_child table)
            'guardian-child.view-any', 'guardian-child.manage',

            // Programs
            'programs.view-any', 'programs.view', 'programs.create',
            'programs.update', 'programs.delete',

            // Enrollments
            'enrollments.view-any', 'enrollments.view', 'enrollments.create',
            'enrollments.update', 'enrollments.delete',


            // Attendance
            'attendance.view-any', 'attendance.view',
            'attendance.create', 'attendance.update',

            // Documents
            'documents.view-any', 'documents.create', 'documents.delete',

            // Activities (master catalog + occurrences + per-child logs)
            'activities.view-any', 'activities.create',
            'activities.update', 'activities.delete',
            'activity-occurrences.view-any', 'activity-occurrences.create',
            'activity-occurrences.update',
            'child-daily-logs.view-any', 'child-daily-logs.view',
            'child-daily-logs.create', 'child-daily-logs.update',

            // Billing
            'invoices.view-any', 'invoices.view',
            'invoices.create', 'invoices.update',
            'payments.view-any', 'payments.create',

            // Staff
            'staff.view-any', 'staff.create', 'staff.update', 'staff.delete',

            // Announcements
            'announcements.view-any', 'announcements.create',

            // Reports & settings
            'reports.view',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',
            ]);
        }

        // ── Admin: gets every permission ─────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // ── Staff: day-to-day operational permissions ────────────────
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions([
            'children.view-any', 'children.view',
            'enrollments.view-any', 'enrollments.view',
     
            'attendance.view-any', 'attendance.view',
            'attendance.create', 'attendance.update',
            'documents.view-any', 'documents.create',
            'activities.view-any',
            'activity-occurrences.view-any', 'activity-occurrences.create',
            'activity-occurrences.update',
            'child-daily-logs.view-any', 'child-daily-logs.view',
            'child-daily-logs.create', 'child-daily-logs.update',
            'announcements.view-any',
        ]);

        // ── Parent: read-only, scoped to their own children ──────────
        $parent = Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        $parent->syncPermissions([
            'children.view-any', 'children.view',
            'enrollments.view-any', 'enrollments.view',
        
            'attendance.view-any', 'attendance.view',
            'documents.view-any',
            'child-daily-logs.view-any', 'child-daily-logs.view',
            'activity-occurrences.view-any',
            'invoices.view-any', 'invoices.view',
            'payments.view-any',
            'announcements.view-any',
        ]);
    }
}
