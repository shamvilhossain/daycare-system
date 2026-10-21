{{-- Single pay button → SSLCommerz checkout, where the parent picks bKash, Nagad, card or net banking.
     Usage: @include('payments._methods', ['action' => url, 'amount' => float]) --}}
<form method="POST" action="{{ $action }}" data-pp-pay-form>
    @csrf
    <input type="hidden" name="method" value="all">
    <button type="submit" class="btn btn-primary pp-btn-lg w-100 d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-lock-fill"></i>
        <span>Pay ৳{{ number_format($amount, 2) }} — bKash, Nagad or card</span>
    </button>
</form>
<div class="d-flex justify-content-center flex-wrap gap-2 mt-3">
    <span class="pp-chip"><i class="bi bi-phone me-1"></i>bKash</span>
    <span class="pp-chip"><i class="bi bi-phone me-1"></i>Nagad</span>
    <span class="pp-chip"><i class="bi bi-phone me-1"></i>Rocket</span>
    <span class="pp-chip"><i class="bi bi-credit-card me-1"></i>Visa / Mastercard / Amex</span>
    <span class="pp-chip"><i class="bi bi-bank me-1"></i>Net banking</span>
</div>
<div class="small text-muted text-center mt-3">
    <i class="bi bi-shield-lock-fill me-1"></i>You’ll choose how to pay on the secure SSLCommerz page.
</div>
<script>
    // Prevent double taps from starting two gateway sessions.
    document.querySelectorAll('[data-pp-pay-form]').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button');
            btn.disabled = true;
            btn.insertAdjacentHTML('beforeend', '<span class="spinner-border spinner-border-sm ms-2"></span>');
        });
    });
</script>
