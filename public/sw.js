const CACHE_NAME = 'schoolbag-pwa-v2';
const DYNAMIC_CACHE_NAME = 'schoolbag-dynamic-v2';

const STATIC_ASSETS = [
    '/offline.html',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME && key !== DYNAMIC_CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - handle caching strategies
self.addEventListener('fetch', event => {
    const req = event.request;
    const url = new URL(req.url);

    // Don't cache non-GET requests (POST, PUT, DELETE)
    if (req.method !== 'GET') {
        return;
    }

    // Ignore cross-origin requests, except for known CDNs if needed, but best to stick to same-origin for simplicity
    // If it's a static asset (CSS, JS, Fonts, Images), use Stale-While-Revalidate or Cache-First
    const isStaticAsset = req.destination === 'style' || req.destination === 'script' || req.destination === 'image' || req.destination === 'font';

    if (isStaticAsset) {
        event.respondWith(
            caches.match(req).then(cachedRes => {
                const fetchPromise = fetch(req).then(networkRes => {
                    if (networkRes.ok) {
                        const resClone = networkRes.clone();
                        caches.open(DYNAMIC_CACHE_NAME).then(cache => {
                            cache.put(req, resClone);
                        });
                    }
                    return networkRes;
                }).catch((err) => {
                    // Ignore fetch errors for static assets if we have cache, otherwise let it fail
                    throw err;
                });
                
                return cachedRes || fetchPromise;
            })
        );
        return;
    }

    // For HTML (Pages) and other requests, use Network First, fallback to offline.html
    event.respondWith(
        fetch(req).then(networkRes => {
            // Check if response is an error (e.g., 500)
            const acceptHeader = req.headers.get('accept');
            const isHtml = acceptHeader && acceptHeader.includes('text/html');
            
            if (!networkRes.ok && isHtml) {
                return caches.match('/offline.html');
            }
            return networkRes;
        }).catch(err => {
            // Network failure (offline)
            const acceptHeader = req.headers.get('accept');
            const isHtml = acceptHeader && acceptHeader.includes('text/html');
            
            if (isHtml) {
                return caches.match('/offline.html');
            }
            // For other failing requests, returning nothing or a generic 503 is standard
            return new Response('', { status: 503, statusText: 'Service Unavailable' });
        })
    );
});

// Push notification listener placeholder
self.addEventListener('push', function(event) {
    if (event.data) {
        const payload = event.data.json();
        const options = {
            body: payload.body || 'You have a new notification.',
            icon: '/icons/icon-192x192.png',
            badge: '/icons/icon-192x192.png'
        };
        event.waitUntil(
            self.registration.showNotification(payload.title || 'Notification', options)
        );
    }
});
