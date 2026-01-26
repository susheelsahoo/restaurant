<x-default-layout>
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 cursor-pointer">
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">View Contact Message</h3>
            </div>
        </div>

        <div class="card-body border-top p-9">
            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Name:</label>
                <div class="col-lg-8">
                    <span class="form-control form-control-lg form-control-solid">{{ $contact->name }}</span>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Email:</label>
                <div class="col-lg-8">
                    <span class="form-control form-control-lg form-control-solid">{{ $contact->email }}</span>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Subject:</label>
                <div class="col-lg-8">
                    <span class="form-control form-control-lg form-control-solid">{{ $contact->subject ?? 'No Subject' }}</span>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Message:</label>
                <div class="col-lg-8">
                    <textarea class="form-control form-control-lg form-control-solid" rows="6" readonly>{{ $contact->message }}</textarea>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 fw-semibold fs-6">Status:</label>
                <div class="col-lg-8">
                    {!! $contact->is_read
                    ? '<span class="badge badge-success">Read</span>'
                    : '<span class="badge badge-danger">Unread</span>' !!}
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-light">Back</a>
                <form method="POST" action="{{ route('admin.contacts.destroy', $contact->id) }}" class="d-inline ms-3" onsubmit="return confirm('Are you sure you want to delete this message?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</x-default-layout>