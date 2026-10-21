<?php

namespace App\Services;

use App\Models\Child;
use App\Models\ChildTherapyPackage;
use App\Models\Invoice;
use App\Models\InvoiceItemPreset;
use App\Models\TherapyPackage;
use App\Models\TherapySession;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Generate a unique invoice number in format: INV-YYYYMMDD-XXXX
     */
    protected function generateInvoiceNumber(): string
    {
        $date = Carbon::today()->format('Ymd');
        $prefix = "INV-{$date}-";

        $lastInvoice = Invoice::where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastSeq = (int) substr($lastInvoice->invoice_number, -4);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create an invoice with line items and optionally attach therapy sessions.
     */
    public function createInvoice(array $data, User $user): Invoice
    {
        return DB::transaction(function () use ($data, $user) {
            $totalAmount = 0;

            // Calculate total from manual items
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $totalAmount += (float) $item['amount'];
                }
            }

            $invoice = Invoice::create([
                'parent_id'      => $data['parent_id'],
                'child_id'       => $data['child_id'],
                'invoice_number' => $this->generateInvoiceNumber(),
                'invoice_date'   => $data['invoice_date'],
                'due_date'       => $data['due_date'],
                'total_amount'   => $totalAmount,
                'status'         => 'draft',
            ]);

            // Create manual line items (daycare tuition, fees, therapy packages, single sessions)
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $childPackageId = !empty($item['therapy_package_id'])
                        ? $this->purchaseTherapyPackage($invoice, (int) $item['therapy_package_id'], (float) $item['amount'])->id
                        : null;

                    $invoice->items()->create([
                        'child_therapy_package_id' => $childPackageId,
                        'description'              => $item['description'],
                        'quantity'                 => 1,
                        'unit_price'               => $item['amount'],
                        'amount'                   => $item['amount'],
                    ]);
                }
            }

            // Remember free-text item names for the Description dropdown next time
            // (therapy packages / sessions come from their own catalogs; system-built invoices opt out)
            if ($data['remember_presets'] ?? true) {
                $this->rememberItemPresets(array_filter(
                    $data['items'] ?? [],
                    fn ($item) => empty($item['therapy_package_id']) && empty($item['therapy_service_id'])
                ));
            }

            // Optionally attach completed therapy sessions
            if (!empty($data['therapy_session_ids'])) {
                $this->attachTherapySessions($invoice, $data['therapy_session_ids']);
            }

            return $invoice;
        });
    }

    /**
     * Buy a therapy package for the invoice's child — same as Therapy Packages → Purchase,
     * but billed on this invoice: the package's session balance starts tracking now.
     */
    protected function purchaseTherapyPackage(Invoice $invoice, int $therapyPackageId, float $price): ChildTherapyPackage
    {
        $package = TherapyPackage::with('items')->findOrFail($therapyPackageId);

        $childPackage = ChildTherapyPackage::create([
            'child_id'           => $invoice->child_id,
            'therapy_package_id' => $package->id,
            'purchase_price'     => $price,
            'purchased_at'       => $invoice->invoice_date,
            'expires_at'         => null,
            'status'             => 'active',
            'invoice_id'         => $invoice->id,
        ]);

        // Snapshot package composition at purchase time
        foreach ($package->items as $item) {
            $childPackage->items()->create([
                'therapy_service_id' => $item->therapy_service_id,
                'sessions_included'  => $item->session_count,
            ]);
        }

        return $childPackage;
    }

    /**
     * Add/refresh catalog entries for manual line items: new names are added,
     * existing ones get their usage count and default amount updated.
     * A hidden entry typed again is shown in the dropdown again.
     */
    protected function rememberItemPresets(array $items): void
    {
        foreach ($items as $item) {
            $name = InvoiceItemPreset::normalizeName($item['description'] ?? '');
            if ($name === '') {
                continue;
            }

            $preset = InvoiceItemPreset::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first()
                ?? new InvoiceItemPreset(['name' => $name, 'usage_count' => 0]);

            $preset->fill([
                'default_amount' => $item['amount'],
                'usage_count'    => $preset->usage_count + 1,
                'last_used_at'   => now(),
                'is_active'      => true,
            ])->save();
        }
    }

    /**
     * Attach completed, unbilled therapy sessions to an invoice.
     *
     * For each session:
     * - Only bills sessions with status = 'completed'
     * - Skips sessions already billed (whereDoesntHave('invoiceItem') dedup guard)
     * - Snapshots therapy_services.session_rate into invoice_items.unit_price
     *   so past invoices reflect what was actually charged, not current rates
     */
    public function attachTherapySessions(Invoice $invoice, array $sessionIds): void
    {
        $sessions = TherapySession::with('service')
            ->whereIn('id', $sessionIds)
            ->where('status', 'completed')
            ->whereDoesntHave('invoiceItem') // dedup — skip already-billed sessions
            ->get();

        $addedTotal = 0;

        foreach ($sessions as $session) {
            $rate = $session->service->session_rate;
            $therapyType = strtoupper($session->service->therapy_type);
            $serviceName = $session->service->name;
            $sessionDate = $session->session_date->format('M d, Y');

            $description = "{$serviceName} ({$therapyType}) — {$sessionDate}";

            $invoice->items()->create([
                'therapy_session_id' => $session->id,
                'description'        => $description,
                'quantity'           => 1,
                'unit_price'         => $rate,  // price snapshot
                'amount'             => $rate,
            ]);

            $addedTotal += $rate;
        }

        // Recalculate total_amount
        if ($addedTotal > 0) {
            $invoice->update([
                'total_amount' => $invoice->total_amount + $addedTotal,
            ]);
        }
    }

    /**
     * Record a manual payment against an invoice.
     */
    public function addPayment(Invoice $invoice, array $data, User $user): Payment
    {
        return DB::transaction(function () use ($invoice, $data, $user) {
            $payment = Payment::create([
                'invoice_id'     => $invoice->id,
                'parent_id'      => $invoice->parent_id,
                'child_id'       => $invoice->child_id,
                'payable_amount' => $invoice->balance_due,
                'penalty_amount' => $data['penalty_amount'] ?? null,
                'paid_amount'    => $data['paid_amount'],
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'paid_at'        => $data['paid_at'] ?? now(),
                'remarks'        => $data['remarks'] ?? null,
                'received_by'    => $user->id,
            ]);

            $this->refreshPaymentStatus($invoice);

            return $payment;
        });
    }

    /**
     * Record a payment confirmed by the online gateway (no staff user involved).
     * Must be called inside a transaction that holds a lock on the invoice row.
     */
    public function recordOnlinePayment(Invoice $invoice, float $amount, string $transactionId, ?string $remarks = null): Payment
    {
        $payment = Payment::create([
            'invoice_id'     => $invoice->id,
            'parent_id'      => $invoice->parent_id,
            'child_id'       => $invoice->child_id,
            'payable_amount' => $invoice->balance_due,
            'paid_amount'    => $amount,
            'payment_method' => 'online',
            'transaction_id' => $transactionId,
            'paid_at'        => now(),
            'remarks'        => $remarks,
            'received_by'    => null,
        ]);

        $this->refreshPaymentStatus($invoice);

        return $payment;
    }

    /**
     * Mark a draft invoice as "sent" (payment link shared with the parent).
     */
    public function markSent(Invoice $invoice): void
    {
        if ($invoice->status === 'draft') {
            $invoice->update(['status' => 'sent']);
        }
    }

    /**
     * Recalculate status after a payment:
     * fully paid → paid; part paid → partially_paid (an overdue invoice stays overdue until settled).
     */
    public function refreshPaymentStatus(Invoice $invoice): void
    {
        $invoice->refresh();

        if (!$invoice->is_open) {
            return;
        }

        $totalPaid = (float) $invoice->payments()->sum('paid_amount');

        if ($totalPaid >= (float) $invoice->total_amount) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0 && $invoice->status !== 'overdue') {
            $invoice->update(['status' => 'partially_paid']);
        }
    }

    /**
     * Cancel an invoice.
     */
    public function cancelInvoice(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $invoice->update(['status' => 'cancelled']);
            $this->cancelPackagesBilledOn($invoice);
            $this->releaseAdmissionBilledOn($invoice);
        });
    }

    /**
     * A cancelled / deleted admission invoice no longer counts as the child's admission,
     * so the admission fee is charged again on the next enrolment (a waiver stays).
     */
    public function releaseAdmissionBilledOn(Invoice $invoice): void
    {
        Child::where('admission_invoice_id', $invoice->id)->update(['admission_invoice_id' => null]);
    }

    /**
     * Therapy packages bought on this invoice are cancelled with it (sessions already
     * used stay recorded; the package just stops offering its remaining balance).
     */
    public function cancelPackagesBilledOn(Invoice $invoice): void
    {
        ChildTherapyPackage::where('invoice_id', $invoice->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);
    }
}
