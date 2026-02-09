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
                    style="padding-bottom:100px;background-color:rgb(91,14,168);">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $new_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">New Booking</span>
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
        {{-- Cancelled Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=cancelled') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#ffc107;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $cancelled_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Cancelled Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Confirmed Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=confirmed') }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#17a2b8;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $confirmed_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Confirmed Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Todays pending Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=pending&visit_date=' . \Carbon\Carbon::today()->format('Y-m-d')) }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#007bff;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $today_pending_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Today's Pending Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Todays upcoming Booking --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=cancelled&visit_date=' . \Carbon\Carbon::today()->format('Y-m-d')) }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#ffc107;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $today_cancelled_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Today's Cancelled Booking</span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Todays Complete bookings --}}
        <div class="col-md-3">
            <a href="{{ url('/admin/bookings?status=complete&visit_date=' . \Carbon\Carbon::today()->format('Y-m-d')) }}" class="text-decoration-none">
                <div class="card card-flush h-md-50 mb-5"
                    style="padding-bottom:100px;background-color:#28a745;">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white">{{ $today_complete_bookings }}</span>
                            <span class="text-white opacity-75 fs-6">Today's Complete Booking</span>
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