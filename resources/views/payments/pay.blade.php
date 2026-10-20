@extends('legal.layout')

@section('title', 'Pay Invoice')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger rounded-4 border-0">{{ session('error') }}</div>
    @endif

    <div class="pp-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <div class="pp-page-title mb-0">{{ $invoice->invoice_number }}</div>
                <div class="small text-muted">{{ $invoice->child?->full_name }} · {{ config('parentapp.organization.name') }}</div>
            </div>
            @include('parent.fees._status', ['invoice' => $invoice])
        </div>

        @foreach ($invoice->items as $item)
            <div class="pp-list-item">
                <div class="flex-grow-1 small">{{ $item->description }}</div>
                <div class="fw-semibold small">৳{{ number_format($item->amount, 2) }}</div>
            </div>
        @endforeach
        <hr>
        <div class="d-flex justify-content-between small"><span>Total</span><span>৳{{ number_format($invoice->total_amount, 2) }}</span></div>
        @if ($invoice->paid_total > 0)
            <div class="d-flex justify-content-between small text-success"><span>Already paid</span><span>৳{{ number_format($invoice->paid_total, 2) }}</span></div>
        @endif
        <div class="d-flex justify-content-between fs-5 fw-bold mt-1"><span>Amount due</span><span>৳{{ number_format($invoice->balance_due, 2) }}</span></div>
        <div class="small text-muted">Due {{ $invoice->due_date->format('j M Y') }}</div>
    </div>

    <div class="pp-card">
        @if ($invoice->status === 'cancelled')
            <div class="pp-empty"><i class="bi bi-x-circle"></i><div class="fw-bold text-dark">This invoice was cancelled.</div></div>
        @elseif ($invoice->balance_due <= 0)
            <div class="pp-empty"><i class="bi bi-check-circle text-success"></i><div class="fw-bold text-dark">This invoice is fully paid. Thank you!</div></div>
        @elseif (!$enabled || $amount === null)
            <div class="pp-empty">
                <i class="bi bi-building"></i>
                <div class="fw-bold text-dark">Online payment isn’t available for this invoice.</div>
                <div>Please pay at the daycare office.</div>
            </div>
        @else
            <div class="pp-card-title"><i class="bi bi-wallet2"></i> Choose how to pay</div>
            @include('payments._methods', ['action' => $formAction, 'amount' => $amount])
        @endif
    </div>
@endsection
