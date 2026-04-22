const CACHE_NAME = 'restaurant-mobile-v1';
const urlsToCache = [
  '/',
  '/mobile/login',
  '/mobile/dashboard',
  '/mobile/quick-add',
  '/mobile/approvals',
  '/mobile/purchasing',
  '/mobile/purchase-order',
  '/mobile/receiving',
  '/mobile/request-detail',
  '/offline',
  '/site.webmanifest',
];

// Install event - cache resources
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(urlsToCache).catch((error) => {
        console.log('Cache addAll error:', error);
      });
    })
  );
  self.skipWaiting();
});

// Activate event - clean up old caches
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

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
  // Skip cross-origin requests
  if (!event.request.url.startsWith(self.location.origin)) {
    return;
  }

  // Handle GET requests
  if (event.request.method === 'GET') {
    event.respondWith(
      caches.match(event.request).then((response) => {
        // Return cached response if available
        if (response) {
          return response;
        }

        // Fetch from network
        return fetch(event.request)
          .then((response) => {
            // Don't cache non-successful responses
            if (!response || response.status !== 200 || response.type === 'error') {
              return response;
            }

            // Clone the response
            const responseToCache = response.clone();

            // Cache the response for HTML pages and assets
            if (
              event.request.url.includes('/mobile/') ||
              event.request.url.endsWith('.js') ||
              event.request.url.endsWith('.css') ||
              event.request.url.endsWith('.png') ||
              event.request.url.endsWith('.jpg') ||
              event.request.url.endsWith('.jpeg') ||
              event.request.url.endsWith('.svg')
            ) {
              caches.open(CACHE_NAME).then((cache) => {
                cache.put(event.request, responseToCache);
              });
            }

            return response;
          })
          .catch(() => {
            // Return offline page if network fails
            return caches.match('/offline') || new Response('Offline - Unable to load page');
          });
      })
    );
  } else {
    // For non-GET requests, try network first
    event.respondWith(
      fetch(event.request)
        .catch(() => {
          return new Response('Network request failed', { status: 408 });
        })
    );
  }
});

// Handle messages from clients
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
