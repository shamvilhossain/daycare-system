/*
 * KinderCare Parent Portal — service worker.
 *
 * Registered with scope "<base>/parent/" so it never controls the admin portal.
 * All paths are relative to where sw.js is served, so it works at a domain root
 * and in a sub-folder (e.g. Laragon's /daycare-system/public/).
 *
 * Caching rules (privacy first — phones get shared):
 *  - Page navigations: always network. If the network fails, show /offline.html.
 *    Authenticated HTML (child data, invoices) is NEVER cached.
 *  - build/assets/*  (Vite, content-hashed): cache-first.
 *  - icons/*, vendor/bootstrap-icons/*, offline.html, manifest.json: precached app shell.
 *  - Google Fonts: stale-while-revalidate.
 *  - Everything else (e.g. /storage photos, POSTs, other origins): untouched.
 *
 * Bump CACHE_VERSION to force clients to drop old caches.
 */
const CACHE_VERSION = 'v1';
const SHELL_CACHE = `kc-shell-${CACHE_VERSION}`;
const ASSET_CACHE = `kc-assets-${CACHE_VERSION}`;
const FONT_CACHE = `kc-fonts-${CACHE_VERSION}`;
const KNOWN_CACHES = [SHELL_CACHE, ASSET_CACHE, FONT_CACHE];

// Base path of the app ("/" in production, "/daycare-system/public/" under Laragon).
const BASE = new URL('./', self.location).pathname;

const OFFLINE_URL = `${BASE}offline.html`;
const SHELL_URLS = [
    OFFLINE_URL,
    `${BASE}manifest.json`,
    `${BASE}icons/icon-192.png`,
    `${BASE}icons/icon-512.png`,
    `${BASE}icons/favicon-32.png`,
    `${BASE}vendor/bootstrap-icons/font/bootstrap-icons.min.css`,
    // The icon font itself is cached at runtime (the CSS requests it with a ?hash query).
];
const ICONS_FONT_PREFIX = `${BASE}vendor/bootstrap-icons/`;
// Vite entries whose built files make up the parent app shell.
const VITE_ENTRIES = ['resources/css/parent.css', 'resources/js/parent.js'];
const MAX_ASSET_ENTRIES = 60;
const MAX_FONT_ENTRIES = 30;

self.addEventListener('install', (event) => {
    event.waitUntil((async () => {
        const shell = await caches.open(SHELL_CACHE);
        await shell.addAll(SHELL_URLS.map((url) => new Request(url, { cache: 'reload' })));

        // Best effort: precache the parent CSS/JS bundle listed in the Vite manifest.
        try {
            const assetUrls = await viteShellAssets();
            if (assetUrls.length) {
                const assets = await caches.open(ASSET_CACHE);
                await assets.addAll(assetUrls);
            }
        } catch (e) {
            // Dev server (npm run dev) or missing manifest — assets get cached at runtime instead.
        }

        await self.skipWaiting();
    })());
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const names = await caches.keys();
        await Promise.all(
            names.filter((n) => n.startsWith('kc-') && !KNOWN_CACHES.includes(n)).map((n) => caches.delete(n))
        );
        if (self.registration.navigationPreload) {
            await self.registration.navigationPreload.enable();
        }
        await self.clients.claim();
    })());
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    // 1. Page navigations: network only, offline page on failure.
    if (request.mode === 'navigate') {
        event.respondWith(networkWithOfflineFallback(event));
        return;
    }

    // 2. Same-origin static files.
    if (url.origin === self.location.origin) {
        if (url.pathname.startsWith(`${BASE}build/assets/`)) {
            event.respondWith(cacheFirst(request, ASSET_CACHE, MAX_ASSET_ENTRIES));
            return;
        }
        if (url.pathname.startsWith(`${BASE}icons/`) || url.pathname.startsWith(ICONS_FONT_PREFIX) || SHELL_URLS.includes(url.pathname)) {
            event.respondWith(cacheFirst(request, SHELL_CACHE));
            return;
        }
        return; // /storage photos, JSON, etc. — leave to the network.
    }

    // 3. Google Fonts.
    if (url.hostname === 'fonts.googleapis.com' || url.hostname === 'fonts.gstatic.com') {
        event.respondWith(staleWhileRevalidate(request, FONT_CACHE, MAX_FONT_ENTRIES));
    }
});

async function networkWithOfflineFallback(event) {
    try {
        const preloaded = await event.preloadResponse;
        if (preloaded) return preloaded;
        return await fetch(event.request);
    } catch (e) {
        const cached = await caches.match(OFFLINE_URL, { cacheName: SHELL_CACHE });
        return cached || new Response('You are offline.', { status: 503, headers: { 'Content-Type': 'text/plain' } });
    }
}

async function cacheFirst(request, cacheName, maxEntries) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    if (cached) return cached;

    const response = await fetch(request);
    if (response.ok) {
        await cache.put(request, response.clone());
        if (maxEntries) trimCache(cacheName, maxEntries);
    }
    return response;
}

async function staleWhileRevalidate(request, cacheName, maxEntries) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);
    const network = fetch(request)
        .then(async (response) => {
            if (response.ok || response.type === 'opaque') {
                await cache.put(request, response.clone());
                trimCache(cacheName, maxEntries);
            }
            return response;
        })
        .catch(() => cached);

    return cached || network;
}

async function trimCache(cacheName, maxEntries) {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    for (let i = 0; i < keys.length - maxEntries; i++) {
        await cache.delete(keys[i]);
    }
}

async function viteShellAssets() {
    const response = await fetch(`${BASE}build/manifest.json`, { cache: 'no-store' });
    if (!response.ok) return [];
    const manifest = await response.json();

    const files = new Set();
    const visit = (key) => {
        const chunk = manifest[key];
        if (!chunk) return;
        files.add(`${BASE}build/${chunk.file}`);
        (chunk.css || []).forEach((f) => files.add(`${BASE}build/${f}`));
        (chunk.imports || []).forEach(visit);
    };
    VITE_ENTRIES.forEach(visit);

    return [...files];
}

// Lets the page ask the worker to drop every cache (used on logout).
self.addEventListener('message', (event) => {
    if (event.data === 'kc:clear-caches') {
        event.waitUntil(
            caches.keys().then((names) => Promise.all(names.filter((n) => n.startsWith('kc-')).map((n) => caches.delete(n))))
        );
    }
});
