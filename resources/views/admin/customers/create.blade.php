<x-default-layout>

    <div class="card mb-5 mb-xl-10">

        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">
                    {{ isset($customer) ? 'Edit' : 'Create' }} Customer
                </h3>
            </div>
        </div>

        <div class="collapse show">

            <form method="POST"
                action="{{ isset($customer) ? route('admin.customers.update', $customer->id) : route('admin.customers.store') }}"
                class="form">

                @csrf
                @if(isset($customer)) @method('PUT') @endif


                <div class="card-body border-top p-9">

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif


                    {{-- First Name --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">
                            First Name
                        </label>

                        <div class="col-lg-8 fv-row">
                            <input type="text"
                                name="first_name"
                                class="form-control form-control-lg form-control-solid @error('first_name') is-invalid @enderror"
                                placeholder="First Name"
                                value="{{ old('first_name', $customer->first_name ?? '') }}"
                                required />

                            @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    {{-- Last Name --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            Last Name
                        </label>

                        <div class="col-lg-8 fv-row">
                            <input type="text"
                                name="last_name"
                                class="form-control form-control-lg form-control-solid"
                                placeholder="Last Name"
                                value="{{ old('last_name', $customer->last_name ?? '') }}" />
                        </div>
                    </div>


                    {{-- Email --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">
                            Email
                        </label>

                        <div class="col-lg-8 fv-row">
                            <input type="email"
                                name="email"
                                class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror"
                                placeholder="Email"
                                value="{{ old('email', $customer->email ?? '') }}"
                                required />

                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    {{-- Phone --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            Phone
                        </label>

                        <div class="col-lg-8 fv-row">
                            <input type="text"
                                name="phone"
                                class="form-control form-control-lg form-control-solid"
                                placeholder="Phone"
                                value="{{ old('phone', $customer->phone ?? '') }}" />
                        </div>
                    </div>


                    {{-- DOB --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            Date of Birth
                        </label>

                        <div class="col-lg-8 fv-row">
                            <input type="date"
                                name="date_of_birth"
                                class="form-control form-control-lg form-control-solid"
                                value="{{ old('date_of_birth', isset($customer) && $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '') }}" />
                        </div>
                    </div>


                    {{-- Anniversary --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            Anniversary
                        </label>

                        <div class="col-lg-8 fv-row">
                            <input type="date"
                                name="date_of_anniversary"
                                class="form-control form-control-lg form-control-solid"
                                value="{{ old('date_of_anniversary', isset($customer) && $customer->date_of_anniversary ? $customer->date_of_anniversary->format('Y-m-d') : '') }}" />
                        </div>
                    </div>


                    {{-- Active --}}
                    <div class="row mb-0">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            Active
                        </label>

                        <div class="col-lg-8 d-flex align-items-center">
                            <div class="form-check form-check-solid form-switch">

                                <input class="form-check-input w-45px h-30px"
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', $customer->is_active ?? true) ? 'checked' : '' }} />

                                <label class="form-check-label ms-2">
                                    Yes
                                </label>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <button type="reset"
                        class="btn btn-light btn-active-light-primary me-2">
                        Discard
                    </button>

                    <button type="submit"
                        class="btn btn-primary">
                        {{ isset($customer) ? 'Update' : 'Create' }} Customer
                    </button>
                </div>

            </form>

        </div>
    </div>

</x-default-layout>