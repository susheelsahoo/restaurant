<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * Generate available time slots for a given date.
     */
    public function generateSlots(string $date): array
    {
        $open  = Carbon::parse(config('restaurant.open_time'));
        $close = Carbon::parse(config('restaurant.close_time'));

        $slots = [];

        while ($open < $close) {
            $time = $open->format('H:i');

            $exists = Reservation::where('visit_date', $date)
                ->where('visit_time', $time)
                ->exists();

            if (!$exists) {
                $slots[] = $time;
            }

            $open->addMinutes(config('restaurant.slot_gap'));
        }

        return $slots;
    }

    /**
     * Generate unique booking reference.
     */
    public function generateBookingCode(): string
    {
        return 'TB-' . strtoupper(Str::random(8));
    }
}
