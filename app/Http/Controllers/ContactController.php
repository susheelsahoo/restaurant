<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'subject' => 'nullable|max:255',
            'message' => 'required',
        ]);

        ContactMessage::create($validated);

        return redirect()->route('admin.contacts.index')->with('success', 'Message sent successfully!');
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
