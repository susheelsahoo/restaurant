<x-default-layout>
    @section('title')
    Contacts
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.contacts.index') }}
    @endsection

    <form method="GET" action="{{ route('admin.contacts.index') }}" class="mb-4">
        <select name="is_read" onchange="this.form.submit()" class="form-select w-auto d-inline-block">
            <option value="">All Status</option>

            <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>
                New Query
            </option>

            <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>
                Read Query
            </option>
        </select>

    </form>
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text" class="form-control form-control-solid w-250px ps-13" placeholder="Search Contact" />
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Contact
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                        <tr>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ $contact->subject ?? 'No Subject' }}</td>
                            <td>
                                {!! $contact->is_read
                                ? '<span class="badge badge-success">Read</span>'
                                : '<span class="badge badge-danger">Unread</span>' !!}
                            </td>
                            <td>
                                <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-sm btn-info">View</a>
                                <form method="POST" action="{{ route('admin.contacts.destroy', $contact->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this contact?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No contacts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Optional: Add custom scripts for contact management here
    </script>
    @endpush
</x-default-layout>