<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users with search and role filters.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'parentProfile', 'staffProfile'])->latest();

        // Search filter (email, first name, last name, phone/mobile)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhereHas('parentProfile', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('mobile', 'like', "%{$search}%")
                         ->orWhere('nid', 'like', "%{$search}%");
                  })
                  ->orWhereHas('staffProfile', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('nid', 'like', "%{$search}%");
                  });
            });
        }

        // Role filter
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Status filter
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $users = $query->paginate(15)->withQueryString();

        // Summary counts
        $counts = [
            'total'   => User::count(),
            'admin'   => User::where('role', 'admin')->count(),
            'staff'   => User::where('role', 'staff')->count(),
            'parent'  => User::where('role', 'parent')->count(),
            'active'  => User::where('is_active', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user and matching profile in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => ['required', 'confirmed', Password::min(8)],
            'role'          => 'required|in:admin,staff,parent',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'is_active'     => 'nullable|boolean',

            // Parent-specific fields
            'mobile'        => 'nullable|string|max:30',
            'occupation'    => 'nullable|string|max:100',
            'city'          => 'nullable|string|max:100',
            'address'       => 'nullable|string|max:500',

            // Staff-specific fields
            'staff_role'    => 'nullable|in:teacher,assistant,admin',
            'nid'           => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'hire_date'     => 'nullable|date',
            'note'          => 'nullable|string|max:500',

            // Profile Image
            'image'         => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profile_images', 'public');
        }

        $user = DB::transaction(function () use ($validated, $request, $imagePath) {
            // 1. Create the User row
            $user = User::create([
                'email'     => strtolower(trim($validated['email'])),
                'password'  => $validated['password'],
                'role'      => $validated['role'],
                'is_active' => $request->boolean('is_active', true),
            ]);

            // 2. Assign Spatie role
            $user->assignRole($validated['role']);

            // 3. Create the matching profile row
            if ($validated['role'] === 'parent') {
                $user->parentProfile()->create([
                    'first_name' => trim($validated['first_name']),
                    'last_name'  => trim($validated['last_name']),
                    'mobile'     => $validated['mobile'] ?? null,
                    'nid'        => $validated['nid'] ?? null,
                    'occupation' => $validated['occupation'] ?? null,
                    'city'       => $validated['city'] ?? null,
                    'address'    => $validated['address'] ?? null,
                    'image'      => $imagePath,
                ]);
            } elseif ($validated['role'] === 'staff') {
                $user->staffProfile()->create([
                    'first_name'    => trim($validated['first_name']),
                    'last_name'     => trim($validated['last_name']),
                    'role'          => $validated['staff_role'] ?? 'teacher',
                    'nid'           => $validated['nid'] ?? null,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'hire_date'     => $validated['hire_date'] ?? now()->toDateString(),
                    'note'          => $validated['note'] ?? null,
                    'is_active'     => true,
                    'image'         => $imagePath,
                ]);
            } elseif ($validated['role'] === 'admin') {
                $user->staffProfile()->create([
                    'first_name'    => trim($validated['first_name']),
                    'last_name'     => trim($validated['last_name']),
                    'role'          => 'admin',
                    'nid'           => $validated['nid'] ?? null,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'hire_date'     => $validated['hire_date'] ?? now()->toDateString(),
                    'note'          => $validated['note'] ?? 'Administrator',
                    'is_active'     => true,
                    'image'         => $imagePath,
                ]);
            }

            return $user;
        });

        return redirect()->route('admin.users.index')->with(
            'success',
            "User {$user->email} ({$user->name}) created successfully with role '{$user->role}'."
        );
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $user->load(['parentProfile', 'staffProfile', 'roles']);
        $roles = Role::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email'         => 'required|email|max:255|unique:users,email,' . $user->id,
            'password'      => ['nullable', 'confirmed', Password::min(8)],
            'role'          => 'required|in:admin,staff,parent',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'is_active'     => 'nullable|boolean',

            // Parent-specific fields
            'mobile'        => 'nullable|string|max:30',
            'occupation'    => 'nullable|string|max:100',
            'city'          => 'nullable|string|max:100',
            'address'       => 'nullable|string|max:500',

            // Staff-specific fields
            'staff_role'    => 'nullable|in:teacher,assistant,admin',
            'nid'           => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'hire_date'     => 'nullable|date',
            'note'          => 'nullable|string|max:500',

            // Profile Image
            'image'         => 'nullable|image|max:2048',
        ]);

        $imagePath = $user->profile?->image;
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('profile_images', 'public');
        }

        DB::transaction(function () use ($validated, $request, $user, $imagePath) {
            // Update User
            $userData = [
                'email'     => strtolower(trim($validated['email'])),
                'role'      => $validated['role'],
                'is_active' => $request->boolean('is_active', true),
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = $validated['password'];
            }
            $user->update($userData);

            // Assign Spatie role
            $user->syncRoles([$validated['role']]);

            // Handle Profile
            $profileData = [
                'first_name' => trim($validated['first_name']),
                'last_name'  => trim($validated['last_name']),
                'image'      => $imagePath,
            ];

            if ($validated['role'] === 'parent') {
                $profileData = array_merge($profileData, [
                    'mobile'     => $validated['mobile'] ?? null,
                    'nid'        => $validated['nid'] ?? null,
                    'occupation' => $validated['occupation'] ?? null,
                    'city'       => $validated['city'] ?? null,
                    'address'    => $validated['address'] ?? null,
                ]);

                // Delete staff profile if changing from staff to parent
                if ($user->staffProfile) $user->staffProfile->delete();

                if ($user->parentProfile) {
                    $user->parentProfile->update($profileData);
                } else {
                    $user->parentProfile()->create($profileData);
                }
            } else { // staff or admin
                $profileData = array_merge($profileData, [
                    'role'          => $validated['role'] === 'admin' ? 'admin' : ($validated['staff_role'] ?? 'teacher'),
                    'nid'           => $validated['nid'] ?? null,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'hire_date'     => $validated['hire_date'] ?? null,
                    'note'          => $validated['role'] === 'admin' ? ($validated['note'] ?? 'Administrator') : ($validated['note'] ?? null),
                    'is_active'     => true,
                ]);

                // Delete parent profile if changing from parent to staff
                if ($user->parentProfile) $user->parentProfile->delete();

                if ($user->staffProfile) {
                    $user->staffProfile->update($profileData);
                } else {
                    $user->staffProfile()->create($profileData);
                }
            }
        });

        return redirect()->route('admin.users.index')->with(
            'success',
            "User {$user->email} ({$user->name}) updated successfully."
        );
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$user->email} has been {$status}.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->is_super_admin) {
            return back()->with('error', 'The super administrator account cannot be deleted.');
        }

        $email = $user->email;

        DB::transaction(function () use ($user) {
            if ($user->parentProfile) {
                $user->parentProfile->delete();
            }
            if ($user->staffProfile) {
                $user->staffProfile->delete();
            }
            $user->syncRoles([]);
            $user->delete();
        });

        return back()->with('success', "User {$email} deleted successfully.");
    }
}
