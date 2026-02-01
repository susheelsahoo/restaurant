<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{

    public function index(Request $request)
    {
        $query = Reservation::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query
            ->orderBy('visit_date', 'desc')
            ->paginate(10)     // ✅ IMPORTANT
            ->withQueryString();

        return view('pages.bookings.index', compact('bookings'));
    }

    public function index_old(Request $request)
    {
        $query = Reservation::latest();

        if ($request->has('status') && in_array($request->status, ['new', 'in_progress', 'converted', 'closed'])) {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        return view('pages.bookings.index', compact('bookings'));
    }


    public function create()
    {
        return view('pages.bookings.create');
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

        $lead = Reservation::create([
            'customer_name'       => $validated['full_name'],
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


    public function edit(Reservation $reservation)
    {
        return view('pages.bookings.edit', compact('reservation'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'customer_name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'source'     => 'nullable|string|max:100',
            'hotel_size' => 'nullable|string|max:255',
            'status'     => 'required|in:new,in_progress,converted,closed',
            'notes'      => 'nullable|string',
        ]);
        $validated['customer_name'] = $validated['customer_name'];
        unset($validated['customer_name']);
        $reservation->update($validated);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking updated successfully!');
    }


    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully!');
    }

    public function show($id)
    {
        $booking = Reservation::findOrFail($id);
        return view('pages.bookings.show', compact('booking'));
    }
}
