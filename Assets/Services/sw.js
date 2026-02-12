// sw.js - Optimized for Luxury Store Performance
const CACHE_NAME = 'luxury-store-v1.1.0';
const BASE = new URL('.', self.location).href;

const STATIC_ASSETS = [
  BASE,
  BASE + '../../Controller/Home.php',
  BASE + '../css/Home.css',
  BASE + '../js/Home.js',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
  'https://unpkg.com/swiper/swiper-bundle.min.css',
  'https://unpkg.com/aos@2.3.4/dist/aos.css',
  'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Cairo:wght@400;600;700;800&display=swap'
];

// Install Event: Cache Static Assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(STATIC_ASSETS);
    }).then(() => self.skipWaiting())
  );
});

// Activate Event: Clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(keys.map(k => k !== CACHE_NAME ? caches.delete(k) : null));
    }).then(() => self.clients.claim())
  );
});

// Fetch Event: Stale-While-Revalidate Strategy
self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // Strategy for Images: Cache First, then Network
  if (event.request.destination === 'image' || url.pathname.includes('../../Controller/image.php')) {
    event.respondWith(
      caches.match(event.request).then(cached => {
        if (cached) return cached;
        return fetch(event.request).then(response => {
          if (!response || response.status !== 200) return response;
          const clone = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
          return response;
        });
      })
    );
    return;
  }

  // Strategy for other assets: Stale-While-Revalidate
  event.respondWith(
    caches.match(event.request).then(cached => {
      const networkFetch = fetch(event.request).then(response => {
        if (response && response.status === 200) {
          const clone = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
        }
        return response;
      }).catch(() => cached);

      return cached || networkFetch;
    })
  );
});