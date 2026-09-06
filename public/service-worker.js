const CACHE = 'kyp-static-v1';

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE).then(cache => cache.addAll([
            '/manifest.webmanifest',
            '/images/app-icons/kyp-192.png',
            '/images/app-icons/kyp-512.png'
        ]))
    );

    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => key.startsWith('kyp-static-') && key !== CACHE)
                    .map(key => caches.delete(key))
            )
        )
    );

    self.clients.claim();
});

self.addEventListener('fetch', event => {
    const request = event.request;

    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) return;

    // Never cache authenticated/dynamic KYP pages.
    if (
        url.pathname.startsWith('/student') ||
        url.pathname.startsWith('/teacher') ||
        url.pathname.startsWith('/admin') ||
        url.pathname.startsWith('/learning') ||
        url.pathname.startsWith('/attendance') ||
        url.pathname.startsWith('/exam') ||
        url.pathname.startsWith('/profile') ||
        url.pathname.startsWith('/iris')
    ) {
        return;
    }

    // Cache only explicit static PWA resources.
    if (
        url.pathname === '/manifest.webmanifest' ||
        url.pathname.startsWith('/images/app-icons/')
    ) {
        event.respondWith(
            caches.match(request).then(cached =>
                cached || fetch(request).then(response => {
                    if (response.ok) {
                        caches.open(CACHE)
                            .then(cache => cache.put(request, response.clone()));
                    }

                    return response;
                })
            )
        );
    }
});
