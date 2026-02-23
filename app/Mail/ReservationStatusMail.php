<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;
    public EmailTemplate $template;

    public function __construct(Reservation $reservation, EmailTemplate $template)
    {
        $this->reservation = $reservation;
        $this->template = $template;
    }

    /**
     * Resolve the active template for a reservation status.
     */
    public static function resolveTemplateForReservation(Reservation $reservation): ?EmailTemplate
    {
        $reservation->loadMissing('reservationStatus');
        $statusName = $reservation->reservationStatus?->name;

        $statusMapping = [
            'pending' => 'reservation-pending',
            'confirmed' => 'reservation-confirmed',
            'canceled' => 'reservation-canceled',
            'cancelled' => 'reservation-canceled',
            'declined' => 'reservation-declined',
            'in-house' => 'reservation-in-house',
            'complete' => 'reservation-complete',
        ];

        $templateSlug = $statusMapping[$statusName] ?? null;
        if (!$templateSlug) {
            return null;
        }

        return EmailTemplate::getBySlug($templateSlug);
    }

    public function build()
    {
        $this->reservation->load('reservationStatus');

        return $this
            ->subject($this->template->subject)
            ->view('emails.reservation-status')
            ->with([
                'reservation' => $this->reservation,
                'template' => $this->template,
            ]);
    }
}
