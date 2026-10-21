<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ScopesForParent;
use App\Models\Child;
use App\Models\ChildTherapyPackage;
use App\Models\TherapyPackage;
use App\Services\InvoiceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChildTherapyPackageController extends Controller
{
    use ScopesForParent;

    public function __construct(protected InvoiceService $invoiceService)
    {
    }

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

        $package = TherapyPackage::with('items')->findOrFail($validated['therapy_package_id']);
        $parent = Child::with('parents')->findOrFail($validated['child_id'])->parent_profile;

        // Bill the purchase: one invoice line for the package, to the child's primary parent.
        // Same path as buying the package from Invoices → Create, so the package is linked to its invoice.
        if ($parent && (float) $validated['purchase_price'] > 0) {
            $invoice = DB::transaction(function () use ($validated, $package, $parent, $request) {
                $invoice = $this->invoiceService->createInvoice([
                    'parent_id'    => $parent->id,
                    'child_id'     => $validated['child_id'],
                    'invoice_date' => $validated['purchased_at'],
                    'due_date'     => Carbon::parse($validated['purchased_at'])->addDays(7)->toDateString(),
                    'items'        => [[
                        'therapy_package_id' => $package->id,
                        'description'        => $package->name,
                        'amount'             => $validated['purchase_price'],
                    ]],
                ], $request->user());

                ChildTherapyPackage::where('invoice_id', $invoice->id)
                    ->update(['expires_at' => $validated['expires_at'] ?? null]);

                return $invoice;
            });

            return redirect()->route('admin.child-therapy-packages.index')
                ->with('success', "Package purchased and invoice {$invoice->invoice_number} created (draft) for ৳"
                    . number_format((float) $invoice->total_amount, 2) . '. Send it to the parent from Invoices.');
        }

        DB::transaction(function () use ($validated, $package) {
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

        $note = $parent ? 'free package — no invoice needed.' : 'no invoice was created because the child has no linked parent.';

        return redirect()->route('admin.child-therapy-packages.index')
            ->with($parent ? 'success' : 'warning', 'Package purchased — ' . $note);
    }

    /**
     * JSON: the child's existing packages and their session balance, shown before buying another one.
     */
    public function balance(Request $request)
    {
        if ($this->isParent()) {
            abort(403);
        }

        $childId = (int) $request->validate(['child_id' => 'required|integer'])['child_id'];
        $today = now()->toDateString();

        $packages = ChildTherapyPackage::with(['package', 'items.service', 'invoice'])
            ->where('child_id', $childId)
            ->where('status', 'active')
            ->orderBy('purchased_at')
            ->orderBy('id')
            ->get()
            ->map(function (ChildTherapyPackage $cp) use ($today) {
                $services = $cp->items->map(fn ($item) => [
                    'name'      => $item->service->name ?? 'Unknown',
                    'type'      => strtoupper($item->service->therapy_type ?? ''),
                    'included'  => (int) $item->sessions_included,
                    'remaining' => $item->service ? $cp->remainingFor($item->service) : 0,
                    'available' => $cp->availableFor($item->therapy_service_id),
                ])->values();

                return [
                    'id'           => $cp->id,
                    'name'         => $cp->package->name ?? 'Package #' . $cp->id,
                    'purchased_at' => $cp->purchased_at->format('d M Y'),
                    'expires_at'   => $cp->expires_at?->format('d M Y'),
                    'expired'      => $cp->expires_at && $cp->expires_at->toDateString() < $today,
                    'invoice'      => $cp->invoice ? ['number' => $cp->invoice->invoice_number, 'status' => $cp->invoice->status] : null,
                    'services'     => $services,
                    'available'    => $services->sum('available'),
                    'url'          => route('admin.child-therapy-packages.show', $cp),
                ];
            })
            // Only packages that can still be used: not expired and with sessions left
            ->filter(fn ($p) => !$p['expired'] && $p['available'] > 0)
            ->values();

        return response()->json(['packages' => $packages]);
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
