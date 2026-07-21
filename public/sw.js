const CACHE_VERSION = 'prono-v1';
const APP_SHELL = ['/offline.html', '/manifest.webmanifest', '/icons/icon-192.png', '/icons/icon-512.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_VERSION).then((cache) => cache.addAll(APP_SHELL))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((key) => key !== CACHE_VERSION).map((key) => caches.delete(key))
        ))
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET' || !request.url.startsWith(self.location.origin)) {
        return;
    }

    const url = new URL(request.url);

    // Vite emits content-hashed, immutable filenames: safe to cache-first.
    if (url.pathname.startsWith('/build/')) {
        event.respondWith(
            caches.open(CACHE_VERSION).then((cache) => cache.match(request).then((cached) => cached || fetch(request).then((response) => {
                cache.put(request, response.clone());
                return response;
            })))
        );
        return;
    }

    // Page navigations: try the network first (data changes constantly),
    // fall back to the offline shell only when the network is unreachable.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );
        return;
    }

    // Everything else (auth pages, API-like requests...) goes straight
    // to the network untouched.
});
