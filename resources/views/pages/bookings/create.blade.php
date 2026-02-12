<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0">
            <div class="card-title">
                <h3 class="fw-bold m-0">
                    {{ isset($booking) ? 'Edit' : 'Create' }} Booking
                </h3>
            </div>
        </div>

        <form method="POST" action="{{ isset($booking) ? route('admin.bookings.update', $booking): route('admin.bookings.store') }}" class="form">
            @csrf
            @if(isset($booking)) @method('PUT') @endif

            <div class="card-body border-top p-9">

                {{-- Customer Name --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold">Customer Name</label>
                    <div class="col-lg-8">
                        <input type="text"
                            name="customer_name"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('customer_name', $booking->customer->first_name ?? $booking->customer_name ?? '') }}"

                            required>
                    </div>
                </div>

                {{-- Email --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold">Email</label>
                    <div class="col-lg-8">
                        <input type="email"
                            name="email"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('email', $booking->customer->email ?? $booking->email ?? '') }}">
                    </div>
                </div>

                {{-- Phone --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold">Phone</label>
                    <div class="col-lg-8">
                        <input type="text"
                            name="phone"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('phone',$booking->customer->phone ?? $booking->phone ?? '') }}"
                            required>
                    </div>
                </div>

                {{-- Visit Date --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold">Visit Date</label>
                    <div class="col-lg-8">
                        <input type="date"
                            name="visit_date"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('visit_date', isset($booking) && $booking->visit_date ? $booking->visit_date->format('Y-m-d'): '') }}"
                            required>
                    </div>
                </div>

                {{-- Visit Time --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold">Visit Time</label>
                    <div class="col-lg-8">
                        <input type="time"
                            name="visit_time"
                            class="form-control form-control-lg form-control-solid"
                            value="{{ old('visit_time', isset($booking) ? $booking->visit_time : '') }}"
                            required>
                    </div>
                </div>

                {{-- Guests --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold">Guests</label>
                    <div class="col-lg-8">
                        <select name="guests"
                            class="form-select form-select-lg form-select-solid"
                            required>

                            @for ($i = 1; $i <= 25; $i++)
                                <option value="{{ $i }}"
                                {{ (int) old('guests', $booking->guests ?? 1) === $i ? 'selected' : '' }}>
                                {{ $i }} {{ $i === 1 ? 'Guest' : 'Guests' }}
                                </option>
                                @endfor

                                <option value="26"
                                    {{ (int) old('guests', $booking->guests ?? 1) >= 26 ? 'selected' : '' }}>
                                    25+ Guests
                                </option>

                        </select>
                    </div>
                </div>


                {{-- Status --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold">Status</label>
                    <div class="col-lg-8">
                        <select name="status"
                            class="form-select form-select-lg form-select-solid">
                            @foreach(config('app.statuses') as $status => $label)
                            <option value="{{ $status }}"
                                {{ old('status', $booking->status ?? 'new') === $status ? 'selected' : '' }}>
                                {{ ucfirst($label) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-light me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($booking) ? 'Update Booking' : 'Create Booking' }}
                </button>
            </div>
        </form>
    </div>
</x-default-layout>