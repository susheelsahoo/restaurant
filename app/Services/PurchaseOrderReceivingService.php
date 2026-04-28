<?php

namespace App\Services;

use App\Models\PoItem;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderReceivingService
{
    private const QUANTITY_EPSILON = 0.00001;

    public function receive(PurchaseOrder $purchaseOrder, array $receipts): PurchaseOrder
    {
        return DB::transaction(function () use ($purchaseOrder, $receipts) {
            $purchaseOrder->load('items.product');

            if ($purchaseOrder->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'receipts' => 'This purchase order does not have any items to receive.',
                ]);
            }

            $normalizedReceipts = $this->normalizeReceipts($receipts);
            $validItemIds = $purchaseOrder->items
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $invalidItemIds = array_diff(array_keys($normalizedReceipts), $validItemIds);

            if (!empty($invalidItemIds)) {
                throw ValidationException::withMessages([
                    'receipts' => 'One or more submitted items do not belong to this purchase order.',
                ]);
            }

            $purchaseOrder->items->each(function (PoItem $item) use ($normalizedReceipts) {
                $itemId = (int) $item->id;

                if (!array_key_exists($itemId, $normalizedReceipts)) {
                    return;
                }

                $orderedQuantity = (float) $item->quantity;
                $receivedQuantity = $normalizedReceipts[$itemId];

                if ($receivedQuantity - $orderedQuantity > self::QUANTITY_EPSILON) {
                    $itemName = $item->product?->name ?: 'this item';

                    throw ValidationException::withMessages([
                        'receipts.' . $itemId => 'Received quantity for ' . $itemName . ' cannot be greater than ordered quantity.',
                    ]);
                }

                $item->update([
                    'received_qty' => $receivedQuantity,
                ]);
            });

            $purchaseOrder->load('items');
            $purchaseOrder->update([
                'status' => $this->allItemsReceived($purchaseOrder) ? 'completed' : 'partial',
            ]);
            $purchaseOrder->refreshStatusFromSubOrders();

            return $purchaseOrder->refresh()->load('items.product');
        });
    }

    private function normalizeReceipts(array $receipts): array
    {
        $normalized = [];

        foreach ($receipts as $itemId => $receivedQuantity) {
            if (!ctype_digit((string) $itemId)) {
                throw ValidationException::withMessages([
                    'receipts' => 'Received item data is invalid.',
                ]);
            }

            if (!is_numeric($receivedQuantity) || (float) $receivedQuantity < 0) {
                throw ValidationException::withMessages([
                    'receipts.' . $itemId => 'Received quantity must be zero or greater.',
                ]);
            }

            $normalized[(int) $itemId] = round((float) $receivedQuantity, 2);
        }

        if (empty($normalized)) {
            throw ValidationException::withMessages([
                'receipts' => 'Please enter received quantities for at least one item.',
            ]);
        }

        return $normalized;
    }

    private function allItemsReceived(PurchaseOrder $purchaseOrder): bool
    {
        return $purchaseOrder->items->every(function (PoItem $item) {
            $orderedQuantity = (float) $item->quantity;
            $receivedQuantity = (float) $item->received_qty;

            return $receivedQuantity + self::QUANTITY_EPSILON >= $orderedQuantity;
        });
    }
}
