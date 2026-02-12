<x-default-layout>

    @section('title')
    Customers
    @endsection

    @section('breadcrumbs')
    {{ Breadcrumbs::render('admin.customers.index') }}
    @endsection

    <div class="card">
        <div class="card-header border-0 pt-6">

            {{-- Search --}}
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                    <input type="text"
                        class="form-control form-control-solid w-250px ps-13"
                        placeholder="Search Customer" />
                </div>
            </div>

            {{-- Add Button --}}
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.customers.create') }}"
                        class="btn btn-primary">
                        {!! getIcon('plus', 'fs-2', '', 'i') !!}
                        Add Customer
                    </a>
                </div>
            </div>

        </div>

        <div class="card-body py-4">

            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">

                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>DOB</th>
                            <th>Anniversary</th>
                            <th>Status</th>
                            <th>view Note</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($customers as $customer)

                        <tr>
                            <td>
                                {{ $customer->first_name }} {{ $customer->last_name }}
                            </td>

                            <td>{{ $customer->email }}</td>

                            <td>{{ $customer->phone ?? '-' }}</td>

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
                                <button
                                    class="btn btn-sm btn-info view-notes-btn"
                                    data-customer-id="{{ $customer->id }}">
                                    View Notes
                                </button>

                            </td>
                            <td class="text-end">

                                <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                    class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form method="POST"
                                    action="{{ route('admin.customers.destroy', $customer->id) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this customer?');">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="btn btn-sm btn-danger">
                                        Delete
                                    </button>

                                </form>

                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
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

    <div class="modal fade" id="customerNotesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Customer Notes</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="customer-notes-content">
                    Loading...
                </div>

            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.view-notes-btn').forEach(btn => {

                btn.addEventListener('click', function() {

                    let customerId = this.dataset.customerId;

                    fetch(`/admin/customer-notes/${customerId}`)
                        .then(res => res.text())
                        .then(html => {

                            document.getElementById('customer-notes-content').innerHTML = html;

                            let modal = new bootstrap.Modal(
                                document.getElementById('customerNotesModal')
                            );

                            modal.show();

                        });

                });

            });

        });
    </script>
    @endpush


</x-default-layout>