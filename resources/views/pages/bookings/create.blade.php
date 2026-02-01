<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">{{ isset($booking) ? 'Edit' : 'Create' }} Booking</h3>
            </div>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($booking) ? route('admin.bookings.update', $booking->id) : route('admin.bookings.store') }}" class="form">
                @csrf
                @if(isset($booking)) @method('PUT') @endif

                <div class="card-body border-top p-9">
                    <!-- Validation Errors -->
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Name -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Name</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="name" class="form-control form-control-lg form-control-solid @error('name') is-invalid @enderror"
                                placeholder="Name" value="{{ old('name', $booking->name ?? '') }}" required />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Email</label>
                        <div class="col-lg-8 fv-row">
                            <input type="email" name="email" class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror"
                                placeholder="Email" value="{{ old('email', $booking->email ?? '') }}" required />
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Phone</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="phone" class="form-control form-control-lg form-control-solid @error('phone') is-invalid @enderror"
                                placeholder="Phone" value="{{ old('phone', $booking->phone ?? '') }}" />
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Source -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Source</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="source" class="form-control form-control-lg form-control-solid @error('source') is-invalid @enderror"
                                placeholder="Source" value="{{ old('source', $booking->source ?? '') }}" />
                            @error('source') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Hotel Size -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Hotel Size</label>
                        <div class="col-lg-8 fv-row">
                            <select name="hotel_size" class="form-control form-control-lg form-control-solid @error('hotel_size') is-invalid @enderror">
                                <option value="">Select Hotel Size</option>
                                <option value="small" {{ old('hotel_size', $booking->hotel_size ?? '') == 'small' ? 'selected' : '' }}>Small</option>
                                <option value="medium" {{ old('hotel_size', $booking->hotel_size ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="large" {{ old('hotel_size', $booking->hotel_size ?? '') == 'large' ? 'selected' : '' }}>Large</option>
                            </select>
                            @error('hotel_size') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Status</label>
                        <div class="col-lg-8 fv-row">
                            <select name="status" class="form-control form-control-lg form-control-solid @error('status') is-invalid @enderror">
                                <option value="new" {{ old('status', $booking->status ?? '') == 'new' ? 'selected' : '' }}>New</option>
                                <option value="in_progress" {{ old('status', $booking->status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="converted" {{ old('status', $booking->status ?? '') == 'converted' ? 'selected' : '' }}>Converted</option>
                                <option value="closed" {{ old('status', $booking->status ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Notes</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="notes" rows="4" class="form-control form-control-lg form-control-solid @error('notes') is-invalid @enderror"
                                placeholder="Enter additional notes">{{ old('notes', $booking->notes ?? '') }}</textarea>
                            @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-light btn-active-light-primary me-2">Discard</a>
                    <button type="submit" class="btn btn-primary">{{ isset($booking) ? 'Update' : 'Create' }} Booking</button>
                </div>
            </form>
        </div>
    </div>
</x-default-layout>