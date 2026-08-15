const CACHE_NAME = 'resto-coto-pos-cache-v6';
const DYNAMIC_CACHE = 'resto-coto-pos-dynamic-v6';

// Aset yang akan di-cache secara default saat instalasi
const ASSETS_TO_CACHE = [
    '/pos', // Halaman utama POS
    '/manifest.json',
    '/favicon.ico',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                return cache.addAll(ASSETS_TO_CACHE);
            })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME && key !== DYNAMIC_CACHE)
                    .map((key) => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    if (event.request.url.includes('/api/')) return;

    const isPosRequest = event.request.url.includes('/pos');
    
    // Untuk dokumen HTML (navigate) dan request ke /pos (termasuk Inertia XHR), gunakan Network First
    // Ini memastikan user selalu mendapatkan versi aplikasi terbaru tanpa harus clear cache manual
    if (event.request.mode === 'navigate' || isPosRequest) {
        event.respondWith(
            fetch(event.request).then((networkResponse) => {
                return caches.open(DYNAMIC_CACHE).then((cache) => {
                    cache.put(event.request, networkResponse.clone());
                    return networkResponse;
                });
            }).catch(() => {
                return caches.match(event.request);
            })
        );
        return;
    }

    // Untuk aset lainnya (JS, CSS, dll), gunakan Cache First / Stale-While-Revalidate
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            const fetchPromise = fetch(event.request).then((networkResponse) => {
                if (networkResponse.ok && event.request.url.startsWith('http')) {
                    const responseToCache = networkResponse.clone();
                    caches.open(DYNAMIC_CACHE).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return networkResponse;
            }).catch(() => {
                return cachedResponse;
            });

            return cachedResponse || fetchPromise;
        })
    );
});
