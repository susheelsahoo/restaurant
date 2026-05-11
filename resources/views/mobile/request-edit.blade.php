@extends('layout.mobile')

@section('title', 'Edit ' . $requestEdit['request_no'])
@section('body-class', 'mobile-app-body')
@section('mobile-standalone', true)

@section('mobile-content')
<div class="order-review-page">
    <header class="or-topbar">
        <a class="or-icon-btn" href="{{ url('/mobile/request-detail') }}" aria-label="Back to requests">
            <span aria-hidden="true">&larr;</span>
        </a>
        <div class="or-title-block">
            <h3>Edit Request</h3>
            <p>Update product quantities and submit again</p>
        </div>
        <div class="or-avatar">{{ strtoupper(substr((string) auth()->user()?->name, 0, 1)) ?: 'U' }}</div>
    </header>

    <main>
        @if($errors->any())
            <div class="or-result-message show danger">{{ $errors->first() }}</div>
        @endif

        <section class="or-card or-hero">
            <h3>{{ $requestEdit['request_no'] }}</h3>
            <p>This request was sent back. Adjust the product quantities, review notes, and submit it for approval again.</p>
        </section>

        @if(filled($requestEdit['manager_comment']))
            <section class="or-card">
                <div class="or-section-head">
                    <h3>Manager Comment</h3>
                    <span>Requested change</span>
                </div>
                <p class="or-empty-state">{!! nl2br(e($requestEdit['manager_comment'])) !!}</p>
            </section>
        @endif

        <form id="mobileRequestEditForm" method="POST" action="{{ $requestEdit['update_url'] }}">
            @csrf
            @method('PATCH')

            <section class="or-card">
                <div class="or-section-head">
                    <h3>Products</h3>
                    <span>{{ $requestEdit['items']->count() }} items</span>
                </div>

                @foreach($requestEdit['items'] as $index => $item)
                    <div class="or-mobile-edit-item">
                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] }}">

                        <div>
                            <div class="or-item-name">{{ $item['name'] }}</div>
                            <div class="or-item-meta">
                                {{ $item['category'] }}
                                &middot;
                                {{ $item['supplier'] !== '-' ? 'Supplier: ' . $item['supplier'] : 'No supplier selected' }}
                            </div>
                        </div>

                        <div class="or-mobile-edit-grid">
                            <div class="or-field">
                                <label for="requestItemQty{{ $item['id'] }}">Quantity</label>
                                <input
                                    id="requestItemQty{{ $item['id'] }}"
                                    type="number"
                                    name="items[{{ $index }}][quantity]"
                                    value="{{ old('items.' . $index . '.quantity', rtrim(rtrim(number_format((float) $item['quantity'], 2, '.', ''), '0'), '.')) }}"
                                    min="0.01"
                                    step="0.01"
                                    required
                                >
                            </div>
                            <div class="or-mobile-edit-unit">{{ $item['unit'] }}</div>
                        </div>

                        <div class="or-field">
                            <label for="requestItemNote{{ $item['id'] }}">Note</label>
                            <input
                                id="requestItemNote{{ $item['id'] }}"
                                type="text"
                                name="items[{{ $index }}][notes]"
                                value="{{ old('items.' . $index . '.notes', $item['notes']) }}"
                                placeholder="Optional note"
                            >
                        </div>
                    </div>
                @endforeach
            </section>
        </form>
    </main>

    <div class="or-sticky-bar">
        <div class="or-sticky-meta">
            <span>Needs edit</span>
            <span>{{ $requestEdit['request_no'] }}</span>
        </div>
        <div class="or-action-grid">
            <a class="or-btn decline" href="{{ $requestEdit['detail_url'] }}">Cancel</a>
            <button class="or-btn confirm" type="submit" form="mobileRequestEditForm">Submit Again</button>
        </div>
    </div>
</div>
@endsection
