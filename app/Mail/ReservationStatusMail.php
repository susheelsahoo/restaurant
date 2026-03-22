<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Reservation;
use Carbon\Carbon;
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
        $this->reservation->loadMissing(['customer', 'reservationStatus']);

        $data = $this->templateData();

        $renderedSubject = $this->renderBladeString($this->template->subject, $data);
        $renderedMessage = $this->renderBladeString($this->template->message, $data);

        return $this
            ->subject($renderedSubject)
            ->html($renderedMessage);
    }

    private function renderBladeString(?string $value, array $data): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        try {
            return Blade::render($value, $data);
        } catch (\Throwable $e) {
            report($e);
            return $value;
        }
    }

    private function templateData(): array
    {
        $customer = $this->reservation->customer;
        $visitDate = $this->reservation->visit_date ? Carbon::parse($this->reservation->visit_date) : null;
        $visitTime = $this->reservation->visit_time ? Carbon::parse($this->reservation->visit_time) : null;
        $customerFirstName = $customer?->first_name ?: 'Guest';
        $customerLastName = $customer?->last_name ?? '';
        $customerName = trim($customerFirstName . ' ' . $customerLastName);

        return [
            'reservation' => $this->reservation,
            'template' => $this->template,
            'customer' => $customer,
            'customer_name' => $customerName,
            'customer_first_name' => $customerFirstName,
            'customer_last_name' => $customerLastName,
            'guest_name' => $customerName,
            'guest_first_name' => $customerFirstName,
            'guest_last_name' => $customerLastName,
            'booking_code' => $this->reservation->booking_code,
            'visit_date_formatted' => $visitDate?->format('d M Y') ?? '',
            'visit_time_formatted' => $visitTime?->format('H:i') ?? '',
            'guests_count' => $this->reservation->guests,
            'status_label' => $this->reservation->reservationStatus?->label ?? '',
            'location' => config('app.LOCATION'),
            'google_maps' => config('app.GOOGLE_MAPS'),
            'contact_number' => config('app.CONTACT_NUMBER'),
        ];
    }
}
