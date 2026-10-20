<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItemPreset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Admin: manage the invoice item catalog behind the Description dropdown
 * (fix typos, set default prices, hide or delete entries).
 * Changes here never alter existing invoices — they keep their own text.
 */
class InvoiceItemPresetController extends Controller
{
    public function index(Request $request): View
    {
        $query = InvoiceItemPreset::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'hidden') {
            $query->where('is_active', false);
        }

        $presets = $query->orderByDesc('is_active')->orderByDesc('usage_count')->orderBy('name')
            ->paginate(25)->withQueryString();

        $stats = [
            'total'  => InvoiceItemPreset::count(),
            'active' => InvoiceItemPreset::where('is_active', true)->count(),
            'hidden' => InvoiceItemPreset::where('is_active', false)->count(),
        ];

        return view('admin.invoice-item-presets.index', compact('presets', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['name'] = InvoiceItemPreset::normalizeName($data['name']);

        InvoiceItemPreset::create($data + ['is_active' => true, 'usage_count' => 0]);

        return back()->with('success', "Item \"{$data['name']}\" added.");
    }

    public function update(Request $request, InvoiceItemPreset $invoiceItemPreset): RedirectResponse
    {
        $data = $this->validated($request, $invoiceItemPreset);
        $data['name'] = InvoiceItemPreset::normalizeName($data['name']);

        $invoiceItemPreset->update($data);

        return back()->with('success', "Item \"{$invoiceItemPreset->name}\" updated.");
    }

    public function toggle(InvoiceItemPreset $invoiceItemPreset): RedirectResponse
    {
        $invoiceItemPreset->update(['is_active' => !$invoiceItemPreset->is_active]);

        return back()->with('success', "Item \"{$invoiceItemPreset->name}\" is now " . ($invoiceItemPreset->is_active ? 'shown in' : 'hidden from') . ' the dropdown.');
    }

    public function destroy(InvoiceItemPreset $invoiceItemPreset): RedirectResponse
    {
        $name = $invoiceItemPreset->name;
        $invoiceItemPreset->delete();

        return back()->with('success', "Item \"{$name}\" removed from the catalog. Existing invoices are unchanged.");
    }

    private function validated(Request $request, ?InvoiceItemPreset $ignore = null): array
    {
        $request->merge(['name' => InvoiceItemPreset::normalizeName($request->input('name'))]);

        return $request->validate([
            'name'           => ['required', 'string', 'max:255', Rule::unique('invoice_item_presets', 'name')->ignore($ignore?->id)],
            'default_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
        ], [
            'name.unique' => 'An item with this name already exists.',
        ]);
    }
}
