const CACHE_VERSION = '2026-08-19-1';
const CACHE_NAME = `restaurant-mobile-v${CACHE_VERSION}`;

// Only cache essential static files
const STATIC_CACHE = [
  '/offline',
  '/site.webmanifest',
];

// =======================
// INSTALL
// =======================
self.addEventListener('install', (event) => {
  self.skipWaiting();

  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return Promise.all(
        STATIC_CACHE.map((url) =>
          cache.add(url).catch(() => {
            console.warn('Failed to cache:', url);
          })
        )
      );
    })
  );
});

// =======================
// ACTIVATE
// =======================
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) =>
      Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      )
    )
  );

  self.clients.claim();
});

// =======================
// FETCH
// =======================
self.addEventListener('fetch', (event) => {
  const request = event.request;

  // Skip non-origin requests
  if (!request.url.startsWith(self.location.origin)) return;

  // ==========================
  // 1. HTML → NETWORK FIRST ✅
  // ==========================
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          return response;
        })
        .catch(() => {
          return caches.match('/offline');
        })
    );
    return;
  }

  // ==========================
  // 2. STATIC FILES → CACHE FIRST ✅
  // ==========================
  if (
    request.url.endsWith('.js') ||
    request.url.endsWith('.css') ||
    request.url.endsWith('.png') ||
    request.url.endsWith('.jpg') ||
    request.url.endsWith('.jpeg') ||
    request.url.endsWith('.svg') ||
    request.url.endsWith('.webp') ||
    request.url.endsWith('.ico')
  ) {
    event.respondWith(
      caches.match(request).then((cachedResponse) => {
        if (cachedResponse) return cachedResponse;

        return fetch(request).then((networkResponse) => {
          // Cache valid response
          if (networkResponse && networkResponse.status === 200) {
            const cloned = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(request, cloned);
            });
          }
          return networkResponse;
        });
      })
    );
    return;
  }

  // ==========================
  // 3. API / OTHER → NETWORK FIRST ✅
  // ==========================
  event.respondWith(
    fetch(request)
      .then((response) => response)
      .catch(() => {
        return new Response('Network error', { status: 408 });
      })
  );
});

// =======================
// FORCE UPDATE (optional)
// =======================
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});
