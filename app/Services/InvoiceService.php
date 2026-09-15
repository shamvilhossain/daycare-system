<?php

namespace App\Services;

use App\Models\Invoice;
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

            // Create manual line items (daycare tuition, fees, etc.)
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $invoice->items()->create([
                        'description' => $item['description'],
                        'quantity'    => 1,
                        'unit_price'  => $item['amount'],
                        'amount'      => $item['amount'],
                    ]);
                }
            }

            // Optionally attach completed therapy sessions
            if (!empty($data['therapy_session_ids'])) {
                $this->attachTherapySessions($invoice, $data['therapy_session_ids']);
            }

            return $invoice;
        });
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

            // Refresh paid total and check if invoice is fully paid
            $invoice->refresh();
            $totalPaid = $invoice->payments()->sum('paid_amount');

            if ($totalPaid >= $invoice->total_amount) {
                $invoice->update(['status' => 'paid']);
            }

            return $payment;
        });
    }

    /**
     * Cancel an invoice.
     */
    public function cancelInvoice(Invoice $invoice): void
    {
        $invoice->update(['status' => 'cancelled']);
    }
}
