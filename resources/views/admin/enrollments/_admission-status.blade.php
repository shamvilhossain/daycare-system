{{-- One-line admission fee status for a child. Params: $child (with admissionInvoice loaded), $compact (bool) --}}
@php
    $status = $child->admission_status;
    $invoice = $child->admissionInvoice;
    $compact = $compact ?? false;
@endphp
@if ($status === 'paid')
    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle"><i class="bi bi-check-circle me-1"></i>Admission paid</span>
@elseif ($status === 'unpaid')
    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="bi bi-hourglass-split me-1"></i>Admission {{ $invoice?->status === 'partially_paid' ? 'part-paid' : 'unpaid' }}</span>
@elseif ($status === 'waived')
    <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle" title="{{ $child->admission_fee_waiver_reason }}"><i class="bi bi-gift me-1"></i>Admission waived</span>
@elseif (config('admission.fee') > 0)
    <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle"><i class="bi bi-exclamation-circle me-1"></i>Admission not charged</span>
@endif
@if (!$compact && $invoice)
    <a href="{{ route('admin.invoices.show', $invoice) }}" class="small ms-1">{{ $invoice->invoice_number }}</a>
@endif
@if (!$compact && $status === 'waived' && $child->admission_fee_waiver_reason)
    <span class="small text-muted ms-1">— {{ $child->admission_fee_waiver_reason }}</span>
@endif
