<x-default-layout>

    <div class="card mb-5 mb-xl-10">

        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">
                    Add Customer Note
                </h3>
            </div>
        </div>

        <div class="collapse show">

            <form method="POST"
                action="{{ route('admin.customer-notes.store') }}"
                class="form">

                @csrf

                <input type="hidden"
                    name="customer_id"
                    value="{{ $customer->id ?? request('customer_id') }}">


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


                    {{-- Note Type --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                            Note Type
                        </label>

                        <div class="col-lg-8 fv-row">
                            <select name="note_type"
                                class="form-select form-select-solid">

                                <option value="General">General</option>
                                <option value="vip"
                                    {{ old('note_type') == 'vip' ? 'selected' : '' }}>
                                    VIP
                                </option>
                                <option value="allergy"
                                    {{ old('note_type') == 'allergy' ? 'selected' : '' }}>
                                    Allergy
                                </option>
                                <option value="complaint"
                                    {{ old('note_type') == 'complaint' ? 'selected' : '' }}>
                                    Complaint
                                </option>
                            </select>
                        </div>
                    </div>


                    {{-- Note Text --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">
                            Note
                        </label>

                        <div class="col-lg-8 fv-row">
                            <textarea name="note"
                                rows="5"
                                class="form-control form-control-lg form-control-solid @error('note') is-invalid @enderror"
                                placeholder="Enter customer note..."
                                required>{{ old('note') }}</textarea>

                            @error('note')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                </div>


                <div class="card-footer d-flex justify-content-end py-6 px-9">

                    <a href="{{ url()->previous() }}"
                        class="btn btn-light btn-active-light-primary me-2">
                        Back
                    </a>

                    <button type="submit"
                        class="btn btn-primary">
                        Save Note
                    </button>

                </div>

            </form>

        </div>
    </div>

</x-default-layout>