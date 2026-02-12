@if($notes->count())

@foreach($notes as $note)

<div class="border rounded p-3 mb-3">

    <strong>
        {{ ucfirst($note->note_type ?? 'General') }}
    </strong>

    <p class="mb-1">{{ $note->note }}</p>

    <small class="text-muted">
        {{ $note->created_at->format('d M Y H:i') }}
    </small>

</div>

@endforeach

@else

<div class="text-muted text-center">
    No notes found.
</div>

@endif