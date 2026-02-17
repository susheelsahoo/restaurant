<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function build()
    {
        $this->reservation->load('reservationStatus');

        return $this
            ->subject($this->subjectByStatus())
            ->view('emails.reservation-status')
            ->with(['reservation' => $this->reservation]);
    }

    /**
     * Get email subject based on reservation status from database
     */
    private function subjectByStatus(): string
    {
        $statusName = $this->reservation->reservationStatus?->name;

        return match ($statusName) {
            'pending' =>
            'Reservation Request Received – Tifliso',

            'confirmed' =>
            'Your Table Is Confirmed – Tifliso',

            'declined' =>
            'Reservation Cancellation Confirmation – Tifliso Georgian Restaurant',

            'in-house' =>
            'Customer Arrived – Tifliso Georgian Restaurant',

            'complete' =>
            'Thank You for Visiting Tifliso',

            default =>
            'Reservation Update – Tifliso',
        };
    }
}
