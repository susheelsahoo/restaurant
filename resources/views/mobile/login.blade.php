@extends('layout.mobile')

@section('mobile-content')
<div class="topbar mobile-top">
    <div class="icon-btn" onclick="window.history.back()">←</div>
    <div class="topbar-title"><h3>Login</h3></div>
    <div></div>
</div>

<div class="card">
    <h4>Welcome Back</h4>
    <p>Please sign in to access the mobile app.</p>
</div>

<form method="POST" action="/mobile/login">
    @csrf
    <div class="card">
        <div class="field">
            <label>Email</label>
            <input class="input" type="email" name="email" required>
        </div>
        <div class="field">
            <label>Password</label>
            <input class="input" type="password" name="password" required>
        </div>
        <button class="button primary full" type="submit">Sign In</button>
    </div>
</form>

<div class="card">
    <p class="text-center">Don't have an account? Contact your administrator.</p>
</div>
@endsection