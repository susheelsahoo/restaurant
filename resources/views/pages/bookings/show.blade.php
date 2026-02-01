<x-default-layout>
    @section('title', 'Lead Details')

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.leads.show', $lead) }}
    @endsection

    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Lead Details</h3>
            </div>
        </div>

        <div class="card-body border-top p-9">
            <!-- Name -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">Name</label>
                <div class="col-lg-8 fv-row">
                    <p>{{ $lead->name }}</p>
                </div>
            </div>

            <!-- Email -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">Email</label>
                <div class="col-lg-8 fv-row">
                    <p>{{ $lead->email }}</p>
                </div>
            </div>

            <!-- Phone -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">Phone</label>
                <div class="col-lg-8 fv-row">
                    <p>{{ $lead->phone ?? 'No Phone Provided' }}</p>
                </div>
            </div>

            <!-- Source -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">Source</label>
                <div class="col-lg-8 fv-row">
                    <p>{{ $lead->source ?? 'No Source Provided' }}</p>
                </div>
            </div>

            <!-- Hotel Size -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">Hotel Size</label>
                <div class="col-lg-8 fv-row">
                    <p>{{ $lead->hotel_size ?? 'Not Provided' }}</p>
                </div>
            </div>

            <!-- Status -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">Status</label>
                <div class="col-lg-8 fv-row">
                    @php
                    $statusColors = [
                    'new' => 'primary',
                    'in_progress' => 'warning',
                    'converted' => 'success',
                    'closed' => 'secondary',
                    ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$lead->status] ?? 'dark' }}">
                        {{ ucfirst($lead->status ?? 'NA') }}
                    </span>
                </div>
            </div>

            <!-- Notes -->
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">Notes</label>
                <div class="col-lg-8 fv-row">
                    <p>{{ $lead->notes ?? 'No notes provided' }}</p>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end py-6 px-9">
            <a href="{{ route('admin.leads.index') }}" class="btn btn-light btn-active-light-primary me-2">
                Back to Leads
            </a>
            <a href="{{ route('admin.leads.edit', $lead->id) }}" class="btn btn-primary">
                Edit Lead
            </a>
        </div>
    </div>
</x-default-layout>