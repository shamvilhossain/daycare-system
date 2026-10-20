<?php

/*
|--------------------------------------------------------------------------
| Parent App / Play Store settings
|--------------------------------------------------------------------------
| Used by the privacy policy, the account-deletion page and the Digital
| Asset Links file that lets the Android (TWA) app open full-screen.
*/

return [

    // Organisation details shown on the privacy policy and account-deletion pages.
    'organization' => [
        'name'    => env('ORG_NAME', 'KinderCare'),
        'email'   => env('ORG_CONTACT_EMAIL'),
        'phone'   => env('ORG_CONTACT_PHONE'),
        'address' => env('ORG_ADDRESS'),
    ],

    // Shown as "Last updated" on the privacy policy. Change it whenever the policy text changes.
    'privacy_policy_updated' => env('PRIVACY_POLICY_UPDATED', '2026-10-20'),

    // Promised turnaround for account-deletion requests (days).
    'deletion_days' => (int) env('ACCOUNT_DELETION_DAYS', 30),

    // Android Trusted Web Activity — served at /.well-known/assetlinks.json.
    'android' => [
        'package_name' => env('TWA_PACKAGE_NAME'),
        // Comma-separated SHA-256 certificate fingerprints, e.g. the Play App Signing key
        // and (optionally) your upload key: "AA:BB:...,CC:DD:..."
        'sha256_fingerprints' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('TWA_SHA256_FINGERPRINTS', ''))
        ))),
    ],

];
