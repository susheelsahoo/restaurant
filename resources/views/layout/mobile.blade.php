@extends('layout.master')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/purchaseflow.css') }}">
@endpush

@section('content')
<main class="page-wrap">
    <div class="handoff">Laravel starter mobile screen with shared Blade layout and CSS.</div>
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
        @yield('mobile-footer')
    </div>
</main>
@endsection
