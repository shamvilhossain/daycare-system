{{-- Parent-friendly invoice status badge. Usage: @include('parent.fees._status', ['invoice' => $invoice]) --}}
@php
    $balance = $invoice->balance_due;
    [$label, $class] = match (true) {
        $invoice->status === 'cancelled'                 => ['Cancelled', 'bg-secondary-subtle text-secondary'],
        $invoice->status === 'paid' || $balance <= 0     => ['Paid', 'bg-success-subtle text-success'],
        $invoice->status === 'overdue'                   => ['Overdue', 'bg-danger-subtle text-danger'],
        $invoice->paid_total > 0                         => ['Partly paid', 'bg-info-subtle text-info-emphasis'],
        default                                          => ['Due', 'bg-warning-subtle text-warning-emphasis'],
    };
@endphp
<span class="badge rounded-pill px-3 py-2 {{ $class }}">{{ $label }}</span>
