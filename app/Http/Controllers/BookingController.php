<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationStatusMail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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
    public function create()
    {
        return view('pages.bookings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'email'         => 'nullable|email|max:150',
            'phone'         => 'required|string|max:25',
            'visit_date'    => 'required|date',
            'visit_time'    => 'required',
            'guests'        => 'required|integer|min:1|max:50',
            'notes'         => 'nullable|string|max:2000',
        ]);

        $reservation = Reservation::create([
            'booking_code'  => $this->generateBookingCode(),
            'customer_name' => $validated['customer_name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'],
            'visit_date'    => $validated['visit_date'],
            'visit_time'    => $validated['visit_time'],
            'guests'        => $validated['guests'],
            'notes'         => $validated['notes'] ?? null,
            'status'        => 'new',
        ]);

        // Admin notification
        Mail::to(config('mail.from.address'))
            ->send(new ReservationStatusMail($reservation));

        // Customer confirmation
        if ($reservation->email) {
            Mail::to($reservation->email)
                ->send(new ReservationStatusMail($reservation));
        }

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking created successfully!');
    }


    public function edit(Reservation $booking)
    {
        return view('pages.bookings.create', compact('booking'));
    }


    public function update(Request $request)
    {
        try {
            $reservation = Reservation::findOrFail($request->id);

            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'email'         => 'nullable|email|max:255',
                'phone'         => 'nullable|string|max:25',
                'visit_date'    => 'required|date',
                'visit_time'    => 'required',
                'guests'        => 'required|integer|min:1|max:50',
                'status'        => [
                    'required',
                    Rule::in(array_keys(config('app.statuses')))
                ],
                'notes'         => 'nullable|string|max:2000',
            ]);

            $oldStatus = $reservation->status;
            $reservation->where('id', $request->id)->update($validated);

            if ($validated['email'] && $oldStatus !== $validated['status']) {
                Mail::to($validated['email'])
                    ->queue(new ReservationStatusMail($reservation->fresh()));
            }

            return redirect()
                ->route('admin.bookings.index')
                ->with('success', 'Booking updated successfully!');
        } catch (\Exception $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }




    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully!');
    }

    public function show($id)
    {
        $booking = Reservation::findOrFail($id);
        return view('pages.bookings.show', compact('booking'));
    }
    private function generateBookingCode(): string
    {
        return 'TFL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }
}
