<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="">
    <title>@yield('title', config('app.name', 'Restaurant'))</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1e40af">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Restaurant">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('purchase-orders-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('purchase-orders.png') }}">

    {!! includeFavicon() !!}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="{{ asset('assets/css/purchaseflow.css') }}?v={{ filemtime(public_path('assets/css/purchaseflow.css')) }}">
    @stack('styles')
</head>
<body class="@yield('body-class', 'mobile-body')">
    @hasSection('mobile-standalone')
        @yield('mobile-content')
    @else
        <main class="mobile-shell">
            <div class="phone">
                <div class="statusbar"></div>
                <div class="screen">
                    @yield('mobile-content')
                </div>
                <div class="mobile-nav">
                    <a href="/mobile/dashboard" @if(request()->path() == 'mobile/dashboard') class="active" @endif>Home</a>
                    <a href="/mobile/request-detail" @if(request()->is('mobile/request-detail*') || request()->is('mobile/quick-add')) class="active" @endif>Requests</a>
                    <a href="/mobile/orders" @if(request()->is('mobile/orders') || request()->is('mobile/purchase-order*')) class="active" @endif>Orders</a>
                    <a href="/mobile/purchasing" @if(request()->path() == 'mobile/purchasing') class="active" @endif>Purchasing</a>
                </div>
            </div>
        </main>
    @endif

    @stack('scripts')
    <script>
        document.addEventListener('gesturestart', function (event) {
            event.preventDefault();
        });

        document.addEventListener('gesturechange', function (event) {
            event.preventDefault();
        });

        let lastMobileTapTime = 0;

        document.addEventListener('touchend', function (event) {
            const currentTapTime = Date.now();

            if (currentTapTime - lastMobileTapTime <= 300) {
                event.preventDefault();
            }

            lastMobileTapTime = currentTapTime;
        }, { passive: false });
    </script>
    <script>
        function toggleMobileProfileMenu(event) {
            event.stopPropagation();
            const wrap = event.currentTarget.closest('.profile-menu-wrap');
            const menu = wrap.querySelector('.mobile-profile-menu');
            const isOpening = menu.hidden;

            document.querySelectorAll('.mobile-profile-menu').forEach(function (openMenu) {
                openMenu.hidden = true;
                const trigger = openMenu.closest('.profile-menu-wrap').querySelector('.avatar-trigger');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            });

            menu.hidden = !isOpening;
            event.currentTarget.setAttribute('aria-expanded', isOpening ? 'true' : 'false');
        }

        document.addEventListener('click', function (event) {
            if (event.target.closest('.profile-menu-wrap')) {
                return;
            }

            document.querySelectorAll('.mobile-profile-menu').forEach(function (menu) {
                menu.hidden = true;
                const trigger = menu.closest('.profile-menu-wrap').querySelector('.avatar-trigger');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ asset("sw.js") }}?v={{ filemtime(public_path("sw.js")) }}', {
                    scope: '/mobile/',
                }).catch(function (error) {
                    console.log('ServiceWorker registration failed:', error);
                });
            });
        }
    </script>
</body>
</html>
