@extends('layout.master')

@section('content')
<style>
    .tifliso-auth-shell {
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(212, 175, 55, 0.14), transparent 30%),
            radial-gradient(circle at bottom right, rgba(140, 98, 57, 0.16), transparent 28%),
            linear-gradient(135deg, #050505 0%, #0c0c0c 45%, #15110c 100%);
    }

    .tifliso-auth-panel {
        position: relative;
        background: rgba(9, 9, 9, 0.92);
        color: #f5f1e8;
    }

    .tifliso-auth-panel::before {
        content: "";
        position: absolute;
        inset: 24px 24px auto auto;
        width: 160px;
        height: 160px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(212, 175, 55, 0.16), transparent 70%);
        pointer-events: none;
    }

    .tifliso-auth-card {
        position: relative;
        width: min(100%, 520px);
        padding: 48px 42px;
        border-radius: 28px;
        border: 1px solid rgba(212, 175, 55, 0.18);
        background: linear-gradient(180deg, rgba(26, 24, 20, 0.94), rgba(12, 12, 12, 0.96));
        box-shadow: 0 28px 80px rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(18px);
    }

    .tifliso-auth-footer a {
        color: rgba(245, 241, 232, 0.66);
        text-decoration: none;
    }

    .tifliso-auth-footer a:hover {
        color: #d4af37;
    }

    .tifliso-auth-aside {
        position: relative;
        overflow: hidden;
        background:
            linear-gradient(180deg, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.72)),
            url('https://staging.tifliszo.hu/storage/gallery_images/UvUGEqI1GG7cFjqKOsRE12MsS2uDXxr27uBawkDY.png') center/cover no-repeat;
    }

    .tifliso-auth-aside::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at top right, rgba(212, 175, 55, 0.18), transparent 22%),
            linear-gradient(140deg, rgba(0, 0, 0, 0.48) 0%, rgba(0, 0, 0, 0.82) 48%, rgba(0, 0, 0, 0.9) 100%);
    }

    .tifliso-auth-aside-content {
        position: relative;
        z-index: 1;
        max-width: 640px;
        color: #fffaf0;
    }

    .tifliso-auth-brand {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 32px;
        color: #fff;
        text-decoration: none;
    }

    .tifliso-auth-brand img {
        height: 68px;
        width: auto;
    }

    .tifliso-auth-kicker {
        display: inline-flex;
        padding: 8px 16px;
        border-radius: 999px;
        border: 1px solid rgba(212, 175, 55, 0.28);
        background: rgba(255, 255, 255, 0.08);
        color: #f3d27a;
        font-size: 12px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
    }

    .tifliso-auth-title {
        margin: 24px 0 16px;
        font-size: clamp(2.5rem, 4vw, 4.8rem);
        line-height: 1.04;
        font-weight: 800;
        letter-spacing: -0.04em;
        color: #fffdf7;
        text-shadow: 0 8px 30px rgba(0, 0, 0, 0.55);
    }

    .tifliso-auth-copy {
        max-width: 520px;
        color: rgba(255, 248, 235, 0.92);
        font-size: 1.05rem;
        line-height: 1.8;
        text-shadow: 0 4px 18px rgba(0, 0, 0, 0.45);
    }

    .tifliso-auth-feature-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
        margin-top: 40px;
    }

    .tifliso-auth-feature {
        padding: 18px 20px;
        border-radius: 22px;
        border: 1px solid rgba(255, 255, 255, 0.09);
        background: rgba(16, 16, 16, 0.45);
        backdrop-filter: blur(8px);
    }

    .tifliso-auth-feature strong {
        display: block;
        color: #f3d27a;
        font-size: 1.2rem;
        margin-bottom: 8px;
    }

    .tifliso-auth-feature span {
        color: rgba(255, 248, 235, 0.75);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    @media (max-width: 991.98px) {
        .tifliso-auth-card {
            padding: 36px 24px;
            border-radius: 24px;
        }

        .tifliso-auth-feature-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!--begin::App-->
<div class="d-flex flex-column flex-root app-root tifliso-auth-shell" id="kt_app_root">
    <!--begin::Wrapper-->
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
        <!--begin::Body-->
        <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-6 p-lg-10 order-2 order-lg-2 tifliso-auth-panel">
            <!--begin::Form-->
            <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                <!--begin::Wrapper-->
                <div class="tifliso-auth-card">
                    <!--begin::Page-->
                    {{ $slot }}
                    <!--end::Page-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Form-->

            <!--begin::Footer-->
            <div class="d-flex flex-center flex-wrap px-5 pt-8 tifliso-auth-footer">
                <!--begin::Links-->
                <div class="d-flex fw-semibold fs-base">
                    <a href="{{ route('home') }}" class="px-5">Home</a>

                    <a href="{{ route('menu.index') }}" class="px-5">Menu</a>

                    <a href="{{ route('contact.store') }}" class="px-5" onclick="event.preventDefault();">Contact Us</a>
                </div>
                <!--end::Links-->
            </div>
            <!--end::Footer-->
        </div>
        <!--end::Body-->

        <!--begin::Aside-->
        <div class="d-flex flex-lg-row-fluid w-lg-50 order-1 order-lg-1 tifliso-auth-aside">
            <!--begin::Content-->
            <div class="d-flex flex-column justify-content-center py-10 py-lg-15 px-6 px-md-12 px-xl-15 w-100">
                <!--begin::Logo-->
                <div class="tifliso-auth-aside-content">
                <a href="{{ route('home') }}" class="tifliso-auth-brand">
                    <img alt="Tifliso Restaurant" src="https://staging.tifliszo.hu/storage/gallery_images/6OXzNuTejRtOTQU3Au2uVkRx765oZYNaKrjottdj.svg" />
                </a>
                <!--end::Logo-->

                <span class="tifliso-auth-kicker">Admin Access</span>

                <!--begin::Title-->
                <h1 class="tifliso-auth-title">
                    Welcome back to the Tifliso control room.
                </h1>
                <!--end::Title-->

                <!--begin::Text-->
                <div class="tifliso-auth-copy">
                    Manage reservations, customer communication, and restaurant operations from a login experience that matches your front-end brand: warm, refined, and confidently premium.
                </div>
                <!--end::Text-->

                <div class="tifliso-auth-feature-grid">
                    <div class="tifliso-auth-feature">
                        <strong>Reservations</strong>
                        <span>Keep table bookings and guest flow organized with less friction.</span>
                    </div>
                    <div class="tifliso-auth-feature">
                        <strong>Customers</strong>
                        <span>Access notes, preferences, and promotional targeting from one place.</span>
                    </div>
                    <div class="tifliso-auth-feature">
                        <strong>Brand</strong>
                        <span>Carry the same black-and-gold restaurant identity into admin access.</span>
                    </div>
                </div>
                </div>
            </div>
            <!--end::Content-->
        </div>
        <!--end::Aside-->
    </div>
    <!--end::Wrapper-->
</div>
<!--end::App-->

@endsection
