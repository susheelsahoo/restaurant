<nav class="navbar navbar-expand-lg glass-navbar fixed-top">
    <div class="container">
        <a href="{{ url('/') }}" class="navbar-brand">
            <img src="{{ asset('frontend/images/logo_wt.svg') }}" alt="Tifliso Restaurant" />
        </a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
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
                        About
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
                    <a class="nav-link {{ request()->is('wines') ? 'active' : '' }}" href="{{ url('wine') }}">
                        Wines
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('gallery') ? 'active' : '' }}" href="{{ url('gallery') }}">
                        Gallery
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('blogs*') ? 'active' : '' }}" href="{{ url('blogs') }}" id="blogsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Blogs
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="blogsDropdown">
                        <li><a class="dropdown-item" href="{{ url('blogs') }}">Blogs</a></li>
                        @if(isset($blogCategories) && $blogCategories->count())
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        @foreach($blogCategories as $cat)
                        <li><a class="dropdown-item" href="{{ route('blog.category', $cat->slug) }}">{{ $cat->name }}</a></li>
                        @endforeach
                        @endif
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('contact-us') ? 'active' : '' }}" href="{{ url('contact-us') }}">
                        Contact Us
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>