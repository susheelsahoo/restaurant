<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request, BookingService $service): JsonResponse
    {
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

        dispatch(new \App\Jobs\SendBookingEmailJob($booking));
        dispatch(new \App\Jobs\SendWhatsappJob($booking));

        return response()->json([
            'message' => "Booking confirmed! ID: {$booking->booking_code}"
        ]);
    }

    public function slots(string $date, BookingService $service): JsonResponse
    {
        return response()->json($service->generateSlots($date));
    }
}
