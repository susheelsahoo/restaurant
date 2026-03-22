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
        $customerFirstName = $this->customer->first_name ?: 'Guest';
        $customerLastName = $this->customer->last_name ?? '';
        $customerName = trim($customerFirstName . ' ' . $customerLastName);

        return [
            'customer' => $this->customer,
            'customer_name' => $customerName,
            'customer_first_name' => $customerFirstName,
            'customer_last_name' => $customerLastName,
            'customer_email' => $this->customer->email ?? '',
            'customer_phone' => $this->customer->phone ?? '',
            // Reservation-style aliases so existing templates can be reused for customer notifications.
            'guest_name' => $customerName,
            'guest_first_name' => $customerFirstName,
            'guest_last_name' => $customerLastName,
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
