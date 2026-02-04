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
        return $this
            ->subject($this->subjectByStatus())
            ->view('emails.reservation-status')
            ->with(['reservation' => $this->reservation]);
    }

    private function subjectByStatus(): string
    {
        return match ($this->reservation->status) {
            Reservation::STATUS_NEW =>
            'Reservation Request Received – Tifliso',

            Reservation::STATUS_CONFIRMED =>
            'Table Reservation Confirmed – Tifliso',

            Reservation::STATUS_CANCELLED =>
            'Reservation Cancelled – Tifliso',

            Reservation::STATUS_COMPLETE =>
            'Thank You for Visiting Tifliso',

            default =>
            'Reservation Update – Tifliso',
        };
    }
}
