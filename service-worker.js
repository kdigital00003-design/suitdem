// SUITDEM ERP — Service Worker
// Strategy:
//   • Local assets  → Cache-first (fast, works offline)
//   • CDN scripts   → Network-first, NO caching (avoids cross-origin ERR_FAILED)
//   • API calls     → Network-only (Supabase, never cache)

const CACHE_NAME = 'suitdem-v2';

// Only local files get pre-cached on install
const LOCAL_ASSETS = [
    '/',
    '/index-combined.html',
    '/css/style.css',
    '/manifest.json',
    '/images/192X192.png',
    '/images/512X512.png',
    '/images/stamp.png',
    '/images/signature01.png'
];

// CDN origins — always go to network, never cache
const CDN_ORIGINS = [
    'cdn.jsdelivr.net',
    'cdn.sheetjs.com',
    'cdnjs.cloudflare.com',
    'fonts.googleapis.com',
    'fonts.gstatic.com'
];

// API origins — network-only
const API_ORIGINS = [
    'supabase.co',
    'xynzdcqwgymzwoieggzx'
];

// ── Install: pre-cache local assets ──
self.addEventListener('install', event => {
    self.skipWaiting(); // Activate immediately
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            // Cache individually so one failure doesn't block all
            return Promise.allSettled(
                LOCAL_ASSETS.map(url =>
                    cache.add(url).catch(e => console.warn('[SW] Could not cache:', url, e))
                )
            );
        })
    );
});

// ── Activate: clean up old caches ──
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            )
        ).then(() => self.clients.claim()) // Take control immediately
    );
});

// ── Fetch: route by origin ──
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // 1. Non-GET → always network (POST to Supabase etc.)
    if (event.request.method !== 'GET') return;

    // 2. CDN scripts → network-only, no caching
    if (CDN_ORIGINS.some(origin => url.hostname.includes(origin))) {
        event.respondWith(fetch(event.request));
        return;
    }

    // 3. API calls → network-only
    if (API_ORIGINS.some(origin => url.hostname.includes(origin))) {
        event.respondWith(fetch(event.request));
        return;
    }

    // 4. Chrome extensions / non-http → ignore
    if (!url.protocol.startsWith('http')) return;

    // 5. Local assets → cache-first, fallback to network then cache
    event.respondWith(
        caches.match(event.request).then(cached => {
            if (cached) return cached;

            return fetch(event.request).then(response => {
                // Only cache valid same-origin responses
                if (
                    response &&
                    response.status === 200 &&
                    response.type === 'basic'
                ) {
                    const toCache = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, toCache));
                }
                return response;
            }).catch(() => {
                // Offline fallback for navigation requests
                if (event.request.mode === 'navigate') {
                    return caches.match('/index-combined.html');
                }
            });
        })
    );
});

// ── Message: force update ──
self.addEventListener('message', event => {
    if (event.data?.type === 'SKIP_WAITING') self.skipWaiting();
});
