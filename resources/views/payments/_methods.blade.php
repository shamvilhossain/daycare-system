{{-- Payment method buttons. Usage: @include('payments._methods', ['action' => url, 'amount' => float]) --}}
@php
    $methods = [
        ['mobile', 'bi-phone',        'bKash / Nagad / Rocket', 'Mobile banking'],
        ['card',   'bi-credit-card',  'Card',                   'Visa, Mastercard, Amex'],
        ['all',    'bi-bank',         'Other options',          'Internet banking & more'],
    ];
@endphp
<div class="d-grid gap-2">
    @foreach ($methods as [$value, $icon, $label, $hint])
        <form method="POST" action="{{ $action }}" data-pp-pay-form>
            @csrf
            <input type="hidden" name="method" value="{{ $value }}">
            <button type="submit" class="btn {{ $loop->first ? 'btn-primary' : 'btn-light border' }} pp-btn-lg w-100 d-flex align-items-center gap-3 text-start">
                <i class="bi {{ $icon }} fs-4"></i>
                <span class="flex-grow-1">
                    <span class="d-block">{{ $label }}</span>
                    <span class="d-block small fw-normal {{ $loop->first ? 'opacity-75' : 'text-muted' }}">{{ $hint }}</span>
                </span>
                <span class="fw-bold">৳{{ number_format($amount, 2) }}</span>
            </button>
        </form>
    @endforeach
</div>
<div class="small text-muted text-center mt-3">
    <i class="bi bi-lock-fill me-1"></i>You’ll complete payment on the secure SSLCommerz page.
</div>
<script>
    // Prevent double taps from starting two gateway sessions.
    document.querySelectorAll('[data-pp-pay-form]').forEach(function (form) {
        form.addEventListener('submit', function () {
            document.querySelectorAll('[data-pp-pay-form] button').forEach(function (b) { b.disabled = true; });
            form.querySelector('button').insertAdjacentHTML('beforeend', '<span class="spinner-border spinner-border-sm ms-2"></span>');
        });
    });
</script>
