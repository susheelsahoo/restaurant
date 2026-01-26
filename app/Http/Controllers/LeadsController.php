<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LeadsController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::latest();

        if ($request->has('status') && in_array($request->status, ['new', 'in_progress', 'converted', 'closed'])) {
            $query->where('status', $request->status);
        }

        $leads = $query->get();

        return view('pages.leads.index', compact('leads'));
    }


    public function create()
    {
        return view('pages.leads.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:191',
            'phone'      => ['required', 'regex:/^\+[1-9]\d{7,14}$/'],
            'hotel_size' => 'required|string|max:255',
            'notes'      => 'nullable|string',
        ]);

        $lead = Lead::create([
            'name'       => $validated['full_name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'],
            'hotel_size' => $validated['hotel_size'],
            'notes'      => $validated['notes'] ?? null,
            'source'     => 'website',
            'status'     => 'new',
        ]);

        // Email to customer
        Mail::raw(
            "Hi {$lead->name},\n\nThank you for contacting Mediator. We’ll respond soon.\n\nBest Regards,\nMediator Team",
            fn($m) => $m->to($lead->email)->subject('Thank you for your inquiry')
        );

        // Email to admin
        Mail::raw(
            "New Lead:\n\nName: {$lead->name}\nEmail: {$lead->email}\nPhone: {$lead->phone}\nHotel Size: {$lead->hotel_size}\nNotes: {$lead->notes}",
            fn($m) => $m->to(env('MANAGER_EMAIL'))->subject('New Lead - Mediator')
        );

        return response()->json(['success' => true, 'message' => 'Lead submitted successfully.']);
    }


    public function edit(Lead $lead)
    {
        return view('pages.leads.create', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'full_name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'source'     => 'nullable|string|max:100',
            'hotel_size' => 'nullable|string|max:255',
            'status'     => 'required|in:new,in_progress,converted,closed',
            'notes'      => 'nullable|string',
        ]);
        $validated['name'] = $validated['full_name'];
        unset($validated['full_name']);
        $lead->update($validated);

        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lead updated successfully!');
    }


    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted successfully!');
    }

    public function show($id)
    {
        $lead = Lead::findOrFail($id);
        return view('pages.leads.show', compact('lead'));
    }
}
