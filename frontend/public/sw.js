const CACHE_NAME = 'oxford-v2.0.0';
const urlsToCache = [
    '/',
    '/assets/index.css',
    '/assets/index.js',
    '/offline.html'
];

// Install event
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(urlsToCache))
    );
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event - Network First strategy for API, Cache First for assets
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // API requests - Network First
    if (request.url.includes('/api/')) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                    return response;
                })
                .catch(() => caches.match(request))
        );
        return;
    }

    // Static assets - Cache First
    event.respondWith(
        caches.match(request)
            .then((response) => response || fetch(request))
            .catch(() => caches.match('/offline.html'))
    );
});
