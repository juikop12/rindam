const CACHE_NAME = 'sipandu-rindam-v1';
const STATIC_ASSETS = [
    '/offline.html',
    '/manifest.json',
    '/img/icons/icon-192x192.png',
    '/img/icons/icon-512x512.png',
    '/img/icons/icon-512x512-maskable.png',
    '/img/rindam-logo.png'
];

// Install: Simpan aset offline dasar ke cache
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// Activate: Bersihkan cache versi lama jika ada
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch: Network First untuk request halaman (agar data dinamis selalu terbaru), fallback ke offline.html
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Hanya tangani metode GET
    if (request.method !== 'GET') {
        return;
    }

    // Untuk navigasi HTML (perpindahan halaman)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => {
                return caches.match('/offline.html');
            })
        );
        return;
    }

    // Untuk aset statis (gambar, font, ikon, dll): Stale-While-Revalidate
    if (
        request.destination === 'image' ||
        request.destination === 'font' ||
        request.destination === 'style' ||
        request.destination === 'script'
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                const fetchPromise = fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseClone = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseClone);
                        });
                    }
                    return networkResponse;
                }).catch(() => cachedResponse);

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // Default fetch
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});
