<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Carbon\Carbon;

class PurchaseOrderGenerationService
{
    public function createFromRequest(PurchaseRequest $purchaseRequest, ?int $buyerId): void
    {
        if ($purchaseRequest->purchaseOrders()->whereNull('parent_po_id')->exists()) {
            return;
        }

        $purchaseRequest->load([
            'items.product:id,name,category_id,estimated_price',
            'items.product.category:id,name',
        ]);

        $itemsForPurchaseOrders = $purchaseRequest->items
            ->map(function ($item) {
                return [
                    'request_item' => $item,
                    'category_id' => $item->product?->category_id ?: 'uncategorized',
                ];
            });

        $itemsByCategory = $itemsForPurchaseOrders->groupBy('category_id');

        if ($itemsByCategory->isEmpty()) {
            return;
        }

        $poNumber = $this->generatePoNumber();
        $orderDate = Carbon::now()->toDateString();
        $expectedDelivery = $purchaseRequest->needed_by?->toDateString();

        $mainPurchaseOrder = PurchaseOrder::create([
            'po_number' => $poNumber,
            'request_id' => $purchaseRequest->id,
            'supplier_id' => null,
            'buyer_id' => $buyerId,
            'status' => 'draft',
            'order_date' => $orderDate,
            'expected_delivery' => $expectedDelivery,
        ]);

        foreach ($itemsByCategory->values() as $index => $items) {
            $firstItem = $items->first();

            if (!$firstItem) {
                continue;
            }

            $suffix = $this->suffixForIndex($index);
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber . '-' . $suffix,
                'parent_po_id' => $mainPurchaseOrder->id,
                'po_suffix' => $suffix,
                'request_id' => $purchaseRequest->id,
                'supplier_id' => null,
                'buyer_id' => $buyerId,
                'status' => 'draft',
                'order_date' => $orderDate,
                'expected_delivery' => $expectedDelivery,
            ]);

            foreach ($items as $item) {
                $requestItem = $item['request_item'];

                $purchaseOrder->items()->create([
                    'product_id' => $requestItem->product_id,
                    'quantity' => $requestItem->quantity,
                    'received_qty' => 0,
                    'unit_price' => (float) ($requestItem->product?->estimated_price ?? 0),
                ]);
            }
        }
    }

    private function generatePoNumber(): string
    {
        $year = Carbon::now()->format('Y');
        $prefix = 'PO-' . $year . '-';

        $maxNumber = PurchaseOrder::query()
            ->where('po_number', 'like', $prefix . '%')
            ->pluck('po_number')
            ->reduce(function (int $max, string $poNumber) use ($prefix) {
                $pattern = '/^' . preg_quote($prefix, '/') . '(\d+)(?:-[A-Z]+)?$/';

                if (!preg_match($pattern, $poNumber, $matches)) {
                    return $max;
                }

                return max($max, (int) $matches[1]);
            }, 0);

        if ($maxNumber <= 0) {
            return $prefix . '0001';
        }

        return $prefix . str_pad($maxNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    private function suffixForIndex(int $index): string
    {
        $suffix = '';
        $number = $index + 1;

        while ($number > 0) {
            $number--;
            $suffix = chr(65 + ($number % 26)) . $suffix;
            $number = intdiv($number, 26);
        }

        return $suffix;
    }
}
