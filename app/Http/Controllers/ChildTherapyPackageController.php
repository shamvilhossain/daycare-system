<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ScopesForParent;
use App\Models\Child;
use App\Models\ChildTherapyPackage;
use App\Models\TherapyPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChildTherapyPackageController extends Controller
{
    use ScopesForParent;

    /**
     * Display a listing of purchased child therapy packages.
     */
    public function index(Request $request)
    {
        $query = ChildTherapyPackage::with(['child', 'package.items.service', 'items.service', 'invoice']);

        // Scope to parent's own children
        $parentChildIds = $this->getParentChildIds();
        if ($parentChildIds !== null) {
            $query->whereIn('child_id', $parentChildIds);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('child', function ($cq) use ($search) {
                    $cq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('package', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $packages = $query->orderBy('purchased_at', 'desc')->paginate(15)->appends($request->query());

        return view('admin.child-therapy-packages.index', compact('packages'));
    }

    /**
     * Display the specified child therapy package.
     */
    public function show(ChildTherapyPackage $childTherapyPackage)
    {
        $this->authorizeParentAccessToChildRecord($childTherapyPackage);

        $childTherapyPackage->load(['child', 'package.items.service', 'items.service', 'sessions.service', 'invoice']);

        return view('admin.child-therapy-packages.show', compact('childTherapyPackage'));
    }

    /**
     * Show the form for purchasing a new package for a child.
     */
    public function create()
    {
        if ($this->isParent()) {
            abort(403);
        }

        $children = Child::where('is_active', true)->orderBy('first_name')->get();
        $packages = TherapyPackage::where('is_active', true)->with('items.service')->orderBy('name')->get();

        return view('admin.child-therapy-packages.create', compact('children', 'packages'));
    }

    /**
     * Store a newly purchased child therapy package.
     */
    public function store(Request $request)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $validated = $request->validate([
            'child_id'           => 'required|exists:children,id',
            'therapy_package_id' => 'required|exists:therapy_packages,id',
            'purchase_price'     => 'required|numeric|min:0',
            'purchased_at'       => 'required|date',
            'expires_at'         => 'nullable|date|after:purchased_at',
        ]);

        DB::transaction(function () use ($validated) {
            $package = TherapyPackage::with('items')->findOrFail($validated['therapy_package_id']);

            $childPkg = ChildTherapyPackage::create([
                'child_id'           => $validated['child_id'],
                'therapy_package_id' => $validated['therapy_package_id'],
                'purchase_price'     => $validated['purchase_price'],
                'purchased_at'       => $validated['purchased_at'],
                'expires_at'         => $validated['expires_at'] ?? null,
                'status'             => 'active',
            ]);

            // Snapshot package composition at purchase time
            foreach ($package->items as $item) {
                $childPkg->items()->create([
                    'therapy_service_id' => $item->therapy_service_id,
                    'sessions_included'  => $item->session_count,
                ]);
            }
        });

        return redirect()->route('admin.child-therapy-packages.index')
            ->with('success', 'Package purchased successfully.');
    }

    /**
     * Cancel a child therapy package.
     */
    public function cancel(ChildTherapyPackage $childTherapyPackage)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $childTherapyPackage->update(['status' => 'cancelled']);

        return back()->with('success', 'Package cancelled successfully.');
    }
}
