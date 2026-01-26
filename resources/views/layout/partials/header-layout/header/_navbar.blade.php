<div class="app-navbar flex-shrink-0">

    <div class="app-navbar-item align-items-stretch ms-1 ms-md-3">
        @include(config('settings.KT_THEME_LAYOUT_DIR').'/partials/header-layout/search/_dropdown')
    </div>


    <div class="app-navbar-item ms-1 ms-md-3">

        <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" id="kt_activities_toggle">{!! getIcon('chart-simple', 'fs-2 fs-md-1') !!}</div>

    </div>


    <div class="app-navbar-item ms-1 ms-md-3">

        <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom-end" id="kt_menu_item_wow">{!! getIcon('element-plus', 'fs-2 fs-md-1') !!}</div>
        @include('partials/menus/_notifications-menu')

    </div>


    <div class="app-navbar-item ms-1 ms-md-3">

        <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px position-relative" id="kt_drawer_chat_toggle">{!! getIcon('message-text-2', 'fs-2 fs-md-1') !!}
            <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
        </div>

    </div>


    <div class="app-navbar-item ms-1 ms-md-3">

        <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom-end">{!! getIcon('element-11', 'fs-2 fs-md-1') !!}</div>
        @include('partials/menus/_my-apps-menu')

    </div>


    <div class="app-navbar-item ms-1 ms-md-3">
        @include('partials/theme-mode/_init')
    </div>


    <div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">

        <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            @if(Auth::user()->profile_photo_url)
            <img src="{{ \Auth::user()->profile_photo_url }}" alt="user" />
            @else
            <div class="symbol-label fs-3 {{ app(\App\Actions\GetThemeType::class)->handle('bg-light-? text-?', Auth::user()->name) }}">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            @endif
        </div>
        @include('partials/menus/_user-account-menu')

    </div>


    <div class="app-navbar-item d-lg-none ms-2 me-n3" title="Show header menu">
        <div class="btn btn-icon btn-active-color-primary w-30px h-30px w-md-35px h-md-35px" id="kt_app_header_menu_toggle">{!! getIcon('text-align-left', 'fs-2 fs-md-1') !!}</div>
    </div>

</div>