{{-- CUSTOMER NOTES MODAL --}}
<div class="modal fade" id="customerNotesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <input type="hidden" id="current_customer_id">

                <h5 class="modal-title">Customer Notes</h5>

                <div class="ms-auto d-flex gap-2">
                    <a href="#"
                        id="add-note-btn"
                        class="btn btn-sm btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#addNoteModal">
                        Add Note
                    </a>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>
            </div>

            <div class="modal-body" id="customer-notes-content">
                Loading...
            </div>

        </div>
    </div>
</div>


{{-- ADD NOTE MODAL --}}
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Customer Note</h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <form method="POST" action="{{ route('admin.customer-notes.store') }}">
                @csrf

                <div class="modal-body">

                    <input type="hidden"
                        name="customer_id"
                        id="note_customer_id">

                    <div class="mb-3">
                        <label class="form-label">Note Type</label>
                        <select name="note_type" class="form-select">
                            <option value="general">General</option>
                            <option value="vip">VIP</option>
                            <option value="warning">Warning</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note"
                            class="form-control"
                            rows="4"
                            required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                        class="btn btn-primary">
                        Save Note
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.view-notes-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let customerId = this.dataset.customerId;
                document.getElementById('current_customer_id').value = customerId;

                fetch(`/admin/customer-notes/${customerId}`)
                    .then(res => res.text())
                    .then(html => {

                        document.getElementById('customer-notes-content').innerHTML = html;

                        function attachDeleteHandlers() {
                            document.querySelectorAll('.delete-note-btn').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    if (!confirm('Are you sure you want to delete this note?')) return;
                                    let noteId = this.dataset.noteId;

                                    fetch(`/admin/customer-notes/${noteId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                            'Accept': 'application/json'
                                        }
                                    }).then(res => {
                                        if (res.ok) {
                                            let customerId = document.getElementById('current_customer_id').value;
                                            fetch(`/admin/customer-notes/${customerId}`)
                                                .then(r => r.text())
                                                .then(html => {
                                                    document.getElementById('customer-notes-content').innerHTML = html;
                                                    attachDeleteHandlers();
                                                });
                                        } else {
                                            res.json().then(j => alert(j.message || 'Failed to delete note'));
                                        }
                                    }).catch(() => alert('Request failed'));
                                });
                            });
                        }

                        attachDeleteHandlers();

                        let modal = new bootstrap.Modal(
                            document.getElementById('customerNotesModal')
                        );

                        modal.show();

                    });

            });

        });
        let addNoteBtn = document.getElementById('add-note-btn');

        if (addNoteBtn) {

            addNoteBtn.addEventListener('click', function() {

                let customerId = document.getElementById('current_customer_id').value;

                document.getElementById('note_customer_id').value = customerId;

            });

        }

    });
</script>
@endpush