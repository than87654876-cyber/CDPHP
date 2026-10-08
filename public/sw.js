// FOODDAILY Service Worker - PWA & Web Push Support
const CACHE_NAME = 'fooddaily-pwa-v1';
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/logo.png',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/apple-touch-icon.png'
];

// Install Event - Pre-cache core assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.warn('[SW] Cache addAll warning:', err);
            });
        })
    );
    self.skipWaiting();
});

// Activate Event - Clean old caches & claim clients
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

// Fetch Event - Network first with Cache fallback for navigation & assets
self.addEventListener('fetch', (event) => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    // Ignore admin and api live requests from caching
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/api/')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                // If response is valid, optionally cache static images/css
                if (
                    networkResponse &&
                    networkResponse.status === 200 &&
                    (url.pathname.startsWith('/icons/') || url.pathname.endsWith('.png') || url.pathname.endsWith('.jpg'))
                ) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                // Network failed: fallback to cache
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // If it's a page navigation request, return offline message
                    if (event.request.mode === 'navigate') {
                        return new Response(
                            `<!DOCTYPE html>
                            <html lang="vi">
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>Mất kết nối - FOODDAILY</title>
                                <style>
                                    body { font-family: sans-serif; text-align: center; padding: 40px 20px; background: #fff5f5; color: #333; }
                                    .card { max-width: 400px; margin: 40px auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
                                    h2 { color: #ee4d2d; margin-bottom: 10px; }
                                    p { color: #666; font-size: 14px; line-height: 1.6; }
                                    button { background: #ee4d2d; color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: bold; cursor: pointer; margin-top: 20px; }
                                </style>
                            </head>
                            <body>
                                <div class="card">
                                    <div style="font-size: 48px; margin-bottom: 16px;">📶</div>
                                    <h2>Không có kết nối mạng</h2>
                                    <p>Vui lòng kiểm tra lại kết nối Wi-Fi hoặc 4G của bạn để tiếp tục đặt món cùng FOODDAILY nhé!</p>
                                    <button onclick="window.location.reload()">Thử lại</button>
                                </div>
                            </body>
                            </html>`,
                            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
                        );
                    }
                });
            })
    );
});

// Push Notification Event (Hỗ trợ Web Push)
self.addEventListener('push', (event) => {
    let data = {
        title: 'FOODDAILY - Nhắc bạn giờ ăn!',
        body: 'Hàng trăm món ngon đang chờ bạn. Đặt ngay nhận ưu đãi!',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-192x192.png',
        data: { url: '/trangchu' }
    };

    if (event.data) {
        try {
            data = Object.assign(data, event.data.json());
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/icons/icon-192x192.png',
        badge: data.badge || '/icons/icon-192x192.png',
        vibrate: [200, 100, 200],
        data: data.data || { url: '/' },
        actions: [
            { action: 'open', title: 'Đặt món ngay' },
            { action: 'close', title: 'Để sau' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification Click Event
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    const targetUrl = (event.notification.data && event.notification.data.url) ? event.notification.data.url : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            for (let client of windowClients) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
