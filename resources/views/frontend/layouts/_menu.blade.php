@php
$isHome = request()->is('/') || request()->is('home');
@endphp

<nav class="navbar navbar-expand-lg glass-navbar {{ $isHome ? 'fixed-top' : '' }}">

    <div class="container">
        <a href="{{ url('/') }}" class="navbar-brand">
            <img src="{{ asset('frontend/images/logo_wt.svg') }}" alt="Tifliso Restaurant" />
        </a>


        <button class="navbar-toggler d-lg-none"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNav"
            aria-controls="mainNav"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <i class="fa fa-navicon" style="color:#fff; font-size:28px;"></i>

        </button>


        <div class="collapse navbar-collapse justify-content-end" id="mainNav">
            <ul class="navbar-nav nav-pill-bg">

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="{{ url('about') }}">
                        About Us
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('menu') ? 'active' : '' }}" href="{{ url('menu') }}">
                        Menu
                    </a>
                </li>

                <!--<li class="nav-item">
                    <a class="nav-link {{ request()->is('kitchen') ? 'active' : '' }}" href="{{ url('kitchen') }}">
                        Kitchen
                    </a>
                </li>-->

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('wines') ? 'active' : '' }}" href="{{ url('wines') }}">
                        Drinks
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('gallery') ? 'active' : '' }}" href="{{ url('gallery') }}">
                        Gallery
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('contact-us') ? 'active' : '' }}" href="{{ url('contact-us') }}">
                        Contact Us
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('book-a-table') ? 'active' : '' }}" href="/home#reservation">
                        Book a Table
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('blogs') ? 'active' : '' }}" href="{{ url('blogs') }}">
                        Our Blogs
                    </a>
                </li>


            </ul>
        </div>
    </div>
</nav>