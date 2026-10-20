{{--
    Invoice PDF (mPDF). mPDF supports table layout and basic CSS only — no flexbox/grid.
    Mirrors the invoice "document" card on admin/invoices/show.
--}}
@php
    $total = (float) $invoice->total_amount;
    $paid = (float) $invoice->paid_total;
    $balance = (float) $invoice->balance_due;
    $isOverdue = $invoice->is_open && $invoice->due_date->isPast() && !$invoice->due_date->isToday();
    $statusColors = [
        'draft' => ['#fef3c7', '#92400e'], 'sent' => ['#e0f2fe', '#075985'], 'partially_paid' => ['#e0e7ff', '#3730a3'],
        'paid' => ['#d1fae5', '#065f46'], 'overdue' => ['#ffe4e6', '#9f1239'], 'cancelled' => ['#f1f5f9', '#475569'],
    ];
    [$statusBg, $statusFg] = $statusColors[$invoice->status] ?? ['#f1f5f9', '#475569'];
    // ৳ in its own span so only the symbol switches to the Bangla font; digits stay in DejaVu Sans
    $tk = fn ($n) => '<span>৳</span>' . number_format((float) $n, 2);
    $contact = collect([$org['address'], $org['phone'], $org['email']])->filter()->implode('  ·  ');
