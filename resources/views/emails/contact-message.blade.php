<!DOCTYPE html>
<html>

<body style="font-family: Arial; background:#f8f8f8; padding:20px">
    <div style="max-width:600px; background:#fff; padding:20px; border-radius:8px">

        <h2>📩 New Contact Message</h2>

        <p><strong>Name:</strong> {{ $contact->name }}</p>
        <p><strong>Email:</strong> {{ $contact->email }}</p>
        <p><strong>Phone:</strong> {{ $contact->phone ?? 'N/A' }}</p>
        <p><strong>Subject:</strong> {{ $contact->subject }}</p>

        <hr>

        <p>{{ $contact->message }}</p>

    </div>
</body>

</html>