<?php

/*
|--------------------------------------------------------------------------
| Online payments (SSLCommerz)
|--------------------------------------------------------------------------
| Gateway credentials and endpoints live in the payment_channels table
| (the active row is used). These settings control links and callbacks.
*/

return [

    'currency' => 'BDT',

    // SSLCommerz minimum transaction amount (BDT).
    'min_amount' => 10,

    // How long a "Send payment link" URL stays valid.
    'link_valid_days' => (int) env('PAYMENT_LINK_DAYS', 7),

    // How long the receipt link shown after payment stays valid.
    'receipt_valid_days' => (int) env('PAYMENT_RECEIPT_DAYS', 365),

    // Base URL used in payment links sent to parents. Leave empty to use the current site URL.
    // Local testing: set to your tunnel URL so the link opens on a phone.
    'public_url' => env('PAYMENT_PUBLIC_URL'),

    // Base URL SSLCommerz posts back to (success / fail / cancel / IPN). Leave empty to use the
    // current site URL. On localhost the gateway cannot reach you, so set this to a tunnel URL,
    // e.g. https://xyz.trycloudflare.com/daycare-system/public
    'callback_url' => env('PAYMENT_CALLBACK_URL'),

    // Verify the gateway's TLS certificate. Only disable for local debugging.
    'verify_ssl' => (bool) env('PAYMENT_VERIFY_SSL', true),

];
