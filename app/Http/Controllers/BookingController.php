<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationStatusMail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class BookingController extends Controller
{

    public function index(Request $request)
    {
        $query = Reservation::with('customer');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('visit_date')) {
            $query->whereDate('visit_date', $request->visit_date);
        }
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('booking_code', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
            });
        }

        $bookings = $query
            ->orderByDesc('visit_date')
            ->orderByAsc('visit_time')
            ->paginate(50)
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

        DB::beginTransaction();

        try {
            $nameParts = explode(' ', trim($validated['customer_name']), 2);
            $firstName = $nameParts[0];
            $lastName  = $nameParts[1] ?? null;
            $customer = Customer::where('email', $validated['email'])
                ->orWhere('phone', $validated['phone'])
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $validated['email'],
                    'phone'      => $validated['phone'],
                    'is_active'  => 1,
                ]);
            }
            $reservation = Reservation::create([
                'booking_code' => $this->generateBookingCode(),
                'customer_id'  => $customer->id,
                'visit_date'   => $validated['visit_date'],
                'visit_time'   => $validated['visit_time'],
                'guests'       => $validated['guests'],
                'notes'        => $validated['notes'] ?? null,
                'status'       => 'pending',
            ]);

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }

        try {
            Mail::to(config('app.HOTEL_EMAIL'))
                ->send(new ReservationStatusMail($reservation));

            if (!empty($customer->email)) {
                Mail::to($customer->email)
                    ->send(new ReservationStatusMail($reservation));
            }
        } catch (\Exception $e) {
            report($e);
        }

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking created successfully!');
    }

    public function edit(Reservation $booking)
    {
        return view('pages.bookings.create', [
            'booking' => $booking
        ]);
    }

    public function update(Request $request, Reservation $booking)
    {
        try {
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

            $oldStatus = $booking->status;

            $booking->update($validated);
            try {
                if (!empty($validated['email']) && $oldStatus !== $validated['status']) {
                    Mail::to($validated['email'])
                        ->queue(new ReservationStatusMail($booking->fresh()));
                }
            } catch (\Exception $e) {
                report($e);
            }
            $redirect_to = $request->redirect_to;
            return redirect()
                ->route('admin.bookings.index', [
                    'status' => $redirect_to
                ])
                ->with('success', 'Booking updated successfully!');
        } catch (\Exception $e) {

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.' . $e->getMessage());
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
