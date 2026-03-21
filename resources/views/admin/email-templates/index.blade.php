<x-default-layout>

    @section('title')
    Email Templates
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.email-templates.index') }}
    @endsection

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13" placeholder="Search template" />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Template
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th>Slug</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        @forelse($templates as $template)
                        <tr>
                            <td>
                                <code class="bg-light px-2 py-1">{{ $template->slug }}</code>
                            </td>
                            <td>{{ Str::limit($template->subject, 50) }}</td>
                            <td>
                                @if($template->is_active)
                                <span class="badge badge-success">Active</span>
                                @else
                                <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-info preview-email-template-btn"
                                        data-template-slug="{{ $template->slug }}"
                                        data-template-subject='@json($template->subject)'
                                        data-template-message='@json($template->message)'
                                    >
                                        {!! getIcon('eye', 'fs-3', '', 'i') !!}
                                    </button>
                                    <a href="{{ route('admin.email-templates.edit', $template->id) }}" class="btn btn-sm btn-warning">
                                        {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                    </a>
                                    <form method="POST" action="{{ route('admin.email-templates.destroy', $template->id) }}" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            {!! getIcon('trash', 'fs-3', '', 'i') !!}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <p class="text-gray-500">No email templates found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="emailTemplatePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Email Template Preview</h5>
                        <div class="text-muted fs-7" id="preview-template-meta"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                    <div class="border rounded overflow-hidden bg-light">
                        <iframe
                            id="email-template-preview-frame"
                            title="Email template preview"
                            style="width: 100%; min-height: 720px; border: 0; background: #ffffff;"
                        ></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const previewModalElement = document.getElementById('emailTemplatePreviewModal');
            const previewFrame = document.getElementById('email-template-preview-frame');
            const previewMeta = document.getElementById('preview-template-meta');

            if (!previewModalElement || !previewFrame || !previewMeta) {
                return;
            }

            const previewModal = new bootstrap.Modal(previewModalElement);
            const sampleValues = {
                '@{{ $guest_name }}': 'Susheel',
                '@{{ $guest_first_name }}': 'Susheel',
                '@{{ $guest_last_name }}': 'Sahoo',
                '@{{ $booking_code }}': 'TFL-20260321-ABCD',
                '@{{ $visit_date_formatted }}': '21 Mar 2026',
                '@{{ $visit_time_formatted }}': '20:30',
                '@{{ $guests_count }}': '6',
                '@{{ $status_label }}': 'Confirmed',
                '@{{ $location }}': 'Budapest, Raday utca 11, Budapest, Hungary',
                '@{{ $google_maps }}': 'https://maps.google.com',
                '@{{ $contact_number }}': '+36 00 000 0000',
                '@{{ $template->subject }}': 'Reservation Status',
                '@{{ $customer_name }}': 'Susheel Sahoo',
                '@{{ $customer_first_name }}': 'Susheel',
                '@{{ $customer_last_name }}': 'Sahoo',
                '@{{ $customer_email }}': 'susheel@example.com',
                '@{{ $customer_phone }}': '+36 00 000 0000',
                '@{{ $offer_code }}': 'WELCOME10',
            };

            function buildPreview(source, subject) {
                let html = source;

                Object.entries(sampleValues).forEach(([pattern, value]) => {
                    html = html.split(pattern).join(value);
                });

                if (html.trim() === '') {
                    return '<!DOCTYPE html><html><body style="font-family: Arial, sans-serif; padding: 24px; color: #666;">No preview available.</body></html>';
                }

                if (!/<title>/i.test(html) && /<head>/i.test(html)) {
                    html = html.replace(/<head>/i, `<head><title>${subject}</title>`);
                }

                return html;
            }

            document.querySelectorAll('.preview-email-template-btn').forEach((button) => {
                button.addEventListener('click', function () {
                    const slug = this.dataset.templateSlug;

                    let subject = '';
                    let message = '';

                    try {
                        subject = JSON.parse(this.dataset.templateSubject || '""');
                        message = JSON.parse(this.dataset.templateMessage || '""');
                    } catch (error) {
                        console.error('Failed to parse template preview payload', error);
                    }

                    previewMeta.textContent = `${slug} | ${subject}`;
                    previewFrame.srcdoc = buildPreview(message, subject);
                    previewModal.show();
                });
            });
        });
    </script>
    @endpush
</x-default-layout>
