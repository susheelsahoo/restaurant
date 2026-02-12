<x-default-layout>
    @section('title', 'Booking Details')

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.bookings.show', $booking) }}
    @endsection

    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0">
            <div class="card-title">
                <h3 class="fw-bold m-0">Booking Details</h3>
            </div>
        </div>

        <div class="card-body border-top p-9">

            {{-- Booking Code --}}
            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Booking Code</label>
                <div class="col-lg-8">
                    <span class="fw-bold">{{ $booking->booking_code }}</span>
                </div>
            </div>

            {{-- Customer Name --}}
            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Customer Name</label>
                <div class="col-lg-8">
                    <p class="mb-0">
                        {{ $booking->customer->first_name ?? $booking->customer_name }}
                        {{ $booking->customer->last_name ?? '' }}
                    </p>

                </div>
            </div>

            {{-- Email --}}
            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Email</label>
                <div class="col-lg-8">
                    <p class="mb-0">
                        {{ $booking->customer->email ?? $booking->email ?? 'N/A' }}
                    </p>

                </div>
            </div>

            {{-- Phone --}}
            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Phone</label>
                <div class="col-lg-8">
                    <p class="mb-0">{{ $booking->customer->phone ?? $booking->phone ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Visit Date --}}
            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Visit Date</label>
                <div class="col-lg-8">
                    <p class="mb-0">
                        {{ $booking->visit_date->format('d M Y') }}
                    </p>

                </div>
            </div>

            {{-- Visit Time --}}
            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Visit Time</label>
                <div class="col-lg-8">
                    <p class="mb-0">
                        {{ $booking->visit_time->format('g:i A') }}
                    </p>

                </div>
            </div>

            {{-- Guests --}}
            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Guests</label>
                <div class="col-lg-8">
                    <p class="mb-0">{{ $booking->guests }}</p>
                </div>
            </div>

            {{-- Status --}}
            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Status</label>
                <div class="col-lg-8">
                    @php
                    $statusColors = [
                    'pending' => 'warning',
                    'confirmed' => 'success',
                    'declined' => 'danger',
                    'complete' => 'primary',
                    ];
                    @endphp

                    <span class="badge bg-{{ $statusColors[$booking->status] ?? 'secondary' }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>

            {{-- Created At --}}
            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Created At</label>
                <div class="col-lg-8">
                    <p class="mb-0">
                        {{ $booking->created_at->format('d M Y, h:i A') }}
                    </p>
                </div>
            </div>

        </div>

        <div class="card-footer d-flex justify-content-end py-6 px-9">
            <a href="{{ route('admin.bookings.index', ['status' => request('status')]) }}"
                class="btn btn-light me-2">
                Back
            </a>

            <a href="{{ route('admin.bookings.edit', [ 'booking' => $booking->id, 'status' => request('status') ]) }}"
                class="btn btn-primary">
                Edit Booking
            </a>
        </div>
    </div>
</x-default-layout>