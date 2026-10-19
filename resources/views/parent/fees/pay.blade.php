@extends('parent.layouts.app')

@section('title', 'Pay ' . $invoice->invoice_number)

@section('content')
    <a href="{{ route('parent.fees.show', $invoice->id) }}" class="d-inline-flex align-items-center gap-1 text-decoration-none mb-3 fw-semibold">
        <i class="bi bi-arrow-left"></i> Invoice
    </a>

    <div class="pp-page-title">Pay invoice</div>
    <div class="pp-page-sub">{{ $invoice->invoice_number }} · {{ $invoice->child?->full_name }}</div>

    <div class="pp-card text-center">
        <div class="small text-muted text-uppercase fw-bold">Amount to pay</div>
        <div class="display-6 fw-bold">৳{{ number_format($invoice->balance_due, 2) }}</div>
        <div class="small text-muted">Due {{ $invoice->due_date->format('j M Y') }}</div>
    </div>

    @if ($invoice->balance_due <= 0)
        <div class="pp-card pp-empty">
            <i class="bi bi-check-circle text-success"></i>
            <div class="fw-bold text-dark">This invoice is fully paid.</div>
        </div>
    @else
        <div class="pp-card">
            <div class="pp-card-title"><i class="bi bi-phone"></i> Pay online</div>
            @foreach (['bKash', 'Nagad', 'Card / Internet banking'] as $method)
                <div class="d-flex align-items-center gap-3 py-2 opacity-50">
                    <span class="pp-icon-bubble"><i class="bi bi-wallet2"></i></span>
                    <span class="fw-semibold flex-grow-1">{{ $method }}</span>
                    @unless ($onlinePaymentEnabled)
                        <span class="pp-chip">Coming soon</span>
                    @endunless
                </div>
            @endforeach

            {{-- TODO: post to the payment gateway initiate route once payment_channels is wired up. --}}
            <button type="button" class="btn btn-primary pp-btn-lg w-100 mt-3" @disabled(!$onlinePaymentEnabled)>
                <i class="bi bi-lock me-1"></i> Pay ৳{{ number_format($invoice->balance_due, 2) }}
            </button>
        </div>

        <div class="pp-card d-flex gap-3">
            <span class="pp-icon-bubble"><i class="bi bi-building"></i></span>
            <div>
                <div class="fw-bold">Pay at the front desk</div>
                <div class="small text-muted">
                    Cash, card or bank transfer at the daycare office. Your receipt will appear in the app once recorded.
                </div>
            </div>
        </div>
    @endif
@endsection
