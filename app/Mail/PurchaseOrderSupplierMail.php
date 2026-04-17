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
    public EmailTemplate $template;

    /**
     * Create a new message instance.
     */
    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
        $this->template = EmailTemplate::getBySlug('purchase-order-supplier');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $this->purchaseOrder->loadMissing(['supplier', 'buyer', 'items.product', 'request']);

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
        $po = $this->purchaseOrder;

        $items = $po->items->map(function ($item) {
            return [
                'name' => $item->product->name ?? 'Unknown Product',
                'quantity' => $item->quantity,
                'unit' => $item->unit ?? 'pcs',
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
}