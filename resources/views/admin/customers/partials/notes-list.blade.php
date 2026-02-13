@if($notes->count())

@foreach($notes as $note)

<div class="border rounded p-3 mb-3">

    <strong>
        {{ ucfirst($note->note_type ?? 'General') }}
    </strong>

    <div class="d-flex justify-content-between align-items-start">
        <div>
            <p class="mb-1">{{ $note->note }}</p>
            <small class="text-muted">{{ $note->created_at->format('d M Y H:i') }}</small>
        </div>
        <div>
            <span class="badge bg-danger text-white delete-note-btn" data-note-id="{{ $note->id }}"><i class="fas fa-trash"></i></span>
        </div>
    </div>

</div>

@endforeach

@else

<div class="text-muted text-center">
    No notes found.
</div>

@endif