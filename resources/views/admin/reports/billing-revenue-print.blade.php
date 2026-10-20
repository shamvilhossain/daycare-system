<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing & Revenue Report — {{ now()->format('M d, Y') }} | {{ $org['name'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @php
        $tk = fn ($n) => '৳' . number_format((float) $n, 2);
        $statusColors = [
            'draft' => ['#fef3c7', '#92400e'], 'sent' => ['#e0f2fe', '#075985'], 'partially_paid' => ['#e0e7ff', '#3730a3'],
            'paid' => ['#d1fae5', '#065f46'], 'overdue' => ['#ffe4e6', '#9f1239'], 'cancelled' => ['#f1f5f9', '#64748b'],
        ];
        $typeColors = ['daycare' => '#4f46e5', 'therapy' => '#0891b2', 'mixed' => '#9333ea'];
        $contact = collect([$org['address'], $org['phone'], $org['email']])->filter()->implode('  ·  ');
    @endphp
    <style>
        :root {
            --ink: #0f172a; --body: #334155; --muted: #64748b; --faint: #94a3b8;
            --line: #e2e8f0; --soft: #f8fafc; --accent: #0d9488; --accent-dark: #0f766e;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; }
        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Nirmala UI', sans-serif;
            color: var(--body); font-size: 10.5px; line-height: 1.38;
            background: #e2e8f0;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }

        /* ---------- Screen preview ---------- */
        .toolbar {
            position: sticky; top: 0; z-index: 10;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            padding: 10px 20px; background: #0f172a; color: #e2e8f0; font-size: 13px;
            box-shadow: 0 2px 12px rgba(0,0,0,.25);
        }
        .toolbar .hint { color: #94a3b8; }
        .toolbar button, .toolbar a {
            font: 600 13px Inter, sans-serif; border-radius: 8px; padding: 8px 16px; cursor: pointer;
            border: 1px solid rgba(255,255,255,.2); background: transparent; color: #fff; text-decoration: none;
        }
        .toolbar .primary { background: var(--accent); border-color: var(--accent); }
        .sheet {
            width: 297mm; min-height: 210mm; margin: 24px auto; padding: 14mm 14mm 12mm;
            background: #fff; box-shadow: 0 10px 40px rgba(15,23,42,.18);
        }

        /* ---------- Letterhead ---------- */
        .letterhead { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand img { width: 40px; height: 40px; border-radius: 10px; }
        .brand .name { font-size: 17px; font-weight: 800; color: var(--ink); line-height: 1.1; }
        .brand .contact { color: var(--muted); font-size: 10px; margin-top: 2px; }
        .doc-title { text-align: right; }
        .doc-title .eyebrow { font-size: 9px; font-weight: 700; letter-spacing: .14em; color: var(--accent); text-transform: uppercase; }
        .doc-title h1 { margin: 2px 0 4px; font-size: 20px; font-weight: 800; color: var(--ink); letter-spacing: -.01em; }
        .doc-title .meta { color: var(--muted); font-size: 9.5px; }
        .rule { height: 3px; margin: 10px 0 8px; background: linear-gradient(90deg, var(--accent-dark), var(--accent) 60%, #5eead4); border-radius: 2px; }

        /* ---------- Filters ---------- */
        .filters { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
        .chip { display: inline-flex; gap: 4px; padding: 3px 9px; border-radius: 999px; background: var(--soft); border: 1px solid var(--line); font-size: 9.5px; }
        .chip b { color: var(--ink); font-weight: 600; }

        /* ---------- KPI band ---------- */
        .kpis { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 10px; }
        .kpi { border: 1px solid var(--line); border-radius: 10px; padding: 8px 12px; position: relative; overflow: hidden; }
        .kpi::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--k, var(--accent)); }
        .kpi .label { font-size: 8.5px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
        .kpi .value { font-size: 16px; font-weight: 800; color: var(--ink); margin: 2px 0 1px; letter-spacing: -.01em; }
        .kpi .sub { font-size: 9px; color: var(--muted); }
        .bar { height: 5px; background: #e2e8f0; border-radius: 99px; overflow: hidden; margin-top: 5px; }
        .bar > i { display: block; height: 100%; background: var(--k, var(--accent)); border-radius: 99px; }

        /* ---------- Breakdowns ---------- */
        .breakdowns { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px; }
        .box-title { font-size: 9px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); margin: 0 0 5px; }
        table { width: 100%; border-collapse: collapse; }
        .mini th { text-align: left; font-size: 8.5px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; padding: 4px 7px; background: var(--soft); border-bottom: 1px solid var(--line); }
        .mini td { padding: 2px 7px; border-bottom: 1px solid #f1f5f9; }
        .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .ctr { text-align: center; }
        .dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; vertical-align: middle; }
        .share { display: inline-block; width: 60px; height: 5px; background: #e2e8f0; border-radius: 99px; overflow: hidden; vertical-align: middle; margin-right: 5px; }
        .share > i { display: block; height: 100%; border-radius: 99px; }

        /* ---------- Detail table ---------- */
        .detail-head { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px; }
        .detail-head .count { font-size: 9.5px; color: var(--muted); }
        .detail thead th {
            font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #fff;
            background: var(--ink); padding: 6px 7px; text-align: left; white-space: nowrap;
        }
        .detail thead th.num { text-align: right; }
        .detail thead th.ctr { text-align: center; }
        .detail td { padding: 3px 7px; border-bottom: 1px solid #eef2f7; vertical-align: top; }
        .detail tbody tr:nth-child(even) td { background: #fafbfc; }
        .detail tbody.keep-together { break-inside: avoid; page-break-inside: avoid; }
        .detail tr { break-inside: avoid; page-break-inside: avoid; }
        .detail .inv { font-weight: 700; color: var(--ink); white-space: nowrap; }
        .detail .who { font-weight: 600; color: var(--ink); }
        .detail .kid { color: var(--muted); font-size: 9px; line-height: 1.25; }
        .detail .muted { color: var(--faint); }
        .detail tr.cancelled td { color: var(--faint); }
        .detail tr.cancelled .strike { text-decoration: line-through; }
        .detail .late { color: #be123c; font-weight: 600; }
        .badge { display: inline-block; padding: 2px 7px; border-radius: 999px; font-size: 8.5px; font-weight: 700; white-space: nowrap; }
        .type { font-weight: 600; font-size: 9.5px; white-space: nowrap; }
        .detail tr.total td {
            background: var(--soft) !important; border-top: 2px solid var(--ink); border-bottom: 0;
            font-weight: 800; color: var(--ink); padding-top: 7px; padding-bottom: 7px; font-size: 11px;
        }
        .empty { text-align: center; padding: 30px; color: var(--muted); }

        /* ---------- Notes & sign-off ---------- */
        .notes { margin-top: 8px; font-size: 9px; color: var(--muted); }
        .signoff { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 18px; break-inside: avoid; }
        .signoff div { border-top: 1px solid var(--body); padding-top: 3px; font-size: 9px; color: var(--muted); }
        .signoff b { display: block; color: var(--ink); font-size: 10px; }
        .screen-footer { display: flex; justify-content: space-between; margin-top: 16px; padding-top: 6px; border-top: 1px solid var(--line); font-size: 8.5px; color: var(--faint); }

        /* ---------- Print ---------- */
        @page {
            size: A4 landscape;
            margin: 11mm 12mm 14mm;
            @bottom-left  { content: "{{ addslashes($org['name']) }} · Billing & Revenue Report · {{ now()->format('M d, Y') }}"; font: 8px Inter, sans-serif; color: #94a3b8; }
            @bottom-right { content: "Page " counter(page) " of " counter(pages); font: 8px Inter, sans-serif; color: #94a3b8; }
        }
        @media print {
            body { background: #fff; }
            .toolbar, .screen-footer { display: none !important; }
            .sheet { width: auto; min-height: 0; margin: 0; padding: 0; box-shadow: none; }
            thead { display: table-header-group; } /* repeat column header on every page */
            .kpis, .breakdowns { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div><strong>Print preview</strong> <span class="hint">· A4 landscape · {{ $rows->count() }} {{ Str::plural('invoice', $rows->count()) }} · tip: enable “Background graphics” for colours</span></div>
        <div style="display:flex; gap:8px">
            <a href="{{ url()->previous() }}" onclick="if (window.opener) { window.close(); return false; }">Close</a>
            <button type="button" class="primary" onclick="window.print()">Print / Save as PDF</button>
        </div>
    </div>

    <div class="sheet">
        {{-- Letterhead --}}
        <div class="letterhead">
            <div class="brand">
                <img src="{{ asset('icons/icon-192.png') }}" alt="">
                <div>
                    <div class="name">{{ $org['name'] }}</div>
                    <div class="contact">{{ $contact ?: 'Daycare & Therapy Center' }}</div>
                </div>
            </div>
            <div class="doc-title">
                <div class="eyebrow">Financial report</div>
                <h1>Billing &amp; Revenue Report</h1>
                <div class="meta">
                    Generated {{ now()->format('M d, Y · h:i A') }}@if($generatedBy) · by {{ $generatedBy }}@endif
                </div>
            </div>
        </div>
        <div class="rule"></div>

        {{-- Applied filters --}}
        <div class="filters">
            @foreach ($filters as $label => $value)
                <span class="chip">{{ $label }}: <b>{{ $value }}</b></span>
            @endforeach
        </div>

        {{-- KPIs --}}
        <div class="kpis">
            <div class="kpi" style="--k:#4f46e5">
                <div class="label">Total invoiced</div>
                <div class="value">{{ $tk($summary['total_invoiced']) }}</div>
                <div class="sub">{{ $summary['total_count'] - $summary['cancelled_count'] }} active {{ Str::plural('invoice', $summary['total_count'] - $summary['cancelled_count']) }}@if($summary['cancelled_count']) · {{ $summary['cancelled_count'] }} cancelled excluded @endif</div>
            </div>
            <div class="kpi" style="--k:#059669">
                <div class="label">Total collected</div>
                <div class="value">{{ $tk($summary['total_collected']) }}</div>
                <div class="sub">{{ $summary['paid_count'] }} fully settled</div>
            </div>
            <div class="kpi" style="--k:#e11d48">
                <div class="label">Outstanding</div>
                <div class="value">{{ $tk($summary['total_outstanding']) }}</div>
                <div class="sub">{{ $summary['overdue_count'] }} overdue · {{ $summary['pending_count'] }} pending</div>
            </div>
            <div class="kpi" style="--k:#0891b2">
                <div class="label">Collection rate</div>
                <div class="value">{{ number_format($summary['collection_rate'], 1) }}%</div>
                <div class="bar"><i style="width: {{ min(100, $summary['collection_rate']) }}%"></i></div>
            </div>
        </div>

        {{-- Breakdowns --}}
        <div class="breakdowns">
            <div>
                <div class="box-title">By status</div>
                <table class="mini">
                    <thead><tr><th>Status</th><th class="ctr">Invoices</th><th class="num">Amount</th><th class="num">Paid</th></tr></thead>
                    <tbody>
                        @forelse ($byStatus as $s)
                            @php [$bg, $fg] = $statusColors[$s['status']] ?? ['#f1f5f9', '#475569']; @endphp
                            <tr>
                                <td><span class="dot" style="background: {{ $fg }}"></span>{{ $s['label'] }}</td>
                                <td class="ctr">{{ $s['count'] }}</td>
                                <td class="num">{{ $tk($s['amount']) }}</td>
                                <td class="num">{{ $tk($s['paid']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                <div class="box-title">By type</div>
                <table class="mini">
                    <thead><tr><th>Type</th><th class="ctr">Invoices</th><th class="num">Invoiced</th><th class="num">Collected</th><th>Share of invoiced</th></tr></thead>
                    <tbody>
                        @forelse ($byType as $t)
                            @php $c = $typeColors[strtolower($t['label'])] ?? '#64748b'; @endphp
                            <tr>
                                <td><span class="dot" style="background: {{ $c }}"></span>{{ $t['label'] }}</td>
                                <td class="ctr">{{ $t['count'] }}</td>
                                <td class="num">{{ $tk($t['invoiced']) }}</td>
                                <td class="num">{{ $tk($t['collected']) }}</td>
                                <td><span class="share"><i style="width: {{ $t['share'] }}%; background: {{ $c }}"></i></span>{{ $t['share'] }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Detail --}}
        <div class="detail-head">
            <div class="box-title" style="margin:0">Invoice detail</div>
            <div class="count">{{ $rows->count() }} {{ Str::plural('invoice', $rows->count()) }}, newest first</div>
        </div>
        <table class="detail">
            <thead>
                <tr>
                    <th class="ctr" style="width:28px">#</th>
                    <th>Invoice #</th>
                    <th>Parent / Child</th>
                    <th>Type</th>
                    <th>Issued</th>
                    <th>Due</th>
                    <th class="num">Amount</th>
                    <th class="num">Paid</th>
                    <th class="num">Balance</th>
                    <th class="ctr">Status</th>
                    <th>Paid on</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $i => $inv)
                @if ($loop->last && $loop->count > 1)
                    {{-- keep the last invoice and the totals row on the same page --}}
                    </tbody><tbody class="keep-together">
                @endif
                    @php
                        $cancelled = $inv->status === 'cancelled';
                        [$bg, $fg] = $statusColors[$inv->status] ?? ['#f1f5f9', '#475569'];
                        $late = !$cancelled && $inv->balance_due > 0 && ($inv->status === 'overdue' || $inv->due_date?->isPast());
                    @endphp
                    <tr class="{{ $cancelled ? 'cancelled' : '' }}">
                        <td class="ctr muted">{{ $i + 1 }}</td>
                        <td class="inv">{{ $inv->invoice_number }}</td>
                        <td>
                            <div class="who">{{ $inv->parent->full_name ?? '—' }}</div>
                            @if ($inv->child)<div class="kid">{{ $inv->child->full_name }}</div>@endif
                        </td>
                        <td><span class="type" style="color: {{ $typeColors[$inv->invoice_type] ?? '#64748b' }}">{{ $inv->invoice_type_label }}</span></td>
                        <td style="white-space:nowrap">{{ $inv->invoice_date?->format('M d, Y') ?? '—' }}</td>
                        <td style="white-space:nowrap" class="{{ $late ? 'late' : '' }}">{{ $inv->due_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="num"><span class="strike">{{ $tk($inv->total_amount) }}</span></td>
                        <td class="num">{{ $inv->paid_total > 0 ? $tk($inv->paid_total) : '—' }}</td>
                        <td class="num" style="{{ !$cancelled && $inv->balance_due > 0 ? 'font-weight:700;color:var(--ink)' : '' }}">{{ $cancelled ? '—' : $tk($inv->balance_due) }}</td>
                        <td class="ctr"><span class="badge" style="background: {{ $bg }}; color: {{ $fg }}">{{ $inv->status_label }}</span></td>
                        <td style="white-space:nowrap">{{ $inv->paid_date?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="empty">No invoices match the selected filters.</td></tr>
                @endforelse
                @if ($rows->isNotEmpty())
                    <tr class="total">
                        <td></td>
                        <td colspan="5">Total · {{ $rows->count() }} {{ Str::plural('invoice', $rows->count()) }}</td>
                        <td class="num">{{ $tk($totals['amount']) }}</td>
                        <td class="num">{{ $tk($totals['paid']) }}</td>
                        <td class="num">{{ $tk($totals['balance']) }}</td>
                        <td colspan="2"></td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="notes">
            All amounts in Bangladeshi Taka (৳). Cancelled invoices are listed for reference but excluded from Total invoiced, Outstanding and the Amount / Balance totals.
            Total collected includes every payment received against the listed invoices, including payments on cancelled invoices (refund or credit due).
        </div>

        <div class="signoff">
            <div><b>Prepared by</b>{{ $generatedBy ?? '' }}</div>
            <div><b>Checked by</b>Name &amp; signature</div>
            <div><b>Approved by</b>Name &amp; signature</div>
        </div>

        <div class="screen-footer">
            <span>{{ $org['name'] }} · Billing &amp; Revenue Report</span>
            <span>Page numbers are added when printed</span>
        </div>
    </div>

    <script>
        // Open the print dialog once fonts are ready (the page stays as a preview afterwards)
        window.addEventListener('load', function () {
            (document.fonts ? document.fonts.ready : Promise.resolve()).then(function () {
                setTimeout(function () { window.print(); }, 350);
            });
        });
    </script>
</body>
</html>
