@extends('layout.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/purchaseflow.css') }}">
@endpush

@section('content')
<main class="page-wrap">
    <div class="file-nav">
        <a href="/mobile/dashboard">Dashboard</a>
        <a href="/mobile/quick-add">Quick Add</a>
        <a href="/mobile/request-detail">Request Detail</a>
        <a href="/mobile/approvals">Approvals</a>
        <a href="/mobile/purchasing">Purchasing</a>
        <a href="/mobile/purchase-order">PO Detail</a>
        <a href="/mobile/receiving">Receiving</a>
        <form method="POST" action="/logout" style="display:inline;">@csrf<button class="linkish">Logout</button></form>
    </div>

    <div class="phone">
        <div class="statusbar"></div>
        <div class="screen">
            @yield('mobile-content')
        </div>
        @if(request()->path() != 'mobile/login')
        <div class="mobile-nav">
            <a href="/mobile/dashboard" @if(request()->path() == 'mobile/dashboard') class="active" @endif>Home</a>
            <a href="/mobile/request-detail" @if(request()->path() == 'mobile/request-detail') class="active" @endif>Requests</a>
            <a href="/mobile/quick-add" @if(request()->path() == 'mobile/quick-add') class="active" @endif>Templates</a>
            <a href="/mobile/purchasing" @if(request()->path() == 'mobile/purchasing') class="active" @endif>Purchasing</a>
        </div>
        @endif
    </div>
</main>
@endsection
