<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    /**
     * Display a listing of the staff members with filtering and summary metrics.
     */
    public function index(Request $request)
    {
        $query = Staff::with('user')->latest();

        // Search filter (name, email, NID, notes)
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Department filter
        if ($department = $request->input('department')) {
            $query->department($department);
        }

        // Role filter
        if ($role = $request->input('role')) {
            $query->role($role);
        }

        // Specialization filter
        if ($specialization = $request->input('specialization')) {
            $query->specialization($specialization);
        }

        // Status filter
        if ($request->has('status') && $request->input('status') !== '') {
            $query->active($request->input('status'));
        }

        $staffMembers = $query->paginate(15)->withQueryString();

        // Dashboard Summary Metrics
        $counts = [
            'total'      => Staff::count(),
            'daycare'    => Staff::where('department', 'daycare')->count(),
            'therapy'    => Staff::where('department', 'therapy')->count(),
            'active'     => Staff::where('is_active', true)->count(),
            'inactive'   => Staff::where('is_active', false)->count(),
            'teachers'   => Staff::where('role', 'teacher')->count(),
            'assistants' => Staff::where('role', 'assistant')->count(),
            'therapists' => Staff::where('role', 'therapist')->count(),
            'admins'     => Staff::where('role', 'admin')->count(),
        ];

        return view('admin.staff.index', compact('staffMembers', 'counts'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        return view('admin.staff.create');
    }

    /**
     * Store a newly created staff member and corresponding user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|max:255|unique:users,email',
            'password'       => ['required', 'confirmed', Password::min(8)],
            'role'           => 'required|in:teacher,assistant,admin,therapist',
            'department'     => 'required|in:daycare,therapy',
            'specialization' => 'nullable|in:slt,aba,ot',
            'nid'            => 'nullable|string|max:50',
            'date_of_birth'  => 'nullable|date|before:today',
            'hire_date'      => 'nullable|date',
            'note'           => 'nullable|string|max:1000',
            'is_active'      => 'nullable|boolean',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('staff_images', 'public');
        }

        $isActive = $request->boolean('is_active', true);
        $userRole = $validated['role'] === 'admin' ? 'admin' : 'staff';

        DB::transaction(function () use ($validated, $imagePath, $isActive, $userRole) {
            // 1. Create User account
            $user = User::create([
                'email'     => strtolower(trim($validated['email'])),
                'password'  => $validated['password'],
                'role'      => $userRole,
                'is_active' => $isActive,
            ]);

            // 2. Assign Spatie role
            if (method_exists($user, 'assignRole')) {
                $user->assignRole($userRole);
            }

            // 3. Create Staff profile
            Staff::create([
                'user_id'        => $user->id,
                'first_name'     => trim($validated['first_name']),
                'last_name'      => trim($validated['last_name']),
                'role'           => $validated['role'],
                'department'     => $validated['department'],
                'specialization' => $validated['department'] === 'therapy' ? ($validated['specialization'] ?? null) : null,
                'nid'            => $validated['nid'] ?? null,
                'date_of_birth'  => $validated['date_of_birth'] ?? null,
                'hire_date'      => $validated['hire_date'] ?? now()->toDateString(),
                'image'          => $imagePath,
                'note'           => $validated['note'] ?? null,
                'is_active'      => $isActive,
            ]);
        });

        return redirect()->route('admin.staff.index')
            ->with('success', "Staff member {$validated['first_name']} {$validated['last_name']} added successfully.");
    }

    /**
     * Display the specified staff member.
     */
    public function show(Staff $staff)
    {
        $staff->load([
            'user',
            'activityOccurrences.activity',
            'childDailyLogs.child',
            'announcements'
        ]);

        return view('admin.staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit(Staff $staff)
    {
        $staff->load('user');
        return view('admin.staff.edit', compact('staff'));
    }

    /**
     * Update the specified staff member in storage.
     */
    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|max:255|unique:users,email,' . $staff->user_id,
            'password'       => ['nullable', 'confirmed', Password::min(8)],
            'role'           => 'required|in:teacher,assistant,admin,therapist',
            'department'     => 'required|in:daycare,therapy',
            'specialization' => 'nullable|in:slt,aba,ot',
            'nid'            => 'nullable|string|max:50',
            'date_of_birth'  => 'nullable|date|before:today',
            'hire_date'      => 'nullable|date',
            'note'           => 'nullable|string|max:1000',
            'is_active'      => 'nullable|boolean',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $staff->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('staff_images', 'public');
        }

        $isActive = $request->boolean('is_active', true);
        $userRole = $validated['role'] === 'admin' ? 'admin' : 'staff';

        DB::transaction(function () use ($staff, $validated, $imagePath, $isActive, $userRole) {
            // 1. Update User account
            $userData = [
                'email'     => strtolower(trim($validated['email'])),
                'role'      => $userRole,
                'is_active' => $isActive,
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = $validated['password'];
            }
            $staff->user->update($userData);

            if (method_exists($staff->user, 'syncRoles')) {
                $staff->user->syncRoles([$userRole]);
            }

            // 2. Update Staff profile
            $staff->update([
                'first_name'     => trim($validated['first_name']),
                'last_name'      => trim($validated['last_name']),
                'role'           => $validated['role'],
                'department'     => $validated['department'],
                'specialization' => $validated['department'] === 'therapy' ? ($validated['specialization'] ?? null) : null,
                'nid'            => $validated['nid'] ?? null,
                'date_of_birth'  => $validated['date_of_birth'] ?? null,
                'hire_date'      => $validated['hire_date'] ?? null,
                'image'          => $imagePath,
                'note'           => $validated['note'] ?? null,
                'is_active'      => $isActive,
            ]);
        });

        return redirect()->route('admin.staff.index')
            ->with('success', "Staff member {$staff->full_name} updated successfully.");
    }

    /**
     * Toggle the active status of a staff member.
     */
    public function toggleStatus(Staff $staff)
    {
        $newStatus = !$staff->is_active;

        DB::transaction(function () use ($staff, $newStatus) {
            $staff->update(['is_active' => $newStatus]);
            if ($staff->user) {
                $staff->user->update(['is_active' => $newStatus]);
            }
        });

        $statusText = $newStatus ? 'activated' : 'deactivated';
        return back()->with('success', "Staff member {$staff->full_name} has been {$statusText}.");
    }

    /**
     * Remove the specified staff member from storage.
     */
    public function destroy(Staff $staff)
    {
        // Check for relational dependencies to avoid foreign key exceptions
        $logsCount = $staff->childDailyLogs()->count();
        $activitiesCount = $staff->activityOccurrences()->count();

        if ($logsCount > 0 || $activitiesCount > 0) {
            return back()->with(
                'error',
                "Cannot delete {$staff->full_name}. This staff member is linked to {$logsCount} daily log(s) and {$activitiesCount} scheduled activity session(s). Please deactivate their profile instead to preserve system audit history."
            );
        }

        DB::transaction(function () use ($staff) {
            if ($staff->image && Storage::disk('public')->exists($staff->image)) {
                Storage::disk('public')->delete($staff->image);
            }

            $user = $staff->user;
            $staff->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.staff.index')
            ->with('success', "Staff member {$staff->full_name} was deleted successfully.");
    }
}
