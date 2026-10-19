@extends('parent.layouts.app')

@section('title', 'Fees')

@section('content')
    <div class="pp-page-title">Fees</div>
    <div class="pp-page-sub">Invoices and receipts for {{ $activeChild->first_name }}.</div>

    {{-- Outstanding summary --}}
    <div class="pp-card text-white border-0" style="background: var(--pp-gradient)">
        <div class="small text-uppercase fw-bold" style="opacity:.85; letter-spacing:.05em">Outstanding balance</div>
        <div class="display-6 fw-bold">৳{{ number_format($outstanding, 2) }}</div>
        <div class="small" style="opacity:.85">
            {{ $outstanding > 0 ? 'Tap an unpaid invoice below to pay.' : 'All caught up — thank you!' }}
        </div>
    </div>

    @forelse ($invoices ?? [] as $invoice)
        <div class="pp-card">
            <a href="{{ route('parent.fees.show', $invoice->id) }}" class="d-flex justify-content-between align-items-start text-decoration-none text-reset">
                <div>
                    <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                    <div class="small text-muted">
                        {{ $invoice->invoice_date->format('j M Y') }} · Due {{ $invoice->due_date->format('j M Y') }}
                    </div>
                </div>
                @include('parent.fees._status', ['invoice' => $invoice])
            </a>
            <div class="d-flex justify-content-between align-items-end mt-3">
                <div>
                    <div class="small text-muted">Total ৳{{ number_format($invoice->total_amount, 2) }}</div>
                    @if ($invoice->balance_due > 0 && $invoice->paid_total > 0)
                        <div class="small text-success">Paid ৳{{ number_format($invoice->paid_total, 2) }}</div>
                    @endif
                    <div class="fs-5 fw-bold">
                        @if ($invoice->balance_due > 0)
                            ৳{{ number_format($invoice->balance_due, 2) }} <span class="small fw-normal text-muted">due</span>
                        @else
                            ৳{{ number_format($invoice->paid_total, 2) }} <span class="small fw-normal text-muted">paid</span>
                        @endif
                    </div>
                </div>
                @if ($invoice->balance_due > 0)
                    <a href="{{ route('parent.fees.pay', $invoice->id) }}" class="btn btn-primary pp-btn-lg px-4">
                        <i class="bi bi-credit-card me-1"></i> Pay
                    </a>
                @else
                    <a href="{{ route('parent.fees.show', $invoice->id) }}" class="btn btn-light border pp-btn-lg px-4">
                        <i class="bi bi-receipt me-1"></i> Receipt
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="pp-card pp-empty">
            <i class="bi bi-receipt"></i>
            <div class="fw-bold text-dark mb-1">No invoices yet</div>
            <div>Invoices from the daycare will appear here.</div>
        </div>
    @endforelse

    @if ($invoices && $invoices->hasPages())
        <div class="mt-3">{{ $invoices->links() }}</div>
    @endif
@endsection
