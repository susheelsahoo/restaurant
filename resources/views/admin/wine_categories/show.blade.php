<x-default-layout>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $menuCategory->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Slug:</strong> {{ $menuCategory->slug }}</p>
            <p><strong>Description:</strong> {{ $menuCategory->description }}</p>
            <p><strong>Status:</strong> {{ $menuCategory->is_active ? 'Active' : 'Inactive' }}</p>
        </div>
    </div>
</x-default-layout>