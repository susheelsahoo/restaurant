<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">{{ isset($emailTemplate) ? 'Edit' : 'Create' }} Email Template</h3>
            </div>
        </div>

        <div class="collapse show">
            <form method="POST" action="{{ isset($emailTemplate) ? route('admin.email-templates.update', $emailTemplate->id) : route('admin.email-templates.store') }}" class="form">
                @csrf
                @if(isset($emailTemplate)) @method('PUT') @endif

                <div class="card-body border-top p-9">

                    {{-- Display Validation Errors --}}
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Slug --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Slug</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="slug" class="form-control form-control-lg form-control-solid @error('slug') is-invalid @enderror" placeholder="e.g., reservation-pending" value="{{ old('slug', $emailTemplate->slug ?? '') }}" required pattern="[a-zA-Z0-9\-]+" />
                            <small class="form-text text-muted">Use lowercase letters, numbers, and hyphens only</small>
                            @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Title --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Title</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="title" class="form-control form-control-lg form-control-solid @error('title') is-invalid @enderror" placeholder="Email Title (e.g., 📩 Reservation Request Sent)" value="{{ old('title', $emailTemplate->title ?? '') }}" required />
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Subject --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Email Subject</label>
                        <div class="col-lg-8 fv-row">
                            <input type="text" name="subject" class="form-control form-control-lg form-control-solid @error('subject') is-invalid @enderror" placeholder="Email subject line" value="{{ old('subject', $emailTemplate->subject ?? '') }}" required />
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Short Text / Preview --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Short Text (Preview)</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="short_text" class="form-control form-control-lg form-control-solid @error('short_text') is-invalid @enderror" rows="3" placeholder="Brief preview text (appears in email preview)">{{ old('short_text', $emailTemplate->short_text ?? '') }}</textarea>
                            @error('short_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Message / Body --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Message / Body</label>
                        <div class="col-lg-8 fv-row">
                            <textarea name="message" id="email-message" class="form-control form-control-lg form-control-solid @error('message') is-invalid @enderror" rows="15" placeholder="Email body content. You can use HTML and variables like: @{{ $reservation->customer->first_name }}, @{{ $reservation->visit_date }}, etc.">{{ old('message', $emailTemplate->message ?? '') }}</textarea>
                            <small class="form-text text-muted d-block mt-2">
                                <strong>Available Variables:</strong><br>
                                <code>@{{ $reservation->customer->first_name }}</code> - Customer first name<br>
                                <code>@{{ $reservation->visit_date }}</code> - Reservation date<br>
                                <code>@{{ $reservation->visit_time }}</code> - Reservation time<br>
                                <code>@{{ $reservation->guests }}</code> - Number of guests<br>
                                <code>@{{ $reservation->reservationStatus?->label }}</code> - Reservation status<br>
                                <code>@{{ config('app.GOOGLE_MAPS') }}</code> - Google Maps link<br>
                                <code>@{{ config('app.LOCATION') }}</code> - Restaurant location<br>
                                <code>@{{ config('app.CONTACT_NUMBER') }}</code> - Contact number
                            </small>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Active Status --}}
                    <div class="row mb-0">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Active</label>
                        <div class="col-lg-8 d-flex align-items-center">
                            <div class="form-check form-check-solid form-switch">
                                <input class="form-check-input w-45px h-30px" type="checkbox" name="is_active" value="1" {{ old('is_active', $emailTemplate->is_active ?? true) ? 'checked' : '' }} />
                                <label class="form-check-label ms-2">Yes</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-light btn-active-light-primary me-2">Back</a>
                    <button type="submit" class="btn btn-primary">{{ isset($emailTemplate) ? 'Update' : 'Create' }} Template</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    @endpush
</x-default-layout>