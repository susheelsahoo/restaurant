<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class CustomerNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public string $subjectTemplate,
        public string $messageTemplate
    ) {
    }

    public function build()
    {
        $data = $this->templateData();

        return $this
            ->subject($this->renderBladeString($this->subjectTemplate, $data))
            ->html($this->renderBladeString($this->messageTemplate, $data));
    }

    private function templateData(): array
    {
        return [
            'customer' => $this->customer,
            'customer_name' => trim(($this->customer->first_name ?? '') . ' ' . ($this->customer->last_name ?? '')) ?: 'Guest',
            'customer_first_name' => $this->customer->first_name ?? 'Guest',
            'customer_last_name' => $this->customer->last_name ?? '',
            'customer_email' => $this->customer->email ?? '',
            'customer_phone' => $this->customer->phone ?? '',
            // Reservation-style aliases so existing templates can be reused for customer notifications.
            'guest_name' => trim(($this->customer->first_name ?? '') . ' ' . ($this->customer->last_name ?? '')) ?: 'Guest',
            'guest_first_name' => $this->customer->first_name ?? 'Guest',
            'guest_last_name' => $this->customer->last_name ?? '',
            'location' => config('app.LOCATION'),
            'google_maps' => config('app.GOOGLE_MAPS'),
            'contact_number' => config('app.CONTACT_NUMBER'),
            // Keep a default offer code available for promotional templates.
            'offer_code' => 'WELCOME10',
        ];
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
}
