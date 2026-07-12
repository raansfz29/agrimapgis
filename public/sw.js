const CACHE_NAME = 'agrimapgis-v7';
const STATIC_ASSETS = [
    '/',
    '/css/style.css',
    '/manifest.json',
    'https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch(err => console.log('Partial cache:', err));
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.filter((name) => name !== CACHE_NAME).map((name) => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // Abaikan request yang bukan GET (POST, PUT, dll tidak bisa di-cache)
    if (event.request.method !== 'GET') return;
    
    // Jangan cache sistem InfinityFree (seperti cPanel/FTP/phpMyAdmin) atau ekstensi chrome
    if (!event.request.url.startsWith('http')) return;

    const url = new URL(event.request.url);

    // Strategi 1: Network First untuk halaman HTML (Dashboard, Peta, dll)
    if (event.request.mode === 'navigate' || event.request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    // Simpan ke cache jika sukses
                    const clonedFetch = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clonedFetch));
                    return response;
                })
                .catch(() => {
                    // Jika offline, cari di cache. Jika tidak ada di cache, kembalikan halaman error offline sederhana
                    return caches.match(event.request).then((cachedResponse) => {
                        if (cachedResponse) return cachedResponse;
                        return new Response(
                            '<html><body style="font-family:sans-serif; text-align:center; padding:50px;"><h1>Anda Sedang Offline 📡</h1><p>Tidak ada koneksi internet dan halaman ini belum tersimpan di memori perangkat Anda. Silakan cari sinyal lalu muat ulang halaman.</p></body></html>',
                            { headers: { 'Content-Type': 'text/html' } }
                        );
                    });
                })
        );
        return;
    }

    // Strategi 2: Cache First untuk Tile Peta (Leaflet/Google) dan Aset Statis (CSS/JS)
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) return cachedResponse;
            
            return fetch(event.request).then((networkResponse) => {
                // Simpan tile peta dan aset statis ke cache agar bisa dibuka offline
                if (url.hostname.includes('google.com') || url.pathname.endsWith('.css') || url.pathname.endsWith('.js') || url.pathname.endsWith('.png')) {
                    const cloned = networkResponse.clone();
                    caches.open('agrimapgis-tiles-v1').then(cache => cache.put(event.request, cloned));
                }
                return networkResponse;
            }).catch(() => {
                // Jika gagal (karena offline) kembalikan response kosong agar map tidak crash
                return new Response('', { status: 408, statusText: 'Request timeout' });
            });
        })
    );
});
