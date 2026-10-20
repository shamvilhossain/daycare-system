<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Public pages needed to publish the parent app on Google Play.
 */
class AppStoreController extends Controller
{
    /**
     * Digital Asset Links: proves the Android (TWA) app and this website belong together,
     * so the app opens full-screen without a browser bar. Must be served at the domain root.
     */
    public function assetLinks(): JsonResponse
    {
        $package = config('parentapp.android.package_name');
        $fingerprints = config('parentapp.android.sha256_fingerprints');

        $statements = ($package && $fingerprints) ? [[
            'relation' => ['delegate_permission/common.handle_all_urls'],
            'target'   => [
                'namespace'                => 'android_app',
                'package_name'             => $package,
                'sha256_cert_fingerprints' => $fingerprints,
            ],
        ]] : [];

        return response()->json($statements, 200, [
            'Cache-Control' => 'public, max-age=3600',
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function privacyPolicy(): View
    {
        return view('legal.privacy-policy', [
            'org'         => config('parentapp.organization'),
            'updated'     => \Carbon\Carbon::parse(config('parentapp.privacy_policy_updated')),
            'deletionDays' => config('parentapp.deletion_days'),
        ]);
    }
}
