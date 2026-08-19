<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class PurchaseOrderSupplierMail extends Mailable
{
    use Queueable, SerializesModels;

    public PurchaseOrder $purchaseOrder;
    public ?EmailTemplate $template;

    /**
     * Create a new message instance.
     */
    public function __construct(
        PurchaseOrder $purchaseOrder,
        string $templateSlug = 'purchase-order-supplier'
    )
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->template = EmailTemplate::getBySlug($templateSlug);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $content = $this->renderedContent();

        return $this
            ->subject($content['subject'])
            ->html($content['html']);
    }

    public function renderedContent(): array
    {
        $this->purchaseOrder->loadMissing(['supplier', 'buyer', 'items.product', 'request']);

        $data = $this->templateData();

        $html = $this->renderBladeString(
            $this->template?->message ?: $this->fallbackMessageTemplate(),
            $data
        );

        return [
            'subject' => $this->renderBladeString(
                $this->template?->subject ?: 'Purchase Order {{ $po_number }}',
                $data
            ),
            'html' => $html,
            'text' => $this->htmlToText($html),
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

    private function templateData(): array
    {
        $po = $this->purchaseOrder;

        $items = $po->items->map(function ($item) {
            return [
                'name' => $item->product->name ?? 'Unknown Product',
                'quantity' => $item->quantity,
                'unit' => $item->product?->unit ?? 'pcs',
                'unit_price' => number_format($item->unit_price, 2),
                'line_total' => number_format($item->quantity * $item->unit_price, 2),
            ];
        });

        $totalAmount = $po->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        return [
            'po_number' => $po->po_number,
            'order_date' => $po->order_date?->format('d M Y') ?? 'N/A',
            'expected_delivery' => $po->expected_delivery?->format('d M Y') ?? 'N/A',
            'supplier_name' => $po->supplier->name ?? 'Supplier',
            'buyer_name' => $po->buyer->name ?? 'Buyer',
            'total_amount' => number_format($totalAmount, 2),
            'location' => config('app.LOCATION', 'Restaurant'),
            'contact_number' => config('app.CONTACT_NUMBER', 'Contact Number'),
            'items' => $items,
            'special_instructions' => $po->notes ?? null,
        ];
    }

    private function fallbackMessageTemplate(): string
    {
        return <<<'HTML'
            <p>Dear {{ $supplier_name }},</p>
            <p>Please find purchase order {{ $po_number }} for expected delivery on {{ $expected_delivery }}.</p>
            <p>Total Amount: {{ $total_amount }}</p>
            <p>Please confirm receipt of this order.</p>
            <p>Best regards,<br>{{ $location }}</p>
        HTML;
    }

    private function htmlToText(string $html): string
    {
        $withBreaks = preg_replace('/<(br|\/p|\/tr|\/div|\/h[1-6])\b[^>]*>/i', "\n", $html) ?? $html;
        $text = html_entity_decode(strip_tags($withBreaks), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }
}
