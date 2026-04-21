# Restaurant Mobile PWA Setup Guide

## Overview

Your Restaurant Mobile App is now configured as a Progressive Web App (PWA). This means it can work offline, be installed on devices like a native app, and provide an app-like experience.

## What Was Set Up

### 1. **Manifest File** (`public/site.webmanifest`)

- **App Name**: Restaurant Mobile App
- **Short Name**: Restaurant (for home screen)
- **Start URL**: `/mobile/login`
- **Display**: Standalone (full screen, like a native app)
- **Theme Color**: Blue (#1e40af)
- **Icons**: Multiple sizes (16x16, 32x32, 192x192, 512x512, 180x180)
- **Shortcuts**: Quick access to Dashboard and Quick Add

### 2. **Service Worker** (`public/sw.js`)

The service worker enables:

- **Offline Caching**: Caches the mobile pages and assets automatically
- **Network-First Strategy**: Tries to fetch from network first, falls back to cache
- **Smart Cache Management**: Auto-updates cache with new versions
- **Fallback Page**: Shows offline.html when network is unavailable
- **Cache Versioning**: Uses `restaurant-mobile-v1` for easy updates

**Cached Resources**:

- `/mobile/login`
- `/mobile/dashboard`
- `/mobile/quick-add`
- `/mobile/approvals`
- `/mobile/purchasing`
- `/mobile/purchase-order`
- `/mobile/receiving`
- `/mobile/request-detail`
- CSS, JS, and image assets

### 3. **Offline Page** (`public/offline.html`)

A beautiful offline fallback page that:

- Shows connection status
- Lists what users can/cannot do while offline
- Auto-checks for reconnection every 3 seconds
- Automatically redirects when connection is restored
- Provides links to cached pages

### 4. **PWA Meta Tags** (in `resources/views/layout/master.blade.php`)

Added PWA capabilities for:

- Web app manifest
- Theme color
- iOS app mode (apple-mobile-web-app-capable)
- iOS status bar styling
- iOS app title

### 5. **Service Worker Registration** (in `resources/views/layout/master.blade.php`)

JavaScript code that:

- Registers the service worker on app load
- Checks for updates every 60 seconds
- Notifies users when a new version is available
- Prevents iOS zoom on input focus

## Installation on Devices

### On Chrome (Android/Desktop)

1. Open `http://127.0.0.1:8000/mobile/login` in Chrome
2. You'll see an "Install" button in the address bar
3. Click "Install" to add to home screen
4. Open from home screen to use as a full-screen app

### On Safari (iOS)

1. Open `http://127.0.0.1:8000/mobile/login` in Safari
2. Tap the Share button
3. Tap "Add to Home Screen"
4. Name the app and tap "Add"
5. Open from home screen to use as a full-screen app

### On Edge (Windows)

1. Open `http://127.0.0.1:8000/mobile/login` in Edge
2. Click the "Install" button in the address bar
3. Confirm installation

## How Offline Works

### Automatic Caching

- When users visit a page, the service worker automatically caches it
- CSS, JavaScript, and images are also cached
- Cache is updated when users revisit pages

### Offline Access

- Users can access cached pages when offline
- Forms won't submit (network required)
- File uploads won't work (network required)
- Data displays will show cached information

### Back Online

- When connection returns, users are notified
- App can send queued requests to server
- Cache automatically updates with fresh data

## Testing the PWA

### Test Offline Mode

1. Open DevTools (F12 on Windows/Linux, Cmd+Option+I on Mac)
2. Go to **Application** or **Storage** tab
3. Select **Service Workers** and check "Offline"
4. Refresh the page - should still load from cache
5. Uncheck "Offline" and refresh

### Check Cached Files

1. Open DevTools
2. Go to **Application** → **Cache Storage**
3. Expand "restaurant-mobile-v1"
4. See all cached resources

### Test on Mobile

1. Use a local IP (not localhost)
2. Example: `http://192.168.x.x:8000/mobile/login`
3. Install the app on your device
4. Go offline (airplane mode)
5. App should still work with cached data

## Updating the App

### Automatic Updates

- Service worker checks for updates every 60 seconds
- When new version is detected, user gets a confirmation dialog
- User can choose to update immediately

### Manual Cache Invalidation

To clear the cache and force a refresh:

1. Change the cache name in `public/sw.js`:

```javascript
const CACHE_NAME = "restaurant-mobile-v2"; // Increment version
```

2. Existing service worker will detect the new version and replace the old cache

## Optimization Tips

### 1. Minimize Bundle Size

- Lazy load images
- Minify CSS/JS in production
- Use WebP format for images

### 2. Improve Offline Experience

- Pre-cache critical assets in service worker
- Use IndexedDB for complex offline data
- Implement request queuing for submissions

### 3. Performance

- Enable GZIP compression on server
- Use CDN for static assets
- Optimize database queries

## Routes Added

```php
// Offline fallback
GET /offline → public/offline.html

// Existing Mobile Routes
GET /mobile/login → mobile.login view
POST /mobile/login → AuthenticatedSessionController@store
GET /mobile/dashboard → MobileController@dashboard
GET /mobile/quick-add → MobileController@quickAdd
GET /mobile/request-detail → MobileController@requestDetail
GET /mobile/approvals → MobileController@approvals
GET /mobile/purchasing → MobileController@purchasing
GET /mobile/purchase-order → MobileController@purchaseOrder
GET /mobile/receiving → MobileController@receiving
```

## Files Modified/Created

### Created

- ✅ `public/sw.js` - Service Worker
- ✅ `public/offline.html` - Offline Page
- ✅ `routes/web.php` - Added /offline route

### Updated

- ✅ `public/site.webmanifest` - Complete PWA manifest
- ✅ `resources/views/layout/master.blade.php` - Added PWA meta tags and SW registration

## Browser Support

| Browser         | Support    | Features                             |
| --------------- | ---------- | ------------------------------------ |
| Chrome 40+      | ✅ Full    | Install, offline, push notifications |
| Firefox 44+     | ✅ Full    | Install, offline, push notifications |
| Safari 14+      | ✅ Partial | Offline, no install prompt           |
| Edge 79+        | ✅ Full    | Install, offline, push notifications |
| Samsung Browser | ✅ Full    | Install, offline                     |

## Security Considerations

1. **HTTPS Recommended**: PWAs work best with HTTPS (service workers require it in production)
2. **CSRF Protection**: Already enabled with Laravel's CSRF tokens
3. **Content Security Policy**: Consider adding CSP headers for additional security
4. **Manifest Integrity**: Manifest is signed by browser during installation

## Monitoring & Debugging

### Console Logs

The service worker logs:

- Registration success/failure
- Cache updates
- Network failures

Example:

```
ServiceWorker registration successful: ServiceWorkerRegistration {...}
```

### Check Service Worker Status

```javascript
// In browser console
navigator.serviceWorker.getRegistrations().then((registrations) => {
  console.log(registrations);
});
```

## Next Steps (Optional Enhancements)

1. **Push Notifications**: Add push notification support
2. **Offline Forms**: Queue form submissions while offline
3. **Background Sync**: Auto-sync data when connection returns
4. **Share Target**: Allow users to share content with your app
5. **File Handling**: Associate file types with your app

## Troubleshooting

### Service Worker Not Registering

- Check browser console for errors
- Ensure `sw.js` is accessible at `/sw.js`
- Verify service worker scope in manifest

### Cache Not Updating

- Increment `CACHE_NAME` in `sw.js`
- Clear browser cache manually
- Use DevTools to delete service worker

### Installation Button Not Showing

- Manifest must be valid JSON
- Site must have HTTPS (in production)
- Need standalone display mode
- App must meet PWA criteria

### Offline Page Not Loading

- Ensure `offline.html` exists in `public/`
- Check route is registered in `web.php`
- Verify service worker can fetch the file

## Support

For more information about PWAs:

- [MDN PWA Documentation](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [Web.dev PWA Guide](https://web.dev/progressive-web-apps/)
- [PWA Builder](https://www.pwabuilder.com)

---

**Installation Date**: April 21, 2026
**Version**: v1.0
