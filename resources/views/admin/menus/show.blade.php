<x-default-layout>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $menu->name }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Slug:</strong> {{ $menu->slug }}</p>
            <p><strong>Category:</strong> {{ $menu->category->name ?? '-' }}</p>
            <p><strong>Price:</strong> {{ $menu->price ? number_format($menu->price, 2) : '-' }}</p>
            <p><strong>Description:</strong> {!! nl2br(e($menu->description)) !!}</p>
            <p><strong>Status:</strong> {{ $menu->is_active ? 'Active' : 'Inactive' }}</p>
            @if($menu->image)
            <div class="mt-3"><img src="{{ asset('storage/' . $menu->image) }}" alt="" width="200"></div>
            @endif
        </div>
    </div>
</x-default-layout>