const CACHE_NAME = 'resto-coto-pos-cache-v1';
const DYNAMIC_CACHE = 'resto-coto-pos-dynamic-v1';

// Aset yang akan di-cache secara default saat instalasi
const ASSETS_TO_CACHE = [
    '/pos', // Halaman utama POS
    '/manifest.json',
    '/favicon.ico',
    // Idealnya, di Laravel Vite, kita perlu meng-cache aset build Vite.
    // Tapi karena nama filenya dinamis, kita akan rely on dynamic caching.
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
    // Kita hanya intercept request GET
    if (event.request.method !== 'GET') return;

    // Abaikan rute API
    if (event.request.url.includes('/api/')) return;

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            // Jika ada di cache, gunakan itu (dengan Stale-While-Revalidate untuk resource dinamis)
            const fetchPromise = fetch(event.request).then((networkResponse) => {
                // Update cache secara dinamis
                caches.open(DYNAMIC_CACHE).then((cache) => {
                    // Jangan cache jika request gagal atau jika itu adalah halaman login / dll
                    if (networkResponse.ok && event.request.url.startsWith('http')) {
                        cache.put(event.request, networkResponse.clone());
                    }
                });
                return networkResponse;
            }).catch(() => {
                // Jika offline dan network gagal
                return cachedResponse;
            });

            // Kembalikan dari cache langsung jika ada, jika tidak, tunggu jaringan
            return cachedResponse || fetchPromise;
        })
    );
});
