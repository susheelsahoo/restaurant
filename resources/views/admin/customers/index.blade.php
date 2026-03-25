<x-default-layout>

    @section('title')
    Customers
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.customers.index') }}
    @endsection

    <div class="card">
        <div class="card-header border-0 pt-6">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4 w-100">
                <div class="card-title me-lg-4 mb-0">
                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                        <div class="position-relative my-1">
                            {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                            <input type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control form-control-solid w-250px ps-13"
                                placeholder="Search customer" />
                        </div>
                        <select name="booking_count" class="form-select form-select-solid w-200px">
                            <option value="">All Booking Counts</option>
                            <option value="1" {{ request('booking_count') === '1' ? 'selected' : '' }}>1 booking</option>
                            <option value="2" {{ request('booking_count') === '2' ? 'selected' : '' }}>2 bookings</option>
                            <option value="3_plus" {{ request('booking_count') === '3_plus' ? 'selected' : '' }}>3+ bookings</option>
                            <option value="5_plus" {{ request('booking_count') === '5_plus' ? 'selected' : '' }}>5+ bookings</option>
                            <option value="10_plus" {{ request('booking_count') === '10_plus' ? 'selected' : '' }}>10+ bookings</option>
                        </select>
                        <select name="last_booking_days" class="form-select form-select-solid w-200px">
                            <option value="">Any Last Booking</option>
                            @foreach([10, 20, 30, 60, 99] as $days)
                            <option value="{{ $days }}" {{ request('last_booking_days') == (string) $days ? 'selected' : '' }}>{{ $days }}+ days ago</option>
                            @endforeach
                        </select>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-light-primary">Apply</button>
                            @if(request()->filled('search') || request()->filled('booking_count') || request()->filled('last_booking_days'))
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-light">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-toolbar">
                    <div class="d-flex justify-content-end gap-3">
                        <button type="button"
                            class="btn btn-light-primary"
                            id="open-bulk-notification-modal"
                            disabled>
                            {!! getIcon('notification-bing', 'fs-2', '', 'i') !!}
                            Send Notification
                        </button>
                        <a href="{{ route('admin.customers.create') }}"
                            class="btn btn-primary">
                            {!! getIcon('plus', 'fs-2', '', 'i') !!}
                            Add Customer
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body py-4">
            @if(request()->filled('search') || request()->filled('booking_count') || request()->filled('last_booking_days'))
            <div class="mb-5 d-flex flex-wrap gap-2">
                @if(request()->filled('search'))
                <span class="badge badge-light-primary">Search: {{ request('search') }}</span>
                @endif
                @if(request()->filled('booking_count'))
                <span class="badge badge-light-success">
                    Booking count:
                    @if(request('booking_count') === '1')
                    1 booking
                    @elseif(request('booking_count') === '2')
                    2 bookings
                    @elseif(request('booking_count') === '3_plus')
                    3+ bookings
                    @elseif(request('booking_count') === '5_plus')
                    5+ bookings
                    @elseif(request('booking_count') === '10_plus')
                    10+ bookings
                    @else
                    {{ request('booking_count') }}
                    @endif
                </span>
                @endif
                @if(request()->filled('last_booking_days'))
                <span class="badge badge-light-warning">Last booking: {{ request('last_booking_days') }}+ days ago</span>
                @endif
            </div>
            @endif

            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">

                    <thead>
                        <tr>
                            <th class="w-40px">
                                <input type="checkbox" class="form-check-input" id="select-all-customers">
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Last Booking</th>
                            <th>DOB</th>
                            <th>Anniversary</th>
                            <th>Status</th>
                            <th>Subscription</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($customers as $customer)

                        <tr>
                            <td>
                                <input type="checkbox"
                                    class="form-check-input customer-checkbox"
                                    value="{{ $customer->id }}"
                                    data-customer-name="{{ trim($customer->first_name . ' ' . $customer->last_name) }}"
                                    data-customer-first-name="{{ $customer->first_name }}"
                                    data-customer-last-name="{{ $customer->last_name }}"
                                    data-customer-email="{{ $customer->email }}">
                            </td>
                            <td>
                                {{ $customer->first_name }} {{ $customer->last_name }}
                                <span class="badge badge-light-info ms-2">
                                    {{ $customer->reservations_count }}
                                </span>
                                @if($customer->id)
                                <span class="view-notes-btn ms-2 text-primary"
                                    style="cursor:pointer"
                                    data-customer-id="{{ $customer->id }}">
                                    <i class="fas fa-sticky-note"></i>
                                </span>
                                @endif
                            </td>

                            <td>{{ $customer->email }}</td>

                            <td>{{ $customer->phone ?? '-' }}</td>

                            <td>
                                {{ $customer->reservations_max_visit_date ? \Illuminate\Support\Carbon::parse($customer->reservations_max_visit_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ optional($customer->date_of_birth)->format('d M Y') ?? '-' }}
                            </td>

                            <td>
                                {{ optional($customer->date_of_anniversary)->format('d M Y') ?? '-' }}
                            </td>

                            <td>
                                {!! $customer->is_active
                                ? '<span class="badge badge-success">Active</span>'
                                : '<span class="badge badge-danger">Inactive</span>' !!}
                            </td>
                            <td>
                                @if($customer->is_subscribed)
                                <span class="badge badge-light-success">Subscribed</span>
                                @else
                                <span class="badge badge-light-danger">Unsubscribed</span>
                                @endif
                            </td>
                            <td class="text-end">

                                <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                    class="btn btn-sm btn-warning">
                                    {!! getIcon('pencil', 'fs-3', '', 'i') !!}
                                </a>

                                <button type="button"
                                    class="btn btn-sm btn-info send-notification-btn"
                                    data-customer-id="{{ $customer->id }}"
                                    data-customer-name="{{ trim($customer->first_name . ' ' . $customer->last_name) }}"
                                    data-customer-first-name="{{ $customer->first_name }}"
                                    data-customer-last-name="{{ $customer->last_name }}"
                                    data-customer-email="{{ $customer->email }}">
                                    {!! getIcon('notification-bing', 'fs-3', '', 'i') !!}
                                </button>

                                <form method="POST"
                                    action="{{ route('admin.customers.destroy', $customer->id) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this customer?');">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="btn btn-sm btn-danger">
                                        {!! getIcon('trash', 'fs-3', '', 'i') !!}
                                    </button>

                                </form>

                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                No customers found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-5">
                {{ $customers->links() }}
            </div>

        </div>
    </div>

    <x-customer-notes />
    <div class="modal fade" id="customerNotificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.customers.notifications.send') }}" id="customer-notification-form">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Send Customer Notification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body notification-modal-body">
                        <div class="alert alert-info d-flex flex-column gap-1">
                            <div><strong>Recipients:</strong> <span id="notification-recipient-summary">No customers selected</span></div>
                            <div class="small text-muted">Emails are queued for background sending. If your queue connection is set to `sync`, they will send immediately.</div>
                        </div>

                        <div id="notification-customer-inputs"></div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email Template</label>
                            <select class="form-select" id="notification-template-select" name="email_template_id">
                                <option value="">Choose a template</option>
                                @foreach($emailTemplates as $template)
                                <option value="{{ $template->id }}"
                                    data-subject='@json($template->subject)'
                                    data-message='@json($template->message)'
                                    data-slug="{{ $template->slug }}">
                                    {{ $template->slug }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email Subject</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" id="notification-subject" value="{{ old('subject') }}" required>
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Message / Body</label>
                            <textarea
                                class="form-control font-monospace @error('message') is-invalid @enderror"
                                name="message"
                                id="notification-message"
                                rows="18"
                                spellcheck="false"
                                required>{{ old('message') }}</textarea>
                            <small class="form-text text-muted d-block mt-2">
                                Available variables:<br>
                                <code>@{{ $customer_name }}</code>,
                                <code>@{{ $customer_email }}</code>,
                                <code>@{{ $customer_phone }}</code>,
                                <code>@{{ $offer_code }}</code>,
                                <code>@{{ $location }}</code>,
                                <code>@{{ $google_maps }}</code>,
                                <code>@{{ $contact_number }}</code>
                            </small>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <div class="d-flex flex-wrap gap-3 mb-3">
                                <button type="button" class="btn btn-light-primary" id="refresh-notification-preview">Refresh Preview</button>
                                <span class="text-muted fs-7 align-self-center">Preview uses sample customer data so you can review the design before sending.</span>
                            </div>
                            <div class="border rounded overflow-hidden bg-light">
                                <iframe
                                    id="notification-preview-frame"
                                    title="Customer notification preview"
                                    style="width: 100%; height: 420px; border: 0; background: #ffffff;"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer notification-modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="queue-notification-submit">
                            <span class="indicator-label">Queue Notification</span>
                            <span class="indicator-progress">
                                Sending...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        #customerNotificationModal .modal-content {
            max-height: calc(100vh - 3rem);
        }

        #customerNotificationModal .notification-modal-body {
            overflow-y: auto;
            max-height: calc(100vh - 220px);
        }

        #customerNotificationModal .notification-modal-footer {
            position: sticky;
            bottom: 0;
            background: #fff;
            z-index: 2;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('customerNotificationModal');
            const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
            const selectAll = document.getElementById('select-all-customers');
            const customerCheckboxes = Array.from(document.querySelectorAll('.customer-checkbox'));
            const bulkButton = document.getElementById('open-bulk-notification-modal');
            const customerInputs = document.getElementById('notification-customer-inputs');
            const recipientSummary = document.getElementById('notification-recipient-summary');
            const templateSelect = document.getElementById('notification-template-select');
            const subjectInput = document.getElementById('notification-subject');
            const messageInput = document.getElementById('notification-message');
            const previewFrame = document.getElementById('notification-preview-frame');
            const refreshPreviewButton = document.getElementById('refresh-notification-preview');
            const notificationForm = document.getElementById('customer-notification-form');
            const submitButton = document.getElementById('queue-notification-submit');
            let isSubmittingNotification = false;

            function selectedCustomers() {
                return customerCheckboxes.filter((checkbox) => checkbox.checked);
            }

            function updateBulkButton() {
                const count = selectedCustomers().length;
                bulkButton.disabled = count === 0;
                bulkButton.innerHTML = `{!! getIcon('notification-bing', 'fs-2', '', 'i') !!} Send Notification${count ? ` (${count})` : ''}`;
            }

            function setRecipientInputs(customers) {
                customerInputs.innerHTML = customers
                    .map((customer) => `<input type="hidden" name="customer_ids[]" value="${customer.value}">`)
                    .join('');

                if (customers.length === 0) {
                    recipientSummary.textContent = 'No customers selected';
                    return;
                }

                if (customers.length === 1) {
                    recipientSummary.textContent = `${customers[0].dataset.customerName} (${customers[0].dataset.customerEmail || 'no email'})`;
                    return;
                }

                recipientSummary.textContent = `${customers.length} customers selected`;
            }

            function placeholderMap() {
                const firstSelected = selectedCustomers()[0];
                const firstName = firstSelected?.dataset.customerFirstName || 'Susheel';
                const lastName = firstSelected?.dataset.customerLastName || '';
                const fullName = `${firstName} ${lastName}`.trim() || 'Susheel';

                return {
                    '@{{ $customer_name }}': fullName,
                    '@{{ $customer_email }}': firstSelected?.dataset.customerEmail || 'guest@example.com',
                    '@{{ $customer_phone }}': '+36 00 000 0000',
                    '@{{ $offer_code }}': 'WELCOME10',
                    '@{{ $location }}': 'Budapest, Raday utca 11, Budapest, Hungary',
                    '@{{ $google_maps }}': 'https://maps.google.com',
                    '@{{ $contact_number }}': '+36 00 000 0000'
                };
            }

            function renderPreview() {
                const replacements = placeholderMap();
                let html = messageInput.value;

                Object.entries(replacements).forEach(([pattern, value]) => {
                    html = html.split(pattern).join(value);
                });

                previewFrame.srcdoc = html.trim() !== '' ? html : '<!DOCTYPE html><html><body style="font-family: Arial, sans-serif; padding: 24px; color: #666;">Notification preview will appear here.</body></html>';
            }

            function openNotificationModal(customers) {
                if (!modal || customers.length === 0) {
                    return;
                }

                setRecipientInputs(customers);
                renderPreview();
                modal.show();
            }

            selectAll?.addEventListener('change', function() {
                customerCheckboxes.forEach((checkbox) => {
                    checkbox.checked = this.checked;
                });
                updateBulkButton();
            });

            customerCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', function() {
                    const allSelected = customerCheckboxes.length > 0 && customerCheckboxes.every((item) => item.checked);
                    if (selectAll) {
                        selectAll.checked = allSelected;
                    }
                    updateBulkButton();
                });
            });

            bulkButton?.addEventListener('click', function() {
                openNotificationModal(selectedCustomers());
            });

            document.querySelectorAll('.send-notification-btn').forEach((button) => {
                button.addEventListener('click', function() {
                    const customerId = this.dataset.customerId;
                    const matchingCheckbox = customerCheckboxes.find((checkbox) => checkbox.value === customerId);
                    if (matchingCheckbox) {
                        matchingCheckbox.checked = true;
                        updateBulkButton();
                        openNotificationModal([matchingCheckbox]);
                    }
                });
            });

            templateSelect?.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                if (!selected || !selected.dataset.subject) {
                    return;
                }

                try {
                    subjectInput.value = JSON.parse(selected.dataset.subject);
                    messageInput.value = JSON.parse(selected.dataset.message);
                } catch (error) {
                    console.error('Failed to parse template data', error);
                }

                renderPreview();
            });

            let previewTimer;
            messageInput?.addEventListener('input', function() {
                window.clearTimeout(previewTimer);
                previewTimer = window.setTimeout(renderPreview, 300);
            });

            refreshPreviewButton?.addEventListener('click', renderPreview);

            notificationForm?.addEventListener('submit', function(event) {
                if (isSubmittingNotification) {
                    event.preventDefault();
                    return;
                }

                isSubmittingNotification = true;

                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.setAttribute('data-kt-indicator', 'on');
                }
            });

            updateBulkButton();
            renderPreview();

            @if($errors->has('customer_ids') || $errors->has('subject') || $errors->has('message'))
            openNotificationModal(selectedCustomers());
            @endif
        });
    </script>
    @endpush


</x-default-layout>
