<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

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
        $renderedMessage = $this->renderBladeString($this->messageTemplate, $data);

        return $this
            ->subject($this->renderBladeString($this->subjectTemplate, $data))
            ->html($this->appendUnsubscribeFooter($renderedMessage, $data['unsubscribe_url']));
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
            'unsubscribe_url' => URL::temporarySignedRoute(
                'customers.unsubscribe',
                now()->addDays(30),
                ['customer' => $this->customer->id]
            ),
        ];
    }

    private function appendUnsubscribeFooter(string $html, string $unsubscribeUrl): string
    {
        $footer = '<div style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e7eb;font-family:Arial,sans-serif;font-size:12px;color:#6b7280;text-align:center;">'
            . 'If you no longer want promotional emails, '
            . '<a href="' . e($unsubscribeUrl) . '" style="color:#0d6efd;">unsubscribe here</a>.'
            . '</div>';

        if (stripos($html, '</body>') !== false) {
            return preg_replace('/<\/body>/i', $footer . '</body>', $html, 1) ?? ($html . $footer);
        }

        return $html . $footer;
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
