<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Carbon\Carbon;

class PurchaseOrderGenerationService
{
    public function createFromRequest(PurchaseRequest $purchaseRequest, ?int $buyerId): void
    {
        if ($purchaseRequest->purchaseOrders()->exists()) {
            return;
        }

        $purchaseRequest->load([
            'items.product:id,category_id,estimated_price',
            'items.supplier:id',
        ]);

        $itemsBySupplierAndCategory = $purchaseRequest->items
            ->filter(fn ($item) => $item->supplier_id !== null)
            ->groupBy(function ($item) {
                $categoryId = $item->product?->category_id ?: 'uncategorized';

                return $item->supplier_id . '|' . $categoryId;
            });

        foreach ($itemsBySupplierAndCategory as $items) {
            $firstItem = $items->first();

            if (!$firstItem) {
                continue;
            }

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $this->generatePoNumber(),
                'request_id' => $purchaseRequest->id,
                'supplier_id' => $firstItem->supplier_id,
                'buyer_id' => $buyerId,
                'status' => 'draft',
                'order_date' => Carbon::now()->toDateString(),
                'expected_delivery' => $purchaseRequest->needed_by?->toDateString(),
            ]);

            foreach ($items as $requestItem) {
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

        $lastNumber = PurchaseOrder::query()
            ->where('po_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('po_number');

        if (!$lastNumber || !preg_match('/(\d+)$/', $lastNumber, $matches)) {
            return $prefix . '0001';
        }

        return $prefix . str_pad(((int) $matches[1]) + 1, 4, '0', STR_PAD_LEFT);
    }
}
