<x-default-layout>
    @section('title')
    {{ $title }}
    @endsection

    <div class="card">
        <div class="card-body py-10 px-10">
            <div class="d-flex flex-column gap-4">
                <div>
                    <h1 class="mb-2">{{ $title }}</h1>
                    <p class="text-muted fs-6 mb-0">{{ $description }}</p>
                </div>

                <div class="rounded border border-dashed border-gray-300 p-8 bg-light">
                    <div class="fs-5 fw-semibold text-gray-800 mb-2">Route is ready</div>
                    <div class="text-muted">
                        This menu item now has a working admin route. You can replace this placeholder with the final module whenever you're ready.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-default-layout>
