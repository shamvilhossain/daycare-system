@extends('parent.layouts.app')

@section('title', $invoice->invoice_number)

@section('content')
    <a href="{{ route('parent.fees.index') }}" class="d-inline-flex align-items-center gap-1 text-decoration-none mb-3 fw-semibold">
        <i class="bi bi-arrow-left"></i> Fees
    </a>

    <div class="pp-card">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <div class="pp-page-title mb-0">{{ $invoice->invoice_number }}</div>
                <div class="small text-muted">{{ $invoice->child?->full_name }} · {{ $invoice->invoice_type_label }}</div>
            </div>
            @include('parent.fees._status', ['invoice' => $invoice])
        </div>

        <div class="d-flex gap-2 mb-3">
            <div class="pp-stat">
                <div class="pp-stat-label">Issued</div>
                <div class="fw-bold">{{ $invoice->invoice_date->format('j M Y') }}</div>
            </div>
            <div class="pp-stat">
                <div class="pp-stat-label">Due</div>
                <div class="fw-bold">{{ $invoice->due_date->format('j M Y') }}</div>
            </div>
        </div>

        <div class="pp-card-title mb-2"><i class="bi bi-list-ul"></i> Items</div>
        @foreach ($invoice->items as $item)
            <div class="pp-list-item">
                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ $item->description }}</div>
                    <div class="small text-muted">{{ $item->quantity }} × ৳{{ number_format($item->unit_price, 2) }}</div>
                </div>
                <div class="fw-bold">৳{{ number_format($item->amount, 2) }}</div>
            </div>
        @endforeach

        <hr>
        <div class="d-flex justify-content-between"><span>Total</span><span class="fw-bold">৳{{ number_format($invoice->total_amount, 2) }}</span></div>
        <div class="d-flex justify-content-between text-success"><span>Paid</span><span class="fw-bold">৳{{ number_format($invoice->paid_total, 2) }}</span></div>
        <div class="d-flex justify-content-between fs-5 mt-1"><span class="fw-bold">Balance due</span><span class="fw-bold">৳{{ number_format($invoice->balance_due, 2) }}</span></div>
    </div>

    {{-- Receipts --}}
    <div class="pp-card">
        <div class="pp-card-title"><i class="bi bi-receipt"></i> Payments &amp; receipts</div>
        @forelse ($invoice->payments->sortByDesc('paid_at') as $payment)
            <div class="pp-list-item">
                <span class="pp-icon-bubble bg-success-subtle text-success"><i class="bi bi-check2-circle"></i></span>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">৳{{ number_format($payment->paid_amount, 2) }}</span>
                        <span class="small text-muted">{{ $payment->paid_at?->format('j M Y, g:i A') }}</span>
                    </div>
                    <div class="small text-muted">
                        {{ $payment->payment_method_label }}
                        @if ($payment->transaction_id) · Ref {{ $payment->transaction_id }} @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-muted">No payments yet.</div>
        @endforelse
    </div>

    @if ($invoice->status !== 'cancelled' && $invoice->balance_due > 0)
        <a href="{{ route('parent.fees.pay', $invoice->id) }}" class="btn btn-primary pp-btn-lg w-100">
            <i class="bi bi-credit-card me-1"></i> Pay ৳{{ number_format($invoice->balance_due, 2) }}
        </a>
    @endif
@endsection
