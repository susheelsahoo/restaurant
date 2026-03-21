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

                    {{-- Message / Body --}}
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Message / Body</label>
                        <div class="col-lg-8 fv-row">
                            <textarea
                                name="message"
                                id="email-message"
                                class="form-control form-control-lg font-monospace @error('message') is-invalid @enderror"
                                rows="24"
                                spellcheck="false"
                                placeholder="Paste the full email HTML here, including <!DOCTYPE html>, <html>, <head>, and <body>."
                            >{{ old('message', $emailTemplate->message ?? '') }}</textarea>
                            <small class="form-text text-muted d-block mt-2">
                                <strong>Use full HTML email markup in this field.</strong><br>
                                This editor now saves the exact HTML you paste, including <code>&lt;!DOCTYPE html&gt;</code>, <code>&lt;html&gt;</code>, <code>&lt;head&gt;</code>, and <code>&lt;body&gt;</code>.<br><br>
                                <strong>Available Variables:</strong><br>
                                <code>@{{ $guest_name }}</code> - Guest name<br>
                                <code>@{{ $guest_first_name }}</code> - Guest first name<br>
                                <code>@{{ $guest_last_name }}</code> - Guest last name<br>
                                <code>@{{ $booking_code }}</code> - Booking code<br>
                                <code>@{{ $visit_date_formatted }}</code> - Formatted reservation date<br>
                                <code>@{{ $visit_time_formatted }}</code> - Formatted reservation time<br>
                                <code>@{{ $guests_count }}</code> - Number of guests<br>
                                <code>@{{ $status_label }}</code> - Reservation status<br>
                                <code>@{{ $google_maps }}</code> - Google Maps link<br>
                                <code>@{{ $location }}</code> - Restaurant location<br>
                                <code>@{{ $contact_number }}</code> - Contact number<br>
                                <code>@{{ $template->subject }}</code> - Current email subject
                            </small>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Live Preview</label>
                        <div class="col-lg-8">
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <button type="button" class="btn btn-light-primary" id="refresh-email-preview">Refresh Preview</button>
                                <span class="text-muted fs-7 align-self-center">Preview renders the current HTML from the editor in an isolated frame.</span>
                            </div>
                            <div class="border rounded overflow-hidden bg-light">
                                <iframe
                                    id="email-preview-frame"
                                    title="Email template preview"
                                    style="width: 100%; min-height: 720px; border: 0; background: #ffffff;"
                                ></iframe>
                            </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editor = document.getElementById('email-message');
            const previewFrame = document.getElementById('email-preview-frame');
            const refreshButton = document.getElementById('refresh-email-preview');

            if (!editor || !previewFrame) {
                return;
            }

            const sampleValues = {
                '@{{ $guest_name }}': 'Susheel',
                '@{{ $guest_first_name }}': 'Susheel',
                '@{{ $guest_last_name }}': 'Sahoo',
                '@{{ $booking_code }}': 'TFL-20260321-ABCD',
                '@{{ $visit_date_formatted }}': '21 Mar 2026',
                '@{{ $visit_time_formatted }}': '20:30',
                '@{{ $guests_count }}': '6',
                '@{{ $status_label }}': 'Pending',
                '@{{ $location }}': 'Budapest, Raday utca 11, Budapest, Hungary',
                '@{{ $google_maps }}': 'https://maps.google.com',
                '@{{ $contact_number }}': '+36 00 000 0000',
                '@{{ $template->subject }}': 'Reservation Request Sent',
            };

            function buildPreviewHtml(source) {
                let preview = source;

                Object.entries(sampleValues).forEach(([pattern, value]) => {
                    preview = preview.split(pattern).join(value);
                });

                return preview.trim() !== '' ? preview : '<!DOCTYPE html><html><body style="font-family: Arial, sans-serif; padding: 24px; color: #666;">Email preview will appear here.</body></html>';
            }

            function renderPreview() {
                previewFrame.srcdoc = buildPreviewHtml(editor.value);
            }

            let previewTimer;
            editor.addEventListener('input', function () {
                window.clearTimeout(previewTimer);
                previewTimer = window.setTimeout(renderPreview, 300);
            });

            refreshButton?.addEventListener('click', renderPreview);

            renderPreview();
        });
    </script>
    @endpush
</x-default-layout>
