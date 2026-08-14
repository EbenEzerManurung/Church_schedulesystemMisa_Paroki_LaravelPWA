// sw.js - VERSI DIPERBAIKI
const CACHE_NAME = 'jadwalgereja-v2';

// HANYA cache file statis, BUKAN route Laravel
const urlsToCache = [
    '/manifest.json',
    '/css/app.css',
    '/js/app.js',
    '/favicon.ico'
    // HAPUS '/', '/dashboard', '/login' - INI PENYEBAB ERROR!
];

// Install - Cache file statis
self.addEventListener('install', event => {
    console.log('[SW] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return Promise.allSettled(
                    urlsToCache.map(url => 
                        cache.add(url).catch(err => console.warn(`Failed: ${url}`, err))
                    )
                );
            })
            .then(() => self.skipWaiting())
    );
});

// Activate - Bersihkan cache lama
self.addEventListener('activate', event => {
    console.log('[SW] Activating...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if (cache !== CACHE_NAME) {
                        console.log('[SW] Deleting:', cache);
                        return caches.delete(cache);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch - AMAN untuk redirect
self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);
    
    // PERBAIKAN 1: Hanya proses request GET
    if (request.method !== 'GET') {
        return;
    }
    
    // PERBAIKAN 2: JANGAN proses request ke route Laravel
    // Biarkan browser handle navigasi normal
    if (request.mode === 'navigate' || request.destination === 'document') {
        return;
    }
    
    // PERBAIKAN 3: Jangan proses request yang mengandung redirect manual
    if (request.redirect === 'manual') {
        return;
    }
    
    // PERBAIKAN 4: Hanya cache file statis (css, js, json, images)
    const staticExtensions = ['.css', '.js', '.json', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2'];
    const isStaticFile = staticExtensions.some(ext => url.pathname.endsWith(ext));
    
    if (!isStaticFile && url.pathname !== '/manifest.json') {
        return;
    }
    
    // Handle fetch untuk file statis
    event.respondWith(
        caches.match(request)
            .then(cachedResponse => {
                if (cachedResponse) {
                    // Update cache di background
                    fetch(request)
                        .then(networkResponse => {
                            if (networkResponse && networkResponse.status === 200) {
                                caches.open(CACHE_NAME).then(cache => {
                                    cache.put(request, networkResponse);
                                });
                            }
                        })
                        .catch(() => {});
                    return cachedResponse;
                }
                
                return fetch(request)
                    .then(networkResponse => {
                        if (networkResponse && networkResponse.status === 200) {
                            const responseToCache = networkResponse.clone();
                            caches.open(CACHE_NAME).then(cache => {
                                cache.put(request, responseToCache);
                            });
                        }
                        return networkResponse;
                    })
                    .catch(error => {
                        console.warn('[SW] Fetch failed:', url.pathname);
                        if (url.pathname === '/offline') {
                            return new Response('Offline', { status: 503 });
                        }
                        return caches.match('/offline');
                    });
            })
    );
});