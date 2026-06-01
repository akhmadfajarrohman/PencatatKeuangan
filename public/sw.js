const CACHE_NAME = 'dompet-harian-v1';
const ASSETS_TO_CACHE = [
  '/',
  '/manifest.json',
  '/pwa-192x192.png',
  '/pwa-512x512.png',
  '/favicon.ico',
  'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
  'https://cdn.tailwindcss.com',
  'https://cdn.jsdelivr.net/npm/chart.js'
];

// Install Service Worker and cache essential files
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('[Service Worker] Caching App Shell Assets');
        return cache.addAll(ASSETS_TO_CACHE);
      })
      .then(() => self.skipWaiting())
  );
});

// Activate Service Worker and clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) {
            console.log('[Service Worker] Clearing Old Cache', cache);
            return caches.delete(cache);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch events interception
self.addEventListener('fetch', event => {
  // Only handle GET requests and exclude dynamic server routes/auth/Vite HMR
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // Exclude Laravel authentication and state mutation endpoints
  if (
    url.pathname.startsWith('/login') || 
    url.pathname.startsWith('/register') || 
    url.pathname.startsWith('/logout') || 
    url.pathname.startsWith('/livewire') || 
    url.pathname.includes('/api/') || 
    url.hostname === 'localhost' && url.port === '5173' // Vite Dev HMR
  ) {
    return;
  }

  event.respondWith(
    caches.match(event.request).then(cachedResponse => {
      // Dynamic loading strategies
      if (cachedResponse) {
        // Cache-first for static resources (CSS, JS, Fonts, Images)
        if (
          event.request.destination === 'font' ||
          event.request.destination === 'style' ||
          event.request.destination === 'script' ||
          event.request.destination === 'image' ||
          event.request.url.includes('tailwindcss') ||
          event.request.url.includes('font-awesome') ||
          event.request.url.includes('jsdelivr') ||
          event.request.url.includes('gstatic')
        ) {
          // Fetch updated version in the background
          fetch(event.request).then(networkResponse => {
            if (networkResponse.status === 200) {
              caches.open(CACHE_NAME).then(cache => cache.put(event.request, networkResponse));
            }
          }).catch(() => {});
          
          return cachedResponse;
        }
      }

      // Network-first for documents/pages (with cache fallback)
      return fetch(event.request)
        .then(networkResponse => {
          // Cache successful dynamic pages
          if (networkResponse.status === 200 && event.request.method === 'GET') {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseClone));
          }
          return networkResponse;
        })
        .catch(() => {
          // Fallback to cache if offline
          if (cachedResponse) return cachedResponse;
          
          // Return the cached root '/' (homepage shell) as fallback for navigate actions
          if (event.request.mode === 'navigate') {
            return caches.match('/');
          }
        });
    })
  );
});
