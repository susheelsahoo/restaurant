<x-default-layout>
    @section('title')
    Dashboard
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('dashboard') }}
    @endsection

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        {{-- New Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=pending') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:{{ $colorMap[$pendingStatus->color] ?? '#5b0ea8' }};">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $new_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">New Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Confirmed Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=confirmed&select_date=' . now()->toDateString()) }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:{{ $colorMap[$confirmedStatus->color] ?? '#17a2b8' }};">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $confirmed_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Confirmed Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- InHouse Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=in-house&select_date=' . now()->toDateString()) }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:{{ $colorMap[$inHouseStatus->color] ?? '#17a2b8' }};">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $in_house_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">In-House Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Complete Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=complete&select_date=' . now()->toDateString()) }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:{{ $colorMap[$completeStatus->color] ?? '#17a2b8' }};">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $complete_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Complete Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Total Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#F1416C;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $total_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Total Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Declined Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=declined') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:{{ $colorMap[$declinedStatus->color] ?? '#ffc107' }};">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $declined_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Declined Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>



        {{-- Total Contacts --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/contacts') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:rgb(18,78,3);">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $total_contact }}</span>
                            <span class="text-white opacity-75 fs-6">Total Contacts</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- New Contacts --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/contacts?filter=new') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#033265;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $new_contact }}</span>
                            <span class="text-white opacity-75 fs-6">New Contacts</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Read Contacts --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/contacts?filter=read') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#6c757d;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $read_contact }}</span>
                            <span class="text-white opacity-75 fs-6">Read Contacts</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>

</x-default-layout>