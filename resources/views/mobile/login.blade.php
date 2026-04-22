@extends('layout.mobile')

@section('title', 'Mobile Login')
@section('body-class', 'login-body')
@section('mobile-standalone', true)

@push('styles')
<link rel="stylesheet" href="{{ asset('mobile-login/style.css') }}">
@endpush

@section('mobile-content')
<div class="login-container">
    <header class="app-header">
        <button class="back-btn" type="button" aria-label="Go back" onclick="window.history.back()">
            <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M19 12H5M5 12L12 19M5 12L12 5"
                    stroke="#111827"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </button>
        <h1 class="screen-title">Login</h1>
        <div class="spacer"></div>
    </header>

    <main class="app-content">
        <section class="card welcome-card">
            <h2>Welcome Back</h2>
            <p>Please sign in to access the mobile app.</p>
        </section>

        @if ($errors->any())
            <div class="login-alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="card form-card">
            <form action="{{ url('/mobile/login') }}" method="POST" class="login-form">
                @csrf
                <div class="input-group">
                    <label for="email">EMAIL</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="input-field input-email"
                        value="{{ old('email') }}"
                        placeholder="Email Address"
                        autocomplete="username"
                        autofocus
                        required
                    >
                </div>

                <div class="input-group">
                    <label for="password">PASSWORD</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="input-field input-password"
                        placeholder="Password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="submit-btn">Sign In</button>
            </form>
        </section>

        <section class="card footer-card">
            <p>Don't have an account? Contact your administrator.</p>
        </section>
    </main>
</div>
@endsection
