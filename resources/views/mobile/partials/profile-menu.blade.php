<div class="profile-menu-wrap">
    <button class="avatar avatar-trigger" type="button" aria-label="Open profile menu" aria-expanded="false" onclick="toggleMobileProfileMenu(event)">
        <span>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
    </button>
    <div class="mobile-profile-menu" hidden>
        <div class="mobile-profile-head">
            <div class="mobile-profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ auth()->user()->email }}</span>
            </div>
        </div>
        <a href="{{ route('mobile.profile') }}" class="mobile-profile-item">Profile</a>
        <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button class="mobile-profile-item danger" type="submit">Logout</button>
        </form>
    </div>
</div>
