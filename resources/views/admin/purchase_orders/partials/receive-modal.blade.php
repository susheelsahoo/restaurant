@php
    $poReceiveItems = $purchaseOrder?->items ?? collect();
    $hasPoReceiveErrors = $errors->has('receipts')
        || collect($errors->getMessages())->keys()->contains(fn ($key) => str_starts_with($key, 'receipts.'));
@endphp

@if($purchaseOrder)
    <div
        class="modal fade po-receive-modal"
        id="{{ $modalId }}"
        tabindex="-1"
        aria-labelledby="{{ $modalId }}Title"
        aria-hidden="true"
        data-po-receive-auto-open="{{ $hasPoReceiveErrors ? 'true' : 'false' }}"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content" method="POST" action="{{ $formAction }}" data-po-receive-form>
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="{{ $modalId }}Title">Receive Items</h5>
                        <div class="text-muted fs-7">{{ $purchaseOrder->po_number }}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-icon btn-light" data-bs-dismiss="modal" data-po-receive-close aria-label="Close">
                        {!! getIcon('cross', 'fs-2', '', 'i') !!}
                    </button>
                </div>

                <div class="modal-body">
                    @if($hasPoReceiveErrors)
                        <div class="alert alert-danger py-3">{{ $errors->first() }}</div>
                    @endif

                    <div class="alert alert-light-primary py-3 mb-5">
                        Enter the total received quantity for each line. The PO will stay partial until every ordered quantity is received.
                    </div>

                    <div class="alert alert-danger py-3 d-none" data-po-receive-error></div>

                    @if($poReceiveItems->isEmpty())
                        <div class="text-center text-muted py-8">No PO items added yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-row-bordered align-middle mb-0">
                                <thead>
                                    <tr class="fw-bold text-muted">
                                        <th>PO Item</th>
                                        <th class="text-end">Requested</th>
                                        <th class="text-end">Current Received</th>
                                        <th style="min-width: 170px;">Update Received</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($poReceiveItems as $item)
                                        @php
                                            $ordered = (float) $item->quantity;
                                            $received = (float) $item->received_qty;
                                            $unit = $item->product->unit ?? '';
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-gray-900">{{ $item->product->name ?? '-' }}</div>
                                                <div class="text-muted fs-7">{{ $unit ?: 'Unit not set' }}</div>
                                            </td>
                                            <td class="text-end">{{ number_format($ordered, 2) }} {{ $unit }}</td>
                                            <td class="text-end">{{ number_format($received, 2) }} {{ $unit }}</td>
                                            <td>
                                                <input
                                                    type="number"
                                                    class="form-control form-control-solid"
                                                    name="receipts[{{ $item->id }}]"
                                                    value="{{ old('receipts.' . $item->id, number_format($received, 2, '.', '')) }}"
                                                    min="0"
                                                    max="{{ number_format($ordered, 2, '.', '') }}"
                                                    step="0.01"
                                                    data-po-receive-input
                                                    data-ordered="{{ number_format($ordered, 2, '.', '') }}"
                                                    aria-label="Received quantity for {{ $item->product->name ?? 'PO item' }}"
                                                >
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    @if($poReceiveItems->isNotEmpty())
                        <button type="button" class="btn btn-light-info me-auto" data-po-receive-fill-all>Receive All</button>
                    @endif
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-po-receive-close>Cancel</button>
                    <button type="submit" class="btn btn-primary" @disabled($poReceiveItems->isEmpty())>Submit Receiving</button>
                </div>
            </form>
        </div>
    </div>

    @once
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    function openReceiveModal(modal) {
                        if (!modal) {
                            return;
                        }

                        if (window.bootstrap?.Modal) {
                            window.bootstrap.Modal.getOrCreateInstance(modal).show();
                            return;
                        }

                        modal.style.display = 'block';
                        modal.classList.add('show');
                        modal.removeAttribute('aria-hidden');
                        document.body.classList.add('modal-open');
                    }

                    function closeReceiveModal(modal) {
                        if (!modal) {
                            return;
                        }

                        if (window.bootstrap?.Modal) {
                            window.bootstrap.Modal.getOrCreateInstance(modal).hide();
                            return;
                        }

                        modal.classList.remove('show');
                        modal.style.display = 'none';
                        modal.setAttribute('aria-hidden', 'true');
                        document.body.classList.remove('modal-open');
                    }

                    document.querySelectorAll('[data-po-receive-open]').forEach(function (button) {
                        button.addEventListener('click', function () {
                            openReceiveModal(document.querySelector(button.dataset.poReceiveOpen));
                        });
                    });

                    document.querySelectorAll('[data-po-receive-close]').forEach(function (button) {
                        button.addEventListener('click', function () {
                            closeReceiveModal(button.closest('.po-receive-modal'));
                        });
                    });

                    document.querySelectorAll('.po-receive-modal').forEach(function (modal) {
                        modal.addEventListener('click', function (event) {
                            if (event.target === modal) {
                                closeReceiveModal(modal);
                            }
                        });
                    });

                    document.querySelectorAll('[data-po-receive-fill-all]').forEach(function (button) {
                        button.addEventListener('click', function () {
                            const modal = button.closest('.po-receive-modal');

                            modal.querySelectorAll('[data-po-receive-input]').forEach(function (input) {
                                input.value = input.dataset.ordered || '0.00';
                            });
                        });
                    });

                    document.querySelectorAll('[data-po-receive-form]').forEach(function (form) {
                        form.addEventListener('submit', function (event) {
                            const error = form.querySelector('[data-po-receive-error]');
                            const invalidInput = Array.from(form.querySelectorAll('[data-po-receive-input]')).find(function (input) {
                                const receivedQuantity = Number(input.value || 0);
                                const orderedQuantity = Number(input.dataset.ordered || 0);

                                return receivedQuantity < 0 || receivedQuantity > orderedQuantity;
                            });

                            if (!invalidInput) {
                                return;
                            }

                            event.preventDefault();
                            error.classList.remove('d-none');
                            error.textContent = 'Received quantity cannot be negative or greater than the requested quantity.';
                            invalidInput.focus();
                        });
                    });

                    document.querySelectorAll('[data-po-receive-auto-open="true"]').forEach(openReceiveModal);
                });
            </script>
        @endpush
    @endonce
@endif
