<?php

namespace App\Http\Controllers;

use App\Models\TherapyPackage;
use App\Models\TherapyPackageItem;
use App\Models\TherapyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TherapyPackageController extends Controller
{
    /**
     * Display a listing of therapy packages.
     */
    public function index(Request $request)
    {
        $query = TherapyPackage::with('items.service');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->input('status') !== null && $request->input('status') !== '') {
            $query->where('is_active', $request->input('status'));
        }

        $packages = $query->orderBy('name')->paginate(15)->appends($request->query());

        return view('admin.therapy-packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new therapy package.
     */
    public function create()
    {
        $services = TherapyService::where('is_active', true)->orderBy('name')->get();
        return view('admin.therapy-packages.create', compact('services'));
    }

    /**
     * Store a newly created therapy package.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'price'                   => 'required|numeric|min:0',
            'is_active'               => 'nullable|boolean',
            'items'                   => 'required|array|min:1',
            'items.*.therapy_service_id' => 'required|exists:therapy_services,id',
            'items.*.session_count'   => 'required|integer|min:1',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($validated) {
            $package = TherapyPackage::create([
                'name'      => $validated['name'],
                'price'     => $validated['price'],
                'is_active' => $validated['is_active'],
            ]);

            foreach ($validated['items'] as $item) {
                $package->items()->create($item);
            }
        });

        return redirect()->route('admin.therapy-packages.index')
            ->with('success', 'Therapy package created successfully.');
    }

    /**
     * Show the form for editing a therapy package.
     */
    public function edit(TherapyPackage $therapyPackage)
    {
        $therapyPackage->load('items.service');
        $services = TherapyService::where('is_active', true)->orderBy('name')->get();
        return view('admin.therapy-packages.edit', compact('therapyPackage', 'services'));
    }

    /**
     * Update the specified therapy package.
     */
    public function update(Request $request, TherapyPackage $therapyPackage)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'price'                   => 'required|numeric|min:0',
            'is_active'               => 'nullable|boolean',
            'items'                   => 'required|array|min:1',
            'items.*.therapy_service_id' => 'required|exists:therapy_services,id',
            'items.*.session_count'   => 'required|integer|min:1',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($therapyPackage, $validated) {
            $therapyPackage->update([
                'name'      => $validated['name'],
                'price'     => $validated['price'],
                'is_active' => $validated['is_active'],
            ]);

            // Replace items: delete old, create new
            $therapyPackage->items()->delete();
            foreach ($validated['items'] as $item) {
                $therapyPackage->items()->create($item);
            }
        });

        return redirect()->route('admin.therapy-packages.index')
            ->with('success', 'Therapy package updated successfully.');
    }

    /**
     * Remove the specified therapy package.
     */
    public function destroy(TherapyPackage $therapyPackage)
    {
        if ($therapyPackage->childPurchases()->exists()) {
            return back()->with('error', 'Cannot delete a package that has been purchased by children.');
        }

        DB::transaction(function () use ($therapyPackage) {
            $therapyPackage->items()->delete();
            $therapyPackage->delete();
        });

        return redirect()->route('admin.therapy-packages.index')
            ->with('success', 'Therapy package deleted successfully.');
    }
}
