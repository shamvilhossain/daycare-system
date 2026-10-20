@extends('legal.layout')

@section('title', $txn->is_successful ? 'Payment Receipt' : 'Payment Status')

@php
    use App\Models\PaymentOnlineTransaction as Txn;
    $status = (int) $txn->status;
    [$icon, $color, $headline, $text] = match (true) {
        $status === Txn::STATUS_SUCCESS => ['bi-check-circle-fill', 'text-success', 'Payment successful', 'Thank you! Your payment has been received.'],
        $status === Txn::STATUS_CANCELLED => ['bi-x-circle-fill', 'text-secondary', 'Payment cancelled', 'No money was taken. You can try again any time.'],
        $status === Txn::STATUS_INITIATED => ['bi-hourglass-split', 'text-primary', 'Confirming your payment…', 'We are waiting for confirmation from the payment gateway. This page will refresh automatically.'],
        $status === Txn::STATUS_CALLBACK_RECEIVED => ['bi-shield-exclamation', 'text-warning', 'Payment under review', 'Your payment was received but needs a quick manual check. The office will confirm it shortly.'],
        default => ['bi-exclamation-circle-fill', 'text-danger', 'Payment not completed', 'The payment did not go through. If money was deducted, please contact the office with the reference below.'],
    };
@endphp

@if ($status === Txn::STATUS_INITIATED)
    @push('head')
        <meta http-equiv="refresh" content="5">
    @endpush
@endif

@section('content')
    <div class="pp-card text-center">
        <i class="bi {{ $icon }} {{ $color }}" style="font-size: 3.5rem"></i>
        <div class="pp-page-title mt-2">{{ $headline }}</div>
        <div class="text-muted">{{ $text }}</div>
        @if ($status === Txn::STATUS_INITIATED)
            <div class="spinner-border text-primary mt-3" role="status"></div>
        @endif
    </div>

    <div class="pp-card" id="receipt">
        <div class="pp-card-title"><i class="bi bi-receipt"></i> {{ $txn->is_successful ? 'Receipt' : 'Details' }}</div>
        @php
            $rows = [
                'Invoice'        => $invoice->invoice_number,
                'Child'          => $invoice->child?->full_name,
                'Paid by'        => $invoice->parent?->full_name,
                'Amount'         => '৳' . number_format($txn->amount, 2),
                'Method'         => $txn->method_label,
                'Reference'      => $txn->transaction_id,
                'Bank reference' => $txn->pg_bank_transaction_id,
                'Date'           => ($txn->pg_transaction_date ?? $txn->updated_at)->format('j M Y, g:i A'),
            ];
        @endphp
        @foreach ($rows as $label => $value)
            @if ($value)
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">{{ $label }}</span>
                    <span class="fw-semibold text-end">{{ $value }}</span>
                </div>
            @endif
        @endforeach
        @if ($txn->is_successful)
            <div class="d-flex justify-content-between pt-2">
                <span class="fw-bold">Remaining balance</span>
                <span class="fw-bold">৳{{ number_format($invoice->balance_due, 2) }}</span>
            </div>
        @endif
    </div>

    <div class="d-grid gap-2">
        @if ($txn->is_successful)
            <button type="button" class="btn btn-primary pp-btn-lg" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print / save receipt
            </button>
        @endif
        @auth
            @if (auth()->user()->hasRole('parent'))
                <a href="{{ route('parent.fees.show', $invoice->id) }}" class="btn btn-light border pp-btn-lg">Back to fees</a>
            @endif
        @endauth
    </div>
@endsection
