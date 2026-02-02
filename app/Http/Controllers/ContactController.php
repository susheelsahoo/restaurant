<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use App\Mail\ContactMessageMail;
use App\Mail\ContactAutoReplyMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = ContactMessage::latest()->get();
        return view('pages.contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('pages.contacts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string|max:120',
            'email'         => 'required|email|max:150',
            'phone'         => 'nullable|string|max:25',
            'subject'       => 'required|string|max:200',
            'message'       => 'required|string|max:2000',
        ]);

        $contactMessage = ContactMessage::create([
            'name'    => $request->full_name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        Mail::to(config('mail.from.address'))
            ->send(new ContactMessageMail($contactMessage));

        Mail::to($contactMessage->email)
            ->send(new ContactAutoReplyMail($contactMessage));
        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function show($id)
    {
        $contact = ContactMessage::findOrFail($id);
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }
        return view('pages.contacts.show', compact('contact'));
    }

    public function edit(ContactMessage $contact)
    {
        return view('pages.contacts.edit', compact('contact'));
    }

    public function update(Request $request, ContactMessage $contact)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'subject' => 'nullable|max:255',
            'message' => 'required',
        ]);

        $contact->update($validated);

        return redirect()->route('admin.contacts.index')->with('success', 'Message updated successfully!');
    }

    public function destroy(ContactMessage $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('success', 'Message deleted!');
    }
}
