<?php

namespace App\Http\Controllers\Mobile\Concerns;

use App\Models\PurchaseRequest;

trait BuildsRequestSummaries
{
    protected function requestListData(?int $limit = null)
    {
        $query = PurchaseRequest::query()
            ->with(['department:id,name', 'items.product:id,name'])
            ->withCount('items')
            ->latest('created_at')
            ->latest('id');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->map(function (PurchaseRequest $purchaseRequest) {
            return [
                'request_no' => $purchaseRequest->request_no,
                'department' => optional($purchaseRequest->department)->name ?: '-',
                'status' => $purchaseRequest->status,
                'priority' => ucfirst($purchaseRequest->priority),
                'needed_by' => optional($purchaseRequest->needed_by)->format('d M, H:i') ?: '-',
                'items_count' => $purchaseRequest->items_count,
                'summary' => $purchaseRequest->items
                    ->map(fn ($item) => optional($item->product)->name)
                    ->filter()
                    ->take(3)
                    ->implode(', ') ?: 'No products',
                'is_urgent' => $purchaseRequest->priority === 'urgent',
                'detail_url' => $purchaseRequest->status === 'returned'
                    ? url('/mobile/request-detail/' . $purchaseRequest->request_no . '/edit')
                    : url('/mobile/request-detail/' . $purchaseRequest->request_no),
            ];
        });
    }
}
