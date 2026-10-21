<?php

namespace App\Services;

use App\Models\Child;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * One-time admission fee: charged on a child's first programme enrolment, once per lifetime.
 * The "admission invoice" carries the admission fee (unless waived) + the first month's tuition.
 */
class AdmissionFeeService
{
    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    public function fee(): float
    {
        return (float) config('admission.fee', 0);
    }

    public function enabled(): bool
    {
        return $this->fee() > 0;
    }

    /** Admission fee still to be charged for this child (fee switched on, never invoiced or waived). */
    public function isDue(Child $child): bool
    {
        return $this->enabled() && !$child->is_admitted;
    }

    /**
     * Stop an enrolment that would need an admission invoice when there is no parent to bill.
     *
     * @throws ValidationException
     */
    public function assertBillable(Child $child, string $status): void
    {
        if ($this->isDue($child) && in_array($status, ['pending', 'active'], true) && !$child->parent_profile) {
            throw ValidationException::withMessages([
                'child_id' => "{$child->full_name} has no parent linked. Link a parent first (Children → Edit) so the admission invoice can be billed.",
            ]);
        }
    }

    /**
     * Raise the admission invoice for a child's first enrolment.
     * Returns the invoice, or null when nothing is billed (already admitted, fee off, waived with no monthly fee...).
     */
    public function invoiceForEnrollment(Enrollment $enrollment, User $user, bool $waive = false, ?string $waiverReason = null): ?Invoice
    {
        $child = $enrollment->child()->with('parents')->first();
        $program = $enrollment->program;

        if (!$this->isDue($child) || !in_array($enrollment->status, ['pending', 'active'], true)) {
            return null;
        }

        if ($waive) {
            $child->update([
                'admission_fee_waived_at'     => now(),
                'admission_fee_waiver_reason' => $waiverReason,
            ]);
        }

        $items = [];
        if (!$waive) {
            $items[] = ['description' => config('admission.label'), 'amount' => $this->fee()];
        }
        if ($program->billing_model === 'monthly' && (float) $program->monthly_fee > 0) {
            $items[] = [
                'description' => 'Monthly Tuition - ' . $enrollment->start_date->format('F Y') . " ({$program->name})",
                'amount'      => (float) $program->monthly_fee,
            ];
        }
        if (!$items) {
            return null;
        }

        $invoice = $this->invoiceService->createInvoice([
            'parent_id'    => $child->parent_profile->id,
            'child_id'     => $child->id,
            'invoice_date' => now()->toDateString(),
            'due_date'     => now()->addDays(config('admission.due_days', 7))->toDateString(),
            'items'        => $items,
            'remember_presets' => false,
        ], $user);

        $child->update(['admission_invoice_id' => $invoice->id]);

        return $invoice;
    }

    /**
     * Admission invoice still waiting for payment (null when paid, waived or never charged).
     */
    public function unpaidInvoice(Child $child): ?Invoice
    {
        if ($child->admission_fee_waived_at || !$child->admission_invoice_id) {
            return null;
        }
        $invoice = $child->admissionInvoice;

        return $invoice && $invoice->status !== 'paid' && $invoice->status !== 'cancelled' ? $invoice : null;
    }

    /** Warning shown when an enrolment is approved before the admission fee is settled. */
    public function approvalWarning(Child $child): ?string
    {
        if ($invoice = $this->unpaidInvoice($child)) {
            return "Admission invoice {$invoice->invoice_number} is not paid yet (balance ৳" . number_format($invoice->balance_due, 2) . ').';
        }
        if ($this->isDue($child)) {
            return 'The admission fee has not been charged for this child.';
        }
        return null;
    }

    /**
     * Enrolment rejected / deleted before payment: cancel the unpaid admission invoice so the
     * fee is charged again on a future enrolment. Paid or part-paid invoices are left alone.
     */
    public function releaseForEnrollment(Enrollment $enrollment): ?Invoice
    {
        $child = Child::find($enrollment->child_id);
        $invoice = $child?->admissionInvoice;

        if (!$invoice || !$invoice->is_open || $invoice->payments()->exists()) {
            return null;
        }

        // Another enrolment still needs (or already used) this admission
        $otherLive = Enrollment::where('child_id', $child->id)
            ->where('id', '!=', $enrollment->id)
            ->whereIn('status', ['pending', 'active', 'withdrawn', 'graduated'])
            ->exists();
        if ($otherLive) {
            return null;
        }

        $this->invoiceService->cancelInvoice($invoice); // also clears children.admission_invoice_id

        return $invoice;
    }
}
