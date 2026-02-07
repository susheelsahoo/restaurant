<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\ReservationStatusMail;
use Illuminate\Support\Facades\Mail;


class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'visit_date'        => 'required|date',
            'visit_time'        => 'required',
            'guests'            => 'required|integer|min:1',
            'customer_name'     => 'required|string|max:255',
            'phone'             => 'required|string|max:20',
            'email'             => 'required|email',
        ]);

        $reservation = Reservation::create([
            'booking_code'      => $this->generateBookingCode(),
            'visit_date'        => $request->visit_date,
            'visit_time'        => $request->visit_time,
            'guests'            => $request->guests,
            'customer_name'     => $request->customer_name,
            'phone'             => $request->phone,
            'email'             => $request->email,
            'status'            => Reservation::STATUS_NEW,
        ]);

        Mail::to(config('app.HOTEL_EMAIL'))
            ->send(new ReservationStatusMail($reservation));

        Mail::to($reservation->email)
            ->send(new ReservationStatusMail($reservation));

        return redirect()
            ->back()
            ->with([
                'alert_title' => 'Reservation Request Sent',
                'alert_text'  => 'Thank you for booking a table at Tifliso. Our team will respond soon!',
                'alert_icon'  => 'success',
            ]);
    }
    private function generateBookingCode(): string
    {
        return 'TFL-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
    }


    public function store_old(Request $request, BookingService $service): JsonResponse
    {
        dd('ReservationController@store is deprecated. Use BookingController@store instead.');
        $validated = $request->validate([
            'visit_date' => ['required', 'date', 'after_or_equal:today'],
            'visit_time' => ['required'],
            'guests'     => ['required', 'integer', 'min:1', 'max:20'],
            'name'       => ['required', 'string', 'max:120'],
            'phone'      => ['required', 'string', 'max:25'],
            'email'      => ['required', 'email', 'max:150'],
        ]);

        if (Reservation::where('visit_date', $validated['visit_date'])
            ->where('visit_time', $validated['visit_time'])->exists()
        ) {
            return response()->json(['message' => 'Slot already booked'], 409);
        }

        $validated['booking_code'] = $service->generateBookingCode();

        $booking = Reservation::create($validated);

        //dispatch(new \App\Jobs\SendBookingEmailJob($booking));
        //dispatch(new \App\Jobs\SendWhatsappJob($booking));

        return response()->json([
            'message' => "Booking confirmed! ID: {$booking->booking_code}"
        ]);
    }

    public function slots(string $date, BookingService $service): JsonResponse
    {
        return response()->json($service->generateSlots($date));
    }
}
