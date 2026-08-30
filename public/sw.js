const CACHE_NAME = 'bims-v1';
const PRECACHE_URLS = ['/dashboard', '/residents', '/households', '/documents', '/ids', '/health', '/services', '/blotter', '/businesses', '/revenue', '/officials', '/announcements', '/meetings', '/reports', '/settings'];

// Install: precache dashboard shell
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(PRECACHE_URLS).catch(() => {});
        })
    );
    self.skipWaiting();
});

// Activate: clean old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// Fetch: network-first with cache fallback
self.addEventListener('fetch', event => {
    const { request } = event;

    // Only cache same-origin GET requests (page navigations)
    if (request.method !== 'GET' || !request.url.startsWith(self.location.origin)) {
        return;
    }

    // Skip Livewire POST/PUT/DELETE and API calls
    if (request.headers.get('X-Livewire') || request.headers.get('Accept')?.includes('text/component')) {
        return;
    }

    event.respondWith(
        fetch(request)
            .then(response => {
                // Cache successful HTML responses
                if (response.ok && response.headers.get('content-type')?.includes('text/html')) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                }
                return response;
            })
            .catch(() => {
                // Fallback to cache when offline or slow
                return caches.match(request).then(cached => {
                    return cached || new Response('Offline', { status: 503 });
                });
            })
    );
});
