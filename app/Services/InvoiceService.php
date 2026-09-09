<?php

namespace App\Services;

use App\Models\Invoice;
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
     * Create an invoice with line items.
     */
    public function createInvoice(array $data, User $user): Invoice
    {
        return DB::transaction(function () use ($data, $user) {
            $totalAmount = 0;

            // Calculate total from items
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

            // Create line items
            if (!empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $invoice->items()->create([
                        'description' => $item['description'],
                        'amount'      => $item['amount'],
                    ]);
                }
            }

            return $invoice;
        });
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
