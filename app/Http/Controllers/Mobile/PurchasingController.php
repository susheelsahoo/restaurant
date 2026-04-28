<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\Concerns\FormatsMobileValues;
use App\Mail\PurchaseOrderSupplierMail;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderReceivingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PurchasingController extends Controller
{
    use FormatsMobileValues;

    public function index()
    {
        return view('mobile.orders', [
            'purchaseOrders' => $this->purchaseOrderListData(),
        ]);
    }

    public function show(?PurchaseOrder $purchaseOrder = null)
    {
        if ($purchaseOrder === null) {
            $purchaseOrder = PurchaseOrder::query()
                ->latest('order_date')
                ->latest('id')
                ->first();
        }

        if (!$purchaseOrder) {
            return redirect('/mobile/orders');
        }

        return view('mobile.purchase-order', [
            'purchaseOrderReview' => $this->purchaseOrderDetailData($purchaseOrder),
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', $this->purchaseOrderStatuses()),
            'channel' => 'nullable|in:email,whatsapp',
        ]);

        $oldStatus = $purchaseOrder->status;
        $newStatus = $validated['status'];
        $channel = $validated['channel'] ?? 'email';

        $purchaseOrder->update([
            'status' => $newStatus,
        ]);

        if ($oldStatus !== 'sent' && $newStatus === 'sent' && $channel === 'email' && $purchaseOrder->supplier?->email) {
            try {
                Mail::to($purchaseOrder->supplier->email)
                    ->queue(new PurchaseOrderSupplierMail($purchaseOrder));
            } catch (\Exception $e) {
                Log::error('Failed to send mobile PO email: ' . $e->getMessage());
            }
        }

        $redirectUrl = $newStatus === 'sent'
            ? '/mobile/orders'
            : '/mobile/purchase-order/' . $purchaseOrder->id;
        $message = $newStatus === 'sent'
            ? 'Purchase order sent successfully.'
            : 'Purchase order status updated successfully.';

        return redirect($redirectUrl)
            ->with('success', $message);
    }

    public function receive(
        Request $request,
        PurchaseOrder $purchaseOrder,
        PurchaseOrderReceivingService $receivingService
    ) {
        $validated = $request->validate([
            'receipts' => 'required|array|min:1',
            'receipts.*' => 'required|numeric|min:0',
        ]);

        $updatedPurchaseOrder = $receivingService->receive($purchaseOrder, $validated['receipts']);
        $message = $updatedPurchaseOrder->status === 'completed'
            ? 'All items received. Purchase order marked completed.'
            : 'Received quantities updated. Purchase order marked partial.';

        return redirect('/mobile/purchase-order/' . $purchaseOrder->id)
            ->with('success', $message);
    }

    private function purchaseOrderListData(?int $limit = null)
    {
        $query = PurchaseOrder::query()
            ->with([
                'request:id,request_no,department_id',
                'request.department:id,name',
                'supplier:id,name',
                'buyer:id,name',
                'items.product:id,name,category_id,estimated_price',
                'items.product.category:id,name',
            ])
            ->withCount('items')
            ->latest('order_date')
            ->latest('id');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->map(function (PurchaseOrder $purchaseOrder) {
            $statusMeta = $this->purchaseOrderStatusMeta($purchaseOrder->status);
            $totalAmount = $this->purchaseOrderDisplayTotal($purchaseOrder);

            return [
                'id' => $purchaseOrder->id,
                'po_number' => $purchaseOrder->po_number,
                'supplier' => $purchaseOrder->supplier?->name ?: '-',
                'buyer' => $purchaseOrder->buyer?->name ?: '-',
                'request_no' => $purchaseOrder->request?->request_no ?: '-',
                'category_summary' => $purchaseOrder->category_summary,
                'department' => $purchaseOrder->request?->department?->name ?: '-',
                'status_label' => $statusMeta['label'],
                'status_tone' => $statusMeta['badge_tone'],
                'summary_badge' => $purchaseOrder->status === 'delayed' ? 'badge-yellow' : 'badge-blue',
                'order_date' => $purchaseOrder->order_date?->format('d M Y') ?: '-',
                'expected_delivery' => $purchaseOrder->expected_delivery?->format('d M Y') ?: '-',
                'items_count' => $purchaseOrder->items_count,
                'total' => $totalAmount,
                'total_label' => $this->formatMoney($totalAmount),
                'detail_url' => url('/mobile/purchase-order/' . $purchaseOrder->id),
            ];
        });
    }

    private function purchaseOrderDetailData(PurchaseOrder $purchaseOrder): array
    {
        $purchaseOrder->load([
            'request.requester:id,name',
            'request.department:id,name',
            'supplier:id,name,email,phone',
            'buyer:id,name',
            'items.product:id,name,unit,category_id,estimated_price',
            'items.product.category:id,name',
        ]);

        $statusMeta = $this->purchaseOrderStatusMeta($purchaseOrder->status);
        $items = $purchaseOrder->items
            ->map(function ($item) {
                $quantity = (float) $item->quantity;
                $receivedQuantity = (float) $item->received_qty;
                $unitPrice = $this->displayUnitPrice($item);
                $unit = $item->product?->unit ?: 'unit';

                return [
                    'id' => $item->id,
                    'name' => $item->product?->name ?: 'Unknown product',
                    'category' => $item->product?->category?->name ?: 'Uncategorized',
                    'ordered_quantity' => $quantity,
                    'received_quantity' => $receivedQuantity,
                    'unit' => $unit,
                    'ordered_label' => $this->formatQuantity($quantity) . ' ' . $unit,
                    'received_label' => $this->formatQuantity($receivedQuantity) . ' ' . $unit,
                    'unit_price_label' => $this->formatMoney($unitPrice),
                    'unit_price' => $unitPrice,
                    'line_total' => $quantity * $unitPrice,
                    'line_total_label' => $this->formatMoney($quantity * $unitPrice),
                ];
            })
            ->values();
        $totalAmount = (float) $items->sum('line_total');

        $categories = $items
            ->groupBy('category')
            ->map(function ($categoryItems, string $categoryName) {
                return [
                    'name' => $categoryName,
                    'items' => $categoryItems->values(),
                    'items_count' => $categoryItems->count(),
                    'total_label' => $this->formatMoney((float) $categoryItems->sum('line_total')),
                ];
            })
            ->values();

        $orderedQuantity = (float) $purchaseOrder->items->sum(fn ($item) => (float) $item->quantity);
        $receivedQuantity = (float) $purchaseOrder->items->sum(fn ($item) => (float) $item->received_qty);
        $receivedPercent = $orderedQuantity > 0 ? min(100, ($receivedQuantity / $orderedQuantity) * 100) : 0;
        $hasSupplier = $purchaseOrder->supplier !== null;
        $sentStatuses = ['sent', 'confirmed', 'partial', 'completed'];
        $dispatchStatus = match (true) {
            !$hasSupplier => 'unassigned',
            $purchaseOrder->status === 'delayed' => 'delayed',
            in_array($purchaseOrder->status, $sentStatuses, true) => 'sent',
            default => 'ready',
        };
        $dispatchMeta = match ($dispatchStatus) {
            'unassigned' => ['label' => 'Supplier Needed', 'tone' => 'orange'],
            'sent' => ['label' => $statusMeta['label'], 'tone' => 'blue'],
            'delayed' => ['label' => 'Delayed', 'tone' => 'red'],
            default => ['label' => 'Ready to Send', 'tone' => 'green'],
        };
        $emailPreview = (new PurchaseOrderSupplierMail($purchaseOrder))->renderedContent();

        return [
            'id' => $purchaseOrder->id,
            'po_number' => $purchaseOrder->po_number,
            'status' => $purchaseOrder->status,
            'status_label' => $statusMeta['label'],
            'status_pill_tone' => $statusMeta['pill_tone'],
            'supplier' => $purchaseOrder->supplier?->name ?: '-',
            'supplier_email' => $purchaseOrder->supplier?->email ?: '-',
            'supplier_phone' => $purchaseOrder->supplier?->phone ?: '-',
            'buyer' => $purchaseOrder->buyer?->name ?: '-',
            'request_no' => $purchaseOrder->request?->request_no ?: '-',
            'category_summary' => $purchaseOrder->category_summary,
            'requester' => $purchaseOrder->request?->requester?->name ?: '-',
            'department' => $purchaseOrder->request?->department?->name ?: '-',
            'order_date' => $purchaseOrder->order_date?->format('M d, Y') ?: '-',
            'order_date_short' => $purchaseOrder->order_date?->format('M d') ?: '-',
            'expected_delivery' => $purchaseOrder->expected_delivery?->format('M d, Y') ?: '-',
            'expected_delivery_short' => $purchaseOrder->expected_delivery?->format('M d') ?: '-',
            'items_count' => $items->count(),
            'items' => $items,
            'categories' => $categories,
            'total' => $totalAmount,
            'total_label' => $this->formatMoney($totalAmount),
            'received_label' => $this->formatQuantity($receivedQuantity) . ' / ' . $this->formatQuantity($orderedQuantity),
            'received_percent' => round($receivedPercent),
            'dispatch_status' => $dispatchStatus,
            'dispatch_label' => $dispatchMeta['label'],
            'dispatch_pill_tone' => $dispatchMeta['tone'],
            'ready_count' => $dispatchStatus === 'ready' ? 1 : 0,
            'sent_count' => $dispatchStatus === 'sent' ? 1 : 0,
            'supplier_needed_count' => $dispatchStatus === 'unassigned' ? 1 : 0,
            'statuses' => $this->purchaseOrderStatuses(),
            'status_actions' => ['confirmed', 'partial', 'completed', 'delayed'],
            'email_preview_subject' => $emailPreview['subject'],
            'email_preview_html' => $emailPreview['html'],
            'supplier_message_text' => $emailPreview['text'],
        ];
    }

    private function purchaseOrderStatusMeta(string $status): array
    {
        return match ($status) {
            'sent' => ['label' => 'Sent', 'badge_tone' => 'blue', 'pill_tone' => 'blue'],
            'confirmed' => ['label' => 'Confirmed', 'badge_tone' => 'success', 'pill_tone' => 'green'],
            'partial' => ['label' => 'Partial', 'badge_tone' => 'blue', 'pill_tone' => 'orange'],
            'completed' => ['label' => 'Completed', 'badge_tone' => 'success', 'pill_tone' => 'green'],
            'delayed' => ['label' => 'Delayed', 'badge_tone' => 'danger', 'pill_tone' => 'red'],
            default => ['label' => 'Draft', 'badge_tone' => 'secondary', 'pill_tone' => 'blue'],
        };
    }

    private function purchaseOrderStatuses(): array
    {
        return ['draft', 'sent', 'confirmed', 'partial', 'completed', 'delayed'];
    }

    private function purchaseOrderDisplayTotal(PurchaseOrder $purchaseOrder): float
    {
        return (float) $purchaseOrder->items->sum(function ($item) {
            return ((float) $item->quantity) * $this->displayUnitPrice($item);
        });
    }

    private function displayUnitPrice($item): float
    {
        $poUnitPrice = (float) $item->unit_price;

        if ($poUnitPrice > 0) {
            return $poUnitPrice;
        }

        return (float) ($item->product?->estimated_price ?? 0);
    }
}
