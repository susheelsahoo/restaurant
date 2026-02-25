<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

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

        $data = [
            'reservation' => $this->reservation,
            'template' => $this->template,
        ];

        $renderedSubject = $this->renderBladeString($this->template->subject, $data);
        $renderedShortText = $this->renderBladeString($this->template->short_text, $data);
        $renderedMessage = $this->renderBladeString($this->template->message, $data);

        return $this
            ->subject($renderedSubject)
            ->view('emails.reservation-status')
            ->with([
                'reservation' => $this->reservation,
                'template' => $this->template,
                'renderedShortText' => $renderedShortText,
                'renderedMessage' => $renderedMessage,
            ]);
    }

    private function renderBladeString(?string $value, array $data): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        try {
            return Blade::render($value, $data);
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
