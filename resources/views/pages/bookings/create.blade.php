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
                {{-- Preserve filter parameters --}}
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="select_date" value="{{ request('select_date') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                {{-- Customer Name --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold" for="customer_name">Customer Name</label>
                    <div class="col-lg-8">
                        <input type="text"
                            name="customer_name"
                            id="customer_name"
                            class="form-control form-control-lg form-control-solid @error('customer_name') is-invalid @enderror"
                            value="{{ old('customer_name', trim(($booking?->customer?->first_name ?? $booking?->customer_name ?? '') . ' ' . ($booking?->customer?->last_name ?? ''))) }}"
                            required>
                        @error('customer_name') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold">Email</label>
                    <div class="col-lg-8">
                        <input type="email"
                            name="email"
                            class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror"
                            value="{{ old('email', $booking?->customer?->email ?? '') }}">
                        @error('email') <div class="text-danger text-small">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Phone --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold">Phone</label>
                    <div class="col-lg-8">
                        <input type="text"
                            name="phone"
                            class="form-control form-control-lg form-control-solid @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $booking?->customer?->phone ?? '') }}"
                            required>
                        @error('phone') <div class="text-danger text-small">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Visit Date --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold">Visit Date</label>
                    <div class="col-lg-8">
                        <input type="date"
                            name="visit_date"
                            class="form-control form-control-lg form-control-solid @error('visit_date') is-invalid @enderror"
                            value="{{ old('visit_date', $booking?->visit_date?->format('Y-m-d') ?? '') }}"
                            required>
                        @error('visit_date') <div class="text-danger text-small">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Visit Time --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold">Visit Time</label>
                    <div class="col-lg-8">
                        <input type="time"
                            name="visit_time"
                            class="form-control form-control-lg form-control-solid @error('visit_time') is-invalid @enderror"
                            value="{{ old('visit_time', $booking?->visit_time?->format('H:i') ?? '') }}"
                            required>
                        @error('visit_time') <div class="text-danger text-small">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Guests --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-semibold">Guests</label>
                    <div class="col-lg-8">
                        <select name="guests"
                            class="form-select form-select-lg form-select-solid @error('guests') is-invalid @enderror"
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
                        @error('guests') <div class="text-danger text-small">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Notes --}}
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold">Notes</label>
                    <div class="col-lg-8">
                        <textarea name="notes"
                            class="form-control form-control-lg form-control-solid @error('notes') is-invalid @enderror"
                            rows="3">{{ old('notes', $booking?->notes ?? '') }}</textarea>
                        @error('notes') <div class="text-danger text-small">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Status (only for edit) --}}
                @isset($booking)
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold">Status</label>
                    <div class="col-lg-8">
                        <select name="status"
                            class="form-select form-select-lg form-select-solid @error('status') is-invalid @enderror">
                            @php
                            $statuses = \App\Models\ReservationStatus::active()->ordered()->get();
                            $selectedStatus = old('status', $booking->reservationStatus?->name ?? 'pending');
                            @endphp
                            @foreach($statuses as $status)
                            <option value="{{ $status->name }}"
                                {{ $selectedStatus === $status->name ? 'selected' : '' }}>
                                {{ $status->label }}
                            </option>
                            @endforeach
                        </select>
                        @error('status') <div class="text-danger text-small">{{ $message }}</div> @enderror
                    </div>
                </div>
                @endisset

            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('admin.bookings.index', request()->query()) }}" class="btn btn-light me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($booking) ? 'Update Booking' : 'Create Booking' }}
                </button>
            </div>
        </form>
    </div>
</x-default-layout>