@endphp
<html>
<head>
<style>
    body { color: #334155; }
    .muted { color: #64748b; }
    .label { font-size: 7pt; color: #94a3b8; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
    .strong { color: #0f172a; font-weight: bold; }
    .title { font-size: 24pt; font-weight: bold; color: #0d9488; letter-spacing: 3px; }
    .accent { height: 4px; background-color: #0d9488; }
    table.items { border-collapse: collapse; width: 100%; }
    table.items th { background-color: #f1f5f9; color: #475569; font-size: 7.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; padding: 7px 8px; }
    table.items td { padding: 8px; border-bottom: 0.5px solid #e2e8f0; }
    table.totals { border-collapse: collapse; }
    table.totals td { padding: 4px 0; }
    .grand td { border-top: 1.5px solid #0f172a; padding-top: 7px; font-size: 12pt; font-weight: bold; color: #0f172a; }
    .badge { padding: 2px 7px; font-size: 7.5pt; font-weight: bold; }
    table.pay { border-collapse: collapse; width: 100%; }
    table.pay th { text-align: left; font-size: 7pt; color: #64748b; text-transform: uppercase; padding: 5px 6px; border-bottom: 0.5px solid #cbd5e1; }
    table.pay td { padding: 5px 6px; border-bottom: 0.5px solid #f1f5f9; font-size: 8.5pt; }
</style>
</head>
<body>

<div class="accent"></div>

{{-- Header: brand + INVOICE --}}
<table width="100%" style="margin-top: 12px">
    <tr>
        <td width="60%" valign="middle">
            <table>
                <tr>
                    @if(is_file($logo))
                        <td valign="middle" style="padding-right: 8px"><img src="{{ $logo }}" width="40" height="40"></td>
                    @endif
                    <td valign="middle">
                        <div class="strong" style="font-size: 13pt">{{ $org['name'] }}</div>
                        <div class="muted" style="font-size: 8pt">{{ $contact ?: 'Daycare & Therapy Center' }}</div>
                    </td>
                </tr>
            </table>
        </td>
        <td width="40%" align="right" valign="middle">
            <div class="title">INVOICE</div>
            <div class="muted">{{ $invoice->invoice_number }}</div>
        </td>
    </tr>
</table>

{{-- Parties + dates --}}
<table width="100%" style="margin-top: 18px">
    <tr>
        <td width="36%" valign="top">
            <div class="label">Bill to</div>
            @if($invoice->parent)
                <div class="strong" style="margin-top: 3px">{{ $invoice->parent->full_name }}</div>
                @if($invoice->parent->mobile)<div class="muted">{{ $invoice->parent->mobile }}</div>@endif
                @if($invoice->parent->user?->email)<div class="muted">{{ $invoice->parent->user->email }}</div>@endif
                @if($invoice->parent->address)<div class="muted">{{ $invoice->parent->address }}</div>@endif
            @else
                <div class="muted">—</div>
            @endif
        </td>
        <td width="28%" valign="top">
            <div class="label">For child</div>
            <div class="strong" style="margin-top: 3px">{{ $invoice->child->full_name ?? '—' }}</div>
            @if($invoice->child?->date_of_birth)<div class="muted">{{ $invoice->child->formatted_age }}</div>@endif
        </td>
        <td width="36%" valign="top">
            <table width="100%">
                <tr><td class="muted">Issued</td><td align="right" class="strong">{{ $invoice->invoice_date->format('M d, Y') }}</td></tr>
                <tr><td class="muted">Due</td><td align="right" class="strong" style="{{ $isOverdue || $invoice->status === 'overdue' ? 'color:#be123c' : '' }}">{{ $invoice->due_date->format('M d, Y') }}</td></tr>
                <tr><td class="muted">Type</td><td align="right">{{ $invoice->invoice_type_label }}</td></tr>
                <tr><td class="muted">Status</td><td align="right"><span class="badge" style="background-color: {{ $statusBg }}; color: {{ $statusFg }}">{{ strtoupper($invoice->status_label) }}</span></td></tr>
            </table>
        </td>
    </tr>
</table>

{{-- Line items --}}
<table class="items" style="margin-top: 18px">
    <thead>
        <tr>
            <th width="6%" align="left">#</th>
            <th align="left">Description</th>
            <th width="16%" align="right">Unit price</th>
            <th width="8%" align="center">Qty</th>
            <th width="17%" align="right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $i => $item)
            <tr>
                <td class="muted">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td>
                    <span class="strong">{{ $item->description }}</span>
                    @if($item->therapy_session_id)<br><span style="font-size: 7pt; color: #0369a1">THERAPY SESSION</span>
                    @elseif($item->child_therapy_package_id)<br><span style="font-size: 7pt; color: #0369a1">THERAPY PACKAGE</span>@endif
                </td>
                <td align="right" class="muted">{!! $tk($item->unit_price) !!}</td>
                <td align="center" class="muted">{{ $item->quantity }}</td>
                <td align="right" class="strong">{!! $tk($item->amount) !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Totals --}}
<table width="100%" style="margin-top: 12px">
    <tr>
        <td width="55%"></td>
        <td width="45%">
            <table class="totals" width="100%">
                <tr><td class="muted">Subtotal</td><td align="right" class="strong">{!! $tk($total) !!}</td></tr>
                <tr><td class="muted">Paid</td><td align="right" style="color: #047857; font-weight: bold">− {!! $tk($paid) !!}</td></tr>
                <tr class="grand"><td>Balance due</td><td align="right">{!! $tk($balance) !!}</td></tr>
            </table>
        </td>
    </tr>
</table>

{{-- Payments received --}}
@if($invoice->payments->isNotEmpty())
    <div class="label" style="margin-top: 22px; margin-bottom: 4px">Payments received</div>
    <table class="pay">
        <thead>
            <tr><th>Date</th><th>Method</th><th>Reference</th><th style="text-align: right">Amount</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->payments->sortBy('paid_at') as $payment)
                <tr>
                    <td>{{ $payment->paid_at->format('M d, Y h:i A') }}</td>
                    <td>{{ $payment->payment_method_label }}</td>
                    <td>{{ $payment->transaction_id ?: '—' }}</td>
                    <td align="right" style="color: #047857; font-weight: bold">{!! $tk($payment->paid_amount) !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

{{-- Note --}}
<div style="margin-top: 24px; padding: 10px 12px; background-color: #f0fdfa; color: #115e59; font-size: 8.5pt">
    @if($balance > 0 && $invoice->is_open)
        Please pay {!! $tk($balance) !!} by {{ $invoice->due_date->format('M d, Y') }} at the front desk, or online with bKash, Nagad or card using your payment link.
    @else
        Thank you for choosing {{ $org['name'] }}!
    @endif
</div>

<div class="muted" style="margin-top: 10px; font-size: 7.5pt">
    Generated {{ now()->format('M d, Y h:i A') }}. This is a computer-generated invoice and does not require a signature.
</div>

</body>
</html>
