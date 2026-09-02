<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\ParentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChildController extends Controller
{
    public function index(Request $request)
    {
        $query = Child::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $children = $query->with('parents')->orderBy('first_name')->paginate(15)->appends($request->query());
        return view('admin.children.index', compact('children'));
    }

    public function create()
    {
        $parents = ParentProfile::orderBy('first_name')->get();
        return view('admin.children.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'          => 'required|string|max:255',
            'last_name'           => 'required|string|max:255',
            'date_of_birth'       => 'required|date|before:today',
            'allergies'           => 'nullable|string',
            'medical_notes'       => 'nullable|string',
            'photo'               => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'ec_name'             => 'nullable|string|max:255',
            'ec_relationship'     => 'nullable|string|max:255',
            'ec_phone'            => 'nullable|string|max:50',
            'ec_authorized_pickup' => 'nullable',
            'is_active'           => 'nullable',
            'parent_ids'          => 'nullable|array',
            'parent_ids.*'        => 'exists:parents,id',
            'relationships'       => 'nullable|array',
            'relationships.*'     => 'nullable|in:mother,father,step_parent,grandparent,legal_guardian,other',
            'is_primary'          => 'nullable|array',
            'can_pickup'          => 'nullable|array',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photoPath = $request->file('photo')->store('children', 'public');
        } elseif ($request->hasFile('photo') && !$request->file('photo')->isValid()) {
            return back()->withErrors(['photo' => 'The image failed to upload (it may be too large).'])->withInput();
        } elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_INI_SIZE) {
            return back()->withErrors(['photo' => 'The image exceeds the maximum upload size allowed by the server.'])->withInput();
        }

        DB::transaction(function () use ($validated, $request, $photoPath) {
            $child = Child::create([
                'first_name'           => $validated['first_name'],
                'last_name'            => $validated['last_name'],
                'date_of_birth'        => $validated['date_of_birth'],
                'allergies'            => $validated['allergies'] ?? null,
                'medical_notes'        => $validated['medical_notes'] ?? null,
                'photo_url'            => $photoPath,
                'ec_name'              => $validated['ec_name'] ?? null,
                'ec_relationship'      => $validated['ec_relationship'] ?? null,
                'ec_phone'             => $validated['ec_phone'] ?? null,
                'ec_authorized_pickup' => $request->has('ec_authorized_pickup'),
                'is_active'            => $request->has('is_active'),
            ]);

            // Attach parents
            if (!empty($validated['parent_ids'])) {
                foreach ($validated['parent_ids'] as $index => $parentId) {
                    $child->parents()->attach($parentId, [
                        'relationship' => $validated['relationships'][$index] ?? null,
                        'is_primary'   => isset($validated['is_primary'][$index]),
                        'can_pickup'   => isset($validated['can_pickup'][$index]),
                    ]);
                }
            }
        });

        return redirect()->route('admin.children.index')->with('success', 'Child added successfully.');
    }

    public function edit(Child $child)
    {
        $child->load('parents');
        $parents = ParentProfile::orderBy('first_name')->get();
        return view('admin.children.edit', compact('child', 'parents'));
    }

    public function update(Request $request, Child $child)
    {
        $validated = $request->validate([
            'first_name'          => 'required|string|max:255',
            'last_name'           => 'required|string|max:255',
            'date_of_birth'       => 'required|date|before:today',
            'allergies'           => 'nullable|string',
            'medical_notes'       => 'nullable|string',
            'photo'               => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'ec_name'             => 'nullable|string|max:255',
            'ec_relationship'     => 'nullable|string|max:255',
            'ec_phone'            => 'nullable|string|max:50',
            'ec_authorized_pickup' => 'nullable',
            'is_active'           => 'nullable',
            'parent_ids'          => 'nullable|array',
            'parent_ids.*'        => 'exists:parents,id',
            'relationships'       => 'nullable|array',
            'relationships.*'     => 'nullable|in:mother,father,step_parent,grandparent,legal_guardian,other',
            'is_primary'          => 'nullable|array',
            'can_pickup'          => 'nullable|array',
        ]);

        $photoPath = $child->photo_url;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($child->photo_url) {
                Storage::disk('public')->delete($child->photo_url);
            }
            $photoPath = $request->file('photo')->store('children', 'public');
        } elseif ($request->hasFile('photo') && !$request->file('photo')->isValid()) {
            return back()->withErrors(['photo' => 'The image failed to upload (it may be too large).'])->withInput();
        } elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_INI_SIZE) {
            return back()->withErrors(['photo' => 'The image exceeds the maximum upload size allowed by the server.'])->withInput();
        }

        DB::transaction(function () use ($validated, $request, $child, $photoPath) {
            $child->update([
                'first_name'           => $validated['first_name'],
                'last_name'            => $validated['last_name'],
                'date_of_birth'        => $validated['date_of_birth'],
                'allergies'            => $validated['allergies'] ?? null,
                'medical_notes'        => $validated['medical_notes'] ?? null,
                'photo_url'            => $photoPath,
                'ec_name'              => $validated['ec_name'] ?? null,
                'ec_relationship'      => $validated['ec_relationship'] ?? null,
                'ec_phone'             => $validated['ec_phone'] ?? null,
                'ec_authorized_pickup' => $request->has('ec_authorized_pickup'),
                'is_active'            => $request->has('is_active'),
            ]);

            // Sync parents
            $syncData = [];
            if (!empty($validated['parent_ids'])) {
                foreach ($validated['parent_ids'] as $index => $parentId) {
                    $syncData[$parentId] = [
                        'relationship' => $validated['relationships'][$index] ?? null,
                        'is_primary'   => isset($validated['is_primary'][$index]),
                        'can_pickup'   => isset($validated['can_pickup'][$index]),
                    ];
                }
            }
            $child->parents()->sync($syncData);
        });

        return redirect()->route('admin.children.index')->with('success', 'Child updated successfully.');
    }

    public function destroy(Child $child)
    {
        DB::transaction(function () use ($child) {
            if ($child->photo_url) {
                Storage::disk('public')->delete($child->photo_url);
            }
            $child->parents()->detach();
            $child->delete();
        });

        return redirect()->route('admin.children.index')->with('success', 'Child deleted successfully.');
    }
}
